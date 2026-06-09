<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Profile
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');           // auth default
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');     // auth default
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');  // auth default
Route::post('/profile.enable2FA', [ProfileController::class, 'enable2FA'])->name('profile.enable2FA'); // enable 2FA
Route::post('/profile.reset2FA', [ProfileController::class, 'reset2FA'])->name('profile.reset2FA'); // reset 2FA secret

// Theme-testing
Route::get('/theme-testing', [ProfileController::class, 'themeTesting'])->name('theme-testing'); // theme-testing page
Route::post('/theme-testing.uploadTheme', [ProfileController::class, 'uploadTheme'])->name('theme-testing.uploadTheme'); // filter the changelog