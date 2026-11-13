<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use App\Notifications\ConfirmEmailChange;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

        return $u;
    }

    public function test_administrator_creates_user_and_sends_password_setup(): void
    {
        Notification::fake();
        $this->actingAs($this->admin())->post(route('admin.users.store'), ['firstname' => 'Анна', 'lastname' => 'Иванова', 'email' => 'anna@example.test', 'phone' => '8 (913) 000-01-23', 'birthdate' => '1990-01-01', 'role' => 'user', 'account_type' => 'athlete'])->assertSessionHasNoErrors();
        $user = User::where('email', 'anna@example.test')->firstOrFail();
        $this->assertSame('+79130000123', $user->phone);
        $this->assertTrue($user->hasRole('user'));
        Notification::assertSentTo($user, ResetPassword::class);
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.created', 'subject_id' => $user->id]);
    }

    public function test_user_cannot_manage_other_accounts(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
        $this->patch(route('admin.users.block', $other), ['blocked' => 1])->assertForbidden();
        $this->assertNull($other->fresh()->blocked_at);
    }

    public function test_block_prevents_login_and_invalidates_old_session_after_unblock(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create();
        $this->actingAs($admin)->patch(route('admin.users.block', $user), ['blocked' => 1])->assertSessionHasNoErrors();
        $this->assertNotNull($user->fresh()->blocked_at);
        $this->actingAs($user->fresh())->withSession(['account_session_version' => 0])->get('/profile')->assertRedirect(route('login'));
        $this->postJson('/login', ['email' => $user->email, 'password' => 'password'])->assertUnprocessable();
        $this->actingAs($admin)->patch(route('admin.users.block', $user), ['blocked' => 0])->assertSessionHasNoErrors();
        $this->actingAs($user->fresh())->withSession(['account_session_version' => 0])->get('/profile')->assertRedirect(route('login'));
        $this->postJson('/login', ['email' => $user->email, 'password' => 'password'])->assertOk();
        $this->get('/profile')->assertOk();
    }

    public function test_admin_cannot_block_or_demote_self(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->patch(route('admin.users.block', $admin), ['blocked' => 1])->assertSessionHasErrors('blocked');
        $this->assertNull($admin->fresh()->blocked_at);
    }

    public function test_child_changes_are_owner_scoped_and_archive_is_reversible(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $branch = Branch::forceCreate(['branch_name' => 'Метеор', 'branch_address' => 'Томск']);
        $data = ['firstname' => 'Иван', 'lastname' => 'Иванов', 'birthdate' => '2015-01-01', 'branch_id' => $branch->id];
        $child = $user->children()->create($data);
        $this->actingAs($other)->get(route('children.edit', $child))->assertNotFound();
        $this->patch(route('children.update', $child), $data)->assertNotFound();
        $this->actingAs($user)->patch(route('children.update', $child), array_merge($data, ['firstname' => 'Пётр']))->assertSessionHasNoErrors();
        $this->assertSame('Пётр', $child->fresh()->firstname);
        $this->patch(route('children.update', $child), array_merge($data, ['branch_id' => 999999]))->assertSessionHasErrors('branch_id');
        $this->patch(route('children.archive', $child), ['archived' => 1])->assertSessionHasNoErrors();
        $this->assertNotNull($child->fresh()->archived_at);
        $this->patch(route('children.archive', $child), ['archived' => 0])->assertSessionHasNoErrors();
        $this->assertNull($child->fresh()->archived_at);
    }

    public function test_avatar_is_private_replaced_and_removed(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('avatar.store'), ['avatar' => UploadedFile::fake()->image('photo.jpg')])->assertSessionHasNoErrors();
        $old = $user->fresh()->avatar_path;
        Storage::disk('local')->assertExists($old);
        $this->get(route('avatar.show'))->assertOk();
        $this->post(route('avatar.store'), ['avatar' => UploadedFile::fake()->image('new.png')])->assertSessionHasNoErrors();
        Storage::disk('local')->assertMissing($old);
        $this->post(route('avatar.store'), ['avatar' => UploadedFile::fake()->create('bad.svg', 1, 'image/svg+xml')])->assertSessionHasErrors('avatar');
        $path = $user->fresh()->avatar_path;
        $this->delete(route('avatar.destroy'))->assertRedirect();
        Storage::disk('local')->assertMissing($path);
    }

    public function test_registration_rejects_minor_and_equivalent_phone_and_accepts_no_patronymic(): void
    {
        User::factory()->create(['phone' => '8 (913) 000-01-23']);
        $data = ['firstname' => 'Анна', 'lastname' => 'Иванова', 'email' => 'anna@example.test', 'phone' => '+79130000123', 'birthdate' => '2015-01-01', 'password' => 'Secret-pass-123', 'password_confirmation' => 'Secret-pass-123', 'agreement' => 1, 'account_type' => 'athlete'];
        $this->postJson('/register', $data)->assertJsonValidationErrors(['phone', 'birthdate']);
        $data['phone'] = '+79130000124';
        $data['birthdate'] = '1990-01-01';
        $this->postJson('/register', $data)->assertCreated();
        $this->assertDatabaseHas('users', ['email' => 'anna@example.test', 'patronymic' => null, 'account_type' => 'athlete']);
    }

    public function test_email_changes_only_after_owner_uses_signed_confirmation(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $old = $user->email;
        $this->actingAs($user)->from('/profile')->patch('/profile', ['firstname' => 'Анна', 'lastname' => 'Иванова', 'email' => 'new@example.test'])->assertSessionHasNoErrors();
        $this->assertSame($old, $user->fresh()->email);
        Notification::assertSentOnDemand(ConfirmEmailChange::class, function ($notification) use ($user) {
            $this->get($notification->url)->assertRedirect(route('profile.edit'));
            $this->assertSame('new@example.test', $user->fresh()->email);
            $this->get($notification->url)->assertForbidden();

            return true;
        });
    }

    public function test_filtered_csv_excludes_other_rows_and_neutralizes_formulas(): void
    {
        $user = User::factory()->create(['lastname' => '=FORMULA', 'phone' => '+79130000123']);
        $user->assignRole(Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']));
        $other = User::factory()->create(['lastname' => 'Невидимый']);
        $other->assignRole('user');
        $response = $this->actingAs($this->admin())->get(route('admin.export', ['section' => 'parents', 'q' => '89130000123']));
        $response->assertOk();
        $csv = $response->streamedContent();
        $this->assertStringContainsString("'=FORMULA", $csv);
        $this->assertStringNotContainsString('Невидимый', $csv);
        $this->assertDatabaseHas('audit_logs', ['action' => 'records.exported']);
    }
}
