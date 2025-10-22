<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyDetailResource;
use App\Http\Resources\CompanySummaryResource;
use App\Models\Company;

class CompanyProfileController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection<CompanySummaryResource>
     */
    public function index()
    {
        $companies = Company::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return CompanySummaryResource::collection($companies);
    }

    public function show(Company $company): CompanyDetailResource
    {
        $company->load('services');

        return new CompanyDetailResource($company);
    }
}

