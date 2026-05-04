<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Seed a clean electronics-only catalog for demos (realistic brands + direct image URLs).
     */
    public function run(): void
    {
        $category = Category::query()->firstOrCreate(
            ['slug' => 'electronics'],
            [
                'name' => 'Electronics',
                'description' => 'Consumer electronics and accessories',
            ],
        );

        Product::query()
            ->where('category_id', '!=', $category->id)
            ->whereDoesntHave('orders')
            ->get()
            ->each(function (Product $p) {
                foreach ($p->images as $img) {
                    $img->forceDelete();
                }
                $p->forceDelete();
            });

        $items = [
            ['name' => 'Apple AirPods Pro (2nd Gen)', 'brand' => 'Apple', 'price' => 89999, 'discount' => 79999, 'stock' => 40, 'img' => 'https://images.pexels.com/photos/1649771/pexels-photo-1649771.jpeg?auto=compress&cs=tinysrgb&w=800'],
            ['name' => 'Samsung Galaxy Buds2 Pro', 'brand' => 'Samsung', 'price' => 45999, 'discount' => null, 'stock' => 55, 'img' => 'https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg?auto=compress&cs=tinysrgb&w=800'],
            ['name' => 'Sony WH-1000XM5 Headphones', 'brand' => 'Sony', 'price' => 129999, 'discount' => 114999, 'stock' => 22, 'img' => 'https://images.pexels.com/photos/3394651/pexels-photo-3394651.jpeg?auto=compress&cs=tinysrgb&w=800'],
            ['name' => 'Dell XPS 15 Laptop', 'brand' => 'Dell', 'price' => 349999, 'discount' => null, 'stock' => 12, 'img' => 'https://images.pexels.com/photos/18105/pexels-photo.jpg?auto=compress&cs=tinysrgb&w=800'],
            ['name' => 'LG UltraGear 27" Monitor', 'brand' => 'LG', 'price' => 67999, 'discount' => 59999, 'stock' => 30, 'img' => 'https://images.pexels.com/photos/777001/pexels-photo-777001.jpeg?auto=compress&cs=tinysrgb&w=800'],
            ['name' => 'Xiaomi Smart Band 8', 'brand' => 'Xiaomi', 'price' => 8999, 'discount' => null, 'stock' => 100, 'img' => 'https://images.pexels.com/photos/437037/pexels-photo-437037.jpeg?auto=compress&cs=tinysrgb&w=800'],
            ['name' => 'Apple iPad Air', 'brand' => 'Apple', 'price' => 189999, 'discount' => 174999, 'stock' => 18, 'img' => 'https://images.pexels.com/photos/1334597/pexels-photo-1334597.jpeg?auto=compress&cs=tinysrgb&w=800'],
            ['name' => 'Samsung Galaxy Tab S9', 'brand' => 'Samsung', 'price' => 159999, 'discount' => null, 'stock' => 15, 'img' => 'https://images.pexels.com/photos/163117/keyboard-full-keys-technology-163117.jpeg?auto=compress&cs=tinysrgb&w=800'],
            ['name' => 'Anker 737 Power Bank', 'brand' => 'Anker', 'price' => 14999, 'discount' => 12999, 'stock' => 80, 'img' => 'https://images.pexels.com/photos/163100/circuit-circuit-board-resistor-computer-163100.jpeg?auto=compress&cs=tinysrgb&w=800'],
            ['name' => 'Logitech MX Master 3S', 'brand' => 'Logitech', 'price' => 21999, 'discount' => null, 'stock' => 45, 'img' => 'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=800'],
        ];

        foreach ($items as $i => $row) {
            $slug = Str::slug($row['name']) . '-' . ($i + 1);

            $product = Product::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'name' => $row['name'],
                    'description' => $row['brand'] . ' ' . $row['name'] . ' — electronics demo listing with seeded reviews-ready description.',
                    'price' => $row['price'],
                    'discount_price' => $row['discount'],
                    'stock' => $row['stock'],
                    'is_active' => true,
                    'attributes' => [
                        'brand' => $row['brand'],
                        'warranty' => '1 year',
                    ],
                    'views' => random_int(50, 500) - $i * 5,
                ],
            );

            $product->images()->delete();
            $product->images()->create([
                'image_path' => $row['img'],
                'is_primary' => true,
            ]);
        }
    }
}
