<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CompanyProfileController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SeoController;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/company', [CompanyController::class, 'show']);
Route::get('/company-profiles', [CompanyProfileController::class, 'index']);
Route::get('/company-profiles/{company}', [CompanyProfileController::class, 'show']);
Route::get('/seo/{slug}', [SeoController::class, 'show']);
Route::post('/contact', [ContactController::class, 'store']);

