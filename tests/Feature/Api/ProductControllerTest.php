<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_active_products_with_expected_structure(): void
    {
        config(['filament.default_filesystem_disk' => 'public']);
        Storage::fake('public');

        $active = Product::factory()->create([
            'name' => 'Alpha Product',
            'description' => '<p>Bright Colors</p><script>alert(1)</script><strong>Only</strong>',
            'image' => 'products/alpha.png',
            'is_active' => true,
            'is_featured' => true,
        ]);

        Storage::disk('public')->put($active->image, 'fake image');

        Product::factory()->create([
            'name' => 'Inactive Product',
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/products');

        $response->assertOk();

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame($active->id, $data[0]['id']);
        $this->assertSame('Alpha Product', $data[0]['name']);

        $allowedTags = '<p><br><strong><em><ul><ol><li><a>';
        $expectedHtml = strip_tags($active->description, $allowedTags);
        $expectedPlain = trim(strip_tags($expectedHtml));

        $this->assertSame($expectedPlain, $data[0]['description']);
        $this->assertSame($expectedHtml, $data[0]['description_html']);
        $this->assertSame(Storage::disk('public')->url($active->image), $data[0]['image_url']);

        $this->assertSame(12, $response->json('meta.per_page'));
    }

    public function test_index_can_filter_by_featured_flag_and_search_term(): void
    {
        Product::factory()->create([
            'name' => 'Standard Banner',
            'is_active' => true,
            'is_featured' => false,
        ]);

        $featured = Product::factory()->create([
            'name' => 'Featured Banner Stand',
            'description' => '<p>Portable displays</p>',
            'is_active' => true,
            'is_featured' => true,
        ]);

        Product::factory()->create([
            'name' => 'Inactive Featured',
            'is_active' => false,
            'is_featured' => true,
        ]);

        $response = $this->getJson('/api/products?featured=1&search=banner');

        $response->assertOk();

        $ids = collect($response->json('data'))->pluck('id');
        $this->assertEquals([$featured->id], $ids->all());
    }

    public function test_index_sorts_by_latest_when_requested(): void
    {
        $older = Product::factory()->create([
            'name' => 'Classic Vinyl',
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
            'is_active' => true,
        ]);

        $newer = Product::factory()->create([
            'name' => 'Modern Vinyl',
            'created_at' => Carbon::now()->subDay(),
            'updated_at' => Carbon::now()->subDay(),
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/products?sort=created_at');

        $response->assertOk();

        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertSame(['Modern Vinyl', 'Classic Vinyl'], $names);
    }

    public function test_show_returns_single_product(): void
    {
        $product = Product::factory()->create([
            'name' => 'Illuminated Sign Cabinet',
            'description' => '<p>Durable aluminum body</p>',
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $product->id,
                'name' => 'Illuminated Sign Cabinet',
                'description_html' => '<p>Durable aluminum body</p>',
            ]);
    }
}
