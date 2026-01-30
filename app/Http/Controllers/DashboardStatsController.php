<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardStatsController extends Controller
{
    public function index()
    {
        $today = now()->toDateString(); // YYYY-MM-DD

        /**
         * 1) Ventas por hora (hoy)
         */
        $salesByHour = DB::table('sales')
            ->whereDate('created_at', $today)
            ->selectRaw("to_char(created_at, 'HH24') as hour, COUNT(*) as qty, COALESCE(SUM(total),0) as total")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        /**
         * 2) Top productos por cantidad (hoy)
         */
        $topProducts = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereDate('sales.created_at', $today)
            ->groupBy('products.id', 'products.name')
            ->selectRaw('products.name, COALESCE(SUM(sale_details.qty),0) as qty, COALESCE(SUM(sale_details.subtotal),0) as total')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        /**
         * 3) Ventas totales por día (últimos 7 días)
         */
        $salesLast7Days = DB::table('sales')
            ->whereDate('created_at', '>=', now()->subDays(6)->toDateString())
            ->selectRaw("to_char(created_at::date, 'YYYY-MM-DD') as day, COUNT(*) as qty, COALESCE(SUM(total),0) as total")
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        /**
         * 4) Ingresos por categoría (hoy)
         */
        $revenueByCategory = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereDate('sales.created_at', $today)
            ->groupBy('products.category')
            ->selectRaw("COALESCE(products.category,'Sin categoría') as category, COALESCE(SUM(sale_details.subtotal),0) as total")
            ->orderByDesc('total')
            ->get();

        /**
         * 5) Productos sin stock (hoy) (realmente es stock actual)
         */
        $outOfStock = DB::table('products')
            ->where('stock', '<=', 0)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'category', 'stock']);

        /**
         * 6) Ventas por vendedor (hoy)
         * Nota: asumo que sales tiene user_id (lo usual en tu proyecto).
         * Si tu columna se llama distinto, me lo dices y lo ajusto.
         */
        $salesBySeller = DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->whereDate('sales.created_at', $today)
            ->groupBy('users.id', 'users.name')
            ->selectRaw("users.name, COUNT(sales.id) as qty, COALESCE(SUM(sales.total),0) as total")
            ->orderByDesc('total')
            ->get();

        /**
         * 7) Ticket promedio por día (últimos 7 días) (plus)
         */
        $avgTicketLast7Days = DB::table('sales')
            ->whereDate('created_at', '>=', now()->subDays(6)->toDateString())
            ->selectRaw("to_char(created_at::date, 'YYYY-MM-DD') as day, COALESCE(AVG(total),0) as avg_total")
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        /**
         * 8) Comparación Hoy vs Ayer (plus)
         */
        $yesterday = now()->subDay()->toDateString();

        $todayTotal = (float) DB::table('sales')->whereDate('created_at', $today)->sum('total');
        $yesterdayTotal = (float) DB::table('sales')->whereDate('created_at', $yesterday)->sum('total');

        $todayQty = (int) DB::table('sales')->whereDate('created_at', $today)->count();
        $yesterdayQty = (int) DB::table('sales')->whereDate('created_at', $yesterday)->count();

        $compare = [
            'today' => ['date' => $today, 'total' => $todayTotal, 'qty' => $todayQty],
            'yesterday' => ['date' => $yesterday, 'total' => $yesterdayTotal, 'qty' => $yesterdayQty],
        ];

        return view('reportes.graficas', compact(
            'today',
            'salesByHour',
            'topProducts',
            'salesLast7Days',
            'revenueByCategory',
            'outOfStock',
            'salesBySeller',
            'avgTicketLast7Days',
            'compare'
        ));
    }
}
