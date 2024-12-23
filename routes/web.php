<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorHistoryController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::post('/api/sensor-histories', [SensorHistoryController::class, 'store'])->withoutMiddleware(VerifyCsrfToken::class);
Route::get('/api/devices/get', [SensorHistoryController::class, 'getDevices'])->withoutMiddleware(VerifyCsrfToken::class);

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Route::get('/dashboard', function () {
//     return view('pages.dashboard');
// })->name('dashboard');

