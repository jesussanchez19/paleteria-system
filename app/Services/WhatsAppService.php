<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public static function isConfigured(): bool
    {
        return !empty(config('services.whatsapp.token'))
            && !empty(config('services.whatsapp.phone_number_id'));
    }

    public function sendText(string $phone, string $message): bool
    {
        if (!self::isConfigured()) {
            Log::warning('WhatsAppService: no configurado.');
            return false;
        }

        $normalizedPhone = preg_replace('/\D+/', '', $phone ?? '');

        if (empty($normalizedPhone)) {
            return false;
        }

        $url = sprintf(
            'https://graph.facebook.com/%s/%s/messages',
            config('services.whatsapp.api_version', 'v20.0'),
            config('services.whatsapp.phone_number_id')
        );

        try {
            $response = Http::timeout(10)
                ->withToken(config('services.whatsapp.token'))
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $normalizedPhone,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $message,
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('WhatsAppService: error enviando mensaje', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('WhatsAppService: excepción enviando mensaje - ' . $e->getMessage());
            return false;
        }
    }
}