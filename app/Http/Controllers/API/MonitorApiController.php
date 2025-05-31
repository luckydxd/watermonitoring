<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaterConsumptionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Models\DeviceAssignment;
use App\Models\User;
use App\Models\Device;

class MonitorApiController extends Controller
{

    public function datatablesAssign(Request $request)
    {
        $query = DeviceAssignment::with([
            'device.deviceType',
            'user.userData'
        ])
            ->select('device_assignments.*');

        return DataTables::eloquent($query)
            ->orderColumn('user_datas.name', function ($query, $order) {
                $query->join('users', 'device_assignments.user_id', '=', 'users.id')
                    ->join('user_datas', 'users.id', '=', 'user_datas.user_id')
                    ->orderBy('user_datas.name', $order);
            })
            ->orderColumn('unique_id', function ($query, $order) {
                $query->join('devices', 'device_assignments.device_id', '=', 'devices.id')
                    ->orderBy('devices.unique_id', $order);
            })
            ->orderColumn('device_type.name', function ($query, $order) {
                $query->join('devices', 'device_assignments.device_id', '=', 'devices.id')
                    ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
                    ->orderBy('device_types.name', $order);
            })
            ->toJson();
    }
    // Controller untuk mengambil data users
    public function getUsersForSelect()
    {
        $users = User::role('user')->with('userData')
            ->select('id', 'email')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->userData->name ?? 'No Name',
                    'email' => $user->email
                ];
            });

        return response()->json($users);
    }

    // Controller untuk mengambil device yang tersedia
    public function getAvailableDevices()
    {
        return Device::select('devices.id', 'devices.unique_id', 'device_types.name as type_name')
            ->leftJoin('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->whereDoesntHave('deviceAssignments', function ($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->map(function ($device) {
                return [
                    'id' => $device->id,
                    'unique_id' => $device->unique_id,
                    'device_type' => $device->type_name ? [
                        'name' => $device->type_name
                    ] : null
                ];
            });
    }


    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'device_id' => 'required|exists:devices,id|unique:device_assignments,device_id,NULL,id,is_active,true',
            'assignment_date' => 'required|date',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            // Nonaktifkan assignment aktif sebelumnya jika ada
            if ($request->is_active) {
                DeviceAssignment::where('device_id', $validated['device_id'])
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $assignment = DeviceAssignment::create([
                'id' => (string) Str::uuid(),
                'user_id' => $validated['user_id'],
                'device_id' => $validated['device_id'],
                'assignment_date' => $validated['assignment_date'],
                'is_active' => $validated['is_active'],
                'notes' => $validated['notes'] ?? null
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Device berhasil di-assign ke user!',
                'data' => $assignment
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal melakukan assignment device',
                'error' => $e->getMessage(),
                'request_data' => $validated // Untuk debugging
            ], 500);
        }
    }
    public function datatables(Request $request)
    {
        $query = WaterConsumptionLog::select('water_consumption_logs.*')
            ->with(['user.userData'])
            ->join('users', 'water_consumption_logs.user_id', '=', 'users.id')
            ->leftJoin('user_datas', 'users.id', '=', 'user_datas.user_id')
            ->filterByMonth($request->month)
            ->filterByYear($request->year);

        return DataTables::of($query)
            ->order(function ($query) use ($request) {
                if ($request->has('order')) {
                    $order = $request->input('order')[0];
                    if ($order['column'] == 1) { // Kolom nama user
                        $query->orderBy('user_datas.name', $order['dir']);
                    } else {
                        $columnName = $request->input('columns')[$order['column']]['data'];
                        $query->orderBy($columnName, $order['dir']);
                    }
                } else {
                    $query->orderBy('date', 'desc');
                }
            })
            ->toJson();
    }
    public function index()
    {
        // Fetch all water consumption logs
        $logs = WaterConsumptionLog::with(['user.userData'])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs,
            'message' => 'Data konsumsi air berhasil diambil'
        ]);
    }
    // CREATE: Menyimpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'total_consumption' => 'required|numeric|min:0',
        ]);

        $log = WaterConsumptionLog::create([
            'id' => Str::uuid(),
            ...$validated
        ]);

        return response()->json([
            'success' => true,
            'data' => $log,
        ], 201);
    }

    // UPDATE: Mengedit data
    // DeviceAssignmentController.php

    public function edit($id)
    {
        $assignment = DeviceAssignment::with(['user.userData', 'device.deviceType'])
            ->findOrFail($id);

        return response()->json([
            'assignment' => $assignment,
            'users' => User::with('userData')->get(),
            'available_devices' => Device::whereDoesntHave('DeviceAssignments', function ($query) {
                $query->where('is_active', true);
            })->orWhere('id', $assignment->device_id) // Include current device
                ->with('deviceType')
                ->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $assignment = DeviceAssignment::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'device_id' => 'required|exists:devices,id|unique:device_assignments,device_id,' . $id . ',id,is_active,true',
            'assignment_date' => 'required|date',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        // Nonaktifkan assignment aktif sebelumnya jika mengaktifkan yang baru
        if ($validated['is_active'] && $assignment->device_id != $validated['device_id']) {
            DeviceAssignment::where('device_id', $validated['device_id'])
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $assignment->update($validated);

        return response()->json([
            'message' => 'Device assignment updated successfully',
            'data' => $assignment
        ]);
    }
    // DELETE: Menghapus data
    public function destroy($id)
    {
        try {
            $assignment = DeviceAssignment::findOrFail($id);
            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Assignment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting assignment: ' . $e->getMessage()
            ], 500);
        }
    }
}
