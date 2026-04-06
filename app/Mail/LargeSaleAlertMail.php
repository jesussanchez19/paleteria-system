<?php

namespace App\Mail;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LargeSaleAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Sale $sale,
        public float $threshold,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Alerta de venta alta',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.large-sale-alert',
        );
    }
}