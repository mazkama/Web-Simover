<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SensorHistory;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class SensorHistoryController extends Controller
{
    /**
     * Menyimpan data history sensor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getDevices(){
        $devices = Device::all();

        return $devices;
    }


    public function store(Request $request)
    {
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|exists:devices,id',
            'temperature' => 'nullable|numeric',
            'humidity' => 'nullable|numeric',
            'smoke' => 'nullable|numeric',
            'motion' => 'nullable|boolean',
        ]);

        $this->sendFirebase($request);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Simpan data ke dalam sensor_histories
        $sensorHistory = SensorHistory::create([
            'device_id' => $request->device_id,
            'temperature' => $request->temperature,
            'humidity' => $request->humidity,
            'smoke' => $request->smoke,
            'motion' => $request->motion,
            'recorded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $sensorHistory,
        ], 200);

    }

    public function sendFirebase($data)
    {
        // Data yang akan dikirim ke Firebase
        $dataSensor = [
            'device_id' => $data->device_id,
            'temperature' => $data->temperature,
            'humidity' => $data->humidity,
            'smoke' => $data->smoke,
            'motion' => $data->motion,
            //'recorded_at' => now()->toDateTimeString(), // Menggunakan format yang sesuai Firebase
            'recorded_at' => now(), // Menggunakan format yang sesuai Firebase
        ];

        // Kirim data ke Firebase menggunakan HTTP Client Laravel
        $response = Http::put('https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/' . $data->device_id .'/sensors.json', $dataSensor);
        //$response = Http::put('https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/1000000001', $dataSensor);

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan dan dikirim ke Firebase',
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim data ke Firebase',
                'errors' => $response->json(),
            ], 500);
        }
    }
}
