<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use Illuminate\Support\Facades\Http;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        return response()->json($devices, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string',
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

        $response = Http::patch('https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/' . $validated['id'] . '/.json', $dataFirebase);

        if ($response->successful()) {
            $device = Device::create([
                'id' => $validated['id'],
                'device_name' => $validated['device_name'],
            ]);

            return response()->json($device, 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim data ke Firebase',
                'errors' => $response->json(),
            ], 500);
        }
    }

    public function show($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['message' => 'Perangkat tidak ditemukan'], 404);
        }

        return response()->json($device, 200);
    }

    public function update(Request $request, $id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['message' => 'Perangkat tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'device_name' => 'sometimes|string|max:255',
            'sensor_temp' => 'sometimes|integer',
            'sensor_humidity' => 'sometimes|integer',
            'sensor_smoke' => 'sometimes|integer',
        ]);

        $dataFirebase = [
            'name' => $validated['device_name'] ?? $device->device_name,
            'thresholds' => [
                'suhu' => $validated['sensor_temp'] ?? null,
                'kelembapan' => $validated['sensor_humidity'] ?? null,
                'asap' => $validated['sensor_smoke'] ?? null,
            ]
        ];

        $response = Http::patch('https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/' . $id . '/.json', $dataFirebase);

        if ($response->successful()) {
            $device->update($validated);

            return response()->json($device, 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim data ke Firebase',
                'errors' => $response->json(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['message' => 'Perangkat tidak ditemukan'], 404);
        }

        $response = Http::delete('https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/' . $id . '/.json');

        if ($response->successful()) {
            $device->delete();

            return response()->json(['message' => 'Perangkat berhasil dihapus'], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data dari Firebase',
                'errors' => $response->json(),
            ], 500);
        }

    }
}
