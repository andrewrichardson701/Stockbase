<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContainersController;

// Containers pages
Route::middleware(['auth', 'check.permission:containers'])->group(function () { // Containers pages - locked behind the containers permission
    Route::get('/containers', [ContainersController::class, 'index'])->name('containers'); // containers page

    // POST REQUESTS
    Route::post('/containers.addContainer', [ContainersController::class, 'addContainer'])->name('containers.addContainer'); // add new container
    Route::post('/containers.deleteContainer', [ContainersController::class, 'deleteContainer'])->name('containers.deleteContainer'); // delete container
    Route::post('/containers.deleteItemContainer', [ContainersController::class, 'deleteItemContainer'])->name('containers.deleteItemContainer'); // delete item contianer
    Route::post('/containers.editContainer', [ContainersController::class, 'editContainer'])->name('containers.editContainer'); // edut container
    Route::post('/containers.unlinkFromContainer', [ContainersController::class, 'unlinkFromContainer'])->name('containers.unlinkFromContainer'); // unlink from container
    Route::post('/containers.linkToContainer', [ContainersController::class, 'linkToContainer'])->name('containers.linkToContainer'); // link to container
});