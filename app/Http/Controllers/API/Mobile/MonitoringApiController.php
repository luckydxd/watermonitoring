<?php

namespace App\Http\Controllers\API\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\FlowPressureSensor;
use App\Models\WaterConsumptionLog;
use App\Models\WaterQualitySensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;

class MonitoringApiController extends Controller
{
    /**
     * Helper function untuk mendapatkan device_id yang aktif untuk user.
     */
    private function getActiveDeviceId(Request $request)
    {
        $assignment = DeviceAssignment::where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->first();

        return $assignment ? $assignment->device_id : null;
    }

    public function getActiveDevicesInfo(Request $request)
    {
        // 1. Ambil semua perangkat yang aktif untuk user yang sedang login
        $devices = Device::join('device_assignments', 'devices.id', '=', 'device_assignments.device_id')
            ->where('device_assignments.user_id', $request->user()->id)
            ->where('device_assignments.is_active', true)
            ->select('devices.unique_id', 'devices.status', 'devices.last_seen_at')
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
        // Validasi parameter 'range' yang baru. 'period' masih ada untuk backward compatibility mobile.
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
            case 'weekly': // Menangani 'weekly' dari mobile
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'last30':
            case 'monthly': // Menangani 'monthly' dari mobile
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
            ->whereBetween('created_at', [$startDate, $endDate]) // Menggunakan whereBetween yang dinamis
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
    public function getLatestReadings(Request $request)
    {
        $deviceId = $this->getActiveDeviceId($request);
        if (!$deviceId) {
            return response()->json(['message' => 'Tidak ada perangkat aktif yang ditemukan.'], 404);
        }

        $latestFlowPressure = FlowPressureSensor::where('device_id', $deviceId)->latest('measured_at')->first();
        $latestWaterQuality = WaterQualitySensor::where('device_id', $deviceId)->latest('measured_at')->first();

        return response()->json([
            'data' => [
                'flow_rate' => $latestFlowPressure->flow_rate ?? 0,
                'pressure' => $latestFlowPressure->pressure ?? 0,
                'turbidity' => $latestWaterQuality->turbidity ?? 0,
                'water_level' => $latestWaterQuality->water_level ?? 0,
                'last_measured_at' => optional($latestFlowPressure)->measured_at ?? optional($latestWaterQuality)->measured_at,
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
        $validMetrics = ['pressure', 'turbidity', 'water_level', 'flow_rate'];
        if (!in_array($metric, $validMetrics)) {
            return response()->json(['message' => 'Metrik tidak valid.'], 400);
        }

        $deviceId = $this->getActiveDeviceId($request);
        if (!$deviceId) {
            return response()->json(['message' => 'Tidak ada perangkat aktif yang ditemukan.'], 404);
        }

        $queryTime = Carbon::now();
        $model = (in_array($metric, ['pressure', 'flow_rate'])) ? new FlowPressureSensor() : new WaterQualitySensor();

        $historyData = $model->where('device_id', $deviceId)
            ->where('measured_at', '>=', $queryTime->copy()->subHours(24))
            ->orderBy('measured_at', 'asc')
            ->select('measured_at', DB::raw("$metric as value"))
            ->get();

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
