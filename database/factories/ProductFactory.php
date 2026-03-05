<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->numberBetween(10, 100),
            'stock' => $this->faker->numberBetween(0, 50),
            'category' => $this->faker->randomElement(['A', 'B', 'C']),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
