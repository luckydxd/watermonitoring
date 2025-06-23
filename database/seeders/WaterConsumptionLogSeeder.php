<?php

namespace Database\Seeders;

use App\Models\WaterConsumptionLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WaterConsumptionLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel untuk menghindari data duplikat
        WaterConsumptionLog::truncate();

        // 2. Ambil semua user dengan role 'user'
        $users = User::role('user')->get();

        if ($users->isEmpty()) {
            $this->command->warn('Tidak ada user dengan role "user" yang ditemukan.');
            return;
        }

        $this->command->info('Membuat data konsumsi air unik per hari untuk ' . $users->count() . ' user...');

        $logsToInsert = [];
        $now = Carbon::now();

        foreach ($users as $user) {
            // === PERUBAHAN LOGIKA UTAMA DIMULAI DI SINI ===

            // 3. Buat daftar hari yang mungkin dalam 3 bulan terakhir (90 hari)
            $possibleDays = range(0, 90);

            // 4. Acak urutan hari tersebut
            shuffle($possibleDays);

            // 5. Tentukan berapa banyak log unik yang akan dibuat untuk user ini (misal, 70-90 log)
            $logsCountForThisUser = rand(70, 90);

            // 6. Ambil sebagian dari hari yang sudah diacak untuk memastikan setiap hari unik
            $uniqueDaysForUser = array_slice($possibleDays, 0, $logsCountForThisUser);

            // 7. Buat log untuk setiap hari yang unik tersebut
            foreach ($uniqueDaysForUser as $daysAgo) {
                // Set tanggal berdasarkan hari yang unik, dan jam acak pada hari itu
                $logDate = $now->copy()->subDays($daysAgo)->setTime(rand(7, 22), rand(0, 59), rand(0, 59));

                // Logika konsumsi acak
                $baseConsumption = rand(150, 400);
                $variation = rand(-50, 100);
                $spike = (rand(1, 20) === 1) ? rand(300, 600) : 0;
                $totalConsumption = max(50, $baseConsumption + $variation + $spike);

                // Siapkan data untuk bulk insert
                $logsToInsert[] = [
                    'id' => Str::uuid()->toString(),
                    'user_id' => $user->id,
                    'total_consumption' => $totalConsumption,
                    'created_at' => $logDate,
                    'updated_at' => $logDate,
                ];
            }
        }

        // 8. Bulk Insert (tetap efisien)
        if (!empty($logsToInsert)) {
            foreach (array_chunk($logsToInsert, 500) as $chunk) {
                WaterConsumptionLog::insert($chunk);
            }
        }

        $this->command->info('WaterConsumptionLogSeeder berhasil dijalankan!');
        $this->command->info('Total logs unik yang dibuat: ' . count($logsToInsert));
    }
}
