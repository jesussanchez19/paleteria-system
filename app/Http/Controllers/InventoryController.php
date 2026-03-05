<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function entry(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        $product->increment('stock', $data['quantity']);

        audit_log('inventory.entry', 'inventory', $product, [
            'producto' => $product->name,
            'cantidad_agregada' => '+' . $data['quantity'] . ' unidades',
            'stock_actual' => $product->fresh()->stock . ' unidades',
        ]);

        return back()->with('success', "Entrada registrada. Stock de '{$product->name}' +{$data['quantity']}.");
    }
}
