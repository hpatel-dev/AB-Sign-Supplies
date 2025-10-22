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
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-#####')),
            'description' => collect($this->faker->paragraphs(3))
                ->map(fn (string $paragraph) => "<p>{$paragraph}</p>")
                ->implode(''),
            'image' => null,
            'is_active' => $this->faker->boolean(90),
            'is_featured' => $this->faker->boolean(20),
        ];
    }
}
