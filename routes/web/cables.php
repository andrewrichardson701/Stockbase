<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CablestockController;

// Cables pages
Route::middleware(['auth', 'check.permission:cables'])->group(function () { // Cables pages - locked behind cables permission
    Route::get('/cablestock', [CablestockController::class, 'index'])->name('cablestock'); // cablestock page

    // POST REQUESTS
    Route::post('/cablestock.addStock', [CablestockController::class, 'addCableStock'])->name('cablestock.addStock'); // modify cable stock 
    Route::post('/cablestock.modifyStock', [CablestockController::class, 'modifyCableStock'])->name('cablestock.modifyStock'); // modify cable stock 
    Route::post('/cablestock.moveStock', [CablestockController::class, 'moveCableStock'])->name('cablestock.moveStock'); // move cable stock 
    Route::post('/cablestock.addType', [CablestockController::class, 'addCableType'])->name('cablestock.addType'); // add cable type
    Route::post('/cablestock.checkCableQuantity', [CablestockController::class, 'checkCableQuantity'])->name('cablestock.checkCableQuantity'); // check cable quantity on the remove page
});