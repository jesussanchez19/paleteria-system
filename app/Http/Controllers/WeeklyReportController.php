<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WeeklyReportController extends Controller
{
    public function index(Request $request)
    {
        // Semana actual: lunes hasta hoy
        $start = now()->startOfWeek()->startOfDay(); // Lunes
        $end   = now()->endOfDay();                   // Hoy

        // Totales semanales
        $summary = DB::table('sales')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("
                COUNT(*) as num_sales,
                COALESCE(SUM(total),0) as total_sales,
                COALESCE(AVG(total),0) as avg_ticket
            ")
            ->first();

        // Top productos semanal (usando qty en lugar de quantity)
        $topProducts = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->groupBy('products.id', 'products.name')
            ->selectRaw("
                products.name,
                COALESCE(SUM(sale_details.qty),0) as qty,
                COALESCE(SUM(sale_details.subtotal),0) as total
            ")
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        return view('panel.reportes-semanal', compact('summary', 'topProducts', 'start', 'end'));
    }
}
