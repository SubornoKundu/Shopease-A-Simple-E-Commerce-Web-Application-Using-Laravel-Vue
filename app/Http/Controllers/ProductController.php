<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Product listing, filterable by brand, category, and a simple
     * name search -- e.g. /products?category=electronics&brand=apple.
     */
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'brand' => ['nullable', 'string'],
            'category' => ['nullable', 'string'],
            'q' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'in:latest,price_asc,price_desc,name'],
        ]);

        $products = Product::query()
            ->with(['brand', 'category'])
            ->active()
            ->when($filters['brand'] ?? null, fn ($query, $slug) => $query->whereHas(
                'brand',
                fn ($brand) => $brand->where('slug', $slug),
            ))
            ->when($filters['category'] ?? null, fn ($query, $slug) => $query->whereHas(
                'category',
                fn ($category) => $category->where('slug', $slug),
            ))
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(
                'name', 'like', '%'.$term.'%',
            ))
            ->when(
                ($filters['sort'] ?? null) === 'price_asc',
                fn ($query) => $query->orderByRaw('COALESCE(discount_price, price) asc'),
            )
            ->when(
                ($filters['sort'] ?? null) === 'price_desc',
                fn ($query) => $query->orderByRaw('COALESCE(discount_price, price) desc'),
            )
            ->when(
                ($filters['sort'] ?? null) === 'name',
                fn ($query) => $query->orderBy('name'),
            )
            ->when(
                ! isset($filters['sort']) || $filters['sort'] === 'latest',
                fn ($query) => $query->latest(),
            )
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'brands' => Brand::orderBy('name')->get(['id', 'name', 'slug']),
            'categories' => Category::orderBy('name')->get(['id', 'name', 'slug']),
            'filters' => [
                'brand' => $filters['brand'] ?? null,
                'category' => $filters['category'] ?? null,
                'q' => $filters['q'] ?? null,
                'sort' => $filters['sort'] ?? 'latest',
            ],
        ]);
    }

    /**
     * A single product's detail page, plus a small "related products"
     * rail from the same category.
     */
    public function show(Product $product): Response
    {
        abort_unless($product->is_active, 404);

        $product->load(['brand', 'category']);

        $related = Product::with(['brand', 'category'])
            ->active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->take(4)
            ->get();

        return Inertia::render('Products/Show', [
            'product' => $product,
            'relatedProducts' => $related,
        ]);
    }
}
