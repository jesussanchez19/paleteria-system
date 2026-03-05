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
            'name' => ['required', 'string', 'min:2'],
            'category' => ['nullable', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable'], // checkbox
        ]);

        $data['is_active'] = $request->boolean('is_active');

<<<<<<< HEAD
        Product::create($data);
=======
        $product = Product::create($data);

        audit_log('product.created', 'products', $product, [
            'nombre' => $product->name,
            'precio' => '$' . number_format($product->price, 2),
            'stock' => $product->stock,
            'categoría' => $product->category ?? 'Sin categoría',
        ]);

>>>>>>> origin/dev
        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2'],
            'category' => ['nullable', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        // Capturar cambios antes de actualizar
        $cambios = [];
        if ($product->name !== $data['name']) {
            $cambios['nombre'] = $product->name . ' → ' . $data['name'];
        }
        if ((float)$product->price !== (float)$data['price']) {
            $cambios['precio'] = '$' . number_format($product->price, 2) . ' → $' . number_format($data['price'], 2);
        }
        if (($product->category ?? '') !== ($data['category'] ?? '')) {
            $cambios['categoría'] = ($product->category ?? 'ninguna') . ' → ' . ($data['category'] ?? 'ninguna');
        }
        if ((bool)$product->is_active !== $data['is_active']) {
            $cambios['estado'] = ($product->is_active ? 'activo' : 'inactivo') . ' → ' . ($data['is_active'] ? 'activo' : 'inactivo');
        }

        $product->update($data);
<<<<<<< HEAD
=======

        audit_log('product.updated', 'products', $product, $cambios ?: ['sin cambios' => 'ninguno']);

>>>>>>> origin/dev
        return redirect()->route('products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }
}
