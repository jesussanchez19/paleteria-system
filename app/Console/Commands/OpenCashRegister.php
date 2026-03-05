<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CashRegister;

class OpenCashRegister extends Command
{
    protected $signature = 'cash:open {--amount=0 : Monto inicial}';

    protected $description = 'Abrir caja automáticamente';

    public function handle()
    {
        // Verificar si ya hay una caja abierta hoy
        $exists = CashRegister::whereDate('opened_at', now()->toDateString())
            ->whereNull('closed_at')
            ->exists();

        if ($exists) {
            $this->info('Ya hay una caja abierta hoy.');
            return 0;
        }

        CashRegister::create([
            'user_id' => 1, // Usuario sistema/admin
            'opening_amount' => $this->option('amount'),
            'opened_at' => now(),
        ]);

        $this->info('Caja abierta automáticamente a las ' . now()->format('H:i'));
        return 0;
    }
}
