<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(
        protected CloudinaryService $cloudinary
    ) {}

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

        // Si se sube imagen manualmente
        if ($request->hasFile('image_file')) {
            $imageData = $this->uploadImage($request->file('image_file'));
            $data['image'] = $imageData['url'];
            $data['cloudinary_public_id'] = $imageData['public_id'];
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

        // Si se sube imagen manualmente
        if ($request->hasFile('image_file')) {
            // Eliminar imagen anterior
            $this->deleteProductImage($product);
            
            // Subir nueva imagen
            $imageData = $this->uploadImage($request->file('image_file'));
            $data['image'] = $imageData['url'];
            $data['cloudinary_public_id'] = $imageData['public_id'];
        }
        // Si se proporciona nueva imagen generada (URL externa)
        elseif (!empty($data['image']) && $data['image'] !== $product->image) {
            $this->deleteProductImage($product);
            $data['cloudinary_public_id'] = null; // URL externa, no es de Cloudinary
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
            $cambios['imagen'] = 'actualizada';
        }

        $product->update($data);

        audit_log('product.updated', 'products', $product, $cambios ?: ['sin cambios' => 'ninguno']);

        return redirect()->route('products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        // Eliminar imagen si existe
        $this->deleteProductImage($product);
        
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Subir imagen a Cloudinary o storage local
     */
    private function uploadImage($file): array
    {
        // Intentar subir a Cloudinary si está configurado
        if (CloudinaryService::isConfigured()) {
            return $this->cloudinary->uploadImage($file, 'products');
        }
        
        // Fallback a storage local
        $path = $file->store('products', 'public');
        return [
            'url' => $path,
            'public_id' => null,
        ];
    }

    /**
     * Eliminar imagen del producto (Cloudinary o local)
     */
    private function deleteProductImage(Product $product): void
    {
        if (!$product->image) {
            return;
        }

        // Si tiene public_id de Cloudinary, eliminar de Cloudinary
        if ($product->cloudinary_public_id) {
            $this->cloudinary->deleteImage($product->cloudinary_public_id);
        }
        // Si es imagen local (no empieza con http), eliminar del storage
        elseif (!str_starts_with($product->image, 'http')) {
            Storage::disk('public')->delete($product->image);
        }
    }
}
