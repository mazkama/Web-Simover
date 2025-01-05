<?php

namespace App\Http\Controllers;

use App\Services\AESDecryptor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Log;

class DataController extends Controller
{
    public function store(Request $request)
    {
        try {

            // Ambil data terenkripsi dari request
            $encryptedData = $request->getContent(); 

            if (!$encryptedData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Encrypted data is required.',
                ], 400);
            }

            // Dekripsi data menggunakan layanan AESDecryptor
            $decryptor = new AESDecryptor();
            $decryptedData = $decryptor->decrypt($encryptedData);

            if (!is_array($decryptedData)) {
                Log::info('Dekripsi Gagal', [
                    'status' => 'error',
                    'message' => 'Decrypted data is not valid JSON.',
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Decrypted data is not valid JSON.',
                ], 400);
            }

            // Validasi data setelah dekripsi
            $validator = Validator::make($decryptedData, [
                'device_id' => 'required',
                'temperature' => 'nullable|numeric',
                'humidity' => 'nullable|numeric',
                'smoke' => 'nullable|numeric',
                'motion' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Lakukan proses dengan data yang valid
            // Misalnya, simpan ke database
            $deviceData = $validator->validated();

            // Simpan data ke database (contoh)
            // DeviceData::create($deviceData);

            Log::info('Data berhasil diproses', [
                'data' => $deviceData,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data processed successfully.',
            ], 200);
        } catch (Exception $e) {
            // Tangani kesalahan dekripsi atau lainnya
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
