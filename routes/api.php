<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// CRUD de Productos (sin autenticación para pruebas en Postman)
Route::prefix('productos')->group(function () {
    
    // Listar todos los productos
    Route::get('/', function () {
        return response()->json(Product::all());
    });

    // Obtener un producto por ID
    Route::get('/{id}', function ($id) {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        return response()->json($product);
    });

    // Crear un producto
    Route::post('/', function (Request $request) {
        $data = $request->validate([
            'name'      => ['required', 'string', 'min:2'],
            'price'     => ['required', 'numeric', 'min:0'],
            'stock'     => ['required', 'integer', 'min:0'],
            'category'  => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $product = Product::create($data);

        return response()->json($product, 201);
    });

    // Actualizar un producto
    Route::put('/{id}', function (Request $request, $id) {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $data = $request->validate([
            'name'      => ['sometimes', 'string', 'min:2'],
            'price'     => ['sometimes', 'numeric', 'min:0'],
            'stock'     => ['sometimes', 'integer', 'min:0'],
            'category'  => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $product->update($data);

        return response()->json($product);
    });

    // Eliminar un producto
    Route::delete('/{id}', function ($id) {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Producto eliminado']);
    });
});
