<?php

namespace Database\Factories;

use App\Models\Complaint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Untuk UUID

class ComplaintFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Complaint::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => (string) Str::uuid(), // Pastikan ID di-generate di sini
            'title' => $this->faker->sentence(rand(3, 8)),
            'description' => $this->faker->paragraph(rand(3, 10)),
            'image' => null, // Biarkan null atau gunakan $this->faker->imageUrl() jika ingin gambar palsu
            'status' => $this->faker->randomElement(['pending', 'processed', 'resolved', 'rejected']),
            // HAPUS BARIS INI JIKA ADA:
            // 'timestamp' => $this->faker->dateTimeBetween('-1 year', 'now'),
            // created_at dan updated_at akan diisi otomatis oleh Eloquent
            // 'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // Contoh jika Anda manual
            // 'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // Contoh jika Anda manual
        ];
    }
}
