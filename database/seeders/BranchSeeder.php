<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use Illuminate\Support\Facades\Schema; // <-- TAMBAHKAN INI

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Nonaktifkan pengecekan foreign key
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel (sekarang aman dilakukan)
        Branch::truncate();

        // 3. Aktifkan kembali pengecekan foreign key
        Schema::enableForeignKeyConstraints();

        $branches = [
            [
                'name' => 'Sindang',
                'code' => 'SND',
                'address' => 'Jl. Singalodra Sindang - Kab.Indramayu',
                'phone_number' => '081200001111',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lohbener',
                'code' => 'LBN',
                'address' => 'Jl. Simpang Tiga Celeng Lohbener - Kab.Indramayu',
                'phone_number' => '(0234) 276930',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Masukkan data baru ke dalam database
        Branch::insert($branches);
    }
}
