<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WaterConsumptionLog;
use App\Models\Device;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class TeknisiDashboardController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        // Format tanggal
        $tanggalHariIni = now()->translatedFormat('l, d F Y');
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
        $activeUsers = User::role('user')
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

        // ----Widget Complaint Pending----

        $totalComplaints = Complaint::where('status', 'pending')->count(); // Statistik baru


        // Line Area Chart
        $waterUsageData = $this->getWaterUsageChartData('week');

        // Donut Chart
        $deviceStats = $this->getDeviceStats();

        // Bar Chart
        $complaintBarData = $this->getComplaintBarData('week');

        return view('teknisi.dashboard', compact(

            'percentageChange',
            'topUser',

            'currentMonthAvg',
            'activeUsers',
            'growth',
            'activeDevices',
            'currentMonthTotal',
            'lastMonthTotal',
            'totalComplaints',
            'tanggalHariIni',
            'waterUsageData',
            'deviceStats',
            'complaintBarData'

        ));
    }

    private function getWaterUsageChartData($period)
    {
        $now = Carbon::now();
        $query = WaterConsumptionLog::query();

        switch ($period) {
            case 'today':
                $query->whereDate('date', $now->toDateString());
                break;
            case 'yesterday':
                $query->whereDate('date', $now->subDay()->toDateString());
                break;
            case 'week':
                $query->whereBetween('date', [$now->subDays(7)->toDateString(), $now->toDateString()]);
                break;
            case 'month':
                $query->whereBetween('date', [$now->subDays(30)->toDateString(), $now->toDateString()]);
                break;
            case 'current_month':
                $query->whereMonth('date', $now->month)
                    ->whereYear('date', $now->year);
                break;
            case 'last_month':
                $query->whereMonth('date', $now->subMonth()->month)
                    ->whereYear('date', $now->year);
                break;
        }

        $data = $query->orderBy('date')->get();

        // Format data untuk chart
        $dates = [];
        $consumption = [];

        foreach ($data as $record) {
            $dates[] = $record->date->format('Y-m-d');
            $consumption[] = $record->total_consumption;
        }

        // Hitung rata-rata (contoh sederhana)
        $average = array_fill(0, count($consumption), array_sum($consumption) / max(1, count($consumption)));

        // Set threshold (contoh: 20% di atas rata-rata)
        $thresholdValue = (array_sum($consumption) / max(1, count($consumption))) * 1.2;
        $threshold = array_fill(0, count($consumption), $thresholdValue);

        return [
            'dates' => $dates,
            'consumption' => $consumption,
            'average' => $average,
            'threshold' => $threshold
        ];
    }

    // API endpoint untuk permintaan AJAX
    public function getWaterUsageData(Request $request)
    {
        $period = $request->query('period', 'week');
        $data = $this->getWaterUsageChartData($period);

        return response()->json($data);
    }

    private function getDeviceStats()
    {
        return [
            'active' => Device::where('status', 'active')->count(),
            'inactive' => Device::where('status', 'inactive')->count(),
            'error' => Device::where('status', 'error')->count(),
        ];
    }



    private function getComplaintBarData($period)
    {
        $now = Carbon::now();
        $query = Complaint::query();

        switch ($period) {
            case 'today':
                $startDate = $now->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $groupFormat = 'H';
                $labelFormat = 'H:00';
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                $groupFormat = 'H';
                $labelFormat = 'H:00';
                break;
            case 'week':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $groupFormat = 'Y-m-d';
                $labelFormat = 'D, j';
                break;
            case 'month':
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $groupFormat = 'Y-m-d';
                $labelFormat = 'M j';
                break;
            case 'current_month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $groupFormat = 'Y-m-d';
                $labelFormat = 'j M';
                break;
            case 'last_month':
                $startDate = $now->copy()->subMonth()->startOfMonth();
                $endDate = $now->copy()->subMonth()->endOfMonth();
                $groupFormat = 'Y-m-d';
                $labelFormat = 'j M';
                break;
        }

        $data = $query
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as date_group, count(*) as count")
            ->groupBy('date_group')
            ->orderBy('date_group')
            ->get();

        $dates = [];
        $counts = [];
        $total = 0;

        // Format data untuk chart
        foreach ($data as $item) {
            if ($groupFormat === 'H') {
                // For hour grouping, just use the hour value
                $dates[] = str_pad($item->date_group, 2, '0', STR_PAD_LEFT) . ':00';
            } else {
                // Handle possible different formats
                try {
                    $date = Carbon::createFromFormat('Y-m-d', $item->date_group);
                    $dates[] = $date->format($labelFormat);
                } catch (\Exception $e) {
                    // Fallback: use the raw value if parsing fails
                    $dates[] = $item->date_group;
                }
            }
            $counts[] = $item->count;
            $total += $item->count;
        }

        return [
            'dates' => $dates,
            'counts' => $counts,
            'total' => $total,
            'period' => $period
        ];
    }

    // API endpoint untuk filter
    public function getComplaintBarDataApi(Request $request)
    {
        $period = $request->query('period', 'week');
        $data = $this->getComplaintBarData($period);

        return response()->json($data);
    }
}
