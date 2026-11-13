<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\EmailChangeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    try {
        DB::select('SELECT 1');

        return response()
            ->json(['status' => 'ok'], 200)
            ->header('Cache-Control', 'no-store');
    } catch (Throwable $e) {
        report($e);

        return response()
            ->json(['status' => 'error'], 503)
            ->header('Cache-Control', 'no-store');
    }
});

Route::get('/', [MainController::class, 'index'])->name('index');
Route::redirect('/index.html', '/');
Route::get('/dos-test', function () {
    return 'OK';
})->middleware('custom.throttle');

Route::get('/our-team', [MainController::class, 'ourTeam'])->name('our-team');
Route::get('/our-branches', [MainController::class, 'ourBranches'])->name('our-branches');
Route::get('/about-club', [MainController::class, 'aboutClub'])->name('about-club');
Route::get('/summer-camp-2026', [MainController::class, 'summerCamp2026'])->name('summer-camp-2026');

Route::get('/summer-camp-beret', function () {
    return view('summer-camp-beret');
});

Route::middleware('auth')->group(function () {
    Route::get('/children/{child}/edit', [ChildController::class, 'edit'])->name('children.edit');
    Route::patch('/children/{child}', [ChildController::class, 'update'])->name('children.update');
    Route::patch('/children/{child}/archive', [ChildController::class, 'archive'])->name('children.archive');
    Route::get('/profile/avatar', [AvatarController::class, 'show'])->name('avatar.show');
    Route::post('/profile/avatar', [AvatarController::class, 'store'])->name('avatar.store');
    Route::delete('/profile/avatar', [AvatarController::class, 'destroy'])->name('avatar.destroy');

    Route::middleware('can:manage roles')->prefix('admin')->name('admin.')->group(function () {
        Route::get('documents', [DocumentController::class, 'registry'])->name('documents');
        Route::get('export', [AdminDashboardController::class, 'export'])->name('export');
        Route::get('templates', [DocumentTemplateController::class, 'index'])->name('templates.index');
        Route::post('templates', [DocumentTemplateController::class, 'store'])->name('templates.store');
        Route::get('templates/{template}/edit', [DocumentTemplateController::class, 'edit'])->name('templates.edit');
        Route::patch('templates/{template}', [DocumentTemplateController::class, 'update'])->name('templates.update');
        Route::post('templates/{template}/publish', [DocumentTemplateController::class, 'publish'])->name('templates.publish');
        Route::resource('users', AdminUserController::class)->except(['show', 'destroy']);
        Route::patch('users/{user}/block', [AdminUserController::class, 'block'])->name('users.block');
        Route::post('users/{user}/invite', [AdminUserController::class, 'invite'])->middleware('throttle:6,1')->name('users.invite');
        Route::get('audit', [AdminUserController::class, 'audit'])->name('audit');
    });
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('verified')->name('admin.dashboard');

    Route::get('/documents/templates/{template}', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::post('/documents/templates/{template}', [DocumentController::class, 'store'])->middleware('throttle:6,1')->name('documents.sign');
    Route::get('/documents/{document}/pdf', [DocumentController::class, 'pdf'])->middleware('throttle:10,1')->name('documents.pdf');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::patch('/documents/{document}/revoke', [DocumentController::class, 'revoke'])->name('documents.revoke');
    Route::get('/profile/email/{hash}', [EmailChangeController::class, 'confirm'])->middleware('signed')->name('profile.email.confirm');
    Route::post('/profile/email/resend', [EmailChangeController::class, 'resend'])->middleware('throttle:3,1')->name('profile.email.resend');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [AccountController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::get('/documents', [DocumentController::class, 'index'])->middleware(['auth', 'verified'])->name('documents');
    Route::get('/sign-contract', [AccountController::class, 'signContract'])->middleware(['auth', 'verified'])->name('sign-contract');
    Route::post('/sign-contract', [ContractController::class, 'signContract'])->middleware('verified')->name('sign-contract.store');

    Route::get('/passport', [PassportController::class, 'edit'])->middleware(['auth', 'verified'])->name('passport.edit');
    Route::post('/passport', [PassportController::class, 'update'])->middleware(['auth', 'verified'])->name('passport.update');

    Route::get('add-child', [ChildController::class, 'create'])->name('add-child')->middleware('can:add child');
    Route::post('store-child', [ChildController::class, 'store'])->name('store-child')->middleware('can:store child');
    Route::resource('roles', RoleController::class)->only(['index', 'create', 'store'])->middleware('can:manage roles');
});

require __DIR__.'/auth.php';
