<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::with('latestSensorHistory')->get();

        if ($devices->isEmpty()) {
            return redirect()->route('device.create');
        }

        return view('pages.devices.index', compact('devices'));
    }

    public function create()
    {
        return view('pages.devices.create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'device_id' => 'required|string',
            'device_name' => 'required|string|max:255',
            'sensor_temp' => 'required|integer',
            'sensor_humidity' => 'required|integer',
            'sensor_smoke' => 'required|integer',
        ]);

        $dataFirebase = [
            'name' => $validated['device_name'],
            'thresholds' => [
                'suhu' => $validated['sensor_temp'],
                'kelembapan' => $validated['sensor_humidity'],
                'asap' => $validated['sensor_smoke'],
            ]
        ];

        $response = Http::patch('https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/' . $validated['device_id'] . '/.json', $dataFirebase);

        if ($response->successful()) {
            // Simpan data perangkat ke database
            Device::create([
                'id' => $validated['device_id'],
                'device_name' => $validated['device_name'],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim data ke Firebase',
                'errors' => $response->json(),
            ], 500);
        }

        return redirect()->route('device.index')->with('success', 'Perangkat berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $device = Device::findOrFail($id);

        // // Ambil threshold dari Firebase
        $firebaseUrl = 'https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/' . $id . '/thresholds/.json';
        $response = Http::get($firebaseUrl);

        if ($response->successful()) {
            $thresholds = $response->json();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data ke Firebase',
                'errors' => $response->json(),
            ], 500);
        }

        return view('pages.devices.edit', compact('device', 'thresholds'));
    }

    public function update(Request $request, $id)
    {
        $device = Device::findOrFail($id);

        $validated = $request->validate([
            'device_id' => 'required|string',
            'device_name' => 'required|string|max:255',
            'sensor_temp' => 'required|integer',
            'sensor_humidity' => 'required|integer',
            'sensor_smoke' => 'required|integer',
        ]);

        $dataFirebase = [
            'name' => $validated['device_name'],
            'thresholds' => [
                'suhu' => $validated['sensor_temp'],
                'kelembapan' => $validated['sensor_humidity'],
                'asap' => $validated['sensor_smoke'],
            ]
        ];

        $response = Http::patch('https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/' . $validated['device_id'] . '/.json', $dataFirebase);

        if ($response->successful()) {

            $device->update([
                'device_name' => $request->input('device_name'),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim data ke Firebase',
                'errors' => $response->json(),
            ], 500);
        }

        return redirect()->route('device.index')->with('success', 'Device updated successfully!');
    }

    public function delete($id)
    {
        $device = Device::find($id);

        if (!$device) {
            // Jika perangkat tidak ditemukan, redirect dengan pesan error
            return redirect()->route('device.index')->with('error', 'Perangkat tidak ditemukan!');
        }

        $response = Http::delete('https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/' . $id . '/.json');

        if ($response->successful()) {
            $device->delete();

            // Redirect dengan pesan sukses
            return redirect()->route('device.index')->with('success', 'Perangkat berhasil dihapus!');
        } else {

            // Redirect dengan pesan gagal
            return redirect()->route('device.index')->with('error', 'Gagal Menghapus perangkat!');
        }
    }

    public function checkDeviceId(Request $request)
    {
        $deviceId = $request->input('device_id');
        $firebaseUrl = "https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/.json";

        // Fetch data from Firebase
        $response = Http::get($firebaseUrl);

        if ($response->failed()) {
            return response()->json(['exists' => false, 'message' => 'Failed to fetch data from Firebase'], 500);
        }

        $devices = $response->json();

        // Check if the device ID exists in Firebase and not in MySQL
        if (isset($devices[$deviceId]) && !Device::where('id', $deviceId)->exists()) {
            return response()->json(['exists' => true]);
        }

        return response()->json(['exists' => false]);
    }
}
