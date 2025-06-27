<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\DeviceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $flowPressureTypeId = DeviceType::where('code', 'F')->value('id');
        $qualityVolumeTypeId = DeviceType::where('code', 'Q')->value('id');

        if (!$flowPressureTypeId || !$qualityVolumeTypeId) {
            $this->command->error('DeviceType "F" or "Q" not found. Please run DeviceTypeSeeder first.');
            return;
        }

        $devices = [
            [
                'unique_id' => '2505Q1001',
                'device_type_id' => $qualityVolumeTypeId,
                'status' => 'active',
            ],
            [
                'unique_id' => '2306F1001',
                'device_type_id' => $flowPressureTypeId,
                'status' => 'active',
            ],
            [
                'unique_id' => '2505F1002',
                'device_type_id' => $flowPressureTypeId,
                'status' => 'error',
            ],
            [
                'unique_id' => '2505Q1002',
                'device_type_id' => $qualityVolumeTypeId,
                'status' => 'inactive',
            ],
        ];

        foreach ($devices as $deviceData) {
            Device::create([
                'id' => Str::uuid(),
                'unique_id' => $deviceData['unique_id'],
                'device_type_id' => $deviceData['device_type_id'],
                'status' => $deviceData['status'],
                'unique_key' => null,
                'last_seen_at' => null,
            ]);
        }

        $this->command->info('DeviceSeeder berhasil dijalankan!');
        $this->command->info('unique_key menggunakan mac address alat.');
    }
}
