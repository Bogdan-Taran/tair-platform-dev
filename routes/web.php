<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [\App\Http\Controllers\AccountController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('add-child', [\App\Http\Controllers\AccountController::class, 'addChild'])->name('add-child');
    Route::post('store-child', [\App\Http\Controllers\AccountController::class, 'storeChild'])->name('store-child');
});


require __DIR__.'/auth.php';
