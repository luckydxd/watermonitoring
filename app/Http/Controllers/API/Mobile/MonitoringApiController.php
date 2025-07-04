<?php

namespace App\Http\Controllers\API\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\DeviceType;
use App\Models\FlowPressureSensor;
use App\Models\WaterConsumptionLog;
use App\Models\WaterQualitySensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class MonitoringApiController extends Controller
{
    /**
     * Helper function untuk mendapatkan device_id yang aktif untuk user.
     */
    // private function getActiveDeviceId(Request $request)
    // {
    //     $assignment = DeviceAssignment::where('user_id', $request->user()->id)
    //         ->where('is_active', true)
    //         ->first();

    //     return $assignment ? $assignment->device_id : null;
    // }

    public function getActiveDevicesInfo(Request $request)
    {
        // 1. Ambil semua perangkat yang aktif untuk user yang sedang login
        $devices = Device::join('device_assignments', 'devices.id', '=', 'device_assignments.device_id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->where('device_assignments.user_id', $request->user()->id)
            ->where('device_assignments.is_active', true)
            ->select('devices.unique_id', 'device_types.code', 'devices.status', 'devices.last_seen_at')
            ->get();

        if ($devices->isEmpty()) {
            return response()->json(['data' => []]);
        }

        // 2. Proses data untuk menentukan status online/offline
        $deviceInfo = $devices->map(function ($device) {
            $isOnline = false;

            // Perangkat dianggap online jika statusnya 'active' DAN
            // terakhir terlihat dalam 15 menit terakhir.
            if ($device->last_seen_at && strtolower($device->status) === 'active') {
                $diffMinutes = Carbon::parse($device->last_seen_at)->diffInMinutes(now());
                if ($diffMinutes <= 60) {
                    $isOnline = true;
                }
            }

            return [
                'unique_id' => $device->unique_id,
                'code' => $device->code,
                'status' => $isOnline ? 'online' : 'offline',
            ];
        });

        // 3. Kembalikan respons dalam format JSON
        return response()->json(['data' => $deviceInfo]);
    }


    /**
     * No. 4: Get water_consumption_logs per hari.
     * Digunakan untuk chart mingguan dan bulanan di Flutter.
     * Endpoint: GET /api/mobile/monitoring/consumption-summary?period=weekly|monthly
     */
    public function getConsumptionSummary(Request $request)
    {
        $request->validate(['range' => 'sometimes|in:today,yesterday,last7,last30,thisMonth,lastMonth,weekly,monthly']);

        $user = $request->user();
        // Gunakan 'range' dari web, atau 'period' dari mobile, dengan default ke 'last7'
        $range = $request->query('range', $request->query('period', 'last7'));

        // Logika baru untuk menentukan rentang tanggal
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
            case 'last7':
            case 'weekly':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'last30':
            case 'monthly':
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
        }

        $consumptionData = WaterConsumptionLog::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_consumption) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json(['data' => $consumptionData]);
    }

    /**
     * No. 5, 6, 8, 10: Get data terakhir untuk widget.
     * Digabungkan menjadi satu endpoint untuk efisiensi.
     * Endpoint: GET /api/mobile/monitoring/latest-readings
     */
    protected function getActiveDeviceId(Request $request)
    {
        // Mendapatkan user yang sedang login
        $user = $request->user();

        if (!$user) {
            return []; // Mengembalikan array kosong jika user tidak login
        }

        // Mengambil semua device_id yang diasosiasikan dengan user ini
        // dan yang berstatus aktif.
        $activeDeviceIds = DeviceAssignment::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('device_id')
            ->toArray();

        Log::info("getActiveDeviceId: Found active device IDs for user {$user->id}: " . json_encode($activeDeviceIds));

        return $activeDeviceIds;
    }


    public function getLatestReadings(Request $request)
    {
        $activeDeviceIds = $this->getActiveDeviceId($request); // Sekarang mengembalikan array ID
        if (empty($activeDeviceIds)) {
            return response()->json(['message' => 'Tidak ada perangkat aktif yang ditemukan untuk pengguna ini.'], 404);
        }

        $latestFlowPressureData = null;
        $latestWaterQualityData = null;
        $latestMeasuredAt = null;

        // Ambil data Flow & Pressure terbaru dari SEMUA perangkat aktif yang dimiliki user
        // Kita perlu meloop atau menggunakan max(measured_at) per device_id
        // Cara terbaik adalah mencari record terbaru dari semua device ID yang aktif.
        $latestFlowPressure = FlowPressureSensor::whereIn('device_id', $activeDeviceIds)
            ->orderByDesc('measured_at')
            ->first(); // Ambil record FlowPressure terbaru secara keseluruhan

        if ($latestFlowPressure) {
            $latestFlowPressureData = $latestFlowPressure;
            $latestMeasuredAt = $latestFlowPressure->measured_at;
        }

        // Ambil data Water Quality terbaru dari SEMUA perangkat aktif yang dimiliki user
        $latestWaterQuality = WaterQualitySensor::whereIn('device_id', $activeDeviceIds)
            ->orderByDesc('measured_at')
            ->first(); // Ambil record WaterQuality terbaru secara keseluruhan

        if ($latestWaterQuality) {
            $latestWaterQualityData = $latestWaterQuality;
            // Pilih timestamp yang paling baru dari kedua jenis sensor
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
                'last_measured_at' => optional($latestMeasuredAt)->toDateTimeString(), // Pastikan diformat ke string
            ]
        ]);
    }

    /**
     * No. 7, 9, 11: Get semua data sensor untuk chart per jam (24 jam terakhir).
     * Dibuat dinamis berdasarkan metrik yang diminta.
     * Endpoint: GET /api/mobile/monitoring/history/{metric}
     * {metric} bisa berupa: 'pressure', 'turbidity', 'water_level', atau 'flow_rate'
     */
    public function getSensorHistory(Request $request, $metric)
    {
        Log::info("API Call: getSensorHistory - Metric: " . $metric); // Log awal panggilan API

        $validMetrics = ['pressure', 'turbidity', 'water_level', 'flow_rate'];
        if (!in_array($metric, $validMetrics)) {
            Log::warning("API Call: Invalid metric provided - " . $metric);
            return response()->json(['message' => 'Metrik tidak valid.'], 400);
        }

        $deviceId = $this->getActiveDeviceId($request); // Asumsi fungsi ini bekerja dengan baik
        Log::info("API Call: getSensorHistory - Device ID found: " . ($deviceId ?? 'NULL')); // Log Device ID
        if (!$deviceId) {
            return response()->json(['message' => 'Tidak ada perangkat aktif yang ditemukan.'], 404);
        }

        $queryTime = Carbon::now();
        $startFilterTime = $queryTime->copy()->subHours(24);

        Log::info("API Call: getSensorHistory - Current server time: " . $queryTime->toDateTimeString());
        Log::info("API Call: getSensorHistory - Filter time range: From " . $startFilterTime->toDateTimeString() . " to " . $queryTime->toDateTimeString());

        $modelInstance = (in_array($metric, ['pressure', 'flow_rate'])) ? new FlowPressureSensor() : new WaterQualitySensor();
        Log::info("API Call: getSensorHistory - Using model: " . get_class($modelInstance)); // Log model yang digunakan

        $historyDataQuery = $modelInstance->where('device_id', $deviceId)
            ->where('measured_at', '>=', $startFilterTime)
            ->orderBy('measured_at', 'asc')
            ->select('measured_at', DB::raw("$metric as value"));

        // Log SQL Query dan Bindings sebelum eksekusi
        Log::info("API Call: getSensorHistory - SQL Query: " . $historyDataQuery->toSql());
        Log::info("API Call: getSensorHistory - Query Bindings: " . json_encode($historyDataQuery->getBindings()));

        $historyData = $historyDataQuery->get();

        // Log hasil query
        Log::info("API Call: getSensorHistory - Data fetched count: " . $historyData->count());
        Log::info("API Call: getSensorHistory - Fetched data (first 5 records): " . json_encode($historyData->take(5)->toArray())); // Log 5 data pertama untuk menghindari log yang terlalu besar

        if ($historyData->isEmpty()) {
            Log::info("API Call: getSensorHistory - No data found for device ID {$deviceId} and metric {$metric} within the last 24 hours.");
        }

        return response()->json(['data' => $historyData]);
    }

    /**
     * No. 12: Export All Monitoring by Month.
     * Ini adalah contoh sederhana yang menghasilkan CSV.
     * Untuk produksi, disarankan menggunakan library seperti Maatwebsite/Excel dan Queue.
     * Endpoint: GET /api/mobile/monitoring/export-monthly?year=2025&month=06
     */


    // di dalam MonitoringApiController.php
    // Pastikan use DB, Pdf, Carbon, dll sudah ada di atas

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

        // --- LOGIKA BARU: MENGAMBIL DATA RINGKASAN HARIAN ---
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
}
