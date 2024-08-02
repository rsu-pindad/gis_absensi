<?php

use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register web routes for your application. These
 * | routes are loaded by the RouteServiceProvider and all of them will
 * | be assigned to the "web" middleware group. Make something great!
 * |
 */

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::view('profile', 'profile')
        ->name('profile');

    Route::view('gis', 'gis')
        ->name('gis');

    Route::view('absen', 'absen')
        ->name('absen');

    Route::view('presensi', 'presensi')
        ->name('presensi');
});

require __DIR__ . '/auth.php';
