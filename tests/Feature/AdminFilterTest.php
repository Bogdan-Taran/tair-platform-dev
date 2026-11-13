<?php

namespace Tests\Feature;

use App\Models\SignedDocument;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminFilterTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

        return $u;
    }

    public function test_age_filter_includes_adult_athlete_without_children(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12'));
        $user = User::factory()->create(['account_type' => 'athlete', 'birthdate' => '2000-11-12']);
        $user->assignRole(Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']));
        $this->actingAs($this->admin())->get(route('admin.dashboard', ['section' => 'parents', 'min_age' => 26, 'max_age' => 26]))->assertOk()->assertViewHas('records', fn ($rows) => $rows->count() === 1 && $rows->first()->id === $user->id);
        $this->get(route('admin.dashboard', ['section' => 'parents', 'min_age' => 27]))->assertOk()->assertViewHas('records', fn ($rows) => $rows->count() === 0);
    }

    public function test_status_uses_snapshot_expiry_and_revocation(): void
    {
        $u = User::factory()->create();
        $contract = $u->contracts()->create(['sign_date' => now(), 'ip_address' => '127.0.0.1']);
        $doc = SignedDocument::factory()->create(['user_id' => $u->id, 'contract_id' => $contract->id, 'expires_at' => now()->subDay()]);
        $this->actingAs($this->admin())->get(route('admin.dashboard', ['status' => 'active']))->assertOk()->assertViewHas('records', fn ($rows) => $rows->count() === 0);
        $this->get(route('admin.dashboard', ['status' => 'expired']))->assertOk()->assertViewHas('records', fn ($rows) => $rows->count() === 1);
        $doc->forceFill(['revoked_at' => now()])->save();
        $this->get(route('admin.dashboard', ['status' => 'revoked']))->assertOk()->assertViewHas('records', fn ($rows) => $rows->count() === 1);
    }

    public function test_legacy_phone_format_is_searchable(): void
    {
        $u = User::factory()->create(['phone' => '8 (913) 000-01-23']);
        $u->assignRole(Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']));
        $this->actingAs($this->admin())->get(route('admin.dashboard', ['section' => 'parents', 'q' => '+7 913 000 01 23']))->assertOk()->assertViewHas('records', fn ($rows) => $rows->count() === 1);
    }
}
