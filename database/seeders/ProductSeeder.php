<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * [slug, name, category slug, brand slug, price, discount price|null, stock, featured, description]
     *
     * The slug is listed explicitly (rather than derived from the name
     * with Str::slug()) so it stays pinned to the matching file under
     * public/images/products/ -- several names below include the brand
     * ("Samsung Galaxy S24 Ultra"), which would otherwise slug to
     * "samsung-galaxy-s24-ultra" and no longer match "galaxy-s24-ultra.jpg".
     *
     * @var list<array{0: string, 1: string, 2: string, 3: string, 4: float, 5: ?float, 6: int, 7: bool, 8: string}>
     */
    public const PRODUCTS = [
        ['iphone-15-pro-max', 'iPhone 15 Pro Max', 'electronics', 'apple', 149999, 139999, 25, true, 'Apple\'s flagship phone with a titanium frame, A17 Pro chip, and a pro-grade camera system.'],
        ['macbook-air-m3', 'MacBook Air M3', 'electronics', 'apple', 139999, null, 15, true, 'Thin, fanless, and fast -- the M3 MacBook Air handles everyday work and creative apps with ease.'],
        ['apple-watch-series-10', 'Apple Watch Series 10', 'electronics', 'apple', 42999, null, 30, false, 'A slimmer, brighter Apple Watch with advanced health and fitness tracking.'],
        ['airpods-pro-2', 'AirPods Pro 2', 'electronics', 'apple', 24999, 21999, 40, true, 'Active noise cancellation, adaptive audio, and all-day battery life in a compact case.'],
        ['magsafe-charger', 'MagSafe Charger', 'accessories', 'apple', 5499, null, 60, false, 'Snap-on wireless charging for iPhone, up to 15W.'],
        ['galaxy-s24-ultra', 'Samsung Galaxy S24 Ultra', 'electronics', 'samsung', 134999, 124999, 20, true, 'A 200MP camera, S Pen, and a bright 6.8" display built for productivity.'],
        ['galaxy-tab-s9', 'Samsung Galaxy Tab S9', 'electronics', 'samsung', 79999, null, 18, false, 'A flagship Android tablet with an AMOLED display and included S Pen.'],
        ['qled-4k-tv-55', 'Samsung 55" QLED 4K TV', 'electronics', 'samsung', 89999, null, 10, false, 'Quantum Dot color, 4K upscaling, and a slim design for your living room.'],
        ['inverter-microwave-oven', 'Samsung Inverter Microwave Oven', 'home-kitchen', 'samsung', 18999, null, 22, false, 'Even, efficient heating with inverter technology and easy-clean interior.'],
        ['redmi-note-13-pro', 'Xiaomi Redmi Note 13 Pro', 'electronics', 'xiaomi', 27999, null, 35, true, 'A 200MP camera and 120Hz AMOLED display at a genuinely affordable price.'],
        ['mi-band-8', 'Xiaomi Mi Band 8', 'electronics', 'xiaomi', 3499, null, 80, false, 'Lightweight fitness tracking with heart-rate, SpO2, and sleep monitoring.'],
        ['robot-vacuum-x10-plus', 'Xiaomi Robot Vacuum X10+', 'home-kitchen', 'xiaomi', 32999, 28999, 12, false, 'Self-emptying robot vacuum and mop with smart room mapping.'],
        ['smart-air-purifier-4', 'Xiaomi Smart Air Purifier 4', 'home-kitchen', 'xiaomi', 12999, null, 25, false, 'HEPA filtration with real-time air quality display and app control.'],
        ['air-max-270', 'Nike Air Max 270', 'footwear', 'nike', 12999, null, 28, true, 'A large Max Air unit underfoot for all-day comfort and street-ready style.'],
        ['revolution-6-running-shoes', 'Nike Revolution 6 Running Shoes', 'footwear', 'nike', 6499, null, 45, false, 'Lightweight cushioning built for everyday runs and casual wear.'],
        ['dri-fit-running-tee', 'Nike Dri-FIT Running Tee', 'fashion', 'nike', 2499, null, 60, false, 'Sweat-wicking fabric that keeps you dry and comfortable on the run.'],
        ['tech-fleece-hoodie', 'Nike Tech Fleece Hoodie', 'fashion', 'nike', 7999, 6999, 20, false, 'A soft, lightweight fleece hoodie that keeps its shape wash after wash.'],
        ['heritage-backpack', 'Nike Heritage Backpack', 'accessories', 'nike', 3999, null, 33, false, 'A durable everyday backpack with a padded laptop sleeve.'],
        ['football-pro-match-ball', 'Nike Football Pro Match Ball', 'sports-outdoor', 'nike', 3499, null, 40, false, 'A match-quality ball with a textured surface for consistent flight.'],
        ['ultraboost-22', 'Adidas Ultraboost 22', 'footwear', 'adidas', 15999, 13999, 18, true, 'Responsive Boost cushioning wrapped in a Primeknit upper.'],
        ['samba-og', 'Adidas Samba OG', 'footwear', 'adidas', 9999, null, 38, false, 'The classic low-top leather trainer that never really left.'],
        ['originals-trefoil-tee', 'Adidas Originals Trefoil Tee', 'fashion', 'adidas', 2299, null, 55, false, 'A soft cotton tee with the iconic Trefoil print.'],
        ['tiro-23-track-jacket', 'Adidas Tiro 23 Track Jacket', 'fashion', 'adidas', 5499, null, 26, false, 'A classic three-stripe track jacket built for training and travel.'],
        ['duffel-gym-bag', 'Adidas Duffel Gym Bag', 'accessories', 'adidas', 4499, null, 30, false, 'A spacious, durable duffel with a separate shoe compartment.'],
        ['training-yoga-mat', 'Adidas Training Yoga Mat', 'sports-outdoor', 'adidas', 2999, null, 50, true, 'A non-slip, cushioned mat for yoga, stretching, and floor workouts.'],
    ];

    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');
        $brands = Brand::pluck('id', 'slug');

        foreach (self::PRODUCTS as [$slug, $name, $categorySlug, $brandSlug, $price, $discountPrice, $stock, $featured, $description]) {
            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categories[$categorySlug],
                    'brand_id' => $brands[$brandSlug],
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'discount_price' => $discountPrice,
                    'image' => "/images/products/{$slug}.jpg",
                    'stock' => $stock,
                    'is_featured' => $featured,
                    'is_active' => true,
                ],
            );
        }
    }
}
