<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DeviceAssignmentApiController extends Controller
{
    /**
     * Generates a unique token for device assignment via QR code.
     * This token will be embedded in the QR code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateQrCodeData(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
        ]);

        $device = Device::find($request->device_id);

        // Generate a unique, short-lived token for this assignment
        $token = Str::uuid(); // Using UUID for robust unique tokens

        // Store the token associated with the device, possibly with an expiry
        // For simplicity, let's just use the device's unique_id directly in the QR
        // If you need temporary tokens, you'd create a new table (e.g., qr_tokens)
        // with columns like device_id, token, expires_at

        // For now, let's just return the device's unique_id,
        // and the mobile app will send this unique_id to the assignByQrCode endpoint.
        // This is simpler as it doesn't require storing temporary tokens.

        return response()->json([
            'success' => true,
            'data' => [
                'unique_id' => $device->unique_id,
                'assignment_url' => route('device.assign.by.qr'), // Endpoint to call
            ],
            'message' => 'QR Code data generated successfully.'
        ]);
    }

    /**
     * Assigns a device to the authenticated user based on a QR code scan.
     * This endpoint will be called by the mobile application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignByQrCode(Request $request)
    {
        // Validasi awal untuk memastikan unique_id ada dan valid
        $initialValidation = Validator::make($request->all(), [
            'unique_id' => 'required|string|exists:devices,unique_id',
        ]);

        if ($initialValidation->fails()) {
            return response()->json(['success' => false, 'message' => 'ID unik perangkat tidak valid atau tidak ditemukan.', 'errors' => $initialValidation->errors()], 422);
        }

        // Otentikasi pengguna
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        // Cari perangkat DAN tipe perangkatnya (eager load)
        $device = Device::with('deviceType')->where('unique_id', $request->unique_id)->first();

        // Validasi Kondisional berdasarkan Tipe Perangkat
        $rules = [];
        $isFlowDevice = $device->deviceType && $device->deviceType->code === 'F';

        if ($isFlowDevice) {
            $rules['initial_meter_reading'] = 'required|numeric|min:0';
        }

        if (!empty($rules)) {
            $conditionalValidation = Validator::make($request->all(), $rules);
            if ($conditionalValidation->fails()) {
                return response()->json(['success' => false, 'message' => 'Input meteran awal diperlukan untuk tipe perangkat ini.', 'errors' => $conditionalValidation->errors()], 422);
            }
        }

        // Pengecekan bisnis: Apakah perangkat sudah aktif ditugaskan?
        $existingAssignment = DeviceAssignment::where('device_id', $device->id)->where('is_active', true)->first();

        if ($existingAssignment) {
            $message = $existingAssignment->user_id === $user->id
                ? 'Perangkat sudah terdaftar pada akun Anda.'
                : 'Perangkat ini sudah terdaftar pada pengguna lain.';
            return response()->json(['success' => false, 'message' => $message], 409);
        }

        $assignment = null;

        // --- MENGGUNAKAN DATABASE TRANSACTION UNTUK KEAMANAN DATA ---
        try {
            DB::transaction(function () use ($request, $user, $device, $isFlowDevice, &$assignment) {
                // Siapkan data untuk assignment baru
                $assignmentData = [
                    'user_id' => $user->id,
                    'device_id' => $device->id,
                    'is_active' => true,
                    'notes' => 'Assigned via QR Code scan.'
                ];

                if ($isFlowDevice) {
                    $assignmentData['initial_meter_reading'] = $request->initial_meter_reading;
                }

                $assignment = DeviceAssignment::create($assignmentData);
                $device->status = 'active';
                $device->save();
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mencoba mendaftarkan perangkat.', 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil didaftarkan dan diaktifkan!',
            'assignment' => $assignment,
        ]);
    }

    public function assignByDashboard(Request $request)
    {
        // Validasi input awal
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required|string|exists:devices,unique_id',
            'initial_meter_reading' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Input tidak valid.', 'errors' => $validator->errors()], 422);
        }

        // 1. Ambil objek 'device' DARI DATABASE berdasarkan unique_id yang dikirim
        $device = Device::where('unique_id', $request->unique_id)->first();
        $user = Auth::user();

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Perangkat tidak ditemukan.'], 404);
        }

        // Pengecekan apakah perangkat sudah ditugaskan ke orang lain
        $isAlreadyAssigned = DeviceAssignment::where('device_id', $device->id)->where('is_active', true)->exists();
        if ($isAlreadyAssigned) {
            return response()->json(['success' => false, 'message' => 'Perangkat ini sudah terdaftar pada pengguna lain.'], 409);
        }

        try {
            DB::transaction(function () use ($request, $user, $device) {
                // 2. Siapkan dan buat 'DeviceAssignment' baru
                $assignmentData = [
                    'user_id' => $user->id,
                    'device_id' => $device->id,
                    'is_active' => true,
                    'notes' => 'Assigned via Dashboard'
                ];
                if ($request->filled('initial_meter_reading')) {
                    $assignmentData['initial_meter_reading'] = $request->initial_meter_reading;
                }
                DeviceAssignment::create($assignmentData);

                // =======================================================
                // 3. UBAH STATUS DAN SIMPAN (INI KUNCINYA)
                // Kode ini secara paksa mengubah status pada objek $device
                // yang kita ambil di awal, lalu menyimpannya.
                // =======================================================
                $device->status = 'active';
                $device->save();
                // =======================================================
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal saat mendaftarkan perangkat.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil didaftarkan dan diaktifkan!'
        ], 201);
    }
}
