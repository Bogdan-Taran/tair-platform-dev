<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && ($user->blocked_at || (int) $request->session()->get('account_session_version', 0) !== (int) $user->session_version)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Доступ прекращён. Войдите снова или обратитесь в клуб.'], 403);
            }

            return redirect()->route('login')->withErrors(['email' => 'Доступ прекращён. Войдите снова или обратитесь в клуб.']);
        }

        return $next($request);
    }
}
