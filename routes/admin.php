<?php

use Illuminate\Support\Facades\Route;


Route::middleware(['admin'])->group(function () {

    Route::view('admin/dashboard', 'admin.dashboard')
        ->name('admin.dashboard');
});
