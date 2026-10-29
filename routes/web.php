<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

\Illuminate\Support\Facades\Route::get('/health', function () {
    try {
        \Illuminate\Support\Facades\DB::select('SELECT 1');

        return response()
            ->json(['status' => 'ok'], 200)
            ->header('Cache-Control', 'no-store');
    } catch (\Throwable $e) {
        report($e);

        return response()
            ->json(['status' => 'error'], 503)
            ->header('Cache-Control', 'no-store');
    }
});


Route::middleware(['throttle:3,1'])->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('index');
});
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
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('verified')->name('admin.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [AccountController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::get('/documents', [AccountController::class, 'documents'])->middleware(['auth', 'verified'])->name('documents');
    Route::get('/sign-contract', [AccountController::class, 'signContract'])->middleware(['auth', 'verified'])->name('sign-contract');
    Route::post('/sign-contract', [ContractController::class, 'signContract'])->middleware(['auth', 'verified'])->name('sign-contract');

    Route::get('/passport', [PassportController::class, 'edit'])->middleware(['auth', 'verified'])->name('passport.edit');
    Route::post('/passport', [PassportController::class, 'update'])->middleware(['auth', 'verified'])->name('passport.update');

    Route::get('add-child', [AccountController::class, 'addChild'])->name('add-child')->middleware('can:add child');
    Route::post('store-child', [AccountController::class, 'storeChild'])->name('store-child')->middleware('can:store child');
    Route::resource('roles', RoleController::class);
});

require __DIR__.'/auth.php';
