<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_categories_with_active_product_counts(): void
    {
        $alpha = Category::factory()->create(['name' => 'Alpha Displays']);
        $beta = Category::factory()->create(['name' => 'Banner Systems']);

        Product::factory()->for($alpha)->create(['is_active' => true]);
        Product::factory()->for($alpha)->create(['is_active' => false]);

        Product::factory()->for($beta)->count(2)->create(['is_active' => true]);
        Product::factory()->for($beta)->create(['is_active' => false]);

        $response = $this->getJson('/api/categories');

        $response->assertOk();

        $data = $response->json('data');
        $this->assertSame(['Alpha Displays', 'Banner Systems'], array_column($data, 'name'));

        $counts = collect($data)->pluck('products_count', 'name');
        $this->assertSame(1, $counts['Alpha Displays']);
        $this->assertSame(2, $counts['Banner Systems']);
    }
}

