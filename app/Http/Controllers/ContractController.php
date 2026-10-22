<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function signContract(Request $request){

        $user = auth()->user();
        $latestContract = $user->contracts()->latest('sign_date')->first();
        if ($latestContract) {
            $expirationDate = \Carbon\Carbon::parse($latestContract->sign_date)->addYear();
            if ($expirationDate->greaterThanOrEqualTo(now())) {
                return redirect()->route('dashboard')->with('status', 'Действие текущего договора еще не истекло.');
            }
        }

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
