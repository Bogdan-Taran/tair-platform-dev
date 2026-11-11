<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class RouteHealthTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['/'])]
    #[TestWith(['/our-team'])]
    #[TestWith(['/our-branches'])]
    #[TestWith(['/about-club'])]
    #[TestWith(['/summer-camp-2026'])]
    #[TestWith(['/summer-camp-beret'])]
    #[TestWith(['/login'])]
    #[TestWith(['/register'])]
    #[TestWith(['/forgot-password'])]
    #[TestWith(['/reset-password/example-token'])]
    public function test_public_pages_render_without_a_vite_server(string $path): void
    {
        $this->get($path)->assertOk()->assertDontSee('@vite/client');
    }

    #[TestWith(['/dashboard', 'Дети'])]
    #[TestWith(['/documents', 'Все документы'])]
    #[TestWith(['/sign-contract', 'Договор-оферта'])]
    #[TestWith(['/passport', 'Паспортные данные'])]
    #[TestWith(['/profile', 'profile-firstname'])]
    #[TestWith(['/add-child', 'firstname'])]
    #[TestWith(['/confirm-password', 'password'])]
    public function test_parent_pages_render_their_content(string $path, string $content): void
    {
        $this->seed(PermissionsSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('user');
        $this->actingAs($user)->get($path)->assertOk()->assertSee($content);
    }

    #[TestWith(['/admin/dashboard', 'Все'])]
    #[TestWith(['/roles', 'Добавить роль'])]
    #[TestWith(['/roles/create', 'Добавление новой роли'])]
    public function test_admin_pages_render_their_content(string $path, string $content): void
    {
        $this->seed(PermissionsSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin)->get($path)->assertOk()->assertSee($content);
    }

    #[TestWith(['/dashboard'])]
    #[TestWith(['/passport'])]
    #[TestWith(['/admin/dashboard'])]
    #[TestWith(['/roles'])]
    public function test_private_pages_send_guests_to_the_custom_login(string $path): void
    {
        $this->get($path)->assertRedirect(route('login'));
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function test_missing_pages_return_a_custom_404_in_both_modes(bool $debug): void
    {
        config(['app.debug' => $debug]);
        $this->get('/nonexistent-page')->assertNotFound()->assertSee('Запрошенная страница не найдена')->assertSee('error-pages.css');
    }

    public function test_parent_cannot_manage_roles_and_sees_custom_403(): void
    {
        $this->actingAs(User::factory()->create())->get('/roles')->assertForbidden()
            ->assertSee('Доступ к запрошенному ресурсу запрещён')->assertSee('access-denied.png');
    }

    public function test_error_responses_remain_json_for_api_clients(): void
    {
        $this->getJson('/nonexistent-page')->assertNotFound()->assertHeader('Content-Type', 'application/json');
        $this->actingAs(User::factory()->create())->getJson('/roles')->assertForbidden()->assertHeader('Content-Type', 'application/json');
    }

    public function test_legacy_home_url_redirects_to_the_home_page(): void
    {
        $this->get('/index.html')->assertRedirect('/');
    }

    public function test_home_can_be_revisited_without_the_three_request_limit(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->get('/')->assertOk();
        }
    }

    public function test_passport_form_saves_only_the_current_users_details(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/passport', ['series' => '1234', 'number' => '123456', 'issue_date' => '2020-01-01', 'issue_by' => 'Отделение МВД города Томска', 'address' => 'Томск, улица Тестовая, дом 1'])
            ->assertSessionHasNoErrors()->assertRedirect(route('documents'));
        $this->assertDatabaseHas('passport_details', ['user_id' => $user->id, 'series' => '1234']);
        $this->get('/passport')->assertOk()->assertSee('123456');
    }

    public function test_roles_can_be_created_without_optional_permissions(): void
    {
        $this->seed(PermissionsSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user)->post('/roles', ['name' => 'coach'])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('roles', ['name' => 'coach']);
        $this->post('/roles', ['name' => 'coach'])->assertSessionHasErrors('name');
    }
}
