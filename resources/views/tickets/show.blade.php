
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ $sale->id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 15px;
            background: #f8fafc;
            color: #22223b;
            margin: 0;
            padding: 0;
        }
        .ticket-container {
            max-width: 420px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px 0 #0001;
            padding: 32px 28px 24px 28px;
        }
        .ticket-header {
            text-align: center;
            margin-bottom: 18px;
        }
        .ticket-header h2 {
            font-size: 2rem;
            margin: 0 0 4px 0;
            color: #e11d48;
            letter-spacing: 1px;
        }
        .ticket-header small {
            color: #64748b;
            font-size: 0.95em;
        }
        .ticket-info {
            margin-bottom: 18px;
            font-size: 1.05em;
        }
        .ticket-info span {
            display: inline-block;
            min-width: 90px;
            color: #475569;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        th, td {
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 6px;
            text-align: left;
        }
        th {
            background: #f1f5f9;
            color: #334155;
            font-size: 1em;
        }
        .ticket-total {
            text-align: right;
            font-size: 1.2em;
            font-weight: bold;
            color: #059669;
            margin-bottom: 18px;
        }
        .ticket-qr {
            display: flex;
            justify-content: center;
            margin-top: 18px;
        }
        .ticket-footer {
            text-align: center;
            color: #64748b;
            font-size: 0.95em;
            margin-top: 18px;
        }
        .btn-pdf {
            display: inline-block;
            margin: 18px auto 0 auto;
            background: #2563eb;
            color: #fff;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1em;
            transition: background 0.2s;
        }
        .btn-pdf:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
<div class="ticket-container">
    <div class="ticket-header">
        <h2>Paletería <span style="font-size:1.2em;">🍧</span></h2>
        <small>Sistema de ventas</small>
    </div>
    <div class="ticket-info">
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
    <div class="ticket-total">
        Total: ${{ number_format($sale->total, 2) }}
    </div>
    <div class="ticket-qr">
        {!! $qr !!}
    </div>
    @php $isPdf = $isPdf ?? false; @endphp
    @if(!$isPdf)
        @auth
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'gerente')
                <div style="display: flex; justify-content: center;">
                    <a href="{{ route('ticket.pdf', $sale) }}" target="_blank" class="btn-pdf">Descargar PDF</a>
                </div>
            @endif
        @endauth
    @endif
    <div class="ticket-footer">
        ¡Gracias por tu compra!
    </div>
</div>
</body>
</html>
