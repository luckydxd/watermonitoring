<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::firstOrCreate(
            ['id' => 1], // Kunci untuk mencari
            [ // Data untuk dibuat jika tidak ditemukan
                'title' => 'Landing Page Utama',
                'slug' => '/',
                'is_published' => true
            ]
        );
    }
}
