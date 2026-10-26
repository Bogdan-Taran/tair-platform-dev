<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function dashboard()
    {
        $current_user = auth()->user();
        if ($current_user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if (! $current_user->can('show my children')) {
            abort(403, 'У вас нет прав для просмотра списка детей');
        }
        $user_children = $current_user->children;

        return view('dashboard', compact('current_user', 'user_children'));
    }

    public function documents()
    {
        $user = auth()->user();
        $user_contracts = $user->contracts;

        return view('documents', compact('user', 'user_contracts'));
    }

    public function signContract()
    {
        $user = auth()->user();

        $latestContract = $user->contracts()->latest('sign_date')->first();
        if ($latestContract) {
            $expiration_date = Carbon::parse($latestContract->sign_date)->addYear();
            if ($expiration_date->greaterThan(now())) {
                return redirect()->route('documents')->with('status', 'У вас уже есть действующий договор до '.$expiration_date->format('d.m.Y'));
            }
        }
        $passport = $user->passportDetail;

        return view('sign-contract', compact('user', 'passport'));
    }

    public function addChild(Request $request)
    {
        return view('add-child');
    }

    public function storeChild(Request $request)
    {
        $validateData = $request->validate([
            'firstname' => 'required|string|max:255|min:3',
            'lastname' => 'required|string|max:255|min:3',
            'patronymic' => 'required|string|max:255|min:3',
            'birthdate' => 'required|date|before:today|',
            'branch_id' => 'required|integer',
        ]);

        //        Child::create($request->all());
        auth()->user()->children()->create($validateData);

        return redirect()->route('dashboard')->with('status', 'Ребёнок добавлен!');
    }
}
