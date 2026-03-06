<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::insert([
            ['name' => 'Paleta de limón',      'price' => 15.00, 'stock' => 50, 'category' => 'Paleta', 'is_active' => true],
            ['name' => 'Paleta de fresa',      'price' => 15.00, 'stock' => 40, 'category' => 'Paleta', 'is_active' => true],
            ['name' => 'Paleta de chocolate',  'price' => 18.00, 'stock' => 35, 'category' => 'Paleta', 'is_active' => true],
            ['name' => 'Paleta de mango',      'price' => 15.00, 'stock' => 30, 'category' => 'Paleta', 'is_active' => true],
            ['name' => 'Paleta de tamarindo',  'price' => 18.00, 'stock' => 25, 'category' => 'Paleta', 'is_active' => true],
            ['name' => 'Helado de vainilla',   'price' => 25.00, 'stock' => 30, 'category' => 'Helado', 'is_active' => true],
            ['name' => 'Helado de chocolate',  'price' => 25.00, 'stock' => 25, 'category' => 'Helado', 'is_active' => true],
            ['name' => 'Helado de fresa',      'price' => 25.00, 'stock' => 20, 'category' => 'Helado', 'is_active' => true],
            ['name' => 'Agua de horchata',     'price' => 20.00, 'stock' => 35, 'category' => 'Agua',   'is_active' => true],
            ['name' => 'Agua de jamaica',      'price' => 20.00, 'stock' => 30, 'category' => 'Agua',   'is_active' => true],
            ['name' => 'Agua de limón',        'price' => 18.00, 'stock' => 40, 'category' => 'Agua',   'is_active' => true],
            ['name' => 'Bolis de mango',       'price' => 10.00, 'stock' => 60, 'category' => 'Bolis',  'is_active' => true],
            ['name' => 'Bolis de fresa',       'price' => 10.00, 'stock' => 55, 'category' => 'Bolis',  'is_active' => true],
        ]);
    }
}
