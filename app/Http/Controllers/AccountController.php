<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{

    public function dashboard(){
        $current_user = auth()->user();
        if(!$current_user->can('show my children')){
            abort(403, 'У вас нет прав для просмотра списка детей');
        }
        $user_children = $current_user->children;
//        $children = Child::all();
//        $users = User::all();

        return view('dashboard', compact('current_user', 'user_children'));
    }
    public function addChild(Request $request){
        return view('add-child');
    }
    public function storeChild(Request $request){
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
