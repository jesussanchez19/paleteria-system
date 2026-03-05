<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashRegister;
use App\Models\Sale;
use Carbon\Carbon;

class CashRegisterReportController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $start = Carbon::parse($date)->startOfDay();
        $end   = Carbon::parse($date)->endOfDay();

        // Caja del día (si hubo más de una, tomamos la última)
        $cash = CashRegister::with('user')
            ->whereBetween('opened_at', [$start, $end])
            ->orderByDesc('id')
            ->first();

        // Ventas del día (para expected)
        $expected = (float) Sale::whereBetween('created_at', [$start, $end])->sum('total');

        // Si hay caja abierta, calcular ventas del turno
        $salesDuringShift = 0;
        $expectedInCash = 0;
        if ($cash) {
            $salesDuringShift = Sale::where('created_at', '>=', $cash->opened_at)
                ->when($cash->closed_at, function($q) use ($cash) {
                    return $q->where('created_at', '<=', $cash->closed_at);
                })
                ->sum('total');
            $expectedInCash = (float)$cash->opening_amount + $salesDuringShift;
        }

        // Historial paginado
        $registers = CashRegister::with('user')
            ->orderByDesc('opened_at')
            ->paginate(15);

        // Estadísticas generales
        $stats = [
            'total_turnos' => CashRegister::count(),
            'turnos_hoy' => CashRegister::whereDate('opened_at', now()->toDateString())->count(),
            'diferencia_total' => CashRegister::whereNotNull('closed_at')->sum('difference'),
            'turnos_con_faltante' => CashRegister::whereNotNull('closed_at')->where('difference', '<', 0)->count(),
            'turnos_con_sobrante' => CashRegister::whereNotNull('closed_at')->where('difference', '>', 0)->count(),
        ];

        // Verificar si es fecha de hoy para mostrar formulario de corte
        $isToday = $date === now()->toDateString();

        return view('panel.caja', compact(
            'date', 
            'cash', 
            'expected', 
            'salesDuringShift',
            'expectedInCash',
            'registers', 
            'stats',
            'isToday'
        ));
    }
}
