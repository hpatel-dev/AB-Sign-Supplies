<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['category', 'supplier'])
            ->active()
            ->search($request->string('search')->toString());

        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->integer('supplier'));
        }

        if ($request->filled('featured')) {
            $query->featured($request->boolean('featured'));
        }

        $query->when($request->input('sort', 'name') === 'created_at', function ($query) {
            $query->latest();
        }, function ($query) {
            $query->orderBy('name');
        });

        $perPage = (int) $request->integer('per_page', 12);
        $perPage = $perPage > 0 ? min($perPage, 50) : 12;

        return ProductResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }
}
