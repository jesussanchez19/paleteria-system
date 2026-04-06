<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SaleController extends Controller
{
    public function show(Sale $sale)
    {
        $sale->load('details.product');
        $qr = QrCode::size(150)->generate(route('ticket.show', $sale));
        return view('tickets.show', compact('sale', 'qr'));
    }

    public function pdf(Sale $sale)
    {
        $sale->load('details.product');
        $qr = QrCode::size(120)->generate(route('ticket.show', $sale));
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tickets.pdf', compact('sale', 'qr'));
        return $pdf->download("ticket_{$sale->id}.pdf");
    }
    public function store(Request $request)
    {
        // Verificar si las ventas están habilitadas
        if (app_setting('sales_enabled', '1') !== '1') {
            if ($request->expectsJson() || $request->isJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las ventas están deshabilitadas temporalmente por el administrador.',
                ], 403);
            }
            return back()->with('error', 'Las ventas están deshabilitadas temporalmente por el administrador.');
        }

        // Permitir items_json desde el formulario oculto
        if ($request->filled('items_json') && !$request->filled('items')) {
            $decoded = json_decode($request->input('items_json'), true);
            $request->merge(['items' => is_array($decoded) ? $decoded : []]);
        }

        try {
            $data = $request->validate([
                'items' => ['required', 'array', 'min:1'],
                'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
                'items.*.qty' => ['required', 'integer', 'min:1'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->isJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        $items = $data['items'];

        // Traer productos involucrados en una sola consulta
        $productIds = collect($items)->pluck('product_id')->unique()->values();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Validación extra: evitar duplicados del mismo product_id en items
        // (si mandas duplicados, sumamos qty)
        $normalized = collect($items)
            ->groupBy('product_id')
            ->map(function ($rows, $pid) {
                return [
                    'product_id' => (int)$pid,
                    'qty' => (int)collect($rows)->sum('qty'),
                ];
            })
            ->values();

        DB::beginTransaction();

        try {
            $total = 0;

            // Crear venta
                $sale = Sale::create([
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'total' => 0,
                    'sold_at' => now(),
                ]);

            // Crear detalles y descontar stock
            foreach ($normalized as $row) {
                $p = $products->get($row['product_id']);
                if (!$p) {
                    throw new \Exception('Producto no encontrado.');
                }

                $qty = (int)$row['qty'];
                $price = (float)$p->price;
                $subtotal = $qty * $price;
                $total += $subtotal;

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $p->id,
                    'qty' => $qty,
                    'price_unit' => $price,
                    'subtotal' => $subtotal,
                ]);

                // Descontar stock
                $p->stock = max(0, $p->stock - $qty);
                $p->save();
            }

            // Actualizar total final
            $sale->update(['total' => $total]);

            DB::commit();

            // Registrar en bitácora
            audit_log('sale.created', 'pos', $sale, [
                'total' => '$' . number_format($sale->total, 2),
                'productos' => $normalized->count() . ' productos',
                'vendedor' => auth()->user()->name ?? 'Sistema',
            ]);

            // Limpieza del carrito en frontend: lo haremos desde JS
            if ($request->expectsJson() || $request->isJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'total' => $total,
                    'sale_id' => $sale->id,
                    'message' => 'Venta realizada correctamente'
                ]);
            }
            return back()->with('success', 'Venta registrada. Total: $' . number_format($total, 2));

        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->isJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo registrar la venta: ' . $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ], 500);
            }
            return back()->with('error', 'No se pudo registrar la venta: ' . $e->getMessage());
        }
    }
}
