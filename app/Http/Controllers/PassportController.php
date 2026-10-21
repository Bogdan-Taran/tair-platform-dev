<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PassportController extends Controller
{
    public function edit(){
        $passport = auth()->user()->passportDetails;
        return view('passport.edit', compact('passport'));
    }
    public function update(Request $request){
        $validated = $request->validate([
            'series' => 'required|string|max:4|min:4|regex:/^[0-9]{4}$/',
            'number' => 'required|string|max:6|min:6|regex:/^[0-9]{6}$/',
            'issue_date' => 'required|date',
            'issue_by' => 'required|string|max:500|min:10',
            'address' => 'required|string|max:500|min:10',
        ],
            [
                'series.regex' => 'Серия паспорта должна состоять из 4 цифр.',
                'number.regex' => 'Номер паспорта должен состоять из 6 цифр.',
            ]
        );
        auth()->user()->passportDetail()->updateOrCreate(
            ['user_id' => auth()->id()],
            $validated
        );
        return redirect()->route('documents')->with('status', 'Паспотные данны успешно сохранены!');
    }

}
