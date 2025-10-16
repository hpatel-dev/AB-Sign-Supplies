<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyInfoResource;
use App\Models\CompanyInfo;

class CompanyController extends Controller
{
    public function show(): CompanyInfoResource
    {
        $company = CompanyInfo::query()->latest('updated_at')->firstOrFail();

        return new CompanyInfoResource($company);
    }
}
