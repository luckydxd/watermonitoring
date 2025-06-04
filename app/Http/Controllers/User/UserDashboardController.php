<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Device;
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

        $lastUpdated = optional($device->updated_at)->format('d M Y - H:i');

        $isOffline = true;
        $offlineTooLong = false;

        if ($device) {
            $diffMinutes = Carbon::parse($device->updated_at)->diffInMinutes(now());

            // Logika baru: cek status juga
            $status = strtolower($device->status); // pastikan lowercase: 'active', 'inactive', 'error'

            if ($status === 'active' && $diffMinutes <= 15) {
                $isOffline = false;
            } elseif ($status === 'inactive') {
                $isOffline = true;
            } else {
                $isOffline = $diffMinutes > 15;
            }

            $offlineTooLong = $diffMinutes > 1440; // Tetap pakai untuk warning 24 jam
        }

        return view('user.dashboard', compact('device', 'lastUpdated', 'isOffline', 'offlineTooLong'));
    }

    public function getTodayUsage()
    {
        $user = auth()->user();
        $today = now()->format('Y-m-d');

        return Cache::remember("user_{$user->id}_usage_{$today}", now()->addHours(1), function () use ($user, $today) {
            return response()->json([
                'total_usage' => WaterConsumptionLog::where('user_id', $user->id)
                    ->whereDate('date', $today)
                    ->sum('total_consumption') ?? 0
            ]);
        });
    }
}
