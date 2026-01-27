<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($data) {

            $total = 0;

            $sale = Sale::create([
                'total' => 0, // se actualiza al final
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['id']);

                if ($product->stock < $item['qty']) {
                    abort(400, 'Stock insuficiente para ' . $product->name);
                }

                $subtotal = $product->price * $item['qty'];
                $total += $subtotal;

                SaleDetail::create([
                    'sale_id'   => $sale->id,
                    'product_id'=> $product->id,
                    'quantity'  => $item['qty'],
                    'price'     => $product->price,
                    'subtotal'  => $subtotal,
                ]);

                $product->decrement('stock', $item['qty']);
            }

            $sale->update(['total' => $total]);

            return response()->json([
                'message' => 'Venta registrada correctamente',
                'sale_id' => $sale->id,
                'total'   => $total
            ]);
        });
    }
}
