<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\FlowPressureSensor;
use App\Models\WaterQualitySensor;
use App\Models\WaterConsumptionLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class DeviceDataController extends Controller
{
    public function registerDevice(Request $request)
    {
        // 1. Validasi payload pendaftaran
        $validated = $request->validate([
            'unique_id'  => 'required|string|exists:devices,unique_id', // Pastikan ID ini sudah dibuat Admin
            'unique_key' => 'required|string|mac_address', // Pastikan format MAC address benar
        ]);

        try {
            // 2. Cari perangkat berdasarkan ID unik yang dikirim
            $device = Device::where('unique_id', $validated['unique_id'])->firstOrFail();

            // 3. Periksa apakah perangkat ini sudah pernah mendaftarkan MAC address
            if (!is_null($device->unique_key)) {
                // Jika sudah ada, bandingkan. Jika sama, anggap sukses. Jika beda, tolak.
                if ($device->unique_key === $validated['unique_key']) {
                    return response()->json(['message' => 'Perangkat ini sudah terdaftar sebelumnya.'], 200);
                } else {
                    Log::warning("Upaya pendaftaran ulang dengan MAC berbeda untuk device ID {$device->id}");
                    return response()->json(['message' => 'Registrasi Gagal: Perangkat sudah terhubung dengan MAC Address lain.'], 409); // 409 Conflict
                }
            }

            // 4. Jika kolom unique_key masih kosong, ini adalah pendaftaran pertama. Simpan!
            $device->unique_key = $validated['unique_key'];
            $device->save();

            Log::info("Registrasi Berhasil: Perangkat dengan unique_id {$device->unique_id} telah terhubung ke MAC {$device->unique_key}.");
            return response()->json(['message' => 'Registrasi berhasil. Perangkat sekarang aktif.'], 201);
        } catch (\Exception $e) {
            Log::error("Gagal saat registrasi perangkat: " . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error saat registrasi.'], 500);
        }
    }
    public function storeFlowPressure(Request $request)
    {
        $validatedData = $request->validate([

            'flow_rate'         => 'required|numeric|min:0',
            'pressure'          => 'required|numeric|min:0',
            'volume'            => 'required|numeric|min:0',
        ]);

        try {
            $device = $request->attributes->get('authenticated_device');

            $assignment = $device->deviceAssignments()->where('is_active', true)->first();
            if (!$assignment) {
                return response()->json(['message' => 'Error: Perangkat tidak ditugaskan ke user manapun.'], 409);
            }

            DB::transaction(function () use ($device, $validatedData, $assignment) {
                FlowPressureSensor::create(['device_id' => $device->id, 'flow_rate' => $validatedData['flow_rate'], 'pressure' => $validatedData['pressure'], 'volume' => $validatedData['volume'], 'measured_at' => Carbon::now()]);
                WaterConsumptionLog::create(['user_id' => $assignment->user_id, 'total_consumption' => $validatedData['volume']]);
                $device->last_seen_at = Carbon::now();
                $device->save();
            });


            return response()->json(['message' => 'Data flow, pressure, dan konsumsi berhasil diterima.'], 201);
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan data flow/pressure untuk MAC {$device->unique_key}: " . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error.'], 500);
        }
    }

    public function storeWaterQuality(Request $request)
    {
        $validatedData = $request->validate([
            'water_level' => 'required|numeric|min:0',
            'turbidity'   => 'required|numeric|min:0',
        ]);

        try {
            $device = $request->attributes->get('authenticated_device');

            DB::transaction(function () use ($device, $validatedData) {
                WaterQualitySensor::create(['device_id' => $device->id, 'water_level' => $validatedData['water_level'], 'turbidity' => $validatedData['turbidity'], 'measured_at' => Carbon::now()]);
                $device->last_seen_at = Carbon::now();
                $device->save();
            });

            return response()->json(['message' => 'Data water level dan turbidity berhasil diterima.'], 201);
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan data water quality untuk MAC {$device->unique_key}: " . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error.'], 500);
        }
    }



    public function getDeviceConfig(Request $request)
    {
        // Middleware sudah mengautentikasi dan melampirkan perangkat
        $device = $request->attributes->get('authenticated_device');

        if (!$device) {
            return response()->json(['message' => 'Perangkat tidak terautentikasi.'], 401);
        }

        // Cari assignment aktif untuk perangkat ini
        $assignment = DeviceAssignment::where('device_id', $device->id)
            ->where('is_active', true)
            ->first();

        if (!$assignment) {
            // Perangkat tidak ditugaskan atau tidak aktif, mungkin tidak ada konfigurasi yang relevan
            return response()->json([
                'message' => 'Perangkat tidak ditugaskan atau tidak aktif, tidak ada konfigurasi spesifik.',
                'config' => []
            ], 200);
        }

        // Siapkan data konfigurasi yang akan dikirim ke perangkat
        $config = [
            'initial_meter_reading' => $assignment->initial_meter_reading,
            // Anda bisa menambahkan konfigurasi lain di sini jika diperlukan,
            // contoh: 'polling_interval_seconds' => 300,
            // 'firmware_update_url' => 'http://example.com/firmware/v2.bin'
        ];

        Log::info("Perangkat {$device->unique_id} meminta konfigurasi. Mengirim initial_meter_reading: {$config['initial_meter_reading']}.");

        return response()->json([
            'message' => 'Konfigurasi perangkat berhasil diambil.',
            'config' => $config
        ], 200);
    }
}
