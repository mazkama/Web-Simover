<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
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
Route::get('/riwayat', [SensorHistoryController::class, 'index'])->name('sensorHistory.index');
# Route Device
Route::get('/perangkat/semua', [DeviceController::class, 'index'])->name('device.index');
Route::post('/perangkat/cek', [DeviceController::class, 'checkDeviceId'])->name('device.cek');
Route::get('/perangkat/tambah', [DeviceController::class, 'create'])->name('device.create');
Route::post('/perangkat/tambah', [DeviceController::class, 'store'])->name('device.store');
Route::delete('/perangkat/delete/{id}', [DeviceController::class, 'delete'])->name('device.delete');
Route::get('/perangkat/ubah/{id}', [DeviceController::class, 'edit'])->name('device.edit');
Route::put('/perangkat/update/{id}', [DeviceController::class, 'update'])->name('device.update');





// Route::get('/dashboard', function () {
//     return view('pages.dashboard');
// })->name('dashboard');
