<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\SensorHistory;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SensorHistoryController extends Controller
{
    /**
     * Menyimpan data history sensor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     protected $NotificationController;

     public function __construct(NotificationController $NotificationController)
     {
         $this->NotificationController = $NotificationController;
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
        Log::info('data history perangkat', [
            'request_data' => $request->all(),
        ]);

        $this->sendFirebase($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 400);
        }

        //cek threshold
        $this->cekThresholdSensor($request);

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
        $response = Http::put('https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/' . $data->device_id . '/sensors.json', $dataSensor);
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

    private function cekThresholdSensor($request)
    {
        // // Ambil threshold dari Firebase
        $firebaseUrl = 'https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/' . $request->device_id . '/thresholds';
        $thresholds = [
            'asap' => $this->getThresholdFromFirebase($firebaseUrl . '/asap.json'),
            'kelembapan' => $this->getThresholdFromFirebase($firebaseUrl . '/kelembapan.json'),
            'suhu' => $this->getThresholdFromFirebase($firebaseUrl . '/suhu.json'),
        ];

        // Kirim notifikasi jika nilai melebihi threshold
        if ($request->smoke > $thresholds['asap']) {
            $this->NotificationController->sendNotificationToTopic('Peringatan Sensor Asap', 'Smoke level is above threshold');
        }
        if ($request->humidity > $thresholds['kelembapan']) {
            $this->NotificationController->sendNotificationToTopic('Peringatan Sensor Kelembapan', 'Humidity level is above threshold');
        }
        if ($request->temperature > $thresholds['suhu']) {
            $this->NotificationController->sendNotificationToTopic('Peringatan Sensor Suhu', 'Temperature is above threshold');
        }
    }

    private function getThresholdFromFirebase($url)
    {
        $response = Http::get($url);

        return $response->successful() ? $response->json() : null;
    }

    public function getData(Request $request)
    {
        // Validasi input device_id
        $request->validate([
            'device_id' => 'required|exists:devices,id',
        ]);

        // Ambil 10 data sensor history terbaru berdasarkan device_id
        $historySensors = SensorHistory::where('device_id', $request->device_id)
            ->orderByDesc('recorded_at')
            ->take(5)
            ->get();

        // Jika data ditemukan, kembalikan respons JSON
        if ($historySensors->isNotEmpty()) {
            return response()->json($historySensors);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data sensor tidak ditemukan.'
        ], 404);
    }

    // public function getData()
    // {
    //     // Validasi input device_id
    //     // $request->validate([
    //     //     'device_id' => 'required|exists:devices,id',
    //     // ]);

    //     // Ambil 10 data sensor history terbaru berdasarkan device_id
    //     //$historySensors = SensorHistory::where('device_id', $request->device_id)
    //     $historySensors = SensorHistory::orderByDesc('recorded_at')
    //         ->take(5)
    //         ->get();

    //     // Jika data ditemukan, kembalikan respons JSON
    //     if ($historySensors->isNotEmpty()) {
    //         return response()->json($historySensors);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Data sensor tidak ditemukan.'
    //     ], 404);
    // }

    public function getDevices()
    {
        $devices = Device::all();

        return $devices;
    }
}
