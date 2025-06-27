<?php

namespace Database\Seeders;

use App\Models\AboutSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutSettingSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     *
     * @return void
     */
    public function run()
    {
        AboutSetting::query()->delete();

        AboutSetting::create([
            'title'       => 'Pantau Konsumsi Air di Rumah Anda',
            'description' => 'Fokus kami di konservasi air, Pantau & kelola penggunaan air rumah tangga Anda lebih efisien.',
            'image'       => null, // Dibiarkan kosong, gambar di-upload melalui admin panel
        ]);
    }
}
