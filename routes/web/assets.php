<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\OpticsController;
use App\Http\Controllers\MemoryController;
use App\Http\Controllers\DiskController;
use App\Http\Controllers\CpuController;

// Assets pages - can be any asset page to allow the assets page
Route::middleware(['auth', 'check.permission:optics,cpus,memory,disks'])->group(function () { // Assets pages - locked behind one of the assets permissions
    
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
        Route::get('/assets/cpus', [CpuController::class, 'cpus'])->name('cpus'); // assets > cpus page
        Route::post('/assets/cpus.add', [CpuController::class, 'cpuAdd'])->name('cpus.add'); // adding cpus
        Route::post('/assets/cpus.move', [CpuController::class, 'cpuMove'])->name('cpus.move'); // move cpus
        Route::post('/assets/cpus.restore', [CpuController::class, 'cpuRestore'])->name('cpus.restore'); // restore cpus
        Route::post('/assets/cpus.delete', [CpuController::class, 'cpuDelete'])->name('cpus.delete'); // deleting cpus
        Route::post('/assets/cpus.edit', [CpuController::class, 'cpuEdit'])->name('cpus.edit'); // editing cpus
        Route::post('/assets/cpus.serialSearch', [CpuController::class, 'cpuSerialSearch'])->name('cpus.serialSearch'); // Search for matching serials
    });

    // Memory pages - locked behind memory permission
    Route::middleware(['auth', 'check.permission:memory'])->group(function () { // Memory pages - locked behind memory permission
        Route::get('/assets/memory', [MemoryController::class, 'memory'])->name('memory'); // assets > memory page
        Route::post('/assets/memory.add', [MemoryController::class, 'memoryAdd'])->name('memory.add'); // adding memory
        Route::post('/assets/memory.move', [MemoryController::class, 'memoryMove'])->name('memory.move'); // move memory
        Route::post('/assets/memory.restore', [MemoryController::class, 'memoryRestore'])->name('memory.restore'); // restore memory
        Route::post('/assets/memory.delete', [MemoryController::class, 'memoryDelete'])->name('memory.delete'); // deleting memory
        Route::post('/assets/memory.edit', [MemoryController::class, 'memoryEdit'])->name('memory.edit'); // editing memory
        Route::post('/assets/memory.serialSearch', [MemoryController::class, 'memorySerialSearch'])->name('memory.serialSearch'); // Search for matching serials
    });

    // Disks pages - locked behind disks permission
    Route::middleware(['auth', 'check.permission:disks'])->group(function () { // Disks pages - locked behind disks permission
        Route::get('/assets/disks', [DiskController::class, 'disks'])->name('disks'); // assets > disks page
        Route::post('/assets/disks.add', [DiskController::class, 'diskAdd'])->name('disks.add'); // adding disks
        Route::post('/assets/disks.move', [DiskController::class, 'diskMove'])->name('disks.move'); // move disks
        Route::post('/assets/disks.restore', [DiskController::class, 'diskRestore'])->name('disks.restore'); // restore disks
        Route::post('/assets/disks.delete', [DiskController::class, 'diskDelete'])->name('disks.delete'); // deleting disks
        Route::post('/assets/disks.edit', [DiskController::class, 'diskEdit'])->name('disks.edit'); // editing disks
        Route::post('/assets/disks.serialSearch', [DiskController::class, 'diskSerialSearch'])->name('disks.serialSearch'); // Search for matching serials
    });
              
});