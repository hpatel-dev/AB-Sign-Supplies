<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyInfo>
 */
class CompanyInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $heroStats = [
            ['value' => '2,000+', 'label' => 'Product catalog'],
            ['value' => 'North America', 'label' => 'Shipping'],
            ['value' => 'Dedicated team', 'label' => 'Support'],
        ];

        return [
            'site_name' => 'AB Sign Supplies',
            'tagline' => 'Premium signage materials and services',
            'logo_path' => null,
            'hero_headline' => 'Your Complete Source for Signage Supplies',
            'hero_subheadline' => 'AB Sign Supplies partners with leading manufacturers to deliver premium materials, hardware, and equipment for custom signage projects of every size.',
            'hero_primary_cta_label' => 'Shop Products',
            'hero_primary_cta_url' => '/products',
            'hero_secondary_cta_label' => 'Request a Quote',
            'hero_secondary_cta_url' => '/contact',
            'hero_media_type' => null,
            'hero_media_path' => null,
            'stat_one_value' => $heroStats[0]['value'],
            'stat_one_label' => $heroStats[0]['label'],
            'stat_two_value' => $heroStats[1]['value'],
            'stat_two_label' => $heroStats[1]['label'],
            'stat_three_value' => $heroStats[2]['value'],
            'stat_three_label' => $heroStats[2]['label'],
            'about_us' => collect($this->faker->paragraphs(3))
                ->map(fn (string $paragraph) => "<p>{$paragraph}</p>")
                ->implode(''),
            'contact_email' => 'hello@absigns.test',
            'contact_phone' => '+1 (800) 555-0199',
            'address' => '1250 Meridian Way, Suite 300, Dallas, TX 75202',
            'google_map_embed' => '<iframe src="https://maps.google.com/maps?q=1250%20Meridian%20Way&t=&z=13&ie=UTF8&iwloc=&output=embed" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
        ];
    }
}



