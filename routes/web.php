<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorHistoryController;
use App\Http\Controllers\NotificationController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;



####  Route Api ####
Route::prefix('api')->withoutMiddleware(VerifyCsrfToken::class)->group(function () {
    # Route Api FCM
    Route::post('/send-to-topic', [NotificationController::class, 'sendToTopic'])->name('sendNotification');
    # Route Api History Sensor
    Route::post('/sensor-histories', [SensorHistoryController::class, 'store']);
    Route::get('/sensor-histories/data', [SensorHistoryController::class, 'getData'])->name('dataSensor');

});

# Route redirect ke dashb
Route::get('/', function () {
    return redirect()->route('dashboard');
});
# Route Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
# Route History
Route::get('/riwayat', [SensorHistoryController::class, 'index'])->name('riwayat');




// Route::get('/dashboard', function () {
//     return view('pages.dashboard');
// })->name('dashboard');
