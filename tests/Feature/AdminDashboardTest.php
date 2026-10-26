<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    #[TestWith(['contracts'])]
    #[TestWith(['parents'])]
    #[TestWith(['children'])]
    public function test_regular_users_cannot_read_admin_sections(string $section): void
    {
        $this->actingAs($this->person())->get(route('admin.dashboard', ['section' => $section]))->assertForbidden();
    }

    public function test_admin_dashboard_redirects_to_admin_workspace(): void
    {
        $this->actingAs($this->admin())->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_parent_dashboard_remains_available(): void
    {
        $parent = $this->person();
        $parent->givePermissionTo(Permission::create(['name' => 'show my children']));
        $this->actingAs($parent)->get(route('dashboard'))->assertOk()->assertViewIs('dashboard');
    }

    public function test_contracts_show_other_families_and_keep_contracts_without_children(): void
    {
        $parent = $this->person('Ченкова');
        $parent->contracts()->create(['sign_date' => '2026-09-17 12:00:00', 'ip_address' => '127.0.0.1']);
        $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk()
            ->assertSee('Ченкова')->assertSee('17 сентября 2026')->assertSee('Дети не добавлены');
    }

    public function test_branch_and_full_child_name_filter_contracts_without_duplicates(): void
    {
        $branch = Branch::forceCreate(['branch_name' => 'Академгородок', 'branch_address' => 'Томск']);
        $parent = $this->person('Ченкова');
        foreach (['Илья', 'Иван'] as $name) {
            $parent->children()->create(['firstname' => $name, 'lastname' => 'Рамазидов', 'patronymic' => 'Тимурович', 'birthdate' => '2015-01-01', 'branch_id' => $branch->id]);
        }
        $contract = $parent->contracts()->create(['sign_date' => '2026-09-17', 'ip_address' => '127.0.0.1']);
        $this->person('Другой')->contracts()->create(['sign_date' => '2026-09-18', 'ip_address' => '127.0.0.1']);
        $this->actingAs($this->admin())->get(route('admin.dashboard', ['branch' => $branch->id, 'q' => 'Рамазидов Илья']))
            ->assertOk()->assertSee('Ченкова')->assertDontSee('Другой')
            ->assertViewHas('records', fn ($records) => $records->total() === 1 && $records->first()->id === $contract->id);
    }

    public function test_parents_include_registered_users_without_children_or_contracts(): void
    {
        $parent = $this->person('НовыйРодитель');
        $parent->assignRole(Role::create(['name' => 'user']));
        $this->actingAs($this->admin())->get(route('admin.dashboard', ['section' => 'parents']))
            ->assertOk()->assertSee('НовыйРодитель')->assertSee('Договоров: 0');
    }

    public function test_children_filter_uses_the_child_branch_and_shows_parent_contacts(): void
    {
        $branch = Branch::forceCreate(['branch_name' => 'Метеор', 'branch_address' => 'Томск']);
        $parent = $this->person('Родитель');
        foreach ([$branch->id, 999] as $id) {
            $parent->children()->create(['firstname' => 'Илья', 'lastname' => 'Ребёнок'.$id, 'patronymic' => 'Иванович', 'birthdate' => '2015-01-01', 'branch_id' => $id]);
        }
        $this->actingAs($this->admin())->get(route('admin.dashboard', ['section' => 'children', 'branch' => $branch->id, 'q' => 'Родитель']))
            ->assertOk()->assertSee($parent->phone)->assertDontSee('Ребёнок999')->assertSee('01.01.2015');
    }

    #[TestWith(['newest', 'Борисов', 'Андреев'])]
    #[TestWith(['oldest', 'Андреев', 'Борисов'])]
    #[TestWith(['name_asc', 'Андреев', 'Борисов'])]
    #[TestWith(['name_desc', 'Борисов', 'Андреев'])]
    public function test_contract_sorting(string $sort, string $first, string $second): void
    {
        $this->person('Андреев')->contracts()->create(['sign_date' => '2026-01-01', 'ip_address' => '127.0.0.1']);
        $this->person('Борисов')->contracts()->create(['sign_date' => '2026-02-01', 'ip_address' => '127.0.0.1']);
        $this->actingAs($this->admin())->get(route('admin.dashboard', ['sort' => $sort]))->assertOk()->assertSeeInOrder([$first, $second]);
    }

    public function test_pagination_keeps_filters(): void
    {
        $parent = $this->person('Ченкова');
        for ($i = 0; $i < 21; $i++) {
            $parent->contracts()->create(['sign_date' => '2026-09-17', 'ip_address' => '127.0.0.1']);
        }
        $this->actingAs($this->admin())->get(route('admin.dashboard', ['q' => 'Ченкова', 'sort' => 'oldest']))
            ->assertOk()->assertViewHas('records', fn ($records) => $records->count() === 20 && $records->total() === 21 && str_contains($records->nextPageUrl(), 'sort=oldest'));
    }

    #[TestWith(['sort', 'sign_date desc; DROP TABLE users', 'Выберите доступный порядок сортировки.'])]
    #[TestWith(['section', 'unknown', 'Выберите существующий раздел.'])]
    #[TestWith(['branch', 999, 'Выберите существующий филиал.'])]
    #[TestWith(['q', ['bad'], 'Введите ФИО текстом.'])]
    #[TestWith(['page', 0, 'Номер страницы должен быть больше нуля.'])]
    public function test_invalid_filters_are_rejected(string $field, mixed $value, string $message): void
    {
        $this->actingAs($this->admin())->getJson(route('admin.dashboard', [$field => $value]))
            ->assertUnprocessable()->assertJsonValidationErrors([$field])->assertJsonPath('errors.'.$field.'.0', $message);
    }

    public function test_empty_search_and_escaped_names(): void
    {
        $parent = $this->person('<script>alert(1)</script>');
        $parent->contracts()->create(['sign_date' => '2026-09-17', 'ip_address' => '127.0.0.1']);
        $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get(route('admin.dashboard', ['q' => 'Несуществующий']))->assertOk()->assertSee('Ничего не найдено');
    }

    private function person(string $lastname = 'Иванова'): User
    {
        $user = User::factory()->state(fn () => [
            'name' => null,
            'firstname' => 'Анна', 'lastname' => $lastname, 'patronymic' => 'Петровна',
            'phone' => fake()->unique()->numerify('79#########'), 'birthdate' => '1990-01-01',
        ])->make();
        unset($user->name);
        $user->save();

        return $user;
    }

    private function admin(): User
    {
        $user = $this->person('Администратор');
        $user->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

        return $user;
    }
}
