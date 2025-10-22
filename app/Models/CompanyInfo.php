<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyInfo extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyInfoFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'site_name',
        'tagline',
        'logo_path',
        'hero_media_type',
        'hero_media_path',
        'hero_headline',
        'hero_subheadline',
        'hero_primary_cta_label',
        'hero_primary_cta_url',
        'hero_secondary_cta_label',
        'hero_secondary_cta_url',
        'stat_one_label',
        'stat_one_value',
        'stat_one_icon',
        'stat_two_label',
        'stat_two_value',
        'stat_two_icon',
        'stat_three_label',
        'stat_three_value',
        'stat_three_icon',
        'about_us',
        'contact_email',
        'contact_phone',
        'address',
        'google_map_embed',
    ];
}

