<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CashRegister;
use App\Models\Sale;

class CloseCashRegister extends Command
{
    protected $signature = 'cash:close';

    protected $description = 'Cerrar caja automáticamente';

    public function handle()
    {
        $cash = CashRegister::whereNull('closed_at')
            ->latest()
            ->first();

        if (!$cash) {
            $this->info('No hay caja abierta para cerrar.');
            return 0;
        }

        // Calcular ventas del turno
        $salesDuringShift = Sale::where('created_at', '>=', $cash->opened_at)->sum('total');
        $expected = (float)$cash->opening_amount + $salesDuringShift;

        // Cierre automático: solo guardar expected, el dinero real lo ingresa el gerente después
        $cash->update([
            'closing_amount' => null, // Pendiente de ingresar por el gerente
            'expected_amount' => $expected,
            'difference' => null, // Se calcula cuando se ingrese el dinero real
            'closed_at' => now(),
        ]);

        $this->info('Caja cerrada automáticamente a las ' . now()->format('H:i'));
        $this->info('Total esperado: $' . number_format($expected, 2));
        $this->info('Pendiente: El gerente debe ingresar el dinero real contado.');
        return 0;
    }
}
