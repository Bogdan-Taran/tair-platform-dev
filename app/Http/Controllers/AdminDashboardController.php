<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminDashboardRequest;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Child;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    public function index(AdminDashboardRequest $request): View
    {
        $filters = $request->validated();
        $section = $filters['section'] ?? 'contracts';
        $sort = $filters['sort'] ?? 'newest';
        $branchId = $filters['branch'] ?? null;
        $search = trim($filters['q'] ?? '');
        $query = $this->recordsQuery($filters);
        $records = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $branches = Branch::orderBy('id')->get();
        $title = match ($section) {
            'parents' => 'Все родители',
            'children' => 'Все дети',
            default => 'Подписанные договоры об оказании платных услуг в СЦ «ТАИР»',
        };

        return view('admin.dashboard', compact('records', 'branches', 'section', 'sort', 'branchId', 'search', 'title'));
    }

    private function recordsQuery(array $filters): Builder
    {
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
            default => Contract::query()->with(['user.children.branch', 'signedDocument']),
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

        if (isset($filters['min_age']) || isset($filters['max_age'])) {
            $age = function (Builder $person) use ($filters) {
                if (isset($filters['min_age'])) {
                    $person->whereDate('birthdate', '<=', now()->subYears((int) $filters['min_age'])->toDateString());
                }
                if (isset($filters['max_age'])) {
                    $person->whereDate('birthdate', '>', now()->subYears((int) $filters['max_age'] + 1)->toDateString());
                }
            };
            if ($section === 'children') {
                $query->where($age);
            } else {
                $familyAge = fn (Builder $person) => $person->where(fn (Builder $adult) => $adult->where('account_type', 'athlete')->where($age))->orWhereHas('children', $age);
                $section === 'contracts' ? $query->whereHas('user', $familyAge) : $query->where($familyAge);
            }
        }
        if (! empty($filters['status'])) {
            $status = $filters['status'];
            $contractFilter = function (Builder $contracts) use ($status) {
                if ($status === 'active') {
                    $contracts->where(fn (Builder $q) => $q->whereHas('signedDocument', fn (Builder $d) => $d->whereNull('revoked_at')->where(fn (Builder $e) => $e->whereNull('expires_at')->orWhere('expires_at', '>', now())))
                        ->orWhere(fn (Builder $legacy) => $legacy->whereDoesntHave('signedDocument')->where('sign_date', '>', now()->subYear())));
                } elseif ($status === 'expired') {
                    $contracts->where(fn (Builder $q) => $q->whereHas('signedDocument', fn (Builder $d) => $d->whereNull('revoked_at')->where('expires_at', '<=', now()))
                        ->orWhere(fn (Builder $legacy) => $legacy->whereDoesntHave('signedDocument')->where('sign_date', '<=', now()->subYear())));
                } elseif ($status === 'revoked') {
                    $contracts->whereHas('signedDocument', fn (Builder $d) => $d->whereNotNull('revoked_at'));
                }
            };
            if ($section === 'contracts') {
                $status === 'missing' ? $query->whereRaw('1 = 0') : $query->where($contractFilter);
            } else {
                $relation = $section === 'parents' ? 'contracts' : 'user.contracts';
                $status === 'missing' ? $query->whereDoesntHave($relation) : $query->whereHas($relation, $contractFilter);
            }
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

        return $query;
    }

    public function export(AdminDashboardRequest $request): StreamedResponse
    {
        $filters = $request->validated();
        $section = $filters['section'] ?? 'contracts';
        $query = $this->recordsQuery($filters)->orderByDesc('id');
        AuditLog::record('records.exported', $section, null, ['filters' => array_diff_key($filters, ['q' => true])]);

        return response()->streamDownload(function () use ($query, $section) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'ФИО', 'Телефон родителя', 'Почта родителя', 'Дата', 'Филиалы'], ';', '"', '');
            foreach ($query->lazy(200) as $record) {
                $person = $section === 'parents' ? $record : $record->user;
                $subject = $section === 'children' ? $record : $person;
                $children = $section === 'children' ? collect([$record]) : ($person?->children ?? collect());
                $row = [$record->id, trim(($subject?->lastname ?? '').' '.($subject?->firstname ?? '').' '.($subject?->patronymic ?? '')), $person?->phone ?? '', $person?->email ?? '', (string) ($section === 'contracts' ? $record->sign_date : $record->birthdate), $children->pluck('branch.branch_name')->filter()->unique()->implode(', ')];
                $row = array_map(fn ($value) => preg_match('/^[=+@\-\t\r\n]/u', (string) $value) ? "'".$value : $value, $row);
                fputcsv($out, $row, ';', '"', '');
            }
            fclose($out);
        }, 'tair-'.$section.'-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store']);
    }

    private function searchName(Builder $query, string $search): void
    {
        $digits = preg_replace('/\D/', '', $search);
        if (strlen($digits) >= 3 && preg_match('/^[+0-9 ()-]+$/', $search)) {
            if (strlen($digits) === 11 && in_array($digits[0], ['8', '7'])) {
                $digits = substr($digits, 1);
            }
            if ($query->getModel() instanceof User) {
                $query->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, '+', ''), ' ', ''), '-', ''), '(', ''), ')', '') LIKE ?", ['%'.$digits.'%']);
            } else {
                $query->whereHas('user', fn (Builder $user) => $this->searchName($user, $search));
            }

            return;
        }
        foreach (preg_split('/\s+/u', $search, -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $query->where(function (Builder $query) use ($word) {
                $query->whereLike('lastname', '%'.$word.'%')
                    ->orWhereLike('firstname', '%'.$word.'%')
                    ->orWhereLike('patronymic', '%'.$word.'%');
            });
        }
    }
}
