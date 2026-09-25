<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    public function index(){
        $children = Child::all();

        return view('dashboard', compact('children'));
    }
    public function addChild(Request $request){
        return view('add-child');
    }
    public function storeChild(Request $request){
        $request->validate([
            'child_firstname' => 'required|string|max:255|min:3',
            'child_lastname' => 'required|string|max:255|min:3',
            'child_patronymic' => 'required|string|max:255|min:3',
            'child_gender' => 'required|string|max:255|min:3',
            'child_birthdate' => 'required|date|before:today|',
            'child_branch_id' => 'required|integer',
        ]);

        Child::create($request->all());

        return redirect()->back()->with('status', 'Ребёнок добавлен!');
    }


}
