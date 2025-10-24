<?php

namespace Tests\Feature\Api;

use App\Models\Company;
use App\Models\CompanyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_companies_sorted_with_logo_urls(): void
    {
        config(['filesystems.default' => 'public']);
        Storage::fake('public');

        $displayPros = Company::factory()->create([
            'name' => 'Display Pros',
            'sort_order' => 0,
            'logo_path' => 'logos/display.png',
        ]);

        $alphaLighting = Company::factory()->create([
            'name' => 'Alpha Lighting',
            'sort_order' => 1,
            'logo_path' => 'logos/alpha.png',
        ]);

        $betaSignage = Company::factory()->create([
            'name' => 'Beta Signage',
            'sort_order' => 1,
            'logo_path' => null,
        ]);

        Storage::disk('public')->put($displayPros->logo_path, 'fake');
        Storage::disk('public')->put($alphaLighting->logo_path, 'fake');

        $response = $this->getJson('/api/company-profiles');

        $response->assertOk();

        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertSame(['Display Pros', 'Alpha Lighting', 'Beta Signage'], $names);

        $logoUrls = collect($response->json('data'))->pluck('logo_url', 'name');
        $this->assertSame(Storage::url($displayPros->logo_path), $logoUrls['Display Pros']);
        $this->assertSame(Storage::url($alphaLighting->logo_path), $logoUrls['Alpha Lighting']);
        $this->assertSame(asset('images/logo.svg'), $logoUrls['Beta Signage']);
    }

    public function test_show_returns_company_detail_with_services_and_contact_info(): void
    {
        config(['filesystems.default' => 'public']);
        Storage::fake('public');

        $company = Company::factory()->create([
            'name' => 'Summit Sign Studio',
            'slug' => 'summit-sign-studio',
            'logo_path' => 'logos/summit.png',
            'contact_email' => 'contact@summit.test',
            'contact_phone' => '800-123-4567',
            'address' => '100 Summit Way',
            'website' => 'https://summit.test',
        ]);

        CompanyService::factory()->for($company)->create([
            'title' => 'Design Consulting',
            'description' => 'Graphic layout expertise',
            'sort_order' => 1,
        ]);

        CompanyService::factory()->for($company)->create([
            'title' => 'Installation',
            'description' => 'Field installation services',
            'sort_order' => 0,
        ]);

        $response = $this->getJson("/api/company-profiles/{$company->slug}");

        $response->assertOk()
            ->assertJsonPath('slug', 'summit-sign-studio')
            ->assertJsonPath('contact.email', 'contact@summit.test')
            ->assertJsonPath('services.0.title', 'Installation')
            ->assertJsonPath('services.1.title', 'Design Consulting');
    }
}

