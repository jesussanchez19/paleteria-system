<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\CashRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyReportController extends Controller
{
    private function buildReportData(?string $date = null): array
    {
        $today = $date ?? now()->toDateString(); // YYYY-MM-DD

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

        // Obtener caja del día
        $cashRegister = CashRegister::whereDate('opened_at', $today)->latest()->first();
        
        $cashData = null;
        if ($cashRegister) {
            $salesDuringShift = Sale::where('created_at', '>=', $cashRegister->opened_at)
                ->when($cashRegister->closed_at, function($q) use ($cashRegister) {
                    return $q->where('created_at', '<=', $cashRegister->closed_at);
                })
                ->sum('total');
            
            $expectedAmount = (float)$cashRegister->opening_amount + $salesDuringShift;
            
            $cashData = [
                'register' => $cashRegister,
                'opening_amount' => (float)$cashRegister->opening_amount,
                'sales_during_shift' => $salesDuringShift,
                'expected_amount' => $expectedAmount,
                'closing_amount' => $cashRegister->closing_amount,
                'difference' => $cashRegister->difference,
                'is_closed' => $cashRegister->closed_at !== null,
                'has_real_amount' => $cashRegister->closing_amount !== null,
                'opened_at' => $cashRegister->opened_at,
                'closed_at' => $cashRegister->closed_at,
            ];
        }

        $privateUrl = route('reporte.diario', ['date' => $today]);
        return [
            'date' => $today,
            'sales' => $sales,
            'salesCount' => $salesCount,
            'total' => $total,
            'byProduct' => $byProduct,
            'cashData' => $cashData,
            'privateUrl' => $privateUrl,
            'qrUrl' => 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($privateUrl),
        ];
    }

    public function show(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $data = $this->buildReportData($date);
        return view('reportes.daily', $data);
    }

    public function public(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $data = $this->buildReportData($date);
        return view('reportes.daily_public', $data);
    }

    public function pdf(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $data = $this->buildReportData($date);
        // Generar QR como imagen base64
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(180)->generate($data['privateUrl']);
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        $data['qrBase64'] = $qrBase64;

        $pdf = Pdf::loadView('reportes.daily_pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('reporte_diario_' . $data['date'] . '.pdf');
    }
}
