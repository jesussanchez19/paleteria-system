<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('id', 'desc')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'min:2'],
            'category'  => ['nullable', 'string', 'max:50'],
            'price'     => ['required', 'numeric', 'min:0'],
            'stock'     => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable'], // checkbox
        ]);

        $data['is_active'] = $request->boolean('is_active');

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'min:2'],
            'category'  => ['nullable', 'string', 'max:50'],
            'price'     => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Producto actualizado.');
    }
}
