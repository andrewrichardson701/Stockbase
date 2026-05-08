<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\OpticsController;

// Assets pages - can be any asset page to allow the assets page
Route::middleware(['auth', 'check.permission:optics,cpus,memory,psus,disks,fans'])->group(function () { // Assets pages - locked behind one of the assets permissions
    
    // main assets page - only visible if one of the assets is permitted
    Route::get('/assets', [AssetsController::class, 'index'])->name('assets'); // assets page

    // Optics pages - locked behind optics permission
    Route::middleware(['auth', 'check.permission:optics'])->group(function () { // Optics pages - locked behind optics permission
        Route::get('/assets/optics', [OpticsController::class, 'index'])->name('optics'); // assets > optics page

        // POST REQUESTS
        Route::post('/assets/optics.add', [OpticsController::class, 'add'])->name('optics.add'); // adding optics
        Route::post('/assets/optics.move', [OpticsController::class, 'move'])->name('optics.move'); // move optics
        Route::post('/assets/optics.restore', [OpticsController::class, 'restore'])->name('optics.restore'); // restore optics
        Route::post('/assets/optics.edit', [OpticsController::class, 'edit'])->name('optics.edit'); // edit optics
        Route::post('/assets/optics.delete', [OpticsController::class, 'delete'])->name('optics.delete'); // deleting optics
        Route::post('/assets/optics.comments', [OpticsController::class, 'comments'])->name('optics.comments'); // comment forms - adding/deleting
        Route::post('/assets/optics.serialSearch', [OpticsController::class, 'serialSearch'])->name('optics.serialSearch'); // Search for matching serials
        
    });

    // CPUs pages - locked behind cpus permission
    Route::middleware(['auth', 'check.permission:cpus'])->group(function () { // CPUs pages - locked behind cpus permission
        Route::get('/assets/cpus', [AssetsController::class, 'cpus'])->name('cpus'); // assets > cpus page
    });

    // Memory pages - locked behind memory permission
    Route::middleware(['auth', 'check.permission:memory'])->group(function () { // Memory pages - locked behind memory permission
        Route::get('/assets/memory', [AssetsController::class, 'incomplete'])->name('memory'); // assets > memory page
    });

    // Disks pages - locked behind disks permission
    Route::middleware(['auth', 'check.permission:disks'])->group(function () { // Disks pages - locked behind disks permission
        Route::get('/assets/disks', [AssetsController::class, 'disks'])->name('disks'); // assets > disks page
        Route::post('/assets/disks.add', [AssetsController::class, 'diskAdd'])->name('disks.add'); // adding disks
        Route::post('/assets/disks.move', [AssetsController::class, 'diskMove'])->name('disks.move'); // move disks
        Route::post('/assets/disks.restore', [AssetsController::class, 'diskRestore'])->name('disks.restore'); // restore disks
        Route::post('/assets/disks.delete', [AssetsController::class, 'diskDelete'])->name('disks.delete'); // deleting disks
        Route::post('/assets/disks.edit', [AssetsController::class, 'diskEdit'])->name('disks.edit'); // editing disks
        Route::post('/assets/disks.serialSearch', [AssetsController::class, 'diskSerialSearch'])->name('disks.serialSearch'); // Search for matching serials
    });

    // Fans pages - locked behind fans permission
    Route::middleware(['auth', 'check.permission:fans'])->group(function () { // Fans pages - locked behind fans permission
        Route::get('/assets/fans', [AssetsController::class, 'incomplete'])->name('fans'); // assets > fans page
    });

    // PSUs pages - locked behind psus permission
    Route::middleware(['auth', 'check.permission:psus'])->group(function () { // PSUs pages - locked behind psus permission
        Route::get('/assets/psus', [AssetsController::class, 'incomplete'])->name('psus'); // assets > psus page
    });                    
});