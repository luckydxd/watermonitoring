<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\User;
use App\Models\DeviceAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class DeviceAssignmentApiController extends Controller
{
    public function generateQrCodeData(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
        ]);

        $device = Device::find($request->device_id);

        $token = Str::uuid();



        return response()->json([
            'success' => true,
            'data' => [
                'unique_id' => $device->unique_id,
                'assignment_url' => route('device.assign.by.qr'),
            ],
            'message' => 'QR Code data generated successfully.'
        ]);
    }

    public function assignByQrCode(Request $request)
    {
        $initialValidation = Validator::make($request->all(), [
            'unique_id' => 'required|string|exists:devices,unique_id',
        ]);

        if ($initialValidation->fails()) {
            return response()->json(['success' => false, 'message' => 'ID unik perangkat tidak valid atau tidak ditemukan.', 'errors' => $initialValidation->errors()], 422);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $device = Device::with('deviceType')->where('unique_id', $request->unique_id)->first();

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

        $existingAssignment = DeviceAssignment::where('device_id', $device->id)->where('is_active', true)->first();

        if ($existingAssignment) {
            $message = $existingAssignment->user_id === $user->id
                ? 'Perangkat sudah terdaftar pada akun Anda.'
                : 'Perangkat ini sudah terdaftar pada pengguna lain.';
            return response()->json(['success' => false, 'message' => $message], 409);
        }

        $assignment = null;

        try {
            DB::transaction(function () use ($request, $user, $device, $isFlowDevice, &$assignment) {
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
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required|string|exists:devices,unique_id',
            'initial_meter_reading' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Input tidak valid.', 'errors' => $validator->errors()], 422);
        }

        $device = Device::where('unique_id', $request->unique_id)->first();
        $user = Auth::user();

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Perangkat tidak ditemukan.'], 404);
        }

        $isAlreadyAssigned = DeviceAssignment::where('device_id', $device->id)->where('is_active', true)->exists();
        if ($isAlreadyAssigned) {
            return response()->json(['success' => false, 'message' => 'Perangkat ini sudah terdaftar pada pengguna lain.'], 409);
        }

        try {
            DB::transaction(function () use ($request, $user, $device) {
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

                $device->status = 'inactive';
                $device->save();

                activity()
                    ->causedBy($user)
                    ->performedOn($device)
                    ->log("Mendaftarkan perangkat {$device->unique_id} ke akunnya");
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal saat mendaftarkan perangkat.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil didaftarkan dan diaktifkan!'
        ], 201);
    }


    // Tambahkan method ini di controller yang sama
    public function assignByTechnician(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|exists:users,id',
            'unique_id' => 'required|string|exists:devices,unique_id',
            'initial_meter_reading' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $device = Device::where('unique_id', $request->unique_id)->first();
        $targetUser = User::find($request->user_id);
        $technician = Auth::user();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan.'
            ], 404);
        }

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.'
            ], 404);
        }

        // Cek apakah device sudah di-assign ke user yang aktif
        $isAlreadyAssigned = DeviceAssignment::where('device_id', $device->id)
            ->where('is_active', true)
            ->exists();

        if ($isAlreadyAssigned) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat ini sudah terdaftar pada pengguna lain.'
            ], 409);
        }

        try {
            DB::transaction(function () use ($request, $targetUser, $device, $technician) {
                $assignmentData = [
                    'user_id' => $targetUser->id,
                    'device_id' => $device->id,
                    'is_active' => true,
                    'notes' => $request->notes ?? 'didaftarkan oleh teknisi: ' . $technician->name
                ];

                if ($request->filled('initial_meter_reading')) {
                    $assignmentData['initial_meter_reading'] = $request->initial_meter_reading;
                }

                DeviceAssignment::create($assignmentData);

                $device->status = 'inactive';
                $device->save();

                $targetUserName = optional($targetUser->userData)->name ?? $targetUser->email; // Gunakan email jika nama kosong
                $targetUserAddress = optional($targetUser->userData)->address;

                // 2. Susun kalimat deskripsi untuk log
                $description = "Teknisi mendaftarkan perangkat {$device->unique_id} ke pelanggan {$targetUserName}";

                // Tambahkan "di alamat..." hanya jika alamatnya ada
                if ($targetUserAddress) {
                    $description .= " di alamat {$targetUserAddress}";
                }

                // Log activity
                activity()
                    ->causedBy($technician)
                    ->performedOn($device)
                    // TAMBAHKAN BARIS INI untuk menyimpan info target user
                    ->withProperties([
                        'target_user_id' => $targetUser->id,
                        'target_user_name' => optional($targetUser->userData)->name
                    ])
                    ->log($description);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal saat mendaftarkan perangkat.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Perangkat berhasil didaftarkan ke {$targetUser->name}!"
        ], 201);
    }
}
