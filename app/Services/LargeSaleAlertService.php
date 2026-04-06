<?php

namespace App\Services;

use App\Mail\LargeSaleAlertMail;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LargeSaleAlertService
{
    public function __construct(private WhatsAppService $whatsAppService)
    {
    }

    public function sendIfNeeded(Sale $sale): void
    {
        if (app_setting('notify_large_sales', '0') !== '1') {
            return;
        }

        $threshold = (float) app_setting('max_sale_without_auth', '5000');

        if ((float) $sale->total < $threshold) {
            return;
        }

        $gerente = User::where('role', 'gerente')->where('is_active', true)->first();

        if (!$gerente) {
            return;
        }

        $sale->loadMissing('details.product', 'user');

        if (!empty($gerente->email)) {
            try {
                Mail::to($gerente->email)->send(new LargeSaleAlertMail($sale, $threshold));
            } catch (\Throwable $e) {
                Log::error('LargeSaleAlertService: error enviando correo - ' . $e->getMessage());
            }
        }

        if (!empty($gerente->phone)) {
            $message = sprintf(
                "Venta alta detectada en %s. Ticket #%d por $%s. Vendedor: %s.",
                app_setting('business_name', 'Creamyx'),
                $sale->id,
                number_format((float) $sale->total, 2),
                $sale->user?->name ?? 'Sistema'
            );

            $this->whatsAppService->sendText($gerente->phone, $message);
        }
    }
}