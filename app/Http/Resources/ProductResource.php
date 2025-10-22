<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Product
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $allowedTags = '<p><br><strong><em><ul><ol><li><a>'; // limited set for rich text
        $sanitizedHtml = $this->description
            ? strip_tags($this->description, $allowedTags)
            : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $sanitizedHtml ? trim(strip_tags($sanitizedHtml)) : null,
            'description_html' => $sanitizedHtml,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
