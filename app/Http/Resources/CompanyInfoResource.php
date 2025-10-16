<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\CompanyInfo
 */
class CompanyInfoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'about_us' => $this->about_us,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'address' => $this->address,
            'google_map_embed' => $this->google_map_embed,
        ];
    }
}
