<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\WaterQualitySensor;
use App\Models\FlowPressureSensor;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SensorDataSeeder extends Seeder
{
    public function run(): void
    {
        // Mengosongkan tabel untuk menghindari data duplikat saat seeding ulang
        WaterQualitySensor::truncate();
        FlowPressureSensor::truncate();

        // --- PERUBAHAN #1: HANYA AMBIL DEVICE YANG SUDAH DI-ASSIGN ---
        // Menggunakan whereHas untuk memastikan hanya device yang memiliki
        // setidaknya satu assignment yang akan diambil.
        $assignedDevices = Device::whereHas('deviceAssignments')
            ->with('deviceType')
            ->get();

        if ($assignedDevices->isEmpty()) {
            $this->command->warn('Tidak ada device yang ditugaskan ke user. Jalankan DeviceAssignmentSeeder terlebih dahulu.');
            return;
        }

        $this->command->info('Membuat data sensor historis untuk ' . $assignedDevices->count() . ' device yang telah ditugaskan...');

        $waterQualityLogs = [];
        $flowPressureLogs = [];

        $now = Carbon::now();
        $daysOfData = 30; // Buat data untuk 30 hari agar lebih fleksibel
        $logsPerDay = 144; // 1 data per 10 menit
        $totalLogs = $daysOfData * $logsPerDay;

        foreach ($assignedDevices as $device) {

            $deviceTypeName = $device->deviceType->name;
            // Dibuat lebih besar agar selisih hariannya signifikan
            $maxVolume = rand(7000, 9500);

            for ($i = 0; $i < $totalLogs; $i++) {
                // Membuat timestamp yang benar-benar mundur dari sekarang
                $measuredAt = $now->copy()->subMinutes($i * 10);

                if ($deviceTypeName === 'Quality and Volume Unit') {
                    $waterQualityLogs[] = [
                        'id' => Str::uuid()->toString(),
                        'device_id' => $device->id,
                        // --- PERUBAHAN #2: MENGHASILKAN BILANGAN BULAT ---
                        'water_level' => rand(30, 95), // Menghasilkan integer antara 30-95
                        'turbidity' => (rand(1, 500) == 1) ? rand(80, 200) : rand(1, 8), // Menghasilkan integer
                        'measured_at' => $measuredAt,
                    ];
                } elseif ($deviceTypeName === 'Flow and Pressure Unit') {
                    $isFlowing = rand(1, 4) > 1;
                    // --- PERUBAHAN #2: MENGHASILKAN BILANGAN BULAT ---
                    $currentFlowRate = $isFlowing ? rand(5, 25) : 0;
                    $currentPressure = $isFlowing ? rand(2, 4) : rand(0, 1);

                    // Logika untuk volume kumulatif
                    $progress = ($totalLogs - $i) / (float)$totalLogs;
                    $baseVolume = $progress * $maxVolume;
                    $noise = $baseVolume > 0 ? rand(-5, 5) : 0; // Variasi kecil
                    $finalVolume = $baseVolume + $noise;

                    // --- PERUBAHAN #2: MENGHASILKAN BILANGAN BULAT ---
                    $simulatedCumulativeVolume = max(0, (int)round($finalVolume));

                    $flowPressureLogs[] = [
                        'id' => Str::uuid()->toString(),
                        'device_id' => $device->id,
                        'flow_rate' => $currentFlowRate,
                        'pressure' => $currentPressure,
                        'volume' => $simulatedCumulativeVolume,
                        'measured_at' => $measuredAt,
                    ];
                }
            }
        }

        $this->command->info('Menyimpan data WaterQualitySensor...');
        foreach (array_chunk($waterQualityLogs, 500) as $chunk) {
            WaterQualitySensor::insert($chunk);
        }

        $this->command->info('Menyimpan data FlowPressureSensor...');
        foreach (array_chunk($flowPressureLogs, 500) as $chunk) {
            FlowPressureSensor::insert($chunk);
        }

        $this->command->info('SensorDataSeeder berhasil dijalankan!');
        $this->command->info('Total Water Quality logs: ' . count($waterQualityLogs));
        $this->command->info('Total Flow & Pressure logs: ' . count($flowPressureLogs));
    }
}
