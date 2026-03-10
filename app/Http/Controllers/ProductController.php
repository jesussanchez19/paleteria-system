<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::orderBy('id', 'desc');
        
        if ($request->has('tipo') && in_array($request->tipo, ['menudeo', 'mayoreo'])) {
            $query->where('sale_type', $request->tipo);
        }
        
        $products = $query->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'min:2'],
            'category'    => ['nullable', 'string', 'max:50'],
            'sale_type'   => ['required', 'in:menudeo,mayoreo'],
            'pieces_per_package' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:500'],
            'price'       => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'is_active'   => ['nullable'],
            'image'       => ['nullable', 'string'],
            'image_file'  => ['nullable', 'image', 'max:2048'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        
        // Limpiar piezas si es menudeo
        if ($data['sale_type'] === 'menudeo') {
            $data['pieces_per_package'] = null;
        }

        // Si se sube imagen manualmente, guardarla
        if ($request->hasFile('image_file')) {
            $data['image'] = $request->file('image_file')->store('products', 'public');
        }

        $product = Product::create($data);

        audit_log('product.created', 'products', $product, [
            'nombre' => $product->name,
            'precio' => '$' . number_format($product->price, 2),
            'stock' => $product->stock,
            'categoría' => $product->category ?? 'Sin categoría',
        ]);

        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'min:2'],
            'category'    => ['nullable', 'string', 'max:50'],
            'sale_type'   => ['required', 'in:menudeo,mayoreo'],
            'pieces_per_package' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:500'],
            'price'       => ['required', 'numeric', 'min:0'],
            'is_active'   => ['nullable'],
            'image'       => ['nullable', 'string'],
            'image_file'  => ['nullable', 'image', 'max:2048'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        
        // Limpiar piezas si cambia a menudeo
        if ($data['sale_type'] === 'menudeo') {
            $data['pieces_per_package'] = null;
        }

        // Si se sube imagen manualmente, guardarla y eliminar anterior
        if ($request->hasFile('image_file')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image_file')->store('products', 'public');
        }
        // Si se proporciona nueva imagen generada, eliminar la anterior
        elseif (!empty($data['image']) && $data['image'] !== $product->image) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
        }

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
        if (!empty($data['image']) && $data['image'] !== $product->image) {
            $cambios['imagen'] = 'actualizada con IA';
        }

        $product->update($data);

        audit_log('product.updated', 'products', $product, $cambios ?: ['sin cambios' => 'ninguno']);

        return redirect()->route('products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        // Eliminar imagen si existe
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }

}
