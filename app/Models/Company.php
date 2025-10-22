<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'logo_path',
        'summary',
        'overview',
        'contact_email',
        'contact_phone',
        'address',
        'website',
        'sort_order',
    ];

    /**
     * @return HasMany<CompanyService>
     */
    public function services(): HasMany
    {
        return $this->hasMany(CompanyService::class)->orderBy('sort_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (Company $company): void {
            if (! $company->slug) {
                $company->slug = Str::slug($company->name);
            }
        });
    }
}
