<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin \App\Models\CompanyInfo
 */
class CompanyInfoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $siteName = trim((string) ($this->site_name ?? ''));
        $tagline = trim((string) ($this->tagline ?? ''));

        return [
            'site_name' => $siteName !== '' ? $siteName : 'AB Sign Supplies',
            'tagline' => $tagline !== '' ? $tagline : null,
            'logo_url' => $this->logo_path ? Storage::url($this->logo_path) : null,
            'about_us' => $this->about_us,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'address' => $this->address,
            'google_map_embed' => $this->google_map_embed,
        ];
    }
}
