<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin \App\Models\Company
 */
class CompanyDetailResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'tagline' => $this->tagline,
            'summary' => $this->summary,
            'overview' => $this->overview,
            'logo_url' => $this->logo_path ? Storage::url($this->logo_path) : asset('images/logo.svg'),
            'contact' => [
                'email' => $this->contact_email,
                'phone' => $this->contact_phone,
                'address' => $this->address,
                'website' => $this->website,
            ],
            'services' => $this->whenLoaded('services', function () {
                return $this->services->map(fn ($service) => [
                    'title' => $service->title,
                    'description' => $service->description,
                ]);
            }),
        ];
    }
}

