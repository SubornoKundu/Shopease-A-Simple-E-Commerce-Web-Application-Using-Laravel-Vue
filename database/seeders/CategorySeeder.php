<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * @var list<string>
     */
    public const CATEGORIES = [
        'Electronics',
        'Home & Kitchen',
        'Footwear',
        'Fashion',
        'Accessories',
        'Sports & Outdoor',
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $name) {
            $slug = Str::slug($name);

            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'image' => "/images/categories/{$slug}.jpg",
                ],
            );
        }
    }
}
