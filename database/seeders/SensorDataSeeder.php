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
        WaterQualitySensor::truncate();
        FlowPressureSensor::truncate();

        $devices = Device::with('deviceType')->get();

        if ($devices->isEmpty()) {
            $this->command->warn('Tidak ada device yang ditemukan. Jalankan DeviceSeeder terlebih dahulu.');
            return;
        }

        $this->command->info('Membuat data sensor historis untuk ' . $devices->count() . ' device...');

        $waterQualityLogs = [];
        $flowPressureLogs = [];

        $now = Carbon::now();

        // Hasilkan data untuk 20 hari terakhir 
        $daysOfData = 20;

        // Data dikirim setiap 10 menit, jadi ada (60/10) * 24 = 144 data per hari
        $logsPerDay = 144;

        // Total log yang akan dibuat per perangkat
        $totalLogs = $daysOfData * $logsPerDay;

        foreach ($devices as $device) {

            $deviceTypeName = $device->deviceType->name;
            $maxVolume = rand(70, 95); // Volume maksimal untuk perangkat ini (misal: 70-95 liter)

            for ($i = 0; $i < $totalLogs; $i++) {
                // Mundur 10 menit di setiap iterasi
                $measuredAt = $now->copy()->subMinutes($i * 10);

                if ($deviceTypeName === 'Quality and Volume Unit') {
                    $waterQualityLogs[] = [
                        'id' => Str::uuid()->toString(),
                        'device_id' => $device->id,
                        'water_level' => rand(300, 950) / 10.0,
                        'turbidity' => (rand(1, 500) == 1) ? rand(800, 2000) / 10.0 : rand(5, 80) / 10.0,
                        'measured_at' => $measuredAt,
                    ];
                } elseif ($deviceTypeName === 'Flow and Pressure Unit') {
                    $isFlowing = rand(1, 4) > 1;
                    $currentFlowRate = $isFlowing ? rand(50, 250) / 10.0 : 0.0;

                    // --- LOGIKA BARU UNTUK VOLUME PULUHAN LITER ---
                    // 1. Hitung progres mundur dari data terbaru ke terlama (1.0 ke 0.0)
                    $progress = ($totalLogs - $i) / (float)$totalLogs;

                    // 2. Hitung volume dasar berdasarkan progres dan volume maksimal
                    $baseVolume = $progress * $maxVolume;

                    // 3. Tambahkan sedikit variasi acak agar tidak terlalu linear
                    $noise = $baseVolume > 0 ? rand(-5, 5) / 10.0 : 0; // noise +/- 0.5 liter
                    $finalVolume = $baseVolume + $noise;

                    // 4. Pastikan volume tidak pernah kurang dari 0
                    $simulatedCumulativeVolume = max(0, $finalVolume);

                    $flowPressureLogs[] = [
                        'id' => Str::uuid()->toString(),
                        'device_id' => $device->id,
                        'flow_rate' => $currentFlowRate,
                        'pressure' => $isFlowing ? rand(25, 45) / 10.0 : rand(0, 5) / 10.0,
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
