<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\SignedDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $newEmail = $data['email'];
        unset($data['email']);
        $changed = $newEmail !== $user->email;
        $user->fill($data);
        $user->pending_email = $changed ? $newEmail : null;
        $user->save();
        if ($changed) {
            try {
                EmailChangeController::send($user);
            } catch (\Throwable $e) {
                report($e);

                return back()->withErrors(['email' => 'Данные сохранены, но письмо подтверждения не отправлено. До подтверждения используется прежняя почта. Повторите отправку позже.']);
            }

            return back()->with('status', 'Подтвердите новую почту по ссылке из письма. До подтверждения используется прежняя почта.');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');

    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->contracts()->exists() || SignedDocument::where('user_id', $user->id)->exists()) {
            return back()->withErrors(['password' => 'В профиле есть подписанные документы. Для удаления данных обратитесь в клуб.'], 'userDeletion');
        }

        Auth::logout();

        $avatar = $user->avatar_path;
        $user->delete();
        if ($avatar) {
            Storage::disk('local')->delete($avatar);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
