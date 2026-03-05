<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\WeatherSnapshot;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(GeminiService $gemini)
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

        // ============================================
        // DATOS PARA IA
        // ============================================

        // Clima actual
        $city = app_setting('business_city', 'Mexico City');
        $weather = WeatherSnapshot::where('date', now()->toDateString())
            ->where('city', $city)
            ->first();

        // Producto estrella (más vendido hoy)
        $productoEstrella = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->whereDate('sales.created_at', today())
            ->select('products.name', DB::raw('SUM(sale_details.qty) as qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->first();

        // Productos sin stock
        $sinStock = Product::where('stock', 0)->where('is_active', true)->pluck('name')->toArray();

        // Productos con stock bajo
        $stockBajo = Product::where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->where('is_active', true)
            ->select('name', 'stock')
            ->get();

        // Mejor horario del día (basado en histórico)
        $mejorHora = Sale::select(
            DB::raw("EXTRACT(HOUR FROM created_at) as hora"),
            DB::raw("SUM(total) as total")
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('hora')
            ->orderByDesc('total')
            ->first();

        // Día de mañana
        $mananaDia = now()->addDay()->locale('es')->isoFormat('dddd');

        // Construir alertas inteligentes
        $alertas = $this->generarAlertas($sinStock, $stockBajo, $topProducts, $weather);

        // Construir recomendaciones
        $recomendaciones = $this->generarRecomendaciones($weather, $stockBajo, $sinStock, $mejorHora);

        // Generar resumen IA (con cache de 5 minutos para no sobrecargar la API)
        $resumenIA = Cache::remember('dashboard_ia_resumen_' . today()->format('Y-m-d-H'), 300, function () use ($gemini, $stats, $productoEstrella, $weather, $sinStock, $stockBajo) {
            return $this->generarResumenIA($gemini, $stats, $productoEstrella, $weather, $sinStock, $stockBajo);
        });

        // Contexto de predicción
        $prediccionContexto = $this->generarContextoPrediccion($prediction, $mananaDia, $weather, $trend);

        return view('panel.dashboard', compact(
            'completeSales',
            'topProducts',
            'salesByCategory',
            'prediction',
            'stats',
            'hourlyData',
            'weather',
            'productoEstrella',
            'alertas',
            'recomendaciones',
            'resumenIA',
            'prediccionContexto'
        ));
    }

    private function generarAlertas($sinStock, $stockBajo, $topProducts, $weather): array
    {
        $alertas = [];

        // Alerta de productos agotados
        if (count($sinStock) > 0) {
            $alertas[] = [
                'tipo' => 'danger',
                'icono' => '🚨',
                'titulo' => count($sinStock) . ' producto(s) agotado(s)',
                'mensaje' => implode(', ', array_slice($sinStock, 0, 3)) . (count($sinStock) > 3 ? '...' : ''),
            ];
        }

        // Alerta de stock bajo en productos populares
        $topNames = $topProducts->pluck('name')->toArray();
        $popularesConStockBajo = $stockBajo->filter(fn($p) => in_array($p->name, $topNames));
        if ($popularesConStockBajo->isNotEmpty()) {
            $alertas[] = [
                'tipo' => 'warning',
                'icono' => '⚠️',
                'titulo' => 'Productos populares con stock bajo',
                'mensaje' => $popularesConStockBajo->map(fn($p) => "{$p->name} ({$p->stock} uds)")->implode(', '),
            ];
        }

        // Alerta por clima caluroso
        if ($weather && $weather->temp >= 30) {
            $alertas[] = [
                'tipo' => 'info',
                'icono' => '🔥',
                'titulo' => 'Día muy caluroso (' . $weather->temp . '°C)',
                'mensaje' => 'Se esperan ventas altas de helados. Verifica stock de paletas y aguas.',
            ];
        }

        return $alertas;
    }

    private function generarRecomendaciones($weather, $stockBajo, $sinStock, $mejorHora): array
    {
        $recs = [];

        // Recomendación por clima
        if ($weather) {
            if ($weather->temp >= 30) {
                $recs[] = [
                    'icono' => '🍦',
                    'texto' => 'Aumentar producción de paletas (clima caluroso)',
                ];
            } elseif ($weather->temp >= 25) {
                $recs[] = [
                    'icono' => '🥤',
                    'texto' => 'Preparar más bebidas frías',
                ];
            } elseif ($weather->temp <= 20) {
                $recs[] = [
                    'icono' => '📉',
                    'texto' => 'Día fresco: posible baja en ventas de helados',
                ];
            }
        }

        // Recomendación de reposición
        if (count($sinStock) > 0) {
            $recs[] = [
                'icono' => '📦',
                'texto' => 'Reponer urgente: ' . implode(', ', array_slice($sinStock, 0, 3)),
            ];
        }

        if ($stockBajo->isNotEmpty()) {
            $recs[] = [
                'icono' => '⚠️',
                'texto' => 'Stock bajo: ' . $stockBajo->take(3)->pluck('name')->implode(', '),
            ];
        }

        // Mejor horario
        if ($mejorHora) {
            $hora = sprintf('%02d:00 - %02d:00', $mejorHora->hora, $mejorHora->hora + 2);
            $recs[] = [
                'icono' => '⏰',
                'texto' => "Mejor horario de ventas: {$hora}",
            ];
        }

        return $recs;
    }

    private function generarResumenIA($gemini, $stats, $productoEstrella, $weather, $sinStock, $stockBajo): string
    {
        $temp = $weather?->temp ?? 'desconocido';
        $condicion = $weather?->condition ?? 'desconocido';
        $estrella = $productoEstrella?->name ?? 'ninguno aún';
        $estrellaQty = $productoEstrella?->qty ?? 0;
        $sinStockTxt = count($sinStock) > 0 ? implode(', ', $sinStock) : 'ninguno';
        $stockBajoTxt = $stockBajo->isNotEmpty() ? $stockBajo->map(fn($p) => "{$p->name}:{$p->stock}")->implode(', ') : 'ninguno';

        $prompt = "
Eres el asistente IA de una paletería. Genera un RESUMEN EJECUTIVO muy breve (máximo 3 oraciones) del estado actual del negocio.

DATOS:
- Ventas hoy: \${$stats['ventas_hoy']} MXN en {$stats['transacciones_hoy']} transacciones
- Ticket promedio: \${$stats['ticket_promedio']}
- Producto estrella hoy: {$estrella} ({$estrellaQty} vendidos)
- Clima: {$temp}°C, {$condicion}
- Sin stock: {$sinStockTxt}
- Stock bajo: {$stockBajoTxt}

Responde en español, de forma directa y profesional. Empieza con un saludo según la hora (buenos días/tardes).
Incluye: 1) estado de ventas, 2) producto destacado, 3) una recomendación clave.
";

        return $gemini->ask($prompt);
    }

    private function generarContextoPrediccion($prediction, $mananaDia, $weather, $trend): string
    {
        $razones = [];

        // Razón por día de la semana
        $diasFuertes = ['viernes', 'sábado', 'domingo'];
        if (in_array(strtolower($mananaDia), $diasFuertes)) {
            $razones[] = "mañana es {$mananaDia} (día fuerte)";
        }

        // Razón por clima
        if ($weather && $weather->temp >= 28) {
            $razones[] = "clima caluroso favorece ventas";
        }

        // Razón por tendencia
        if ($trend > 0.1) {
            $razones[] = "tendencia alcista esta semana";
        } elseif ($trend < -0.1) {
            $razones[] = "tendencia a la baja esta semana";
        }

        if (empty($razones)) {
            return "Basado en promedio de ventas recientes";
        }

        return "Porque " . implode(', ', $razones);
    }

    /**
     * Pregunta rápida desde el dashboard (AJAX)
     */
    public function askQuick(Request $request, GeminiService $gemini)
    {
        $request->validate(['question' => 'required|string|max:200']);

        // Obtener datos del negocio
        $ventasHoy = Sale::whereDate('created_at', today())->sum('total');
        $transacciones = Sale::whereDate('created_at', today())->count();
        
        $topProducto = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->whereDate('sales.created_at', today())
            ->select('products.name', DB::raw('SUM(sale_details.qty) as qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->first();

        $sinStock = Product::where('stock', 0)->where('is_active', true)->pluck('name')->implode(', ') ?: 'Ninguno';
        $stockBajo = Product::where('stock', '>', 0)->where('stock', '<=', 5)->where('is_active', true)
            ->select('name', 'stock')->get()->map(fn($p) => "{$p->name}:{$p->stock}")->implode(', ') ?: 'Ninguno';

        $city = app_setting('business_city', 'Mexico City');
        $weather = WeatherSnapshot::where('date', now()->toDateString())->where('city', $city)->first();

        $prompt = "
Eres el asistente IA de una paletería. Responde breve y directamente.

DATOS DEL NEGOCIO HOY:
- Ventas: \${$ventasHoy} MXN en {$transacciones} ventas
- Producto más vendido: " . ($topProducto?->name ?? 'Ninguno aún') . "
- Sin stock: {$sinStock}
- Stock bajo: {$stockBajo}
- Clima: " . ($weather ? "{$weather->temp}°C, {$weather->condition}" : 'Sin datos') . "

PREGUNTA: {$request->question}

Responde en español, máximo 2-3 oraciones.
";

        $answer = $gemini->ask($prompt);

        return response()->json([
            'success' => true,
            'answer' => $answer,
        ]);
    }
}
