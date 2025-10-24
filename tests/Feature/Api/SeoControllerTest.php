<?php

namespace Tests\Feature\Api;

use App\Models\SeoEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_returns_seo_entry_with_filtered_meta(): void
    {
        config(['filesystems.default' => 'public']);
        Storage::fake('public');

        $entry = SeoEntry::create([
            'slug' => 'homepage',
            'title' => 'Home | AB Sign Supplies',
            'description' => 'Welcome to AB Sign Supplies.',
            'extra_meta' => [
                ['name' => 'robots', 'content' => 'index,follow'],
                ['property' => 'og:type', 'content' => 'website'],
                ['name' => 'invalid'],
                ['http_equiv' => 'Content-Type'],
            ],
            'og_image_path' => 'seo/home-og.png',
            'twitter_image_path' => 'seo/home-twitter.png',
        ]);

        Storage::disk('public')->put('seo/home-og.png', 'fake');
        Storage::disk('public')->put('seo/home-twitter.png', 'fake');

        $response = $this->getJson('/api/seo/homepage');

        $response->assertOk()
            ->assertJsonPath('data.slug', 'homepage')
            ->assertJsonPath('data.meta.0.name', 'robots')
            ->assertJsonPath('data.meta.0.content', 'index,follow')
            ->assertJsonPath('data.meta.1.property', 'og:type')
            ->assertJsonPath('data.meta.1.content', 'website')
            ->assertJsonPath('data.open_graph.image_url', Storage::url('seo/home-og.png'))
            ->assertJsonPath('data.twitter.image_url', Storage::url('seo/home-twitter.png'));

        $this->assertCount(2, $response->json('data.meta'));
    }

    public function test_show_returns_null_when_entry_is_missing(): void
    {
        $response = $this->getJson('/api/seo/unknown-page');

        $response->assertOk();
        $this->assertSame([], $response->json());
    }
}
