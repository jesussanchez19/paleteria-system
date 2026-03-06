<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleDetail;
use App\Models\WeatherSnapshot;
use App\Services\GeminiService;
use Illuminate\Support\Facades\DB;

class AIController extends Controller
{
    public function index()
    {
        return view('panel.ia');
    }

    public function ask(Request $request, GeminiService $gemini)
    {
        $request->validate([
            'question' => 'required|string|max:200'
        ]);

        $q = strtolower($request->question);
        $q = $this->normalizeText($q);

        $answer = $this->processQuestion($q, $request->question, $gemini);

        // Auditoría
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'ai.query',
            'module' => 'ia',
            'entity_type' => 'AI',
            'entity_id' => null,
            'meta' => [
                '_entity_name' => 'Consulta IA',
                'pregunta' => $request->question,
            ],
        ]);

        // Si es petición AJAX, devolver JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'response' => $answer
            ]);
        }

        return back()
            ->with('answer', $answer)
            ->withInput();
    }

    private function normalizeText(string $text): string
    {
        // Remover acentos para facilitar comparaciones
        $replacements = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ü' => 'u', 'ñ' => 'n',
        ];
        return strtr($text, $replacements);
    }

    private function processQuestion(string $q, string $originalQuestion, GeminiService $gemini): string
    {
        // Ventas hoy
        if (str_contains($q, 'ventas hoy') || str_contains($q, 'vendido hoy')) {
            return $this->getVentasHoy();
        }

        // Ventas semana
        if (str_contains($q, 'ventas semana') || str_contains($q, 'esta semana')) {
            return $this->getVentasSemana();
        }

        // Ventas mes
        if (str_contains($q, 'ventas mes') || str_contains($q, 'este mes')) {
            return $this->getVentasMes();
        }

        // Producto más vendido
        if (str_contains($q, 'producto mas vendido') || str_contains($q, 'mas vendido') || str_contains($q, 'top producto')) {
            return $this->getTopProducto();
        }

        // Productos menos vendidos / baja rotación
        if (str_contains($q, 'menos vendido') || str_contains($q, 'baja rotacion') || str_contains($q, 'no se vende')) {
            return $this->getProductosBajaRotacion();
        }

        // Sin stock
        if (str_contains($q, 'sin stock') || str_contains($q, 'agotado')) {
            return $this->getProductosSinStock();
        }

        // Inventario bajo
        if (str_contains($q, 'inventario bajo') || str_contains($q, 'stock bajo') || str_contains($q, 'poco stock')) {
            return $this->getInventarioBajo();
        }

        // Sugerencia de reposición
        if (str_contains($q, 'reposicion') || str_contains($q, 'reponer') || str_contains($q, 'comprar')) {
            return $this->getSugerenciaReposicion();
        }

        // Tendencias
        if (str_contains($q, 'tendencia') || str_contains($q, 'patron') || str_contains($q, 'comportamiento')) {
            return $this->getTendencias();
        }

        // Mejor día
        if (str_contains($q, 'mejor dia') || str_contains($q, 'dia con mas ventas')) {
            return $this->getMejorDia();
        }

        // Resumen / reporte
        if (str_contains($q, 'resumen') || str_contains($q, 'reporte') || str_contains($q, 'como va')) {
            return $this->getResumenGeneral();
        }

        // Categoría más vendida
        if (str_contains($q, 'categoria') && (str_contains($q, 'vendida') || str_contains($q, 'popular'))) {
            return $this->getCategoriaPopular();
        }

        // Promedio de venta
        if (str_contains($q, 'promedio') || str_contains($q, 'ticket promedio')) {
            return $this->getPromedioVenta();
        }

        // Ayuda
        if (str_contains($q, 'ayuda') || str_contains($q, 'que puedo preguntar') || str_contains($q, 'opciones')) {
            return $this->getAyuda();
        }

        // Fallback: usar Gemini con contexto del negocio
        return $this->askGemini($originalQuestion, $gemini);
    }

    private function askGemini(string $question, GeminiService $gemini): string
    {
        // === VENTAS ===
        $ventasHoy = Sale::whereDate('created_at', today())->sum('total');
        $ventasAyer = Sale::whereDate('created_at', today()->subDay())->sum('total');
        $ventasSemana = Sale::whereBetween('created_at', [now()->startOfWeek(), now()])->sum('total');
        $ventasSemanaAnterior = Sale::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('total');
        $ventasMes = Sale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total');
        $transaccionesHoy = Sale::whereDate('created_at', today())->count();
        $ticketPromedio = $transaccionesHoy > 0 ? round($ventasHoy / $transaccionesHoy, 2) : 0;
        $ticketPromedioGeneral = Sale::avg('total') ?? 0;

        // Tendencia semanal
        $tendenciaSemanal = $ventasSemanaAnterior > 0 
            ? round((($ventasSemana - $ventasSemanaAnterior) / $ventasSemanaAnterior) * 100, 1) 
            : 0;
        $tendenciaTexto = $tendenciaSemanal >= 0 ? "+{$tendenciaSemanal}%" : "{$tendenciaSemanal}%";

        // === TOP PRODUCTOS ===
        $topProductos = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(sale_details.qty) as qty'), DB::raw('SUM(sale_details.qty * sale_details.price_unit) as total'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get()
            ->map(fn($p) => "{$p->name}: {$p->qty} vendidos (\${$p->total})")
            ->implode(', ');

        // Productos menos vendidos
        $menosVendidos = DB::table('products')
            ->leftJoin('sale_details', 'products.id', '=', 'sale_details.product_id')
            ->where('products.is_active', true)
            ->select('products.name', DB::raw('COALESCE(SUM(sale_details.qty), 0) as qty'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('qty')
            ->limit(5)
            ->get()
            ->map(fn($p) => "{$p->name}: {$p->qty} vendidos")
            ->implode(', ');

        // === INVENTARIO ===
        $productosSinStock = Product::where('stock', 0)
            ->where('is_active', true)
            ->pluck('name')
            ->implode(', ');
        $sinStockInfo = $productosSinStock ?: 'Ninguno';
        $cantidadSinStock = Product::where('stock', 0)->where('is_active', true)->count();

        $productosStockBajo = Product::where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->where('is_active', true)
            ->select('name', 'stock')
            ->get()
            ->map(fn($p) => "{$p->name}: {$p->stock} uds")
            ->implode(', ');
        $stockBajoInfo = $productosStockBajo ?: 'Ninguno';
        $cantidadStockBajo = Product::where('stock', '>', 0)->where('stock', '<=', 5)->where('is_active', true)->count();

        // Inventario completo
        $inventario = Product::where('is_active', true)
            ->select('name', 'stock', 'price', 'category')
            ->orderBy('stock')
            ->get()
            ->map(fn($p) => "{$p->name}: {$p->stock} uds, \${$p->price}, categoría: {$p->category}")
            ->implode(' | ');

        $totalProductos = Product::where('is_active', true)->count();
        $valorInventario = Product::where('is_active', true)->selectRaw('SUM(stock * price) as total')->value('total') ?? 0;

        // === CATEGORÍAS ===
        $ventasPorCategoria = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('products.category', DB::raw('SUM(sale_details.qty) as qty'), DB::raw('SUM(sale_details.qty * sale_details.price_unit) as total'))
            ->whereNotNull('products.category')
            ->groupBy('products.category')
            ->orderByDesc('total')
            ->get()
            ->map(fn($c) => "{$c->category}: {$c->qty} uds, \${$c->total}")
            ->implode(', ');

        // === DÍAS Y HORARIOS ===
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $ventasPorDia = Sale::select(DB::raw('EXTRACT(DOW FROM created_at) as dia'), DB::raw('SUM(total) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('dia')
            ->orderByDesc('total')
            ->get()
            ->map(fn($d) => "{$dias[(int)$d->dia]}: \${$d->total}")
            ->implode(', ');

        $mejorDia = Sale::select(DB::raw('EXTRACT(DOW FROM created_at) as dia'), DB::raw('SUM(total) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('dia')
            ->orderByDesc('total')
            ->first();
        $mejorDiaTexto = $mejorDia ? $dias[(int)$mejorDia->dia] : 'Sin datos';

        // === ÚLTIMAS VENTAS ===
        $ultimasVentas = Sale::with('details.product')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function($s) {
                $productos = $s->details->map(fn($d) => $d->product->name ?? 'Producto')->implode(', ');
                return "{$s->created_at->format('H:i')}: \${$s->total} ({$productos})";
            })
            ->implode(' | ');

        // === CLIMA ===
        $city = app_setting('business_city', 'Mexico City');
        $weather = WeatherSnapshot::where('date', now()->toDateString())
            ->where('city', $city)
            ->first();
        $climaInfo = $weather 
            ? "{$weather->temp}°C, {$weather->condition}" 
            : "Sin datos";

        // Recomendación por clima
        $recomendacionClima = '';
        if ($weather) {
            if ($weather->temp >= 30) {
                $recomendacionClima = 'Día muy caluroso - aumentar producción de paletas y aguas';
            } elseif ($weather->temp >= 25) {
                $recomendacionClima = 'Día cálido - buenas ventas de helados esperadas';
            } elseif ($weather->temp <= 20) {
                $recomendacionClima = 'Día fresco - posible baja en ventas de helados';
            } else {
                $recomendacionClima = 'Clima templado - ventas normales';
            }
        }

        // === NEGOCIO ===
        $nombreNegocio = app_setting('business_name', 'Creamyx');
        $fechaHoy = now()->format('l d/m/Y');
        $horaActual = now()->format('H:i');

        $context = "
Eres el asistente IA de '{$nombreNegocio}', una heladería/paletería en México.
Fecha: {$fechaHoy}, Hora: {$horaActual}
Responde SIEMPRE en español, de forma directa y con datos específicos del negocio.

══════════════════════════════════════
📊 RESUMEN DE VENTAS
══════════════════════════════════════
• Hoy: \${$ventasHoy} MXN ({$transaccionesHoy} ventas)
• Ayer: \${$ventasAyer} MXN
• Esta semana: \${$ventasSemana} MXN
• Semana anterior: \${$ventasSemanaAnterior} MXN
• Tendencia semanal: {$tendenciaTexto}
• Este mes: \${$ventasMes} MXN
• Ticket promedio hoy: \${$ticketPromedio} MXN
• Ticket promedio general: \$" . round($ticketPromedioGeneral, 2) . " MXN

══════════════════════════════════════
🏆 TOP 5 PRODUCTOS MÁS VENDIDOS
══════════════════════════════════════
{$topProductos}

📉 PRODUCTOS MENOS VENDIDOS:
{$menosVendidos}

══════════════════════════════════════
📦 INVENTARIO ({$totalProductos} productos activos)
══════════════════════════════════════
Valor total del inventario: \${$valorInventario} MXN

🚨 SIN STOCK ({$cantidadSinStock}): {$sinStockInfo}
⚠️ STOCK BAJO ({$cantidadStockBajo}): {$stockBajoInfo}

Detalle: {$inventario}

══════════════════════════════════════
🏷️ VENTAS POR CATEGORÍA
══════════════════════════════════════
{$ventasPorCategoria}

══════════════════════════════════════
📅 VENTAS POR DÍA (últimos 30 días)
══════════════════════════════════════
{$ventasPorDia}
Mejor día: {$mejorDiaTexto}

══════════════════════════════════════
🧾 ÚLTIMAS 5 VENTAS DE HOY
══════════════════════════════════════
{$ultimasVentas}

══════════════════════════════════════
🌤️ CLIMA EN {$city}
══════════════════════════════════════
Actual: {$climaInfo}
Recomendación: {$recomendacionClima}

══════════════════════════════════════

PREGUNTA DEL GERENTE: {$question}

Instrucciones:
- Responde de forma clara, breve y directa
- Usa los datos reales proporcionados arriba
- Si preguntan qué reponer, menciona los productos sin stock y con stock bajo por nombre
- Si preguntan sobre ventas, da cifras exactas
- Si preguntan tendencias, compara con períodos anteriores
- Si preguntan recomendaciones, basáte en los datos del clima y ventas
- No inventes datos, usa solo la información proporcionada
";

        return $gemini->ask($context);
    }

    private function getVentasHoy(): string
    {
        $ventas = Sale::whereDate('created_at', today());
        $total = $ventas->sum('total');
        $count = $ventas->count();

        if ($count === 0) {
            return "📊 Aún no hay ventas registradas hoy.";
        }

        return "📊 **Ventas de hoy:** $" . number_format($total, 2) . " en {$count} transacciones.";
    }

    private function getVentasSemana(): string
    {
        $ventas = Sale::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        $total = $ventas->sum('total');
        $count = $ventas->count();

        return "📈 **Ventas de la semana:** $" . number_format($total, 2) . " en {$count} transacciones.";
    }

    private function getVentasMes(): string
    {
        $ventas = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
        $total = $ventas->sum('total');
        $count = $ventas->count();

        return "📅 **Ventas del mes:** $" . number_format($total, 2) . " en {$count} transacciones.";
    }

    private function getTopProducto(): string
    {
        $top = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(sale_details.qty) as qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        if ($top->isEmpty()) {
            return "📦 Aún no hay datos de ventas para analizar.";
        }

        $list = $top->map(fn($p, $i) => ($i + 1) . ". {$p->name} ({$p->qty} uds)")->implode("\n");

        return "🏆 **Top 5 productos más vendidos:**\n{$list}";
    }

    private function getProductosBajaRotacion(): string
    {
        // Productos activos que menos se han vendido en los últimos 30 días
        $lowSales = DB::table('products')
            ->leftJoin('sale_details', function ($join) {
                $join->on('products.id', '=', 'sale_details.product_id')
                    ->where('sale_details.created_at', '>=', now()->subDays(30));
            })
            ->where('products.is_active', true)
            ->select('products.name', DB::raw('COALESCE(SUM(sale_details.qty), 0) as qty'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('qty')
            ->limit(5)
            ->get();

        if ($lowSales->isEmpty()) {
            return "📦 No hay productos para analizar.";
        }

        $list = $lowSales->map(fn($p) => "• {$p->name} ({$p->qty} uds en 30 días)")->implode("\n");

        return "📉 **Productos con baja rotación (últimos 30 días):**\n{$list}\n\n💡 Considera promociones o revisar precios.";
    }

    private function getProductosSinStock(): string
    {
        $products = Product::where('stock', 0)
            ->where('is_active', true)
            ->pluck('name')
            ->take(10);

        if ($products->isEmpty()) {
            return "✅ ¡Excelente! No hay productos sin stock.";
        }

        return "🚨 **Productos agotados ({$products->count()}):**\n• " . $products->implode("\n• ");
    }

    private function getInventarioBajo(): string
    {
        $threshold = (int) app_setting('low_stock_threshold', 5);

        $products = Product::where('stock', '>', 0)
            ->where('stock', '<=', $threshold)
            ->where('is_active', true)
            ->select('name', 'stock')
            ->get();

        if ($products->isEmpty()) {
            return "✅ No hay productos con inventario bajo (umbral: {$threshold} unidades).";
        }

        $list = $products->map(fn($p) => "• {$p->name} ({$p->stock} uds)")->implode("\n");

        return "⚠️ **Productos con inventario bajo ({$products->count()}):**\n{$list}";
    }

    private function getSugerenciaReposicion(): string
    {
        $threshold = (int) app_setting('low_stock_threshold', 5);

        // Productos que necesitan reposición basado en ventas recientes
        $sugerencias = DB::table('products')
            ->leftJoin('sale_details', function ($join) {
                $join->on('products.id', '=', 'sale_details.product_id')
                    ->where('sale_details.created_at', '>=', now()->subDays(7));
            })
            ->where('products.is_active', true)
            ->where('products.stock', '<=', $threshold)
            ->select(
                'products.name',
                'products.stock',
                DB::raw('COALESCE(SUM(sale_details.qty), 0) as vendidos_semana')
            )
            ->groupBy('products.id', 'products.name', 'products.stock')
            ->orderByDesc('vendidos_semana')
            ->limit(10)
            ->get();

        if ($sugerencias->isEmpty()) {
            return "✅ No hay sugerencias de reposición urgentes.";
        }

        $list = $sugerencias->map(function ($p) {
            $sugerido = max(10, $p->vendidos_semana * 2);
            return "• {$p->name}: stock {$p->stock}, vendidos/sem {$p->vendidos_semana} → **pedir ~{$sugerido} uds**";
        })->implode("\n");

        return "📦 **Sugerencia de reposición:**\n{$list}";
    }

    private function getTendencias(): string
    {
        // Comparar última semana vs semana anterior
        $semanaActual = Sale::whereBetween('created_at', [now()->subDays(7), now()])->sum('total');
        $semanaAnterior = Sale::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->sum('total');

        $diferencia = $semanaAnterior > 0
            ? round((($semanaActual - $semanaAnterior) / $semanaAnterior) * 100, 1)
            : 0;

        $tendencia = $diferencia >= 0 ? "📈 +{$diferencia}%" : "📉 {$diferencia}%";

        // Día más fuerte
        $mejorDia = Sale::select(DB::raw('EXTRACT(DOW FROM created_at) as dia'), DB::raw('SUM(total) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('dia')
            ->orderByDesc('total')
            ->first();

        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $diaFuerte = $mejorDia ? $dias[(int)$mejorDia->dia] : 'Sin datos';

        return "🔥 **Tendencias:**\n" .
            "• Semana actual vs anterior: {$tendencia}\n" .
            "• Día más fuerte (último mes): {$diaFuerte}\n" .
            "• Ventas semana actual: $" . number_format($semanaActual, 2);
    }

    private function getMejorDia(): string
    {
        $datos = Sale::select(DB::raw('EXTRACT(DOW FROM created_at) as dia'), DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as transacciones'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('dia')
            ->orderByDesc('total')
            ->get();

        if ($datos->isEmpty()) {
            return "📅 No hay suficientes datos para determinar el mejor día.";
        }

        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        $list = $datos->map(fn($d) => "• {$dias[(int)$d->dia]}: $" . number_format($d->total, 2) . " ({$d->transacciones} ventas)")->implode("\n");

        return "📅 **Ventas por día (últimos 30 días):**\n{$list}";
    }

    private function getResumenGeneral(): string
    {
        $hoy = Sale::whereDate('created_at', today())->sum('total');
        $semana = Sale::whereBetween('created_at', [now()->startOfWeek(), now()])->sum('total');
        $mes = Sale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total');

        $sinStock = Product::where('stock', 0)->where('is_active', true)->count();
        $threshold = (int) app_setting('low_stock_threshold', 5);
        $stockBajo = Product::where('stock', '>', 0)->where('stock', '<=', $threshold)->where('is_active', true)->count();

        return "📋 **Resumen general:**\n" .
            "💰 Ventas hoy: $" . number_format($hoy, 2) . "\n" .
            "💰 Ventas semana: $" . number_format($semana, 2) . "\n" .
            "💰 Ventas mes: $" . number_format($mes, 2) . "\n" .
            "🚨 Sin stock: {$sinStock} productos\n" .
            "⚠️ Stock bajo: {$stockBajo} productos";
    }

    private function getCategoriaPopular(): string
    {
        $categorias = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('products.category', DB::raw('SUM(sale_details.qty) as qty'))
            ->whereNotNull('products.category')
            ->groupBy('products.category')
            ->orderByDesc('qty')
            ->get();

        if ($categorias->isEmpty()) {
            return "📦 No hay datos de categorías para analizar.";
        }

        $list = $categorias->map(fn($c) => "• {$c->category}: {$c->qty} unidades")->implode("\n");

        return "🏷️ **Categorías más vendidas:**\n{$list}";
    }

    private function getPromedioVenta(): string
    {
        $promedio = Sale::avg('total');
        $promedioHoy = Sale::whereDate('created_at', today())->avg('total');

        return "🧾 **Ticket promedio:**\n" .
            "• General: $" . number_format($promedio ?? 0, 2) . "\n" .
            "• Hoy: $" . number_format($promedioHoy ?? 0, 2);
    }

    private function getAyuda(): string
    {
        return "💡 **Puedes preguntarme:**\n" .
            "📊 Ventas: 'ventas hoy', 'ventas semana', 'ventas mes'\n" .
            "🏆 Productos: 'producto más vendido', 'menos vendido', 'categoría popular'\n" .
            "📦 Inventario: 'sin stock', 'inventario bajo', 'sugerencia de reposición'\n" .
            "📈 Análisis: 'tendencias', 'mejor día', 'ticket promedio'\n" .
            "📋 General: 'resumen', 'cómo va el negocio'";
    }
}
