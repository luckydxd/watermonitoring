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
        $user_id = auth()->id();

        $query = Device::query()
            ->with(['deviceType', 'activeAssignment'])

            ->whereHas('activeAssignment', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });

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
