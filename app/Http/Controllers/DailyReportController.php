<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyReportController extends Controller
{
    private function buildReportData(): array
    {
        $today = now()->toDateString(); // YYYY-MM-DD

        // Ventas del día
        $sales = Sale::with('user')
            ->whereDate('created_at', $today)
            ->orderByDesc('id')
            ->get();

        $salesCount = $sales->count();
        $total = (float) $sales->sum('total');

        // Resumen por producto (cantidad y total)
        $byProduct = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereDate('sales.created_at', $today)
            ->groupBy('products.id', 'products.name')
            ->selectRaw('products.id as product_id, products.name, SUM(sale_details.qty) as qty, SUM(sale_details.subtotal) as total')
            ->orderByDesc('total')
            ->get();

        $privateUrl = route('reporte.diario');
        return [
            'date' => $today,
            'sales' => $sales,
            'salesCount' => $salesCount,
            'total' => $total,
            'byProduct' => $byProduct,
            'privateUrl' => $privateUrl,
            'qrUrl' => 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($privateUrl),
        ];
    }

    public function show()
    {
        $data = $this->buildReportData();
        return view('reportes.daily', $data);
    }

    public function public()
    {
        $data = $this->buildReportData();
        return view('reportes.daily_public', $data);
    }

    public function pdf()
    {
        $data = $this->buildReportData();
        // Generar QR como imagen base64
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(180)->generate($data['privateUrl']);
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        $data['qrBase64'] = $qrBase64;

        $pdf = Pdf::loadView('reportes.daily_pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('reporte_diario_' . $data['date'] . '.pdf');
    }
}
