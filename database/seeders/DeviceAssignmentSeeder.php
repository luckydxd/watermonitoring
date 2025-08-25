<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeviceAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel di awal agar seeder bisa dijalankan berkali-kali
        DeviceAssignment::truncate();

        // 2. Definisikan semua penugasan yang ingin dibuat dalam sebuah array
        $assignmentsData = [
            [
                'user_email' => 'lucky@dummy.com',
                'devices' => [
                    ['unique_id' => '2505Q1001', 'notes' => 'Device kualitas air untuk Lucky.'],
                    ['unique_id' => '2306F1001', 'notes' => 'Device aliran & tekanan untuk Lucky.'],
                ]
            ],
            [
                'user_email' => 'rama@dummy.com',
                'devices' => [
                    ['unique_id' => '2505Q1002', 'notes' => 'Device kualitas air untuk Rama.'],
                    ['unique_id' => '2306F1002', 'notes' => 'Device aliran & tekanan untuk Rama.'],
                ]
            ],
        ];

        $this->command->info('Memulai proses penugasan perangkat...');

        // 3. Lakukan perulangan untuk setiap data penugasan
        foreach ($assignmentsData as $data) {
            $user = User::where('email', $data['user_email'])->first();

            // Validasi User
            if (!$user) {
                $this->command->warn("User dengan email {$data['user_email']} tidak ditemukan. Melanjutkan ke data berikutnya.");
                continue; // Lanjut ke iterasi berikutnya
            }

            $this->command->info("--> Menugaskan perangkat ke user: {$user->email}");

            // Lakukan perulangan untuk setiap device milik user tersebut
            foreach ($data['devices'] as $deviceInfo) {
                $device = Device::where('unique_id', $deviceInfo['unique_id'])->first();

                // Validasi Device
                if (!$device) {
                    $this->command->warn("    - Device dengan ID unik {$deviceInfo['unique_id']} tidak ditemukan.");
                    continue; // Lanjut ke device berikutnya
                }

                // Buat assignment
                DeviceAssignment::create([
                    'user_id' => $user->id,
                    'device_id' => $device->id,
                    'is_active' => true,
                    'initial_meter_reading' => mt_rand(100, 1000) / 10, // Angka meteran awal acak
                    'notes' => $deviceInfo['notes'],
                ]);

                $this->command->info("    - Sukses: Device {$device->unique_id} ditugaskan.");
            }
        }

        $this->command->info('Proses penugasan perangkat selesai!');
        $this->command->info('Total Device Assignments sekarang: ' . DeviceAssignment::count());
    }
}
