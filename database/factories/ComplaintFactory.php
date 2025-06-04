<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ComplaintFactory extends Factory
{
    public function definition()
    {
        $statuses = ['pending', 'processed', 'resolved', 'rejected'];
        $timestamps = $this->faker->dateTimeBetween('-3 months', 'now');

        return [
            'id' => Str::uuid(),
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraphs(3, true),
            'image' => $this->faker->optional(0.3)->imageUrl(640, 480, 'technics'),
            'status' => $this->faker->randomElement($statuses),
            'timestamp' => $timestamps,
            'created_at' => $timestamps,
            'updated_at' => $timestamps,
        ];
    }
}
