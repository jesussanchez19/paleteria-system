<?php

namespace App\Http\Controllers;

use App\Models\Product;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('pos.index', compact('products'));
    }
}
