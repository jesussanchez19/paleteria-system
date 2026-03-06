<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class SalesSeeder extends Seeder
{
    public function run(): void
    {
        $vendedor = User::where('role', 'vendedor')->first();
        $gerente = User::where('role', 'gerente')->first();
        $productos = Product::all();

        if ($productos->isEmpty()) {
            return;
        }

        // Generar ventas de los últimos 7 días
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $ventasDelDia = rand(3, 8);

            for ($j = 0; $j < $ventasDelDia; $j++) {
                $usuario = rand(0, 1) ? $vendedor : $gerente;
                
                // Crear venta
                $sale = Sale::create([
                    'user_id' => $usuario?->id,
                    'total' => 0,
                    'created_at' => $fecha->copy()->addHours(rand(9, 20))->addMinutes(rand(0, 59)),
                ]);

                // Agregar 1-4 productos aleatorios
                $total = 0;
                $productosVenta = $productos->random(rand(1, min(4, $productos->count())));
                
                foreach ($productosVenta as $producto) {
                    $qty = rand(1, 3);
                    $subtotal = $qty * $producto->price;
                    $total += $subtotal;

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $producto->id,
                        'qty' => $qty,
                        'price_unit' => $producto->price,
                        'created_at' => $sale->created_at,
                    ]);
                }

                // Actualizar total de la venta
                $sale->update(['total' => $total]);
            }
        }
    }
}
