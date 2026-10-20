<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'index'])->name('index');
Route::get('/our-team', [MainController::class, 'ourTeam'])->name('our-team');
Route::get('/our-branches', [MainController::class, 'ourBranches'])->name('our-branches');
Route::get('/about-club', [MainController::class, 'aboutClub'])->name('about-club');
Route::get('/summer-camp-2026', [MainController::class, 'summerCamp2026'])->name('summer-camp-2026');


Route::get('/summer-camp-beret', function () {
    return view('summer-camp-beret');
});




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [\App\Http\Controllers\AccountController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::get('/documents', [\App\Http\Controllers\AccountController::class, 'documents'])->middleware(['auth', 'verified'])->name('documents');
    Route::get('/sign-contract', [\App\Http\Controllers\AccountController::class, 'signContract'])->middleware(['auth', 'verified'])->name('sign-contract');

    Route::get('/passport', [\App\Http\Controllers\PassportController::class, 'edit'])->middleware(['auth', 'verified'])->name('passport.edit');
    Route::post('/passport', [\App\Http\Controllers\PassportController::class, 'update'])->middleware(['auth', 'verified'])->name('passport.update');

    Route::get('add-child', [\App\Http\Controllers\AccountController::class, 'addChild'])->name('add-child')->middleware('can:add child');
    Route::post('store-child', [\App\Http\Controllers\AccountController::class, 'storeChild'])->name('store-child')->middleware('can:store child');
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
});



require __DIR__.'/auth.php';
