<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeoEntryResource;
use App\Models\SeoEntry;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $seoEntry = SeoEntry::query()->where('slug', $slug)->first();

        if (! $seoEntry) {
            return response()->json(null);
        }

        return SeoEntryResource::make($seoEntry);
    }
}
