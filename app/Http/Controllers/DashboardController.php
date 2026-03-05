<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ventas últimos 7 días
        $sales = Sale::select(
            DB::raw("DATE(created_at) as date"),
            DB::raw("SUM(total) as total"),
            DB::raw("COUNT(*) as transacciones")
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Rellenar días sin ventas con 0
        $salesByDate = $sales->keyBy('date');
        $completeSales = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $completeSales->push([
                'date' => $date,
                'day_name' => Carbon::parse($date)->locale('es')->isoFormat('ddd'),
                'total' => $salesByDate->get($date)?->total ?? 0,
                'transacciones' => $salesByDate->get($date)?->transacciones ?? 0,
            ]);
        }

        // Top 5 productos más vendidos
        $topProducts = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(sale_details.qty) as qty'),
                DB::raw('SUM(sale_details.subtotal) as revenue')
            )
            ->where('sale_details.created_at', '>=', now()->subDays(30))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        // Ventas por categoría
        $salesByCategory = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select(
                'products.category',
                DB::raw('SUM(sale_details.qty) as qty'),
                DB::raw('SUM(sale_details.subtotal) as revenue')
            )
            ->whereNotNull('products.category')
            ->where('sale_details.created_at', '>=', now()->subDays(30))
            ->groupBy('products.category')
            ->orderByDesc('revenue')
            ->get();

        // Predicción simple (promedio últimos 7 días)
        $avgLast7Days = $completeSales->avg('total');
        
        // Predicción mejorada: considerar tendencia
        $firstHalf = $completeSales->take(3)->avg('total');
        $secondHalf = $completeSales->skip(4)->avg('total');
        $trend = $secondHalf > 0 ? ($secondHalf - $firstHalf) / max($firstHalf, 1) : 0;
        
        // Predicción = promedio + ajuste por tendencia
        $prediction = $avgLast7Days * (1 + ($trend * 0.3));
        $prediction = max(0, $prediction); // No negativo

        // Estadísticas rápidas
        $stats = [
            'ventas_hoy' => Sale::whereDate('created_at', today())->sum('total'),
            'ventas_ayer' => Sale::whereDate('created_at', today()->subDay())->sum('total'),
            'transacciones_hoy' => Sale::whereDate('created_at', today())->count(),
            'ticket_promedio' => Sale::whereDate('created_at', today())->avg('total') ?? 0,
            'productos_bajo_stock' => Product::where('is_active', true)
                ->where('stock', '<=', (int) app_setting('low_stock_threshold', 5))
                ->count(),
        ];

        // Cambio porcentual vs ayer
        $stats['cambio_vs_ayer'] = $stats['ventas_ayer'] > 0
            ? round((($stats['ventas_hoy'] - $stats['ventas_ayer']) / $stats['ventas_ayer']) * 100, 1)
            : 0;

        // Ventas por hora (hoy)
        $salesByHour = Sale::select(
            DB::raw("EXTRACT(HOUR FROM created_at) as hora"),
            DB::raw("SUM(total) as total"),
            DB::raw("COUNT(*) as transacciones")
        )
            ->whereDate('created_at', today())
            ->groupBy('hora')
            ->orderBy('hora')
            ->get()
            ->keyBy('hora');

        // Completar horas del día
        $hourlyData = collect();
        for ($h = 8; $h <= 20; $h++) {
            $hourlyData->push([
                'hora' => sprintf('%02d:00', $h),
                'total' => $salesByHour->get($h)?->total ?? 0,
                'transacciones' => $salesByHour->get($h)?->transacciones ?? 0,
            ]);
        }

        return view('panel.dashboard', compact(
            'completeSales',
            'topProducts',
            'salesByCategory',
            'prediction',
            'stats',
            'hourlyData'
        ));
    }
}
