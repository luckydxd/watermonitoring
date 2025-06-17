<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    public function run()
    {
        AppSetting::create([
            'name_app' => 'Water Monitoring',
            'desc' => 'Sistem Pemantauan Konsumsi Air Rumah Tangga',
            'no_contact' => '+62 123 4567 8901',
            'email' => 'contact@example.com',
            'instagram' => 'myapp',
            'alamat' => 'Perumahan Perum Graha Panyindangan No.A8',
            'gmap_coordinat' => '-6.175392, 106.827153',
        ]);
    }
}
