<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * The storefront home page: hero slider, brand list, category list,
     * latest products, and featured products.
     */
    public function index(): Response
    {
        return Inertia::render('Home', [
            'sliders' => Slider::active()->orderBy('sort_order')->get(),
            'brands' => Brand::withCount('products')->orderBy('name')->get(),
            'categories' => Category::withCount('products')->orderBy('name')->get(),
            'latestProducts' => Product::with(['brand', 'category'])
                ->active()
                ->latest()
                ->take(8)
                ->get(),
            'featuredProducts' => Product::with(['brand', 'category'])
                ->active()
                ->featured()
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }
}
