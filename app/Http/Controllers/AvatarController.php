<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AvatarController extends Controller
{
    public function show(Request $request): StreamedResponse
    {
        $path = $request->user()->avatar_path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store']);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000']]);
        $user = $request->user();
        $old = $user->avatar_path;
        $path = $request->file('avatar')->store('avatars', 'local');
        abort_unless($path, 503, 'Не удалось сохранить изображение.');
        try {
            $user->forceFill(['avatar_path' => $path])->save();
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }
        if ($old) {
            Storage::disk('local')->delete($old);
        }

        return back()->with('status', 'Аватар обновлён.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $old = $request->user()->avatar_path;
        $request->user()->forceFill(['avatar_path' => null])->save();
        if ($old) {
            Storage::disk('local')->delete($old);
        }

        return back()->with('status', 'Аватар удалён.');
    }
}
