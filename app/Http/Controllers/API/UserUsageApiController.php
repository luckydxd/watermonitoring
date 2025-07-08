<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FlowPressureSensor;
use App\Models\DeviceAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class UserUsageApiController extends Controller
{

    public function getUserConsumption(Request $request)
    {
        $user = auth()->user();

        // 1. Dapatkan data assignment lengkap
        $activeAssignment = $user->deviceAssignments()
            ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->where('device_assignments.is_active', true)
            ->where('device_types.name', 'Flow and Pressure Unit')
            ->select(
                'device_assignments.device_id',
                'device_assignments.initial_meter_reading'
            )
            ->first();

        if (!$activeAssignment) {
            return DataTables::of(collect([]))->make(true);
        }

        $deviceId = $activeAssignment->device_id;
        $initialMeterReading = (float) $activeAssignment->initial_meter_reading;

        // 2. Dapatkan semua pembacaan terakhir untuk SETIAP HARI.
        // Diurutkan dari yang paling lama ke paling baru untuk kemudahan proses.
        $endOfDayReadings = FlowPressureSensor::where('device_id', $deviceId)
            ->select(
                DB::raw('DATE(measured_at) as date'),
                DB::raw('MAX(volume) as end_of_day_volume')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc') // PENTING: Urutkan ASC untuk proses di PHP
            ->get();

        // Jika tidak ada data sama sekali, kembalikan tabel kosong.
        if ($endOfDayReadings->isEmpty()) {
            return DataTables::of(collect([]))->make(true);
        }

        // 3. Proses hasil di PHP untuk menghitung selisih antar hari
        $processedData = [];
        // Titik awal pertama adalah initial_meter_reading
        $previousDayVolume = $initialMeterReading;

        foreach ($endOfDayReadings as $reading) {
            $currentDayVolume = (float) $reading->end_of_day_volume;

            // Hitung pemakaian untuk hari ini
            $dailyConsumption = $currentDayVolume - $previousDayVolume;

            // Hanya tambahkan ke hasil jika ada pemakaian
            if ($dailyConsumption > 0) {
                $processedData[] = [
                    'usage_date' => $reading->date,
                    'total_consumption' => max(0, $dailyConsumption) // Pastikan tidak negatif
                ];
            }

            // Perbarui volume hari sebelumnya untuk iterasi berikutnya
            $previousDayVolume = $currentDayVolume;
        }

        // 4. Balik urutan array agar yang terbaru muncul di atas
        $processedData = array_reverse($processedData);

        // 5. Serahkan KOLEKSI yang sudah diproses ke DataTables
        return DataTables::of($processedData)->make(true);
    }

    // public function getUserConsumption(Request $request)
    // {
    //     $user = auth()->user();

    //     // 1. Dapatkan ID perangkat aktif tipe 'Flow and Pressure Unit' milik pengguna
    //     $activeFlowDeviceAssignment = $user->deviceAssignments()
    //         ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
    //         ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
    //         ->where('device_assignments.is_active', true)
    //         ->where('device_types.name', 'Flow and Pressure Unit') // Pastikan hanya tipe yang benar
    //         ->select('device_assignments.device_id')
    //         ->first();

    //     // Jika pengguna tidak memiliki perangkat yang relevan, kembalikan tabel kosong
    //     if (!$activeFlowDeviceAssignment) {
    //         return DataTables::of(collect([]))->make(true);
    //     }

    //     $deviceId = $activeFlowDeviceAssignment->device_id;

    //     // 2. Buat query agregasi untuk menghitung pemakaian harian
    //     $data = FlowPressureSensor::query()
    //         ->select(
    //             // Mengambil tanggal dari measured_at sebagai 'usage_date'
    //             DB::raw('DATE(measured_at) as usage_date'),
    //             // Menghitung selisih MAX dan MIN volume sebagai 'total_consumption'
    //             DB::raw('MAX(volume) - MIN(volume) as total_consumption')
    //         )
    //         // Filter hanya untuk perangkat milik pengguna ini
    //         ->where('device_id', $deviceId)
    //         // Kelompokkan hasilnya per hari
    //         ->groupBy('usage_date')
    //         // Hanya tampilkan hari di mana ada konsumsi
    //         ->having('total_consumption', '>', 0)
    //         // Urutkan dari yang terbaru
    //         ->orderBy('usage_date', 'DESC');

    //     // 3. Kirim data yang sudah diolah ke DataTables
    //     return DataTables::of($data)->make(true);
    // }

    // public function getTodayUsage(Request $request)
    // {
    //     $user = $request->user();
    //     $dailyConsumption = 0; // Nilai default

    //     // 1. Mengambil semua ID perangkat aktif yang relevan
    //     $activeDeviceIds = $user->deviceAssignments()
    //         ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
    //         ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
    //         ->where('device_assignments.is_active', true)
    //         ->where('device_types.name', 'Flow and Pressure Unit')
    //         ->pluck('device_assignments.device_id')
    //         ->toArray();

    //     if (!empty($activeDeviceIds)) {
    //         // 2. Dapatkan pembacaan meteran TERAKHIR dari HARI INI
    //         $todayLatestReading = FlowPressureSensor::whereIn('device_id', $activeDeviceIds)
    //             ->whereDate('measured_at', Carbon::today())
    //             ->max('volume');

    //         // Hanya lanjutkan jika ada data hari ini
    //         if (!is_null($todayLatestReading)) {
    //             // 3. Dapatkan pembacaan meteran TERAKHIR dari KEMARIN untuk titik awal
    //             $yesterdayLatestReading = FlowPressureSensor::whereIn('device_id', $activeDeviceIds)
    //                 ->whereDate('measured_at', Carbon::yesterday())
    //                 ->max('volume');

    //             if (!is_null($yesterdayLatestReading)) {
    //                 // 4a. Jika ada data kemarin, hitung selisihnya
    //                 $dailyConsumption = $todayLatestReading - $yesterdayLatestReading;
    //             } else {
    //                 // 4b. FALLBACK: Jika tidak ada data kemarin (hari pertama penggunaan)
    //                 // Gunakan logika MAX - MIN untuk hari ini saja
    //                 $todayEarliestReading = FlowPressureSensor::whereIn('device_id', $activeDeviceIds)
    //                     ->whereDate('measured_at', Carbon::today())
    //                     ->min('volume');

    //                 $dailyConsumption = $todayLatestReading - $todayEarliestReading;
    //             }
    //         }
    //     }

    //     // 5. Kembalikan hasil dalam format JSON
    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'today_usage_liter' => round(max(0, $dailyConsumption), 2) // Pastikan tidak negatif dan dibulatkan
    //         ]
    //     ]);
    // }

    // Fungsi usageByUser() dan usageByMonth() yang lama sudah tidak diperlukan lagi
    // karena fungsionalitasnya sudah tercakup dalam logika DataTables yang baru
    // dan endpoint chart/estimasi yang sudah kita buat sebelumnya.


    // public function usageByUser(Request $request)
    // {
    //     try {
    //         $data = WaterConsumptionLog::where('user_id', $request->user()->id)
    //             ->select(['id', 'date', 'total_consumption'])
    //             ->orderBy('date', 'DESC')
    //             ->get();

    //         return response()->json([
    //             'success' => true,
    //             'data' => $data,
    //             'message' => 'Water consumption data retrieved successfully'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve data',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function usageByMonth(Request $request)
    // {
    //     $userId = $request->user()->id;
    //     $now = now();
    //     $startOfMonth = $now->copy()->startOfMonth();
    //     $endOfMonth = $now->copy()->endOfMonth();

    //     // Gunakan cursor() untuk memory efficiency pada data besar
    //     $monthlyTotal = 0;
    //     $days = [];
    //     $recordCount = 0;

    //     WaterConsumptionLog::where('user_id', $userId)
    //         ->whereBetween('date', [$startOfMonth, $endOfMonth])
    //         ->select(['date', 'total_consumption'])
    //         ->orderBy('date')
    //         ->cursor()
    //         ->each(function ($item) use (&$monthlyTotal, &$days, &$recordCount) {
    //             $monthlyTotal += $item->total_consumption;
    //             $days[] = [
    //                 'date' => $item->date->format('Y-m-d'),
    //                 'day' => $item->date->day,
    //                 'consumption' => (float) $item->total_consumption
    //             ];
    //             $recordCount++;
    //         });

    //     return response()->json([
    //         'success' => true,
    //         'data' => $days,
    //         'statistics' => [
    //             'monthly_total' => round($monthlyTotal, 2),
    //             'average_daily' => $recordCount > 0 ? round($monthlyTotal / $recordCount, 2) : 0,
    //             'days_recorded' => $recordCount
    //         ]
    //     ]);
    // }
}
