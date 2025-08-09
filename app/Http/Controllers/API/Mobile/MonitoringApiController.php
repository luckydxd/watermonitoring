<?php

namespace App\Http\Controllers\API\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\DeviceType;
use App\Models\FlowPressureSensor;
use App\Models\WaterConsumptionLog;
use App\Models\WaterQualitySensor;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class MonitoringApiController extends Controller
{

    // private function getActiveDeviceId(Request $request)
    // {
    //     $assignment = DeviceAssignment::where('user_id', $request->user()->id)
    //         ->where('is_active', true)
    //         ->first();

    //     return $assignment ? $assignment->device_id : null;
    // }

    public function getActiveDevicesInfo(Request $request)
    {
        //  Ambil semua perangkat yang aktif untuk user yang sedang login
        $devices = Device::join('device_assignments', 'devices.id', '=', 'device_assignments.device_id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->where('device_assignments.user_id', $request->user()->id)
            ->where('device_assignments.is_active', true)
            ->select('devices.unique_id', 'device_types.code', 'devices.status', 'devices.last_seen_at')
            ->get();

        if ($devices->isEmpty()) {
            return response()->json(['data' => []]);
        }

        // Proses data untuk menentukan status online/offline
        $deviceInfo = $devices->map(function ($device) {
            $isOnline = false;

            // Perangkat dianggap online jika statusnya 'active' &
            // terakhir terlihat dalam 15 menit terakhir.
            if ($device->last_seen_at && strtolower($device->status) === 'active') {
                $diffMinutes = Carbon::parse($device->last_seen_at)->diffInMinutes(now());
                if ($diffMinutes <= 15) {
                    $isOnline = true;
                }
            }

            return [
                'unique_id' => $device->unique_id,
                'code' => $device->code,
                'status' => $isOnline ? 'online' : 'offline',
            ];
        });

        return response()->json(['data' => $deviceInfo]);
    }

    public function getConsumptionSummary(Request $request)
    {
        $request->validate(['range' => 'sometimes|in:today,yesterday,last7,last30,thisMonth,lastMonth,weekly,monthly']);
        $user = $request->user();
        $range = $request->query('range', $request->query('period', 'last7'));

        // Dapatkan data assignment lengkap
        $activeAssignment = $user->deviceAssignments()
            ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->where('device_assignments.is_active', true)
            ->where('device_types.name', 'Flow and Pressure Unit')
            ->select('device_assignments.device_id', 'device_assignments.initial_meter_reading')
            ->first();

        if (!$activeAssignment) {
            return response()->json(['data' => []]);
        }
        $deviceId = $activeAssignment->device_id;
        $initialMeterReading = (float) $activeAssignment->initial_meter_reading;

        // rentang tanggal
        $now = Carbon::now();
        switch ($range) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                break;
            case 'last30':
            case 'monthly':
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'thisMonth':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'lastMonth':
                $startDate = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $endDate = $now->copy()->subMonthNoOverflow()->endOfMonth();
                break;
            case 'last7':
            case 'weekly':
            default:
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
        }

        // Dapatkan titik awal: pembacaan terakhir sebelum periode dimulai.
        $startVolume = FlowPressureSensor::where('device_id', $deviceId)
            ->where('measured_at', '<', $startDate)
            ->orderBy('measured_at', 'desc')
            ->value('volume');

        // Jika tidak ada data sama sekali sebelum periode ini, gunakan initial_meter_reading
        $previousDayVolume = !is_null($startVolume) ? (float)$startVolume : $initialMeterReading;

        // Dapatkan pembacaan terakhir untuk setiap hari di dalam periode.
        $endOfDayReadings = FlowPressureSensor::where('device_id', $deviceId)
            ->whereBetween('measured_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(measured_at) as date'),
                DB::raw('MAX(volume) as end_of_day_volume')
            )
            ->groupBy('date')->orderBy('date')->get()
            ->keyBy('date'); // Mengubah koleksi menjadi array asosiatif

        // Iterasi melalui setiap hari dalam rentang dan hitung konsumsi
        $dailyConsumptions = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate) && $currentDate->lte(Carbon::today())) { // Hanya proses sampai hari ini
            $dateString = $currentDate->toDateString();
            $readingForToday = $endOfDayReadings->get($dateString);

            // Jika ada data untuk hari ini, gunakan itu. Jika tidak, anggap sama dengan hari sebelumnya (tidak ada pemakaian).
            $currentDayVolume = $readingForToday ? (float)$readingForToday->end_of_day_volume : $previousDayVolume;

            $consumption = $currentDayVolume - $previousDayVolume;

            $dailyConsumptions[] = [
                'date' => $dateString,
                'total' => round(max(0, $consumption), 2)
            ];

            // Perbarui volume hari sebelumnya untuk iterasi berikutnya
            $previousDayVolume = $currentDayVolume;
            $currentDate->addDay();
        }

        return response()->json(['data' => $dailyConsumptions]);
    }

    /**
     * Endpoint: GET /latest-readings
     */
    protected function getActiveDeviceId(Request $request)
    {
        // Mendapatkan user yang sedang login
        $user = $request->user();

        if (!$user) {
            return [];
        }

        $activeDeviceIds = DeviceAssignment::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('device_id')
            ->toArray();

        Log::info("getActiveDeviceId: Found active device IDs for user {$user->id}: " . json_encode($activeDeviceIds));

        return $activeDeviceIds;
    }

    public function getLatestReadings(Request $request)
    {
        $activeDeviceIds = $this->getActiveDeviceId($request);
        if (empty($activeDeviceIds)) {
            return response()->json(['message' => 'Tidak ada perangkat aktif yang ditemukan untuk pengguna ini.'], 404);
        }

        $latestFlowPressureData = null;
        $latestWaterQualityData = null;
        $latestMeasuredAt = null;

        // Ambil data Flow & Pressure terbaru dari SEMUA perangkat aktif yang dimiliki user
        // mencari record terbaru.
        $latestFlowPressure = FlowPressureSensor::whereIn('device_id', $activeDeviceIds)
            ->orderByDesc('measured_at')
            ->first(); // Ambil record FlowPressure terbaru secara keseluruhan

        if ($latestFlowPressure) {
            $latestFlowPressureData = $latestFlowPressure;
            $latestMeasuredAt = $latestFlowPressure->measured_at;
        }

        $latestWaterQuality = WaterQualitySensor::whereIn('device_id', $activeDeviceIds)
            ->orderByDesc('measured_at')
            ->first();

        if ($latestWaterQuality) {
            $latestWaterQualityData = $latestWaterQuality;
            if (is_null($latestMeasuredAt) || $latestWaterQuality->measured_at > $latestMeasuredAt) {
                $latestMeasuredAt = $latestWaterQuality->measured_at;
            }
        }

        return response()->json([
            'data' => [
                'flow_rate' => optional($latestFlowPressureData)->flow_rate ?? 0,
                'pressure' => optional($latestFlowPressureData)->pressure ?? 0,
                'turbidity' => optional($latestWaterQualityData)->turbidity ?? 0,
                'water_level' => optional($latestWaterQualityData)->water_level ?? 0,
                'last_measured_at' => optional($latestMeasuredAt)->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Endpoint: GET /monitoring/history/{metric}
     * {metric} : 'pressure', 'turbidity', 'water_level', atau 'flow_rate'
     */
    public function getSensorHistory(Request $request, $metric)
    {
        Log::info("API Call: getSensorHistory - Metric: " . $metric);

        $validMetrics = ['pressure', 'turbidity', 'water_level', 'flow_rate'];
        if (!in_array($metric, $validMetrics)) {
            Log::warning("API Call: Invalid metric provided - " . $metric);
            return response()->json(['message' => 'Metrik tidak valid.'], 400);
        }

        $activeDeviceIds = $this->getActiveDeviceId($request);
        Log::info("API Call: getSensorHistory - Device ID found: " . ($deviceId ?? 'NULL'));
        if (!$activeDeviceIds) {
            return response()->json(['message' => 'Tidak ada perangkat aktif yang ditemukan.'], 404);
        }

        $queryTime = Carbon::now();
        $startFilterTime = $queryTime->copy()->subHours(24);

        Log::info("API Call: getSensorHistory - Current server time: " . $queryTime->toDateTimeString());
        Log::info("API Call: getSensorHistory - Filter time range: From " . $startFilterTime->toDateTimeString() . " to " . $queryTime->toDateTimeString());

        $modelInstance = (in_array($metric, ['pressure', 'flow_rate'])) ? new FlowPressureSensor() : new WaterQualitySensor();
        Log::info("API Call: getSensorHistory - Using model: " . get_class($modelInstance));

        $historyDataQuery = $modelInstance->whereIn('device_id', $activeDeviceIds)
            ->where('measured_at', '>=', $startFilterTime)
            ->orderBy('measured_at', 'asc')
            ->select('measured_at', DB::raw("$metric as value"));

        Log::info("API Call: getSensorHistory - SQL Query: " . $historyDataQuery->toSql());
        Log::info("API Call: getSensorHistory - Query Bindings: " . json_encode($historyDataQuery->getBindings()));

        $historyData = $historyDataQuery->get();

        // Log hasil query
        Log::info("API Call: getSensorHistory - Data fetched count: " . $historyData->count());
        Log::info("API Call: getSensorHistory - Fetched data (first 5 records): " . json_encode($historyData->take(5)->toArray())); // Log 5 data pertama untuk menghindari log yang terlalu besar

        if ($historyData->isEmpty()) {
            Log::info("API Call: getSensorHistory - No data found for device ID {$activeDeviceIds} and metric {$metric} within the last 24 hours.");
        }

        return response()->json(['data' => $historyData]);
    }

    public function getSensorHistoryDashboard(Request $request, $metric)
    {
        // Validasi Metrik
        $validMetrics = ['pressure', 'turbidity', 'water_level', 'flow_rate'];
        if (!in_array($metric, $validMetrics)) {
            return response()->json(['message' => 'Metrik tidak valid.'], 400);
        }

        // Dapatkan Perangkat Aktif
        $activeDeviceIds = $this->getActiveDeviceId($request);
        if (!$activeDeviceIds || empty($activeDeviceIds)) {
            return response()->json(['data' => [], 'message' => 'Tidak ada perangkat aktif yang ditemukan.']);
        }

        // Filter Tanggal 
        $range = $request->query('range', 'last7'); // Default ke 7 hari
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                break;
            case 'last30':
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'thisMonth':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'lastMonth':
                $startDate = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $endDate = $now->copy()->subMonthNoOverflow()->endOfMonth();
                break;
            case 'last7':
            default:
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
        }

        $model = (in_array($metric, ['pressure', 'flow_rate'])) ? new FlowPressureSensor() : new WaterQualitySensor();
        $query = $model->whereIn('device_id', $activeDeviceIds)
            ->whereBetween('measured_at', [$startDate, $endDate]);

        // Hitung selisih hari
        $diffDays = $startDate->diffInDays($endDate);

        // Jika rentang lebih dari 2 hari, lakukan agregasi data per jam
        if ($diffDays > 2) {
            $historyData = $query->select(
                // Kelompokkan data per jam
                DB::raw("DATE_FORMAT(measured_at, '%Y-%m-%d %H:00:00') as date"),
                // Ambil nilai rata-ratanya
                DB::raw("AVG($metric) as value")
            )
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
        } else {
            // Jika rentang pendek (<= 2 hari), ambil data per 10 menit
            $historyData = $query->orderBy('measured_at', 'asc')
                ->select('measured_at as date', DB::raw("$metric as value"))
                ->get();
        }

        return response()->json(['data' => $historyData]);
    }

    /**
     * Endpoint: GET /monitoring/export-monthly?year=2025&month=06
     */
    public function exportMonthlyReport(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020',
            'month' => 'required|integer|between:1,12',
        ]);

        $year = $request->query('year');
        $month = $request->query('month');
        $user = $request->user();
        $deviceId = $this->getActiveDeviceId($request);

        if (!$deviceId) {
            return response()->json(['message' => 'Tidak ada perangkat aktif yang ditemukan.'], 404);
        }

        // --- MENGAMBIL DATA RINGKASAN HARIAN ---
        $selectStatement = DB::raw('
        DATE(measured_at) as date, 
        COUNT(*) as record_count,
        AVG(flow_rate) as avg_flow_rate,
        MIN(flow_rate) as min_flow_rate,
        MAX(flow_rate) as max_flow_rate,
        AVG(pressure) as avg_pressure
    ');
        $flowSummary = FlowPressureSensor::where('device_id', $deviceId)
            ->whereYear('measured_at', $year)->whereMonth('measured_at', $month)
            ->selectRaw($selectStatement)
            ->groupBy('date')->orderBy('date')->get();

        $selectStatement = DB::raw('
        DATE(measured_at) as date, 
        COUNT(*) as record_count,
        AVG(turbidity) as avg_turbidity,
        AVG(water_level) as avg_water_level
    ');
        $qualitySummary = WaterQualitySensor::where('device_id', $deviceId)
            ->whereYear('measured_at', $year)->whereMonth('measured_at', $month)
            ->selectRaw($selectStatement)
            ->groupBy('date')->orderBy('date')->get();


        if ($flowSummary->isEmpty() && $qualitySummary->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data untuk diekspor pada periode yang dipilih.'], 404);
        }

        $data = [
            'user' => $user,
            'flowSummary' => $flowSummary,
            'qualitySummary' => $qualitySummary,
            'year' => $year,
            'monthName' => Carbon::create()->month($month)->translatedFormat('F')
        ];

        $fileName = "ringkasan_{$user->username}_{$year}-{$month}.pdf";
        $pdf = PDF::loadView('pdf.monthly_report', $data); // Gunakan view baru

        return $pdf->download($fileName);
    }

    // ============== CSV EXPORT ==============

    //     public function exportMonthlyReport(Request $request)
    // {
    //     $request->validate([
    //         'year' => 'required|integer|min:2020',
    //         'month' => 'required|integer|between:1,12',
    //     ]);

    //     $year = $request->query('year');
    //     $month = $request->query('month');
    //     $user = $request->user();
    //     $deviceId = $this->getActiveDeviceId($request);

    //     // Header untuk file CSV
    //     $fileName = "laporan_{$user->id}_{$year}-{$month}.csv";
    //     $headers = [
    //         "Content-type"        => "text/csv",
    //         "Content-Disposition" => "attachment; filename=$fileName",
    //         "Pragma"              => "no-cache",
    //         "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
    //         "Expires"             => "0"
    //     ];

    //     // Data yang akan diexport
    //     $flowData = FlowPressureSensor::where('device_id', $deviceId)->whereYear('measured_at', $year)->whereMonth('measured_at', $month)->get();
    //     $qualityData = WaterQualitySensor::where('device_id', $deviceId)->whereYear('measured_at', $year)->whereMonth('measured_at', $month)->get();
    //     // Anda juga bisa menambahkan consumption log, dll.

    //     $callback = function () use ($flowData, $qualityData) {
    //         $file = fopen('php://output', 'w');

    //         // Header kolom
    //         fputcsv($file, ['Waktu Pengukuran', 'Tipe Data', 'Nilai 1', 'Nilai 2']);

    //         foreach ($flowData as $row) {
    //             fputcsv($file, [$row->measured_at, 'Flow & Pressure', $row->flow_rate, $row->pressure]);
    //         }
    //         foreach ($qualityData as $row) {
    //             fputcsv($file, [$row->measured_at, 'Quality & Level', $row->turbidity, $row->water_level]);
    //         }

    //         fclose($file);
    //     };

    //     return response()->stream($callback, 200, $headers);
    // }

    public function getCostEstimation(Request $request)
    {
        $user = $request->user();

        // Dapatkan harga air dari pengaturan
        $appSetting = AppSetting::first();
        if (!$appSetting || !is_numeric($appSetting->price_per_liter) || $appSetting->price_per_liter <= 0) {
            return response()->json(['success' => false, 'message' => 'Harga air belum diatur.'], 500);
        }
        $hargaPerLiter = (float) $appSetting->price_per_liter;

        // Dapatkan data penugasan aktif 
        $activeAssignment = $user->deviceAssignments()
            ->where('is_active', true)
            ->whereNotNull('initial_meter_reading')
            ->first();

        if (!$activeAssignment) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data meteran awal yang aktif.'], 404);
        }
        $deviceId = $activeAssignment->device_id;

        // Tentukan rentang waktu untuk bulan ini dan bulan lalu
        $startOfThisMonth = Carbon::now()->startOfMonth();
        $endOfThisMonth = Carbon::now()->endOfMonth();
        $startOfLastMonth = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        // Dapatkan bacaan meteran terkini
        $latestLogThisMonth = FlowPressureSensor::where('device_id', $deviceId)
            ->whereBetween('measured_at', [$startOfThisMonth, $endOfThisMonth])
            ->latest('measured_at')
            ->first();

        $meteranBulanIni = $latestLogThisMonth ? (float)$latestLogThisMonth->volume : null;

        // Dapatkan bacaan meteran awal terakhir
        $latestLogLastMonth = FlowPressureSensor::where('device_id', $deviceId)
            ->whereBetween('measured_at', [$startOfLastMonth, $endOfLastMonth])
            ->latest('measured_at')
            ->first();

        // Jika tidak ada data bulan lalu, gunakan initial_meter_reading sebagai titik awal
        $meteranBulanLalu = $latestLogLastMonth
            ? (float)$latestLogLastMonth->volume
            : (float)$activeAssignment->initial_meter_reading;

        // Jika tidak ada data sama sekali di bulan ini, anggap meteran terkini = meteran awal bulan
        if (is_null($meteranBulanIni)) {
            $meteranBulanIni = $meteranBulanLalu;
        }

        // perhitungan
        $totalPemakaianBulanIni = 0;
        if ($meteranBulanIni >= $meteranBulanLalu) {
            $totalPemakaianBulanIni = $meteranBulanIni - $meteranBulanLalu;
        }

        $estimasiBiayaBulanIni = $totalPemakaianBulanIni * $hargaPerLiter;

        return response()->json([
            'success' => true,
            'data' => [
                'meteran_awal_bulan_liter' => round($meteranBulanLalu, 2),
                'meteran_terkini_liter' => round($meteranBulanIni, 2),
                'pemakaian_bulan_ini_liter' => round($totalPemakaianBulanIni, 2),
                'harga_per_liter_rp' => $hargaPerLiter,
                'estimasi_biaya_bulan_ini_rp' => round($estimasiBiayaBulanIni, 0),
            ]
        ]);
    }
}
