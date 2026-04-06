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
                'gerente_auth_code' => ['nullable', 'string'], // Código de autorización del gerente
                'discount_percent' => ['nullable', 'numeric', 'min:0'], // Porcentaje de descuento
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

        // Validar descuento contra el máximo permitido
        $maxDiscountPercent = (float) app_setting('max_discount_percent', '15');
        $discountPercent = min((float) ($data['discount_percent'] ?? 0), $maxDiscountPercent);
        $discountPercent = max(0, $discountPercent); // No permitir negativos

        // Traer productos involucrados en una sola consulta
        $productIds = collect($items)->pluck('product_id')->unique()->values();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Calcular total anticipado para verificar límite
        $anticipatedTotal = 0;
        foreach ($items as $item) {
            $p = $products->get($item['product_id']);
            if ($p) {
                $anticipatedTotal += $p->price * $item['qty'];
            }
        }

        // Verificar límite de venta sin autorización
        $maxSaleWithoutAuth = (float) app_setting('max_sale_without_auth', '5000');
        $user = Auth::user();
        
        if ($maxSaleWithoutAuth > 0 && $anticipatedTotal > $maxSaleWithoutAuth) {
            // Si es vendedor, necesita autorización
            if ($user && $user->role === 'vendedor') {
                $gerenteAuthCode = $request->input('gerente_auth_code');
                
                if (!$gerenteAuthCode) {
                    if ($request->expectsJson() || $request->isJson() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'requires_auth' => true,
                            'message' => "Venta de \${$anticipatedTotal} excede el límite de \${$maxSaleWithoutAuth}. Requiere autorización del gerente.",
                            'limit' => $maxSaleWithoutAuth,
                            'total' => $anticipatedTotal,
                        ], 403);
                    }
                    return back()->with('error', "Venta excede el límite. Requiere autorización del gerente.");
                }
                
                // Verificar código de autorización (PIN del gerente - últimos 4 dígitos de su ID + 1234)
                // En producción, esto debería ser más seguro
                $gerente = \App\Models\User::where('role', 'gerente')->first();
                $expectedCode = $gerente ? str_pad($gerente->id % 10000, 4, '0', STR_PAD_LEFT) : '0000';
                
                if ($gerenteAuthCode !== $expectedCode) {
                    if ($request->expectsJson() || $request->isJson() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Código de autorización inválido.',
                        ], 403);
                    }
                    return back()->with('error', 'Código de autorización inválido.');
                }
            }
        }

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
                    'discount_percent' => $discountPercent,
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

            // Actualizar total final (aplicar descuento)
            $discountAmount = $total * ($discountPercent / 100);
            $finalTotal = $total - $discountAmount;
            $sale->update([
                'total' => $finalTotal,
                'subtotal' => $total,
                'discount_amount' => $discountAmount,
            ]);

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
