<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\DeviceResource;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class DeviceApiController extends Controller
{

    /**
     * Get all device types for dropdown
     */

    public function ping(Request $request)
    {
        $request->validate([
            'unique_id' => 'required|string|exists:devices,unique_id',
        ]);

        // Update updated_at pada device
        $device = Device::where('unique_id', $request->unique_id)->first();
        $device->touch(); // hanya update updated_at
        return response()->json(['message' => 'Device status updated'], 200);
    }

    public function getDeviceTypes()
    {
        $types = DeviceType::select('id', 'name')->get(); // Ambil id dan nama saja
        return response()->json($types);
    }

    public function getDeviceTypeforDatatables()
    {
        $types = DeviceType::query()
            ->select('name')
            ->distinct()
            ->orderBy('name', 'asc')
            ->pluck('name'); // pluck() untuk mendapatkan array nama

        return response()->json($types);
    }


    /**
     * Get all devices
     */
    public function index()
    {
        $data = Device::query()
            ->select('devices.*')
            ->with('deviceType')
            ->orderBy('devices.created_at', 'desc');

        return DataTables::of($data)->make(true);
    }
    /**
     * Get single device
     */
    public function show($id)
    {
        try {
            $device = Device::with('deviceType')->findOrFail($id);

            return response()->json([
                'device' => new DeviceResource($device)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memuat data device',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new device
     */
    public function store(Request $request)
    {
        $request->validate([
            // unique_id tidak perlu divalidasi 'required' atau 'unique' di sini karena akan digenerate
            'device_type_id' => 'required|exists:device_types,id',
            'status' => 'required|in:active,inactive,error,maintenance', // Pastikan semua status valid
        ]);

        DB::beginTransaction();

        try {
            $deviceType = DeviceType::findOrFail($request->device_type_id);

            // Generate unique_id
            $now = now(); // Carbon instance
            $year = $now->format('y'); // 2 digit tahun (e.g., 25)
            $month = $now->format('m'); // 2 digit bulan (e.g., 06)
            $typeCode = $deviceType->code; // <--- AMBIL DARI KOLOM 'code' YANG BARU
            $deviceVersion = '1'; // Asumsi versi alat default 1. Bisa diambil dari input jika ada

            // Logika generate nomor seri (3 digit, increment per bulan)
            $prefix = $year . $month . $typeCode . $deviceVersion;

            // Dapatkan nomor seri terakhir untuk bulan, tahun, tipe, dan versi ini
            $lastDevice = Device::where('unique_id', 'like', $prefix . '%')
                ->whereBetween('created_at', [$now->startOfMonth(), $now->endOfMonth()]) // Filter per bulan
                ->orderBy('unique_id', 'desc')
                ->first();

            $serial = 1;
            if ($lastDevice) {
                $lastSerial = (int) substr($lastDevice->unique_id, -3);
                $serial = $lastSerial + 1;
            }

            $generatedSerial = str_pad($serial, 3, '0', STR_PAD_LEFT);
            $uniqueId = $prefix . $generatedSerial;

            // Loop untuk memastikan unique_id unik (walaupun jarang dengan skema ini)
            $counter = 0;
            while (Device::where('unique_id', $uniqueId)->exists() && $counter < 1000) {
                $serial++;
                $generatedSerial = str_pad($serial, 3, '0', STR_PAD_LEFT);
                $uniqueId = $prefix . $generatedSerial;
                $counter++;
            }
            if ($counter >= 1000) {
                throw new \Exception('Failed to generate a unique ID after multiple attempts. Consider larger serial digits or clearer unique identifier logic.');
            }

            $device = Device::create([
                'id' => (string) Str::uuid(),
                'unique_id' => $uniqueId, // Gunakan unique_id yang digenerate
                'device_type_id' => $request->device_type_id,
                'status' => $request->status,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Device berhasil ditambahkan!',
                'device' => new DeviceResource($device)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menambahkan device',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update device
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'unique_id' => 'required|unique:devices,unique_id,' . $id,
            'device_type_id' => 'required|exists:device_types,id',
            'status' => 'required|in:active,inactive,maintenance',
            // 'latitude' => 'nullable|numeric',
            // 'longitude' => 'nullable|numeric',
        ]);

        try {
            $device = Device::findOrFail($id);

            $device->update([
                'unique_id' => $request->unique_id,
                'device_type_id' => $request->device_type_id,
                'status' => $request->status,
                // 'latitude' => $request->latitude,
                // 'longitude' => $request->longitude,
            ]);

            return response()->json([
                'message' => 'Device berhasil diperbarui!',
                'device' => new DeviceResource($device)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui device',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete device
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $device = Device::findOrFail($id);
            $device->delete();

            DB::commit();

            return response()->json([
                'message' => 'Device berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus device',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get devices for DataTables
     */
}
