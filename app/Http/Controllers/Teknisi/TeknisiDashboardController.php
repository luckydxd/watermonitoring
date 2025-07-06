<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FlowPressureSensor;
use App\Models\Device;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class TeknisiDashboardController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $tanggalHariIni = now()->translatedFormat('l, d F Y');

        // --- Data untuk Widget ---
        $currentMonthTotal = $this->calculateTotalSystemConsumption(now()->startOfMonth(), now()->endOfMonth());
        $lastMonthTotal = $this->calculateTotalSystemConsumption(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth());
        $percentageChange = $lastMonthTotal != 0 ? round(($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal * 100, 2) : ($currentMonthTotal > 0 ? 100 : 0);
        $topUser = $this->getTopConsumingUser(now()->startOfMonth(), now()->endOfMonth());
        $currentMonthAvg = round($currentMonthTotal / max(1, now()->day), 2);
        $activeUsers = User::role('user')->where('is_active', 1)->count();
        $activeDevices = Device::where('status', 'active')->count();
        $totalComplaints = Complaint::where('status', 'pending')->count();
        $growth = 0; // Placeholder

        $initialConsumptionData = $this->getWaterUsageChartData('month');

        // 2. Siapkan data untuk Donut Chart (Status Perangkat)
        $deviceStats = $this->getDeviceStats() ?? ['labels' => [], 'series' => []];

        // 3. Siapkan data untuk Bar Chart (Keluhan)
        $initialChartData = $this->prepareComplaintChartData('month') ?? ['labels' => [], 'data' => []];

        // 4. Kirim semua data yang sudah disiapkan ke view
        return view('teknisi.dashboard', compact(
            'tanggalHariIni',
            'currentMonthTotal',
            'percentageChange',
            'topUser',
            'currentMonthAvg',
            'activeUsers',
            'activeDevices',
            'totalComplaints',
            'growth',
            'initialConsumptionData',
            'deviceStats',
            'initialChartData'
        ));
    }

    /**
     * Helper function untuk menghitung total konsumsi seluruh sistem pada rentang tanggal tertentu.
     */
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

    /**
     * Helper function untuk menemukan pengguna dengan konsumsi tertinggi pada rentang tanggal tertentu.
     */
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



    private function getWaterUsageChartData($period)
    {
        $now = Carbon::now();
        $endDate = $now->copy()->endOfDay(); // Akhir selalu hari ini (kecuali untuk bulan lalu)

        // --- PERUBAHAN UTAMA: TAMBAHKAN SEMUA CASE DARI DROPDOWN ---
        switch ($period) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                break;
            case 'week': // data-period="week"
                $startDate = $now->copy()->subDays(6)->startOfDay();
                break;
            case 'month': // data-period="month"
                $startDate = $now->copy()->subDays(29)->startOfDay();
                break;
            case 'current_month': // data-period="current_month"
                $startDate = $now->copy()->startOfMonth();
                break;
            case 'last_month': // data-period="last_month"
                $startDate = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $endDate = $now->copy()->subMonthNoOverflow()->endOfMonth();
                break;
            default: // Default jika tidak ada yang cocok
                $startDate = $now->copy()->subDays(29)->startOfDay();
                break;
        }

        // Query tidak berubah, ia akan bekerja dengan rentang tanggal apa pun
        $dailyReadings = FlowPressureSensor::whereBetween('measured_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(measured_at) as date'), 'device_id', DB::raw('MAX(volume) as max_vol'), DB::raw('MIN(volume) as min_vol'))
            ->groupBy('date', 'device_id')->get();

        $dailyTotals = [];
        foreach ($dailyReadings as $reading) {
            $date = $reading->date;
            $consumption = $reading->max_vol - $reading->min_vol;
            if (!isset($dailyTotals[$date])) $dailyTotals[$date] = 0;
            $dailyTotals[$date] += $consumption;
        }

        $chartData = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->toDateString();
            $chartData[$dateString] = $dailyTotals[$dateString] ?? 0;
            $currentDate->addDay();
        }

        return [
            'dates' => array_keys($chartData),
            'consumption' => array_values($chartData),
        ];
    }


    public function getWaterUsageData(Request $request)
    {
        // Ambil periode dari request, default ke 'month' agar konsisten
        $period = $request->query('period', 'month');

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



    public function getComplaintChartData(Request $request)
    {
        $period = $request->query('period', 'month'); // Default ke 'month' jika tidak ada periode
        $data = $this->prepareComplaintChartData($period);
        return response()->json($data);
    }

    // API endpoint untuk filter

    protected function prepareComplaintChartData(string $period): array
    {
        $startDate = null;
        $endDate = Carbon::now();
        $dateFormatForLabels = 'D, d M';
        $groupByExpression = 'DATE(created_at)';

        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $dateFormatForLabels = 'H:00';
                $groupByExpression = 'DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00")';
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday()->endOfDay();
                $dateFormatForLabels = 'H:00';
                $groupByExpression = 'DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00")';
                break;
            case 'week':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                break;
            case 'month':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                break;
            case 'current_month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            default:
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                break;
        }

        $complaints = Complaint::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw($groupByExpression . ' as time_unit_raw'),
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('time_unit_raw', 'status')
            ->orderBy('time_unit_raw', 'asc')
            ->get();

        $dates = [];
        $dataByStatus = [
            'pending' => [],
            'processed' => [],
            'resolved' => [],
            'rejected' => [],
        ];
        $totalComplaints = 0;

        $currentUnit = $startDate->copy();
        while ($currentUnit->lte($endDate)) {
            Carbon::setLocale('id');
            $formattedLabel = $currentUnit->translatedFormat($dateFormatForLabels);

            $dates[] = $formattedLabel;

            foreach (array_keys($dataByStatus) as $status) {
                $dataByStatus[$status][] = 0;
            }

            if ($period === 'today' || $period === 'yesterday') {
                $currentUnit->addHour();
            } else {
                $currentUnit->addDay();
            }
        }

        foreach ($complaints as $complaint) {
            $unitTime = Carbon::parse($complaint->time_unit_raw);
            Carbon::setLocale('id');
            $formattedUnit = $unitTime->translatedFormat($dateFormatForLabels);

            $dateIndex = array_search($formattedUnit, $dates);
            if ($dateIndex !== false) {
                $dataByStatus[$complaint->status][$dateIndex] += $complaint->count;
                $totalComplaints += $complaint->count;
            }
        }

        $series = [];
        $statusOrder = ['resolved', 'processed', 'pending', 'rejected'];

        foreach ($statusOrder as $status) {
            $series[] = [
                'name' => ucfirst($status),
                'data' => $dataByStatus[$status],
            ];
        }

        return [
            'dates' => $dates,
            'series' => $series,
            'total' => $totalComplaints,
            'period' => $period,
        ];
    }
}
