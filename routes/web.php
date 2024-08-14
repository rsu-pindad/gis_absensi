<?php

use Illuminate\Http\Request;
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

// Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::view('profile', 'profile')
        ->name('profile');

    Route::view('gis', 'gis')
        ->name('gis');

    Route::view('absen', 'absen')
        ->name('absen');

    Route::view('presensi', 'presensi')
        ->name('presensi');

    Route::view('user-presensi', 'user-presensi')
        ->name('user-presensi');

    Route::view('dinas', 'dinas')
        ->name('dinas');

    Route::view('dinas-scan', 'dinas-scan')
        ->name('dinas-scan');

    Route::view('finger', 'finger')
        ->name('finger');

    Route::get('/signedabsensi/{user}/{otp}', function (Request $request) {
        if (!$request->hasValidSignature()) {
            return response()->json(['status' => 403, 'message' => 'not ok']);
        }

        return response()->json(['status' => 200, 'message' => 'ok']);
    })->name('signedabsensi')->middleware('signed');

    // Route::get('/playground', function(Request $request){
    //     // if (! $request->hasValidSignature()) {
    //     //     abort(401);
    //     // }
    //     $idUser = 2;
    //     $random_string = md5(microtime());
    //     $otp = rand(1000,9999);
    //     return URL::temporarySignedRoute('signedabsensi', now()->addHours(1), ['user' => $idUser, 'otp' => $otp], absolute:true);
    // })->name('playground');
});

require __DIR__ . '/auth.php';
