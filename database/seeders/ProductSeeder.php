<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shop\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing products first (handle foreign key constraints)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $products = [
            [
                'name' => 'MacBook Pro 14" M3',
                'description' => 'Supercharged by M3 chip with 8-core CPU and 10-core GPU. 16GB unified memory, 512GB SSD storage. Perfect for professionals and creatives.',
                'price' => 1999.99,
                'currency' => 'EUR',
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&h=300&fit=crop&crop=center',
                'status' => 'active'
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'description' => 'Titanium design with A17 Pro chip. ProRAW photography, Action button, and 5x telephoto camera. Available in 256GB natural titanium.',
                'price' => 1199.99,
                'currency' => 'EUR',
                'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&h=300&fit=crop&crop=center',
                'status' => 'active'
            ],
            [
                'name' => 'Sony WH-1000XM5 Headphones',
                'description' => 'Industry-leading noise canceling with premium sound quality. 30-hour battery life, multipoint connection, and adaptive sound control.',
                'price' => 399.99,
                'currency' => 'EUR',
                'image' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=300&fit=crop&crop=center',
                'status' => 'active'
            ],
            [
                'name' => 'Samsung 65" OLED 4K TV',
                'description' => 'Quantum Dot technology with AI-powered 4K upscaling. Smart TV with Tizen OS, HDR10+, and gaming mode for next-gen consoles.',
                'price' => 1899.99,
                'currency' => 'EUR',
                'image' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=400&h=300&fit=crop&crop=center',
                'status' => 'active'
            ],
            [
                'name' => 'Nintendo Switch OLED',
                'description' => 'Vibrant 7-inch OLED screen gaming console. Enhanced audio, improved kickstand, and 64GB internal storage. Includes dock and Joy-Con controllers.',
                'price' => 349.99,
                'currency' => 'EUR',
                'image' => 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=400&h=300&fit=crop&crop=center',
                'status' => 'active'
            ],
            [
                'name' => 'iPad Pro 12.9" M2',
                'description' => 'Liquid Retina XDR display with M2 chip performance. Apple Pencil and Magic Keyboard compatible. 256GB storage with Wi-Fi + Cellular.',
                'price' => 1299.99,
                'currency' => 'EUR',
                'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=300&fit=crop&crop=center',
                'status' => 'active'
            ],
            [
                'name' => 'AirPods Pro 2nd Gen',
                'description' => 'Next-level active noise cancellation with adaptive transparency. H2 chip, personalized spatial audio, and precision finding capabilities.',
                'price' => 279.99,
                'currency' => 'EUR',
                'image' => 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=400&h=300&fit=crop&crop=center',
                'status' => 'active'
            ],
            [
                'name' => 'Canon EOS R6 Mark II',
                'description' => 'Full-frame mirrorless camera with 24.2MP sensor. 4K 60p video recording, in-body image stabilization, and dual pixel autofocus.',
                'price' => 2499.99,
                'currency' => 'EUR',
                'image' => 'https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?w=400&h=300&fit=crop&crop=center',
                'status' => 'active'
            ],
            [
                'name' => 'Tesla Model 3 Accessories Kit',
                'description' => 'Complete accessory bundle including premium floor mats, center console organizer, phone mount, and charging cable management.',
                'price' => 299.99,
                'currency' => 'EUR',
                'image' => 'https://images.unsplash.com/photo-1560958089-b8a1929cea89?w=400&h=300&fit=crop&crop=center',
                'status' => 'active'
            ],
            [
                'name' => 'Apple Watch Ultra 2',
                'description' => 'Rugged titanium smartwatch with precision dual-frequency GPS. Action button, 36-hour battery life, and 49mm display. Perfect for adventure.',
                'price' => 799.99,
                'currency' => 'EUR',
                'image' => 'https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?w=400&h=300&fit=crop&crop=center',
                'status' => 'active'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
