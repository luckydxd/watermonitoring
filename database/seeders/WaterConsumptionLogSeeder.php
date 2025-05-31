<?php

namespace Database\Seeders;

use App\Models\WaterConsumptionLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WaterConsumptionLogSeeder extends Seeder
{
    public function run(): void
    {
        // Get all regular users (non-admin)
        $users = User::role('user')->get();

        if ($users->isEmpty()) {
            $this->command->warn('No regular users found. Please run UserSeeder first.');
            return;
        }

        // Generate consumption data for each user
        foreach ($users as $user) {
            // Generate 3-6 months of historical data for each user
            $months = rand(3, 6);

            for ($i = 0; $i < $months; $i++) {
                // Generate daily data for each month
                $daysInMonth = rand(20, 31); // Not all users report every day

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    // Random date within the past 6 months
                    $date = Carbon::now()
                        ->subMonths(rand(0, 5))
                        ->subDays(rand(0, 30))
                        ->format('Y-m-d');

                    // Base consumption with some randomness
                    $baseConsumption = rand(100, 300); // 100-300 liters base

                    // Random variation (weekends might have higher usage)
                    $variation = rand(-50, 100);

                    // Random spikes (occasional high usage)
                    $spike = (rand(1, 10) === 1) ? rand(200, 500) : 0;

                    $totalConsumption = $baseConsumption + $variation + $spike;

                    WaterConsumptionLog::create([
                        'id' => Str::uuid(),
                        'user_id' => $user->id,
                        'date' => $date,
                        'total_consumption' => $totalConsumption,
                    ]);
                }
            }
        }

        $this->command->info('WaterConsumptionLogSeeder berhasil dijalankan!');
        $this->command->info('Total logs created: ' . WaterConsumptionLog::count());
    }
}
