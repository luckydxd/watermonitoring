<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WaterConsumptionLog;
use App\Models\Complaint;
use App\Models\Device;
use App\Models\VisitorActivity;
use App\Models\FlowPressureSensor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;


class DashboardController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        // Format tanggal
        $tanggalHariIni = now()->translatedFormat('l, d F Y');
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();

        // $todayUsage = WaterConsumptionLog::whereDate('created_at', Carbon::today())
        //     ->sum('total_consumption');

        $totalComplaints = Complaint::where('status', 'pending')->count(); // Statistik keluhan

        // Total Device Active
        $activeDevices = Device::where('status', 'active')->count();

        //widget untuk total penggunaan air bulan ini
        $currentMonthTotal = $this->calculateTotalSystemConsumption(now()->startOfMonth(), now()->endOfMonth());
        $lastMonthTotal = $this->calculateTotalSystemConsumption(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth());
        $percentageChange = $lastMonthTotal != 0 ? round(($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal * 100, 2) : ($currentMonthTotal > 0 ? 100 : 0);

        //Top user penggunaan air bulan ini
        $topUser = $this->getTopConsumingUser(now()->startOfMonth(), now()->endOfMonth());

        // Rata-rata penggunaan air bulan ini
        $currentMonthAvg = round($currentMonthTotal / max(1, now()->day), 2);


        // Visitor Line Chart //
        $activities = VisitorActivity::orderBy('date', 'asc')
            ->limit(14)
            ->get();


        // $chartData = [
        //     'dates' => $activities->pluck('date')->map(function ($date) {
        //         return Carbon::parse($date)->format('j/n');
        //     }),
        //     'visitors' => $activities->pluck('visitors'),
        //     'contact_clicks' => $activities->pluck('contact_clicks'),
        //     'login_clicks' => $activities->pluck('login_clicks'),
        //     'download_clicks' => $activities->pluck('download_clicks')
        // ];

        $chartData = $this->getWaterUsageChartData('last30');

        $complaintRaw = Complaint::selectRaw('LOWER(status) as status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => (int)$item->total]);

        $deviceRaw = Device::selectRaw('LOWER(status) as status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => (int)$item->total]);

        $defaultComplaintStatuses = ['pending', 'processed', 'resolved', 'rejected'];
        $defaultDeviceStatuses = ['active', 'inactive', 'error'];

        // Gabungkan data yang ada dengan status default, isi dengan 0 jika tidak ada
        $complaintStatusCounts = collect($defaultComplaintStatuses)
            ->mapWithKeys(fn($status) => [$status => (int)($complaintRaw[$status] ?? 0)]);

        $deviceStatusCounts = collect($defaultDeviceStatuses)
            ->mapWithKeys(fn($status) => [$status => (int)($deviceRaw[$status] ?? 0)]);

        //activity log
        $latestActivities = Activity::with('causer')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'activeDevices',
            'totalComplaints',
            'tanggalHariIni',
            'chartData',
            'complaintStatusCounts',
            'deviceStatusCounts',
            'topUser',
            'currentMonthTotal',
            'percentageChange',
            'currentMonthAvg',
            'chartData',
            'latestActivities'
        ));
    }

    private function getTopConsumingUser(Carbon $startDate, Carbon $endDate)
    {
        $topUserQuery = FlowPressureSensor::select(
            'users.id',
            'user_datas.name',
            // Menghitung selisih MAX-MIN untuk setiap grup user/device
            DB::raw('MAX(flow_pressure_sensors.volume) - MIN(flow_pressure_sensors.volume) as total_consumption')
        )
            ->join('devices', 'flow_pressure_sensors.device_id', '=', 'devices.id')
            ->join('device_assignments', 'devices.id', '=', 'device_assignments.device_id')
            ->join('users', 'device_assignments.user_id', '=', 'users.id')
            ->leftJoin('user_datas', 'users.id', '=', 'user_datas.user_id')
            ->where('device_assignments.is_active', true)
            ->whereBetween('flow_pressure_sensors.measured_at', [$startDate, $endDate])
            ->groupBy('users.id', 'user_datas.name')
            ->orderByDesc('total_consumption')
            ->first();

        if (!$topUserQuery) {
            return (object) ['name' => 'No Data', 'total_consumption' => 0];
        }

        return $topUserQuery;
    }

    private function calculateTotalSystemConsumption(Carbon $startDate, Carbon $endDate): float
    {
        // Query ini mengambil selisih MAX-MIN untuk setiap device, lalu menjumlahkan semua selisih tersebut.
        $total = FlowPressureSensor::whereBetween('measured_at', [$startDate, $endDate])
            ->select(DB::raw('MAX(volume) - MIN(volume) as consumption'))
            ->groupBy('device_id')
            ->get()
            ->sum('consumption');

        return (float) $total;
    }

    private function getWaterUsageChartData($period)
    {
        // Logika penentuan rentang tanggal (tidak diubah)
        $now = Carbon::now();
        $endDate = $now->copy()->endOfDay();
        switch ($period) {
            case 'last7':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                break;
            case 'lastMonth':
                $startDate = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $endDate = $now->copy()->subMonthNoOverflow()->endOfMonth();
                break;
            case 'thisMonth':
                $startDate = $now->copy()->startOfMonth();
                break;
            case 'last30':
            default:
                $startDate = $now->copy()->subDays(29)->startOfDay();
                break;
        }

        // --- QUERY UTAMA YANG DIOPTIMALKAN ---
        // Mengambil semua data agregat dalam satu query untuk efisiensi
        $aggregatedData = FlowPressureSensor::whereBetween('measured_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(measured_at) as date'),
                // Menghitung Rata-rata Flow Rate & Pressure
                DB::raw('AVG(flow_rate) as avg_flow_rate'),
                DB::raw('AVG(pressure) as avg_pressure')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date'); // Menggunakan 'date' sebagai key agar mudah diakses

        // --- PERHITUNGAN KONSUMSI (logika Anda yang sudah ada, tetap efisien) ---
        $dailyReadings = FlowPressureSensor::whereBetween('measured_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(measured_at) as date'), 'device_id', DB::raw('MAX(volume) as max_vol'), DB::raw('MIN(volume) as min_vol'))
            ->groupBy('date', 'device_id')->get();

        $dailyConsumptionTotals = [];
        $dailyDeviceCount = [];
        foreach ($dailyReadings as $reading) {
            $date = $reading->date;
            $consumption = $reading->max_vol - $reading->min_vol;
            if (!isset($dailyConsumptionTotals[$date])) {
                $dailyConsumptionTotals[$date] = 0;
                $dailyDeviceCount[$date] = [];
            }
            $dailyConsumptionTotals[$date] += $consumption;
            // Tandai device ini aktif pada hari itu
            $dailyDeviceCount[$date][$reading->device_id] = true;
        }

        // --- MEMBANGUN DATA FINAL UNTUK DIKIRIM KE CHART ---
        $dates = [];
        $series = [
            'total_consumption'   => [],
            'average_consumption' => [],
            'average_pressure'    => [],
            'average_flow_rate'   => [],
        ];

        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->toDateString();
            $dates[] = $dateString;

            $totalConsumption = $dailyConsumptionTotals[$dateString] ?? 0;
            $activeDevicesToday = isset($dailyDeviceCount[$dateString]) ? count($dailyDeviceCount[$dateString]) : 0;
            $avgConsumption = ($activeDevicesToday > 0) ? ($totalConsumption / $activeDevicesToday) : 0;

            $series['total_consumption'][] = round($totalConsumption, 2);
            $series['average_consumption'][] = round($avgConsumption, 2);
            $series['average_pressure'][] = round($aggregatedData[$dateString]->avg_pressure ?? 0, 2);
            $series['average_flow_rate'][] = round($aggregatedData[$dateString]->avg_flow_rate ?? 0, 2);

            $currentDate->addDay();
        }

        // Mengembalikan data dalam format baru
        return [
            'dates'  => $dates,
            'series' => $series,
        ];
    }


    public function getWaterUsageData(Request $request)
    {
        // Ambil periode dari request, default ke 'month' agar konsisten
        $period = $request->query('period', 'month');

        $data = $this->getWaterUsageChartData($period);

        return response()->json($data);
    }

    public function getChartData(Request $request)
    {
        $range = $request->get('range');
        $query = VisitorActivity::query();

        switch ($range) {
            case 'today':
                $query->whereDate('date', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('date', Carbon::yesterday());
                break;
            case 'last7':
                $query->whereBetween('date', [Carbon::now()->subDays(6), Carbon::now()]);
                break;
            case 'last30':
                $query->whereBetween('date', [Carbon::now()->subDays(29), Carbon::now()]);
                break;
            case 'thisMonth':
                $query->whereMonth('date', Carbon::now()->month);
                break;
            case 'lastMonth':
                $query->whereMonth('date', Carbon::now()->subMonth()->month);
                break;
            default:
                $query->limit(14);
        }

        $activities = $query->orderBy('date', 'asc')->get();

        return response()->json([
            'dates' => $activities->pluck('date')->map(fn($d) => Carbon::parse($d)->format('j/n')),
            'series' => [
                ['name' => 'Pengunjung', 'data' => $activities->pluck('visitors')],
                ['name' => 'Klik Kontak', 'data' => $activities->pluck('contact_clicks')],
                ['name' => 'Klik Login', 'data' => $activities->pluck('login_clicks')],
                ['name' => 'Klik Download', 'data' => $activities->pluck('download_clicks')],
            ],
        ]);
    }
}
