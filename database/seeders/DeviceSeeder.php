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
        // Ambil semua device type yang sudah dibuat
        $deviceTypes = DeviceType::all();

        $devices = [
            [
                'unique_id' => 'DEV-001',
                'device_type_id' => $deviceTypes[0]->id,
                'status' => 'active',
                'latitude' => -6.914744,
                'longitude' => 107.609810,
            ],
            [
                'unique_id' => 'DEV-002',
                'device_type_id' => $deviceTypes[1]->id,
                'status' => 'active',
                'latitude' => -6.915744,
                'longitude' => 107.610810,
            ],
            [
                'unique_id' => 'DEV-003',
                'device_type_id' => $deviceTypes[1]->id,
                'status' => 'error',
                'latitude' => -6.916744,
                'longitude' => 107.611810,
            ],
            [
                'unique_id' => 'DEV-004',
                'device_type_id' => $deviceTypes[0]->id,
                'status' => 'inactive',
                'latitude' => -6.917744,
                'longitude' => 107.612810,
            ],
        ];

        foreach ($devices as $device) {
            Device::create([
                'id' => Str::uuid(),
                'unique_id' => $device['unique_id'],
                'device_type_id' => $device['device_type_id'],
                'status' => $device['status'],
                'latitude' => $device['latitude'],
                'longitude' => $device['longitude'],
            ]);
        }

        $this->command->info('DeviceSeeder berhasil dijalankan!');
    }
}
