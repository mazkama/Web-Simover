<?php

namespace App\Http\Controllers;

use App\Models\SensorHistory;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isEmpty;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        // Ambil daftar perangkat
        $devices = Device::all();

        if ($devices->isEmpty()) {
            return redirect()->route('device.create');
        }

        // Jika tidak ada 'device_id' di URL, arahkan ke perangkat dengan ID tertinggi
        if (!$request->has('device_id') && $devices->isNotEmpty()) {
            $deviceId = $devices->min('id'); // Ambil device dengan ID terbesar
            return redirect()->route('dashboard', ['device_id' => $deviceId]);
        }

        // Ambil data sensor berdasarkan device_id jika ada
        $historySensors = SensorHistory::query();

        if ($request->has('device_id')) {
            // Filter data sensor berdasarkan device_id
            $historySensors = $historySensors->where('device_id', $request->device_id);

            // Ambil device yang sesuai dengan device_id
            $device = Device::find($request->device_id); // Menggunakan find() untuk mendapatkan model tunggal

            if ($device) {
                // Log nama perangkat jika ditemukan
                Log::info('Nama perangkat: ' . $device->device_name);
            } else {
                // Jika perangkat tidak ditemukan
                Log::warning('Perangkat dengan ID ' . $request->device_id . ' tidak ditemukan.');
            }
        }

        // Ambil data sensor dan urutkan berdasarkan recorded_at, lalu paginasi
        $historySensors = $historySensors->orderByDesc('recorded_at')->paginate(10);

        // Mengirimkan data ke view
        return view('pages.dashboard', compact('device', 'devices', 'historySensors'));
    }
}
