<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminDashboardRequest;
use App\Models\Branch;
use App\Models\Child;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(AdminDashboardRequest $request): View
    {
        $filters = $request->validated();
        $section = $filters['section'] ?? 'contracts';
        $sort = $filters['sort'] ?? 'newest';
        $branchId = $filters['branch'] ?? null;
        $search = trim($filters['q'] ?? '');
        $query = match ($section) {
            'parents' => User::query()->where(function (Builder $query) {
                $query->whereHas('roles', fn (Builder $roles) => $roles->where('name', 'user'))
                    ->orWhereHas('children')->orWhereHas('contracts');
            })->with('children.branch')->withCount('contracts'),
            'children' => Child::query()->with(['user', 'branch']),
            default => Contract::query()->with('user.children.branch'),
        };

        if ($branchId) {
            if ($section === 'children') {
                $query->where('branch_id', $branchId);
            } else {
                $query->whereHas($section === 'contracts' ? 'user.children' : 'children',
                    fn (Builder $children) => $children->where('branch_id', $branchId));
            }
        }

        if ($search !== '') {
            $query->where(function (Builder $query) use ($section, $search) {
                if ($section === 'contracts') {
                    $query->whereHas('user', fn (Builder $person) => $this->searchName($person, $search))
                        ->orWhereHas('user.children', fn (Builder $person) => $this->searchName($person, $search));
                } else {
                    $query->where(fn (Builder $person) => $this->searchName($person, $search))
                        ->orWhereHas($section === 'children' ? 'user' : 'children',
                            fn (Builder $person) => $this->searchName($person, $search));
                }
            });
        }

        if (str_starts_with($sort, 'name_')) {
            $direction = $sort === 'name_asc' ? 'asc' : 'desc';
            foreach (['lastname', 'firstname', 'patronymic'] as $column) {
                $query->orderBy($section === 'contracts'
                    ? User::select($column)->whereColumn('users.id', 'contracts.user_id')->limit(1)
                    : $column, $direction);
            }
        } else {
            $query->orderBy($section === 'contracts' ? 'sign_date' : 'created_at', $sort === 'oldest' ? 'asc' : 'desc');
        }

        $records = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $branches = Branch::orderBy('id')->get();
        $title = match ($section) {
            'parents' => 'Все родители',
            'children' => 'Все дети',
            default => 'Подписанные договоры об оказании платных услуг в СЦ «ТАИР»',
        };

        return view('admin.dashboard', compact('records', 'branches', 'section', 'sort', 'branchId', 'search', 'title'));
    }

    private function searchName(Builder $query, string $search): void
    {
        foreach (preg_split('/\s+/u', $search, -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $query->where(function (Builder $query) use ($word) {
                $query->whereLike('lastname', '%'.$word.'%')
                    ->orWhereLike('firstname', '%'.$word.'%')
                    ->orWhereLike('patronymic', '%'.$word.'%');
            });
        }
    }
}
