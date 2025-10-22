<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyService extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyServiceFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'title',
        'description',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Company, CompanyService>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

