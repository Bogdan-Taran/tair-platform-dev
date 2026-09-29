<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(){
        return view('index');
    }
    public function ourTeam(){
        return view('our-team');
    }
    public function ourBranches(){
        return view('our-branches');
    }
    public function aboutClub(){
        return view('about-club');
    }
    public function summerCamp2026(){
        return view('summer-camp-2026');
    }
}
