<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Complaint;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB; // <-- Import DB Facade
use Illuminate\Support\Facades\Schema; // <-- Import Schema Facade

class ComplaintSeeder extends Seeder
{
    public function run()
    {
        // Ambil user dengan role 'user' menggunakan metode Spatie
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->get();

        if ($users->isEmpty()) {
            $this->command->info('No users found with role "user". Please ensure UserSeeder and RoleSeeder are run first.');
            return;
        }

        // --- BAGIAN PERBAIKAN: Nonaktifkan Foreign Key Check dan Truncate ---
        Schema::disableForeignKeyConstraints(); // Nonaktifkan pengecekan FK
        DB::table('notifications')->truncate(); // <-- KOSONGKAN TABEL ANAK DULU
        Complaint::truncate(); // Sekarang Anda bisa mengosongkan tabel complaints
        Schema::enableForeignKeyConstraints(); // Aktifkan kembali pengecekan FK
        // --- AKHIR PERBAIKAN ---


        // Buat 20-30 complaint acak
        $complaintCount = rand(20, 30);

        Complaint::factory()
            ->count($complaintCount)
            ->make()
            ->each(function ($complaint) use ($users) {
                // Assign random user
                $complaint->user_id = $users->random()->id;
                $complaint->save();
            });

        $this->command->info("Created {$complaintCount} complaints with random users.");
    }
}
