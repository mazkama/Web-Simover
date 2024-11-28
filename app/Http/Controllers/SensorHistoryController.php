<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SensorHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SensorHistoryController extends Controller
{
    /**
     * Menyimpan data history sensor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|exists:devices,id',
            'temperature' => 'nullable|numeric',
            'humidity' => 'nullable|numeric',
            'smoke' => 'nullable|numeric',
            'motion' => 'nullable|boolean',
            'recorded_at' => 'required|date',
        ]);

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
            'recorded_at' => $request->recorded_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $sensorHistory,
        ], 200);
    }
}
