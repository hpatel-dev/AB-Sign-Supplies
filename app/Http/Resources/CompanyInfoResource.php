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
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        $siteName = trim((string) ($this->site_name ?? ''));
        $tagline = trim((string) ($this->tagline ?? ''));

        $logoUrl = $this->logo_path
            ? url(Storage::disk(config('filament.default_filesystem_disk', 'public'))->url($this->logo_path))
            : null;

        return [
            'site_name' => $siteName !== '' ? $siteName : 'AB Sign Supplies',
            'tagline' => $tagline !== '' ? $tagline : null,
            'logo_url' => $logoUrl,
            'hero' => [
                'headline' => $this->hero_headline ?: 'Your Complete Source for Signage Supplies',
                'subheadline' => $this->hero_subheadline
                    ?: 'AB Sign Supplies partners with leading manufacturers to deliver premium materials, hardware, and equipment for custom signage projects of every size.',
                'background' => $this->hero_media_path ? [
                    'type' => $this->hero_media_type ?: 'image',
                    'url' => url(Storage::disk(
                        config('filament.default_filesystem_disk', 'public')
                    )->url($this->hero_media_path)),
                ] : null,
                'primary_cta' => [
                    'label' => $this->hero_primary_cta_label ?: 'Shop Products',
                    'url' => $this->hero_primary_cta_url ?: '/products',
                ],
                'secondary_cta' => [
                    'label' => $this->hero_secondary_cta_label ?: 'Request a Quote',
                    'url' => $this->hero_secondary_cta_url ?: '/contact',
                ],
                'stats' => array_values(array_filter([
                    $this->stat_one_value || $this->stat_one_label ? [
                        'value' => $this->stat_one_value,
                        'label' => $this->stat_one_label,
                    ] : null,
                    $this->stat_two_value || $this->stat_two_label ? [
                        'value' => $this->stat_two_value,
                        'label' => $this->stat_two_label,
                    ] : null,
                    $this->stat_three_value || $this->stat_three_label ? [
                        'value' => $this->stat_three_value,
                        'label' => $this->stat_three_label,
                    ] : null,
                ])),
            ],
            'about_us' => $this->about_us,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'address' => $this->address,
            'google_map_embed' => $this->google_map_embed,
        ];
    }
}

