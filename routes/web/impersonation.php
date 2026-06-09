<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

Route::middleware(['auth', 'check.permission:root'])->group(function () { // Impersonation can only be done by the root user
    // Impersonation
    Route::post('/impersonate/{id}', function ($id) {
        Session::put('impersonator_id', Auth::id()); // Save original user
        Session::put('impersonate_id', $id);         // Impersonated user ID
        // impersonation starts
        Auth::loginUsingId($id);
        return redirect('/');                        // Redirect to dashboard
    })->whereNumber('id')
        ->name('impersonate');

    // Missing impersonation ID
    Route::get('/impersonate', function () {
        return redirect('/')->with('error', 'No user ID provided for impersonation.');
    })->name('impersonate.missing');
});

// Leave imperonsation
// This needs to be usable by ALL users otherwise the root user cant leave the impersonation
Route::post('/leave-impersonation', function () {
    $originalUserId = Session::get('impersonator_id');
    Session::forget('impersonate_id');
    Session::forget('impersonator_id');
    Auth::loginUsingId(Session::get($originalUserId));
    $url = route('admin').'#users-settings';
    return redirect($url);
})->name('leave-impersonate');