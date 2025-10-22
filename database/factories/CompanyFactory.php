<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'tagline' => $this->faker->catchPhrase(),
            'logo_path' => null,
            'summary' => $this->faker->sentence(12),
            'overview' => $this->faker->paragraphs(3, true),
            'contact_email' => $this->faker->companyEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'website' => $this->faker->url(),
            'sort_order' => $this->faker->numberBetween(0, 20),
        ];
    }
}

