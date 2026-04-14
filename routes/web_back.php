<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\StockController;

use App\Http\Middleware\SecurityMiddleware;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {  // auth default
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

 Route::middleware([SecurityMiddleware::class])->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');           // auth default
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');     // auth default
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');  // auth default
    
        Route::get('/', [IndexController::class, 'index'])->name('index'); // home page
        Route::get('/_ajax-stock', [StockController::class, 'getStockAjax']); // for the index page ajax
    });

    // no auth needed
    Route::get('/about', [AboutController::class, 'index'])->name('about'); // no auth needed
});



require __DIR__.'/auth.php';
