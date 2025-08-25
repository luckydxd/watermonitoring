<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;

class BranchToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 1. Ambil data cabang untuk mendapatkan ID-nya
        $branchSindang = Branch::where('code', 'SND')->first();
        $branchLohbener = Branch::where('code', 'LBN')->first();

        // Pastikan cabang ditemukan sebelum melanjutkan
        if (!$branchSindang || !$branchLohbener) {
            $this->command->error('Seeder cabang (BranchSeeder) harus dijalankan terlebih dahulu.');
            return;
        }

        // 2. Definisikan pengguna mana yang akan diupdate dan cabang mana yang akan diberikan
        $userBranchMapping = [
            // Teknisi
            'teknisi.udin@dummy.com' => $branchSindang->id,
            'teknisi.sudrajat@dummy.com' => $branchLohbener->id,

            // Pengguna biasa (user)
            // Anda bisa menetapkan cabang secara acak atau berdasarkan logika lain
            'lucky@dummy.com'    => $branchSindang->id,
            'rama@dummy.com'     => $branchLohbener->id,
            'mugni@dummy.com'    => $branchSindang->id,
        ];

        // 3. Loop melalui pemetaan dan update setiap pengguna
        foreach ($userBranchMapping as $email => $branchId) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->branch_id = $branchId;
                $user->save();
                $this->command->info("Pengguna dengan email {$email} telah diberi cabang.");
            } else {
                $this->command->warn("Pengguna dengan email {$email} tidak ditemukan.");
            }
        }

        $this->command->info('Proses penetapan cabang ke pengguna selesai.');
    }
}
