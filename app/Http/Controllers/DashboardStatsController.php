<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardStatsController extends Controller
{
    public function index(Request $request)
    {
        // Soporte para rango de fechas
        $startDate = $request->input('start_date', now()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        // Validar que las fechas sean válidas
        if (!strtotime($startDate)) $startDate = now()->toDateString();
        if (!strtotime($endDate)) $endDate = now()->toDateString();
        
        // Asegurar que start <= end
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }
        
        $isRange = $startDate !== $endDate;
        $today = now()->toDateString();
        
        // Detectar tipo de período para la etiqueta
        $weekStart = now()->startOfWeek()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        
        if ($startDate === $today && $endDate === $today) {
            $periodoLabel = 'hoy';
        } elseif ($startDate === $weekStart && $endDate === $today) {
            $periodoLabel = 'esta semana';
        } elseif ($startDate === $monthStart && $endDate === $today) {
            $periodoLabel = 'este mes';
        } else {
            $periodoLabel = $startDate . ' a ' . $endDate;
        }

        /**
         * 1) Ventas por hora (rango o hoy)
         */
        $salesByHour = DB::table('sales')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw("to_char(created_at, 'HH24') as hour, COUNT(*) as qty, COALESCE(SUM(total),0) as total")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        /**
         * 2) Top productos por cantidad (rango)
         */
        $topProducts = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereDate('sales.created_at', '>=', $startDate)
            ->whereDate('sales.created_at', '<=', $endDate)
            ->groupBy('products.id', 'products.name')
            ->selectRaw('products.name, COALESCE(SUM(sale_details.qty),0) as qty, COALESCE(SUM(sale_details.subtotal),0) as total')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        /**
         * 3) Ventas totales por día (rango o últimos 7 días)
         */
        $salesLast7Days = DB::table('sales')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw("to_char(created_at::date, 'YYYY-MM-DD') as day, COUNT(*) as qty, COALESCE(SUM(total),0) as total")
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        /**
         * 4) Ingresos por categoría (rango)
         */
        $revenueByCategory = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereDate('sales.created_at', '>=', $startDate)
            ->whereDate('sales.created_at', '<=', $endDate)
            ->groupBy('products.category')
            ->selectRaw("COALESCE(products.category,'Sin categoría') as category, COALESCE(SUM(sale_details.subtotal),0) as total")
            ->orderByDesc('total')
            ->get();

        /**
         * 5) Productos sin stock
         */
        $outOfStock = DB::table('products')
            ->where('stock', '<=', 0)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'category', 'stock']);

        /**
         * 6) Ventas por vendedor (rango)
         */
        $salesBySeller = DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->whereDate('sales.created_at', '>=', $startDate)
            ->whereDate('sales.created_at', '<=', $endDate)
            ->groupBy('users.id', 'users.name')
            ->selectRaw("users.name, COUNT(sales.id) as qty, COALESCE(SUM(sales.total),0) as total")
            ->orderByDesc('total')
            ->get();

        /**
         * 7) Ticket promedio por día (rango)
         */
        $avgTicketLast7Days = DB::table('sales')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw("to_char(created_at::date, 'YYYY-MM-DD') as day, COALESCE(AVG(total),0) as avg_total")
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        /**
         * 8) Comparación período vs período anterior
         */
        $daysDiff = max(1, (int) ceil((strtotime($endDate) - strtotime($startDate)) / 86400) + 1);
        $prevStart = date('Y-m-d', strtotime($startDate . " -$daysDiff days"));
        $prevEnd = date('Y-m-d', strtotime($startDate . ' -1 day'));

        $currentTotal = (float) DB::table('sales')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('total');
        $prevTotal = (float) DB::table('sales')
            ->whereDate('created_at', '>=', $prevStart)
            ->whereDate('created_at', '<=', $prevEnd)
            ->sum('total');

        $currentQty = (int) DB::table('sales')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();
        $prevQty = (int) DB::table('sales')
            ->whereDate('created_at', '>=', $prevStart)
            ->whereDate('created_at', '<=', $prevEnd)
            ->count();

        $compare = [
            'today' => ['date' => $isRange ? "$startDate a $endDate" : $startDate, 'total' => $currentTotal, 'qty' => $currentQty],
            'yesterday' => ['date' => $isRange ? "$prevStart a $prevEnd" : $prevEnd, 'total' => $prevTotal, 'qty' => $prevQty],
        ];

        // Generar QR con enlace al PDF del reporte
        $pdfUrl = route('reportes.pdf', ['start_date' => $startDate, 'end_date' => $endDate]);
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(150)->generate($pdfUrl);
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        $data = compact(
            'today',
            'startDate',
            'endDate',
            'isRange',
            'periodoLabel',
            'salesByHour',
            'topProducts',
            'salesLast7Days',
            'revenueByCategory',
            'outOfStock',
            'salesBySeller',
            'avgTicketLast7Days',
            'compare',
            'qrBase64',
            'pdfUrl'
        );

        // Determinar qué vista usar según la ruta
        if ($request->route()->getName() === 'reportes.graficas') {
            return view('reportes.graficas', $data);
        }

        return view('panel.reportes', $data);
    }

    public function pdf(Request $request)
    {
        // Reutilizar la lógica de datos
        $startDate = $request->input('start_date', now()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        if (!strtotime($startDate)) $startDate = now()->toDateString();
        if (!strtotime($endDate)) $endDate = now()->toDateString();
        
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }
        
        $isRange = $startDate !== $endDate;
        $today = now()->toDateString();
        
        $weekStart = now()->startOfWeek()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        
        if ($startDate === $today && $endDate === $today) {
            $periodoLabel = 'hoy';
        } elseif ($startDate === $weekStart && $endDate === $today) {
            $periodoLabel = 'esta semana';
        } elseif ($startDate === $monthStart && $endDate === $today) {
            $periodoLabel = 'este mes';
        } else {
            $periodoLabel = $startDate . ' a ' . $endDate;
        }

        // Ventas por hora
        $salesByHour = DB::table('sales')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw("to_char(created_at, 'HH24') as hour, COUNT(*) as qty, COALESCE(SUM(total),0) as total")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Top productos
        $topProducts = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereDate('sales.created_at', '>=', $startDate)
            ->whereDate('sales.created_at', '<=', $endDate)
            ->groupBy('products.id', 'products.name')
            ->selectRaw('products.name, COALESCE(SUM(sale_details.qty),0) as qty, COALESCE(SUM(sale_details.subtotal),0) as total')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        // Ventas por vendedor
        $salesBySeller = DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->whereDate('sales.created_at', '>=', $startDate)
            ->whereDate('sales.created_at', '<=', $endDate)
            ->groupBy('users.id', 'users.name')
            ->selectRaw("users.name, COUNT(sales.id) as qty, COALESCE(SUM(sales.total),0) as total")
            ->orderByDesc('total')
            ->get();

        // Ingresos por categoría
        $revenueByCategory = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereDate('sales.created_at', '>=', $startDate)
            ->whereDate('sales.created_at', '<=', $endDate)
            ->groupBy('products.category')
            ->selectRaw("COALESCE(products.category,'Sin categoría') as category, COALESCE(SUM(sale_details.subtotal),0) as total")
            ->orderByDesc('total')
            ->get();

        // Totales
        $totalVentas = (float) DB::table('sales')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('total');
        
        $totalTickets = (int) DB::table('sales')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        // Generar QR con enlace al PDF del reporte
        $reportUrl = route('reportes.pdf', ['start_date' => $startDate, 'end_date' => $endDate]);
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(180)->generate($reportUrl);
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        $data = compact(
            'startDate',
            'endDate',
            'periodoLabel',
            'salesByHour',
            'topProducts',
            'salesBySeller',
            'revenueByCategory',
            'totalVentas',
            'totalTickets',
            'qrBase64',
            'reportUrl'
        );

        $pdf = Pdf::loadView('reportes.rango_pdf', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'reporte_' . $startDate . '_a_' . $endDate . '.pdf';
        
        return $pdf->download($filename);
    }
}
