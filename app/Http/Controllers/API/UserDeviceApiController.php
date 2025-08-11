<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceAssignment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserDeviceApiController extends Controller
{

    public function getUserDevices()
    {
        $query = DeviceAssignment::query()
            ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->where('device_assignments.user_id', auth()->id())
            ->where('device_assignments.is_active', true)
            ->select([
                'device_assignments.id',
                'device_assignments.created_at',
                'devices.unique_id',
                'devices.status',
                'devices.last_seen_at',
                'device_types.name as device_type_name'
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->make(true);
    }
    public function edit(Request $request, DeviceAssignment $assignment)
    {
        if ($assignment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($assignment->load('device.deviceType'));
    }

    public function update(Request $request, DeviceAssignment $assignment)
    {
        if ($assignment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $assignment->update($validated);

        if (!$validated['is_active']) {
            $assignment->device()->update(['status' => 'inactive']);
        } else {
            $assignment->device()->update(['status' => 'active']);
        }

        return response()->json(['success' => true, 'message' => 'Data perangkat berhasil diperbarui.']);
    }

    public function destroy(Request $request, DeviceAssignment $assignment)
    {
        if ($assignment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $assignment->delete();

        $isDeviceStillAssigned = DeviceAssignment::where('device_id', $assignment->device_id)
            ->where('is_active', true)
            ->exists();
        if (!$isDeviceStillAssigned) {
            $assignment->device()->update(['status' => 'inactive']);
        }

        return response()->json(['success' => true, 'message' => 'Perangkat berhasil dilepas dari akun Anda.']);
    }
}
