<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SellerReportController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $start = Carbon::parse($date)->startOfDay();
        $end   = Carbon::parse($date)->endOfDay();

        // Requiere sales.user_id
        $rows = DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->selectRaw("
                users.name,
                users.email,
                COUNT(sales.id) as num_sales,
                COALESCE(SUM(sales.total),0) as total_sales,
                COALESCE(AVG(sales.total),0) as avg_ticket
            ")
            ->orderByDesc('total_sales')
            ->get();

        $grandTotal = (float) $rows->sum('total_sales');
        $grandCount = (int) $rows->sum('num_sales');

        return view('panel.reportes-vendedores', compact('rows', 'date', 'grandTotal', 'grandCount'));
    }
}
