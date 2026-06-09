<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;

// index page
Route::get('/', [IndexController::class, 'index'])->name('index'); // home page

Route::post('/index.addFirstLocations', [IndexController::class, 'addFirstLocations'])->name('index.addFirstLocations'); // add inital locations on the index page