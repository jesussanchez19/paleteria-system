<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::insert([
            ['name' => 'Paleta de limón', 'price' => 15.00, 'stock' => 20, 'category' => 'Paleta', 'is_active' => true],
            ['name' => 'Paleta de fresa', 'price' => 15.00, 'stock' => 0, 'category' => 'Paleta', 'is_active' => true],
            ['name' => 'Paleta de chocolate', 'price' => 18.00, 'stock' => 12, 'category' => 'Paleta', 'is_active' => true],
            ['name' => 'Helado de vainilla', 'price' => 25.00, 'stock' => 10, 'category' => 'Helado', 'is_active' => true],
            ['name' => 'Agua de horchata', 'price' => 20.00, 'stock' => 8, 'category' => 'Agua', 'is_active' => true],
        ]);
    }
}
