<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate(['email' => ['required', 'string', 'email']], [
            'email.required' => 'Введите почту.', 'email.email' => 'Введите корректную почту.',
        ]);
        $email = mb_strtolower(trim($request->input('email')));
        try {
            $status = Password::sendResetLink(['email' => $email]);
        } catch (Throwable $exception) {
            report($exception);
            $message = 'Не удалось отправить письмо. Попробуйте позже.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 503)
                : back()->withInput(['email' => $email])->withErrors(['email' => $message]);
        }
        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages(['email' => 'Письмо уже запрошено. Подождите минуту перед повторной отправкой.']);
        }
        $message = 'Если эта почта зарегистрирована, на неё отправлена ссылка для восстановления пароля.';

        return $request->expectsJson()
            ? response()->json(['status' => 'success', 'message' => $message])
            : back()->with('status', $message);
    }
}
