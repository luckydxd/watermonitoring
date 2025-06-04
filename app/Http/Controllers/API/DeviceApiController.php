<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\DeviceResource;
use Illuminate\Support\Facades\DB;

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


    /**
     * Get all devices
     */
    public function index()
    {

        return DeviceResource::collection(Device::with('deviceType')->get());
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
            'unique_id' => 'required|unique:devices',
            'device_type_id' => 'required|exists:device_types,id',
            'status' => 'required|in:active,inactive,maintenance',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try {
            $device = Device::create([
                'id' => (string) Str::uuid(),
                'unique_id' => $request->unique_id,
                'device_type_id' => $request->device_type_id,
                'status' => $request->status,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
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
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            $device = Device::findOrFail($id);

            $device->update([
                'unique_id' => $request->unique_id,
                'device_type_id' => $request->device_type_id,
                'status' => $request->status,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
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
