<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class ContractController extends Controller
{
    public function signContract(): RedirectResponse
    {
        return redirect()->route('documents')->with('status', 'Выберите опубликованную версию договора.');
    }
}
