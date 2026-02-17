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
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        $product->increment('stock', $data['quantity']);

        return back()->with('success', "Entrada registrada. Stock de '{$product->name}' +{$data['quantity']}.");
    }
}
