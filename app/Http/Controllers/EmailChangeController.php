<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\ConfirmEmailChange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class EmailChangeController extends Controller
{
    public static function send(User $user): void
    {
        Notification::route('mail', $user->pending_email)->notify(new ConfirmEmailChange(URL::temporarySignedRoute('profile.email.confirm', now()->addMinutes(60), ['hash' => hash('sha256', $user->id.'|'.$user->pending_email)])));
    }

    public function resend(Request $request): RedirectResponse
    {
        abort_unless($request->user()->pending_email, 404);
        try {
            self::send($request->user());
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['email' => 'Не удалось отправить письмо. Попробуйте позже.']);
        }

        return back()->with('status', 'Письмо подтверждения отправлено.');
    }

    public function confirm(Request $request, string $hash): RedirectResponse
    {
        DB::transaction(function () use ($request, $hash) {
            $user = User::lockForUpdate()->findOrFail($request->user()->id);
            abort_unless($user->pending_email && hash_equals(hash('sha256', $user->id.'|'.$user->pending_email), $hash), 403);
            if (User::where('email', $user->pending_email)->whereKeyNot($user->id)->exists()) {
                throw ValidationException::withMessages(['email' => 'Этот адрес уже используется. Укажите другой.']);
            }
            $user->forceFill(['email' => $user->pending_email, 'pending_email' => null, 'email_verified_at' => now()])->save();
        });

        return redirect()->route('profile.edit')->with('status', 'Новая почта подтверждена.');
    }
}
