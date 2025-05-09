<?php

namespace Database\Seeders;

use App\Models\DeviceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeviceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $deviceTypes = [
            [
                'id' => Str::uuid(),
                'name' => 'Flow and Pressure Unit',
                'description' => 'Sensor for measuring water flow and pressure',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Quality and Volume Unit',
                'description' => 'Sensors for measuring water quality and volume',
            ],
        ];

        foreach ($deviceTypes as $type) {
            DeviceType::create($type);
        }

        $this->command->info('DeviceTypeSeeder berhasil dijalankan!');
        $this->command->info('Total Device Types: ' . DeviceType::count());
    }
}
