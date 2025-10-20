<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin \App\Models\SeoEntry
 */
class SeoEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $meta = is_array($this->extra_meta) ? $this->extra_meta : [];

        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'canonical_url' => $this->canonical_url,
            'meta' => array_values(array_filter($meta, static function ($item) {
                if (! is_array($item) || ! isset($item['content'])) {
                    return false;
                }

                return ($item['name'] ?? $item['property'] ?? $item['http_equiv'] ?? null) !== null;
            })),
            'open_graph' => [
                'title' => $this->og_title,
                'description' => $this->og_description,
                'image_url' => $this->og_image_path ? Storage::url($this->og_image_path) : null,
            ],
            'twitter' => [
                'title' => $this->twitter_title,
                'description' => $this->twitter_description,
                'image_url' => $this->twitter_image_path ? Storage::url($this->twitter_image_path) : null,
            ],
        ];
    }
}
