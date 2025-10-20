<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeoEntry extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'canonical_url',
        'extra_meta',
        'og_title',
        'og_description',
        'og_image_path',
        'twitter_title',
        'twitter_description',
        'twitter_image_path',
    ];

    protected $casts = [
        'extra_meta' => 'array',
    ];
}
