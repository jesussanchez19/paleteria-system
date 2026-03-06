<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ $sale->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 13px;
            color: #22223b;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .pdf-ticket-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0001;
            padding: 28px 24px 18px 24px;
        }
        .pdf-ticket-header {
            text-align: center;
            margin-bottom: 12px;
        }
        .pdf-ticket-header h2 {
            font-size: 1.7rem;
            margin: 0 0 2px 0;
            color: #e11d48;
            letter-spacing: 1px;
        }
        .pdf-ticket-header small {
            color: #64748b;
            font-size: 0.95em;
        }
        .pdf-ticket-info {
            margin-bottom: 12px;
            font-size: 1em;
        }
        .pdf-ticket-info span {
            display: inline-block;
            min-width: 80px;
            color: #475569;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th, td {
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 4px;
            text-align: left;
        }
        th {
            background: #f1f5f9;
            color: #334155;
            font-size: 1em;
        }
        .pdf-ticket-total {
            text-align: right;
            font-size: 1.1em;
            font-weight: bold;
            color: #059669;
            margin-bottom: 12px;
        }
        .pdf-ticket-qr {
            text-align: center;
            margin-top: 12px;
        }
        .pdf-ticket-footer {
            text-align: center;
            color: #64748b;
            font-size: 0.95em;
            margin-top: 12px;
        }
    </style>
</head>
<body>
<div class="pdf-ticket-container">
    <div class="pdf-ticket-header">
        <h2>Creamyx <span style="font-size:1.1em;">🍦</span></h2>
        <small>Sistema de ventas</small>
    </div>
    <div class="pdf-ticket-info">
        <span><b>Ticket:</b></span> #{{ $sale->id }}<br>
        <span><b>Fecha:</b></span> {{ $sale->created_at }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $d)
                <tr>
                    <td>{{ $d->product->name }}</td>
                    <td>{{ $d->qty }}</td>
                    <td>${{ number_format($d->price_unit, 2) }}</td>
                    <td>${{ number_format($d->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pdf-ticket-total">
        Total: ${{ number_format($sale->total, 2) }}<br>
        @if(request('pago') && request('cambio'))
            <span>Pagó: ${{ number_format(request('pago'), 2) }}</span><br>
            <span>Cambio: ${{ number_format(request('cambio'), 2) }}</span>
        @endif
    </div>
    <div class="pdf-ticket-qr">
        {!! $qr !!}
    </div>
    <div class="pdf-ticket-footer">
        ¡Gracias por tu compra!
    </div>
</div>
</body>
</html>
