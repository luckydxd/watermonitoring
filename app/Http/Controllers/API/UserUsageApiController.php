<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FlowPressureSensor;
use App\Models\DeviceAssignment;
use App\Models\AppSetting;

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

        // Dapatkan semua pembacaan terakhir.
        $endOfDayReadings = FlowPressureSensor::where('device_id', $deviceId)
            ->select(
                DB::raw('DATE(measured_at) as date'),
                DB::raw('MAX(volume) as end_of_day_volume')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        if ($endOfDayReadings->isEmpty()) {
            return DataTables::of(collect([]))->make(true);
        }

        $processedData = [];
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

            // Perbarui volume hari sebelumnya 
            $previousDayVolume = $currentDayVolume;
        }

        $processedData = array_reverse($processedData);

        // kirim ke DataTables
        return DataTables::of($processedData)->make(true);
    }
    public function getMonthlyUsageWithCost(Request $request)
    {
        try {
            $user = auth()->user();

            $appSetting = AppSetting::first();
            if (!$appSetting || !is_numeric($appSetting->price_per_liter) || $appSetting->price_per_liter <= 0) {
                return response()->json(['success' => false, 'message' => 'Harga air belum diatur.'], 500);
            }
            $hargaPerLiter = (float) $appSetting->price_per_liter;

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
                return response()->json(['success' => true, 'data' => [], 'message' => 'Tidak ada perangkat aktif yang ditemukan.']);
            }

            $deviceId = $activeAssignment->device_id;
            $initialMeterReading = (float) $activeAssignment->initial_meter_reading;

            $endOfMonthReadings = FlowPressureSensor::where('device_id', $deviceId)
                ->select(
                    DB::raw("DATE_FORMAT(measured_at, '%Y-%m') as month"),
                    DB::raw('MAX(volume) as end_of_month_volume')
                )
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            if ($endOfMonthReadings->isEmpty()) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $processedData = [];
            $previousMonthVolume = $initialMeterReading;

            foreach ($endOfMonthReadings as $reading) {
                $currentMonthVolume = (float) $reading->end_of_month_volume;
                $monthlyConsumption = $currentMonthVolume - $previousMonthVolume;

                if ($monthlyConsumption > 0) {
                    $estimatedCost = max(0, $monthlyConsumption) * $hargaPerLiter;

                    $processedData[] = [
                        'month_name' => Carbon::parse($reading->month . '-01')->translatedFormat('F Y'),
                        'total_consumption_liter' => round(max(0, $monthlyConsumption), 2),
                        'estimated_cost_rp' => round($estimatedCost, 0) // Rupiah biasanya dibulatkan
                    ];
                }

                $previousMonthVolume = $currentMonthVolume;
            }

            $processedData = array_reverse($processedData);

            return response()->json([
                'success' => true,
                'data' => $processedData,
                'message' => 'Riwayat penggunaan bulanan berhasil diambil.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat penggunaan bulanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsageData(Request $request)
    {
        $user = auth()->user();
        $period = $request->input('period', 'daily');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $specificDate = $request->input('date');

        // Dapatkan device assignment aktif untuk Flow and Pressure Unit
        $activeAssignment = $user->deviceAssignments()
            ->where('is_active', true)
            ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->where('device_types.name', 'Flow and Pressure Unit')
            ->select('device_assignments.device_id', 'device_assignments.initial_meter_reading')
            ->first();

        if (!$activeAssignment) {
            return DataTables::of(collect([]))->make(true);
        }

        // Ambil data volume akhir hari dari sensor
        $endOfDayReadings = FlowPressureSensor::where('device_id', $activeAssignment->device_id)
            ->select(DB::raw('DATE(measured_at) as date'), DB::raw('MAX(volume) as end_of_day_volume'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        if ($endOfDayReadings->isEmpty()) {
            return DataTables::of(collect([]))->make(true);
        }

        // Hitung konsumsi harian
        $dailyConsumptions = collect();
        $previousDayVolume = (float) $activeAssignment->initial_meter_reading;
        $firstDate = Carbon::parse($endOfDayReadings->keys()->first());
        $lastDate = Carbon::parse($endOfDayReadings->keys()->last());

        for ($date = $firstDate; $date->lte($lastDate); $date->addDay()) {
            $dateString = $date->toDateString();
            $currentDayVolume = $endOfDayReadings->get($dateString, (object)['end_of_day_volume' => $previousDayVolume])->end_of_day_volume;
            $consumption = $currentDayVolume - $previousDayVolume;

            $dailyConsumptions->push([
                'date' => $dateString,
                'total_consumption' => max(0, $consumption),
                'year' => $date->year,
                'month' => $date->month,
                'day' => $date->day
            ]);
            $previousDayVolume = $currentDayVolume;
        }

        $query = collect();

        // Jika ada tanggal spesifik yang diminta
        if ($specificDate) {
            $query = $dailyConsumptions->where('date', $specificDate);
        } else {
            // Logika Roll-Up/Drill-Down berdasarkan periode
            switch ($period) {
                case 'yearly':
                    $query = $dailyConsumptions
                        ->groupBy('year')
                        ->map(function ($group, $yearKey) {
                            return [
                                'year' => $yearKey,
                                'period_label' => $yearKey,
                                'total_consumption' => $group->sum('total_consumption'),
                                'period_type' => 'yearly'
                            ];
                        })
                        ->values();
                    break;

                case 'monthly':
                    $query = $dailyConsumptions
                        ->filter(function ($item) use ($year) {
                            return $item['year'] == $year;
                        })
                        ->groupBy(function ($item) {
                            return $item['year'] . '-' . str_pad($item['month'], 2, '0', STR_PAD_LEFT);
                        })

                        ->map(function ($group, $monthKey) {
                            $firstItem = $group->first();
                            return [
                                'year' => $firstItem['year'],
                                'month' => $firstItem['month'],
                                'period_label' => Carbon::create($firstItem['year'], $firstItem['month'])->isoFormat('MMMM YYYY'),
                                'total_consumption' => $group->sum('total_consumption'),
                                'period_type' => 'monthly'
                            ];
                        })
                        ->values();
                    break;

                case 'daily':
                default:
                    $monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
                    $query = $dailyConsumptions
                        ->filter(function ($item) use ($year, $month) {
                            return $item['year'] == $year && $item['month'] == $month;
                        })
                        ->sortByDesc(function ($item) {
                            return Carbon::parse($item['date'])->timestamp;
                        })
                        ->values()
                        ->map(function ($item) {
                            return [
                                'date' => $item['date'],
                                'year' => $item['year'],
                                'month' => $item['month'],
                                'day' => $item['day'],
                                'period_label' => Carbon::parse($item['date'])->isoFormat('dddd, D MMMM YYYY'),
                                'total_consumption' => $item['total_consumption'],
                                'period_type' => 'daily'
                            ];
                        });
                    break;
            }
        }

        return DataTables::of($query)
            ->addColumn('formatted_date', function ($row) use ($period, $specificDate) {
                if ($specificDate) {
                    return Carbon::parse($row['date'])->isoFormat('dddd, D MMMM YYYY');
                }

                return $row['period_label'] ?? '';
            })
            ->addColumn('formatted_consumption', function ($row) {
                return number_format($row['total_consumption'], 0, ',', '.') . ' Liter';
            })
            ->rawColumns(['formatted_date', 'formatted_consumption'])
            ->make(true);
    }

    // public function getUsageHistoryForMobile(Request $request)
    // {
    //     try {
    //         $user = auth()->user();

    //         // 1. Dapatkan data assignment lengkap
    //         $activeAssignment = $user->deviceAssignments()
    //             ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
    //             ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
    //             ->where('device_assignments.is_active', true)
    //             ->where('device_types.name', 'Flow and Pressure Unit')
    //             ->select(
    //                 'device_assignments.device_id',
    //                 'device_assignments.initial_meter_reading'
    //             )
    //             ->first();

    //         // Jika pengguna tidak memiliki perangkat yang relevan, kembalikan array data kosong
    //         if (!$activeAssignment) {
    //             return response()->json([
    //                 'success' => true,
    //                 'data' => [],
    //                 'message' => 'Tidak ada perangkat aktif yang ditemukan.'
    //             ]);
    //         }

    //         $deviceId = $activeAssignment->device_id;
    //         $initialMeterReading = (float) $activeAssignment->initial_meter_reading;

    //         // 2. Dapatkan semua pembacaan terakhir untuk SETIAP HARI.
    //         $endOfDayReadings = FlowPressureSensor::where('device_id', $deviceId)
    //             ->select(
    //                 DB::raw('DATE(measured_at) as date'),
    //                 DB::raw('MAX(volume) as end_of_day_volume')
    //             )
    //             ->groupBy('date')
    //             ->orderBy('date', 'asc')
    //             ->get();

    //         if ($endOfDayReadings->isEmpty()) {
    //             return response()->json(['success' => true, 'data' => []]);
    //         }

    //         // 3. Proses hasil di PHP untuk menghitung selisih antar hari
    //         $processedData = [];
    //         $previousDayVolume = $initialMeterReading;

    //         foreach ($endOfDayReadings as $reading) {
    //             $currentDayVolume = (float) $reading->end_of_day_volume;
    //             $dailyConsumption = $currentDayVolume - $previousDayVolume;

    //             if ($dailyConsumption > 0) {
    //                 $processedData[] = [
    //                     'usage_date' => $reading->date,
    //                     'total_consumption' => round(max(0, $dailyConsumption), 2)
    //                 ];
    //             }
    //             $previousDayVolume = $currentDayVolume;
    //         }

    //         // 4. Balik urutan array agar yang terbaru muncul di atas
    //         $processedData = array_reverse($processedData);

    //         // 5. Kembalikan sebagai respons JSON standar
    //         return response()->json([
    //             'success' => true,
    //             'data' => $processedData,
    //             'message' => 'Riwayat penggunaan berhasil diambil.'
    //         ]);
    //     } catch (\Exception $e) {
    //         // Tangani error jika terjadi
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal mengambil riwayat penggunaan.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

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
