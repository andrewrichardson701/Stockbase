<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChangelogController;

// Changelog pages
Route::middleware(['auth', 'check.permission:changelog'])->group(function () { // Changelog pages - locked behind the changelog permission
    Route::get('/changelog/{start_date?}/{end_date?}/{table?}/{user?}/{page?}', [ChangelogController::class, 'index'])->name('changelog'); // changelog page
    
    Route::post('/changelog.filter', [ChangelogController::class, 'filterChangelog'])->name('changelog.filter'); // filter the changelog
});