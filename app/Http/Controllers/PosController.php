<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CashRegister;
use App\Models\Sale;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        // Obtener estado de caja
        $openRegister = CashRegister::getOpenRegister();
        $salesDuringShift = 0;
        $expectedAmount = 0;
        
        if ($openRegister) {
            $salesDuringShift = Sale::where('created_at', '>=', $openRegister->opened_at)->sum('total');
            $expectedAmount = (float)$openRegister->opening_amount + $salesDuringShift;
        }

        return view('pos.index', compact('products', 'openRegister', 'salesDuringShift', 'expectedAmount'));
    }
}
