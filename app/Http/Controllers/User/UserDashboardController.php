<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\WaterConsumptionLog;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;


class UserDashboardController extends Controller
{

    public function index()
    {
        $device = Device::join('device_assignments', 'devices.id', '=', 'device_assignments.device_id')
            ->where('device_assignments.user_id', auth()->id())
            ->where('device_assignments.is_active', true)
            ->select('devices.*')
            ->first();

        // Inisialisasi variabel dengan nilai default
        $lastUpdated = null;
        $isOffline = null;
        $offlineTooLong = false;
        $hasDevice = !is_null($device);

        if ($hasDevice) {
            $lastUpdated = optional($device->updated_at)->format('d M Y - H:i');

            $diffMinutes = Carbon::parse($device->updated_at)->diffInMinutes(now());
            $status = strtolower($device->status);

            if ($status === 'active' && $diffMinutes <= 15) {
                $isOffline = false;
            } elseif ($status === 'inactive') {
                $isOffline = true;
            } else {
                $isOffline = $diffMinutes > 15;
            }

            $offlineTooLong = $diffMinutes > 1440;
        }

        return view('user.dashboard', compact(
            'device',
            'lastUpdated',
            'isOffline',
            'offlineTooLong',
            'hasDevice'
        ));
    }

    public function getTodayUsage()
    {
        $user = auth()->user();
        $today = now()->format('Y-m-d');

        // Cek apakah user memiliki device
        $hasDevice = DeviceAssignment::where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();

        if (!$hasDevice) {
            return response()->json([
                'error' => 'Anda tidak memiliki perangkat yang terhubung',
                'total_usage' => 0
            ], 200);
        }

        return Cache::remember("user_{$user->id}_usage_{$today}", now()->addHours(1), function () use ($user, $today) {
            return response()->json([
                'total_usage' => WaterConsumptionLog::where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->sum('total_consumption') ?? 0
            ]);
        });
    }
}
