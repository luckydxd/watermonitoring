<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VisitorActivity;
use Carbon\Carbon;

class VisitorActivitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $startDate = Carbon::now()->subDays(14);

        for ($i = 0; $i < 14; $i++) {
            VisitorActivity::create([
                'date' => $startDate->copy()->addDays($i),
                'visitors' => rand(100, 300),
                'contact_clicks' => rand(50, 200),
                'login_clicks' => rand(20, 100),
                'download_clicks' => rand(10, 80)
            ]);
        }
    }
}
