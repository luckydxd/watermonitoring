<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    public function run()
    {
        AppSetting::query()->delete();

        AppSetting::create([
            'name_app' => 'Water Monitoring',
            'desc' => 'Sistem Pemantauan Konsumsi Air Rumah Tangga',
            'logo' => null,
            'secondary_logo' => null,
            'app_mockup' => null,

            'address' => 'Perumahan Perum Graha Panyindangan No.A8',
            'email' => 'flowater@polindra.ac.id',
            'phone' => '0895345990299',

            'whatsapp' => '62895345990299',
            'instagram' => 'flowater.id',
            'youtube' => 'https://www.youtube.com/@flowater',

            'gmap_coordinat' => '-6.3467, 108.3224',
            'price_per_liter' => 5.20
        ]);
    }
}
