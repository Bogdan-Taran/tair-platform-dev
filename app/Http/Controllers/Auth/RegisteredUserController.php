<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse|JsonResponse
    {
        $user = DB::transaction(function () use ($request): User {
            $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
            if ($role->wasRecentlyCreated) {
                foreach (['add child', 'edit child', 'store child', 'delete child', 'sign contract', 'show my contract', 'show my children'] as $permission) {
                    $role->givePermissionTo(Permission::findOrCreate($permission, 'web'));
                }
            }
            $user = User::create($request->safe()->except('agreement'));
            $user->assignRole($role);

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();

        return $request->expectsJson()
            ? response()->json(['status' => 'success', 'redirect' => route('dashboard', absolute: false)], 201)
            : redirect()->route('dashboard');
    }
}
