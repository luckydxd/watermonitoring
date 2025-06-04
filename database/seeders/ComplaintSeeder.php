<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Complaint;
use App\Models\User;

class ComplaintSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua user dengan role 'user'
        $users = User::where('role', 'user')->get();

        // Pastikan ada user yang tersedia
        if ($users->isEmpty()) {
            $this->command->info('No users found with role "user". Please run UserSeeder first.');
            return;
        }

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
