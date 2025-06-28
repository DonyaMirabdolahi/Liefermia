<?php

namespace Database\Factories;

use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

class SizeFactory extends Factory
{
    protected $model = Size::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Small', 'Medium', 'Large', 'XLarge']),
        ];
    }
} 