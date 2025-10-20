<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Supplier;
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
            'description' => $this->faker->paragraph(),
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'image' => null,
            'is_active' => $this->faker->boolean(90),
            'is_featured' => $this->faker->boolean(20),
        ];
    }
}
