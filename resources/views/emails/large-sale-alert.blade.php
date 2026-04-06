<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alerta de venta alta</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; background: #f8fafc; padding: 24px;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px;">
        <h1 style="margin-top: 0; font-size: 24px;">Alerta de venta alta</h1>
        <p>Se registró una venta que superó el umbral configurado de ${{ number_format($threshold, 2) }}.</p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
            <tr>
                <td style="padding: 8px 0; font-weight: 700;">Ticket</td>
                <td style="padding: 8px 0;">#{{ $sale->id }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: 700;">Total</td>
                <td style="padding: 8px 0;">${{ number_format((float) $sale->total, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: 700;">Vendedor</td>
                <td style="padding: 8px 0;">{{ $sale->user?->name ?? 'Sistema' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: 700;">Fecha</td>
                <td style="padding: 8px 0;">{{ $sale->created_at?->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <h2 style="margin-top: 24px; font-size: 18px;">Productos</h2>
        <ul style="padding-left: 20px;">
            @foreach($sale->details as $detail)
                <li>{{ $detail->product?->name ?? 'Producto' }} x {{ $detail->qty }} = ${{ number_format((float) $detail->subtotal, 2) }}</li>
            @endforeach
        </ul>
    </div>
</body>
</html>