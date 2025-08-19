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
        $validated = $request->validate([
            'unique_id'  => 'required|string|exists:devices,unique_id',
            'unique_key' => 'required|string|mac_address',
        ]);

        try {
            // Cari perangkat berdasarkan ID unik yang dikirim
            $device = Device::where('unique_id', $validated['unique_id'])->firstOrFail();

            // Periksa apakah perangkat ini sudah pernah mendaftarkan MAC address
            if (!is_null($device->unique_key)) {
                // validasikan
                if ($device->unique_key === $validated['unique_key']) {
                    return response()->json(['message' => 'Perangkat ini sudah terdaftar sebelumnya.'], 200);
                } else {
                    Log::warning("Upaya pendaftaran ulang dengan MAC berbeda untuk device ID {$device->id}");
                    return response()->json(['message' => 'Registrasi Gagal: Perangkat sudah terhubung dengan MAC Address lain.'], 409); // 409 Conflict
                }
            }

            // Jika kolom unique_key masih kosong = pendaftaran pertama.
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
                $device->status = 'active';
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
                $device->status = 'active';
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
        $device = $request->attributes->get('authenticated_device');

        if (!$device) {
            return response()->json(['message' => 'Perangkat tidak terautentikasi.'], 401);
        }

        $assignment = DeviceAssignment::where('device_id', $device->id)
            ->where('is_active', true)
            ->first();

        if (!$assignment) {

            return response()->json([
                'message' => 'Perangkat tidak ditugaskan atau tidak aktif, tidak ada konfigurasi spesifik.',
                'config' => []
            ], 200);
        }

        $config = [
            'initial_meter_reading' => $assignment->initial_meter_reading,
            // 
            // 'polling_interval_seconds' => 300,
        ];

        Log::info("Perangkat {$device->unique_id} meminta konfigurasi. Mengirim initial_meter_reading: {$config['initial_meter_reading']}.");

        return response()->json([
            'message' => 'Konfigurasi perangkat berhasil diambil.',
            'config' => $config
        ], 200);
    }
}
