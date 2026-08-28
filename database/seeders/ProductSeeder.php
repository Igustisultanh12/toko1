<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create(['name' => 'Indomie Goreng', 'barcode' => '8992761132333', 'stock' => 100, 'price' => 3000]);
        Product::create(['name' => 'Teh Botol Sosro 250ml', 'barcode' => '8999909030027', 'stock' => 50, 'price' => 3500]);
        Product::create(['name' => 'Chitato Sapi Panggang', 'barcode' => '8992741950012', 'stock' => 75, 'price' => 11000, 'discount_percent' => 10]);
        Product::create(['name' => 'Produk Tes Manual', 'barcode' => '1234567890123', 'stock' => 20, 'price' => 50000]);
    }
}
