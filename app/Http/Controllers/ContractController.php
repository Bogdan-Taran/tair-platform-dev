<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function signContract(Request $request){
        $validated = request()->validate([
            'series' => 'required|numeric',
            'number' => 'required|numeric',
            'issue_date' => 'required|date|before:today',
            'issue_by' => 'required|string|max:500|min:10',
            'address' => 'required|string|max:500|min:10',
            'agreement' => 'required|accepted',
        ],
            [
                'series.regex' => 'Серия паспорта должна состоять из 4 цифр.',
                'number.regex' => 'Номер паспорта должен состоять из 6 цифр.',
            ]);
        $user = auth()->user();

        $user->passportDetail()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'series' => $validated['series'],
                'number' => $validated['number'],
                'issue_date' => $validated['issue_date'],
                'issue_by' => $validated['issue_by'],
                'address' => $validated['address'],
            ]
        );

        $user->contracts()->create([
            'sign_date' => Carbon::now(),
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('documents')->with('status', 'Договор успешно подписан');

    }
}
