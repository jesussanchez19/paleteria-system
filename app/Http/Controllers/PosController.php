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

        // Tiempo mínimo de caja en horas
        $minCashHours = (float) app_setting('min_cash_hours', '8');
        $canCloseCash = true;
        $hoursRemaining = 0;
        
        if ($openRegister && $minCashHours > 0) {
            $hoursOpen = $openRegister->opened_at->diffInMinutes(now()) / 60;
            $canCloseCash = $hoursOpen >= $minCashHours;
            $hoursRemaining = max(0, $minCashHours - $hoursOpen);
        }

        // Verificar si las ventas están habilitadas
        $salesEnabled = app_setting('sales_enabled', '1') === '1';

        return view('pos.index', compact('products', 'openRegister', 'salesDuringShift', 'expectedAmount', 'canCloseCash', 'hoursRemaining', 'salesEnabled'));
    }
}
