<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

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
        // Langkah 1: Validasi awal untuk memastikan unique_id ada
        $initialValidation = Validator::make($request->all(), [
            'unique_id' => 'required|string|exists:devices,unique_id',
        ]);

        if ($initialValidation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $initialValidation->errors()
            ], 422);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        // Langkah 2: Cari perangkat DAN tipe perangkatnya (eager load)
        $device = Device::with('deviceType')->where('unique_id', $request->unique_id)->first();

        // (Pemeriksaan ini sebenarnya sudah ditangani oleh validasi 'exists', tapi sebagai pengaman tambahan)
        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Perangkat dengan ID unik ini tidak ditemukan.'], 404);
        }

        // Langkah 3: Validasi Kondisional berdasarkan Tipe Perangkat
        $rules = []; // Aturan validasi tambahan
        $isFlowDevice = false;

        // Periksa apakah deviceType ada dan kodenya adalah 'F' (atau nama yang sesuai)
        if ($device->deviceType && $device->deviceType->code === 'F') {
            $isFlowDevice = true;
            $rules['initial_meter_reading'] = 'required|numeric|min:0';
        }

        // Jalankan validasi kedua jika ada aturan tambahan
        if (!empty($rules)) {
            $conditionalValidation = Validator::make($request->all(), $rules);
            if ($conditionalValidation->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Input meteran awal diperlukan untuk tipe perangkat ini.',
                    'errors' => $conditionalValidation->errors()
                ], 422);
            }
        }

        // Langkah 4: Lakukan pengecekan bisnis (kode Anda yang sudah ada)
        $existingAssignment = DeviceAssignment::where('device_id', $device->id)
            ->where('is_active', true)
            ->first();

        if ($existingAssignment) {
            if ($existingAssignment->user_id === $user->id) {
                return response()->json(['success' => false, 'message' => 'Perangkat sudah terdaftar pada akun Anda.'], 409);
            } else {
                return response()->json(['success' => false, 'message' => 'Perangkat ini sudah terdaftar pada pengguna lain.'], 409);
            }
        }

        // Langkah 5: Siapkan data untuk dibuat, termasuk meteran awal jika relevan
        $assignmentData = [
            'user_id' => $user->id,
            'device_id' => $device->id,
            'is_active' => true,
            'notes' => 'Assigned via QR Code scan.'
        ];

        if ($isFlowDevice) {
            $assignmentData['initial_meter_reading'] = $request->initial_meter_reading;
        }

        // Langkah 6: Buat assignment baru
        $assignment = DeviceAssignment::create($assignmentData);

        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil didaftarkan!',
            'assignment' => $assignment,
        ]);
    }
}
