<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\IndexController;

// no auth needed
Route::get('/about', [AboutController::class, 'index'])->name('about'); // no auth needed
Route::get('/error', [IndexController::class, 'error'])->name('error'); // no auth needed
Route::get('/test', [IndexController::class, 'test'])->name('test'); // Testing page