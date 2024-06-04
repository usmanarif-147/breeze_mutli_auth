<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['student'])->group(function () {
    Route::view('/dashboard', 'student.dashboard')
        ->name('student.dashboard');
});
