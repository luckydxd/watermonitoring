<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Device;
use Carbon\Carbon;
use App\Models\WaterConsumptionLog;
use Illuminate\Support\Facades\DB;

class MonitorManagementController extends Controller
{
    public function index()
    {
        // ----Widget Water Consumption----
        // Hitung total bulan ini
        $currentMonthTotal = WaterConsumptionLog::whereBetween('date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->sum('total_consumption');

        // Hitung total bulan lalu
        $lastMonthTotal = WaterConsumptionLog::whereBetween('date', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])->sum('total_consumption');

        // Hitung persentase perubahan
        $percentageChange = $lastMonthTotal != 0
            ? round(($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal * 100, 2)
            : 0;

        // ----Widget Highest Water Consumption by User----
        $topUser = WaterConsumptionLog::select(
            'users.id',
            'user_datas.name',
            DB::raw('SUM(water_consumption_logs.total_consumption) as total_consumption')
        )
            ->join('users', 'water_consumption_logs.user_id', '=', 'users.id')
            ->leftJoin('user_datas', 'users.id', '=', 'user_datas.user_id')
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('users.id', 'user_datas.name')
            ->orderByDesc('total_consumption')
            ->first();

        // Calculate percentage change vs last month
        if ($topUser) {
            $lastMonthUsage = WaterConsumptionLog::where('user_id', $topUser->id)
                ->whereBetween('date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->sum('total_consumption');

            $topUser->percentage = $lastMonthUsage > 0
                ? round(($topUser->total_consumption - $lastMonthUsage) / $lastMonthUsage * 100, 2)
                : 0;
        } else {
            // Default values if no data
            $topUser = (object)[
                'name' => 'No Data',
                'total_consumption' => 0,
                'percentage' => 0
            ];
        }

        // ----Widget Daily Avg. Usage----
        // Hitung rata-rata harian
        $daysInMonth = now()->daysInMonth;
        $currentMonthAvg = round($currentMonthTotal / $daysInMonth, 2);


        // ----Widget Active Users----
        $activeUsers = User::role('user') // Filter hanya role 'user'
            ->where('is_active', 1)
            ->count();
        // ----Widget Active Devices----
        $activeDevices = Device::where('status', 'active')->count();
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthActive = User::where('is_active', 1)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        $growth = $lastMonthActive > 0
            ? round((($activeUsers - $lastMonthActive) / $lastMonthActive) * 100, 2)
            : 0;

        return view('admin.monitor.index', compact(

            'percentageChange',
            'topUser',

            'currentMonthAvg',
            'activeUsers',
            'growth',
            'activeDevices',
            'currentMonthTotal',
            'lastMonthTotal',

        ));
    }
}
