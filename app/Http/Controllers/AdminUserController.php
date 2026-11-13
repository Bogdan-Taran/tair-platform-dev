<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminUserRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:150']]);
        $q = trim($data['q'] ?? '');
        $users = User::with('roles')->when($q !== '', function ($query) use ($q) {
            $query->where(function ($query) use ($q) {
                foreach (['firstname', 'lastname', 'email', 'phone'] as $field) {
                    $query->orWhereLike($field, '%'.$q.'%');
                }
            });
        })->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function create(): View
    {
        return view('admin.users.edit', ['person' => new User]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', ['person' => $user]);
    }

    private function role(string $name): Role
    {
        $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        if ($name === 'user') {
            foreach (['add child', 'store child', 'show my children'] as $ability) {
                $role->givePermissionTo(Permission::findOrCreate($ability, 'web'));
            }
        }

        return $role;
    }

    public function store(AdminUserRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create($request->safe()->except('role') + ['password' => Str::random(64)]);
            $user->assignRole($this->role($request->validated('role')));
            AuditLog::record('user.created', 'user', $user->id);

            return $user;
        });

        return $this->invitation($user);
    }

    public function update(AdminUserRequest $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id && $request->validated('role') !== 'admin') {
            return back()->withErrors(['role' => 'Нельзя снять собственную роль администратора.']);
        }
        DB::transaction(function () use ($request, $user) {
            $user->fill($request->safe()->except('role'));
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $user->save();
            $user->syncRoles([$this->role($request->validated('role'))]);
            AuditLog::record('user.updated', 'user', $user->id, ['role' => $request->validated('role')]);
        });

        return back()->with('status', 'Профиль сохранён.');
    }

    public function block(Request $request, User $user): RedirectResponse
    {
        $request->validate(['blocked' => ['required', 'boolean']]);
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['blocked' => 'Нельзя заблокировать собственный аккаунт.']);
        }
        DB::transaction(function () use ($request, $user) {
            $user = User::lockForUpdate()->findOrFail($user->id);
            $user->forceFill(['blocked_at' => $request->boolean('blocked') ? now() : null, 'session_version' => $user->session_version + 1, 'remember_token' => Str::random(60)])->save();
            DB::table('sessions')->where('user_id', $user->id)->delete();
            AuditLog::record($request->boolean('blocked') ? 'user.blocked' : 'user.unblocked', 'user', $user->id);
        });

        return back()->with('status', $request->boolean('blocked') ? 'Пользователь заблокирован.' : 'Доступ восстановлен.');
    }

    public function invite(User $user): RedirectResponse
    {
        return $this->invitation($user);
    }

    private function invitation(User $user): RedirectResponse
    {
        try {
            $status = Password::sendResetLink(['email' => $user->email]);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('admin.users.edit', $user)->withErrors(['email' => 'Профиль сохранён, но письмо не отправлено. Проверьте почтовый сервис и повторите отправку.']);
        }

        return redirect()->route('admin.users.edit', $user)->with('status', $status === Password::RESET_LINK_SENT ? 'Ссылка установки пароля отправлена.' : 'Повторите отправку ссылки через минуту.');
    }

    public function audit(): View
    {
        return view('admin.audit', ['logs' => AuditLog::orderByDesc('id')->paginate(30)]);
    }
}
