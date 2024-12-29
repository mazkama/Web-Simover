<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorHistoryController;
use App\Http\Controllers\NotificationController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\AuthController;

// Authentication routes for login and registration
Route::get('login', function () {
    if (session('user_name'))
        return redirect()->route('dashboard');
    else
        return view('auth.login');
})->name('login');

Route::get('register', function () {
    if (session('user_name'))
        return redirect()->route('dashboard');
    else
        return view('auth.register');
})->name('register');

// Firebase Authentication handling
Route::post('register', [AuthController::class, 'register'])->name('firebase.register');
Route::post('login', [AuthController::class, 'login'])->name('firebase.login');
Route::post('logout', [AuthController::class, 'logout'])->name('firebase.logout');

// Resend Email Verification
Route::view('/resend-verification', 'auth.resend-verification')->name('resend-verification.index');
Route::post('/resend-verification', [AuthController::class, 'resendEmailVerification'])->name('resend-verification.store');

Route::middleware(['firebase.auth'])->group(function () {
    // If the user is authenticated, redirect to dashboard if they access the root URL
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // History Route
    Route::get('/riwayat', [SensorHistoryController::class, 'index'])->name('sensorHistory.index');

    // Device Management Routes
    Route::get('/perangkat/semua', [DeviceController::class, 'index'])->name('device.index');
    Route::post('/perangkat/cek', [DeviceController::class, 'checkDeviceId'])->name('device.cek');
    Route::get('/perangkat/tambah', [DeviceController::class, 'create'])->name('device.create');
    Route::post('/perangkat/tambah', [DeviceController::class, 'store'])->name('device.store');
    Route::delete('/perangkat/delete/{id}', [DeviceController::class, 'delete'])->name('device.delete');
    Route::get('/perangkat/ubah/{id}', [DeviceController::class, 'edit'])->name('device.edit');
    Route::put('/perangkat/update/{id}', [DeviceController::class, 'update'])->name('device.update');
});



####  Route Api ####
Route::prefix('api')->withoutMiddleware(VerifyCsrfToken::class)->group(function () {
    # Route Api FCM
    Route::post('/send-to-topic', [NotificationController::class, 'sendToTopic'])->name('sendNotification');
    # Route Api History Sensor
    Route::post('/sensor-histories', [SensorHistoryController::class, 'store']);
    Route::get('/sensor-histories/data', [SensorHistoryController::class, 'getData'])->name('dataSensor');
});







// Route::get('/dashboard', function () {
//     return view('pages.dashboard');
// })->name('dashboard');
