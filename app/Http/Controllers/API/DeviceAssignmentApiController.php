<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // Untuk UUID

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
        $request->validate([
            // Validasi unique_id yang discan (misalnya, pastikan sesuai format atau ada di DB)
            'unique_id' => 'required|string|regex:/^\d{4}[A-Z]\d{4}$/', // Contoh regex untuk YYMMTVSSS
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        // Cari perangkat berdasarkan unique_id yang discan
        $device = Device::where('unique_id', $request->unique_id)->first();

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Perangkat dengan ID unik ini tidak ditemukan.'], 404);
        }

        // Cek apakah perangkat sudah di-assign ke user ini dan masih aktif
        $existingAssignment = DeviceAssignment::where('device_id', $device->id)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if ($existingAssignment) {
            return response()->json(['success' => false, 'message' => 'Perangkat sudah terdaftar pada akun Anda.'], 409);
        }

        // Opsi: Jika Anda hanya ingin satu pengguna yang aktif meng-assign satu perangkat pada satu waktu
        $anyActiveAssignment = DeviceAssignment::where('device_id', $device->id)
            ->where('is_active', true)
            ->first();
        if ($anyActiveAssignment && $anyActiveAssignment->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Perangkat ini sudah terdaftar pada pengguna lain.'], 409);
        }


        // Buat assignment baru
        $assignment = DeviceAssignment::create([
            'user_id' => $user->id,
            'device_id' => $device->id,
            'is_active' => true,
            'notes' => 'Assigned via QR Code scan.'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil didaftarkan!',
            'assignment' => $assignment,
        ]);
    }
}
