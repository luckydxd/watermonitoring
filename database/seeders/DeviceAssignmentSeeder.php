<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeviceAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cari user spesifik yang ingin Anda tuju
        $targetUser = User::where('email', 'lucky@dummy.com')->first();

        // 2. Cari kedua device spesifik berdasarkan unique_id mereka
        $deviceQuality = Device::where('unique_id', '2505Q1001')->first();
        $deviceFlow = Device::where('unique_id', '2306F1001')->first();

        // 3. Lakukan pemeriksaan untuk memastikan user dan device ditemukan
        //    Ini mencegah error jika seeder lain belum dijalankan.
        if (!$targetUser) {
            $this->command->error('User dengan email lucky@dummy.com tidak ditemukan. Pastikan UserSeeder sudah dijalankan.');
            return;
        }

        if (!$deviceQuality || !$deviceFlow) {
            $this->command->error('Satu atau kedua device (2505Q1001, 2306F1001) tidak ditemukan. Pastikan DeviceSeeder sudah dijalankan.');
            return;
        }

        // 4. Buat assignment untuk setiap device ke user target
        $this->command->info("Menugaskan perangkat ke user: {$targetUser->email}...");

        // Hapus assignment lama jika ada, untuk menghindari duplikat saat seeder dijalankan ulang
        DeviceAssignment::where('user_id', $targetUser->id)->delete();

        // Assignment untuk device Quality (2505Q1001)
        DeviceAssignment::create([
            // ID akan dibuat otomatis oleh trait HasUuids di model Anda
            'user_id' => $targetUser->id,
            'device_id' => $deviceQuality->id,
            'is_active' => true,
            'notes' => 'Device kualitas air ditugaskan ke Lucky melalui seeder.',
        ]);

        // Assignment untuk device Flow (2306F1001)
        DeviceAssignment::create([
            'user_id' => $targetUser->id,
            'device_id' => $deviceFlow->id,
            'is_active' => true,
            'notes' => 'Device aliran & tekanan ditugaskan ke Lucky melalui seeder.',
        ]);

        $this->command->info('DeviceAssignmentSeeder berhasil dijalankan untuk user lucky@dummy.com!');
        $this->command->info('Total Device Assignments sekarang: ' . DeviceAssignment::count());
    }
}
