<?php

namespace Tests\Feature\Api;

use App\Models\CompanyInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_returns_latest_company_profile_with_defaults(): void
    {
        CompanyInfo::factory()->create([
            'site_name' => 'Old Site Name',
            'updated_at' => now()->subDay(),
        ]);

        $latest = CompanyInfo::factory()->create([
            'site_name' => 'New Brand',
            'tagline' => null,
            'hero_headline' => null,
            'hero_primary_cta_label' => null,
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/company');

        $response->assertOk()
            ->assertJsonPath('site_name', 'New Brand')
            ->assertJsonPath('tagline', null)
            ->assertJsonPath('hero.headline', 'Your Complete Source for Signage Supplies')
            ->assertJsonPath('hero.primary_cta.label', 'Shop Products')
            ->assertJsonPath('hero.secondary_cta.url', '/contact');

        $this->assertSame($latest->id, CompanyInfo::latest('updated_at')->first()->id);
    }
}

