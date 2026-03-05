<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte {{ $periodoLabel }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        .title { font-size: 18px; font-weight: 700; margin: 0; }
        .meta { color: #444; margin: 6px 0 12px; }

        .cards { width: 100%; margin-bottom: 10px; }
        .card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
            width: 100%;
            text-align: left;
            margin-bottom: 8px;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border-bottom: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }

        .section-title { font-size: 14px; font-weight: 700; margin: 16px 0 8px; border-bottom: 2px solid #ec4899; padding-bottom: 4px; }
        
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <p class="title">Reporte por rango — Paletería 🍧</p>
    <p class="meta">Período: <b>{{ $periodoLabel }}</b></p>
    <p class="meta">Generado: {{ now()->format('d/m/Y H:i') }}</p>

    <div class="cards">
        <div class="card">
            <div>Total de ventas</div>
            <div style="font-size:22px; font-weight:700;">
                ${{ number_format($totalVentas, 2) }}
            </div>
        </div>

        <div class="card">
            <div>Tickets / transacciones</div>
            <div style="font-size:22px; font-weight:700;">
                {{ $totalTickets }}
            </div>
        </div>

        <div class="card">
            <div>Ticket promedio</div>
            <div style="font-size:22px; font-weight:700;">
                ${{ $totalTickets > 0 ? number_format($totalVentas / $totalTickets, 2) : '0.00' }}
            </div>
        </div>
    </div>

    <p class="section-title">Top 10 productos</p>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ (int)$row->qty }}</td>
                    <td>${{ number_format((float)$row->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No hay productos vendidos en este período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="section-title">Ventas por vendedor</p>
    <table>
        <thead>
            <tr>
                <th>Vendedor</th>
                <th>Ventas</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesBySeller as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ (int)$row->qty }}</td>
                    <td>${{ number_format((float)$row->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No hay ventas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="section-title">Ingresos por categoría</p>
    <table>
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($revenueByCategory as $row)
                <tr>
                    <td>{{ $row->category }}</td>
                    <td>${{ number_format((float)$row->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No hay datos de categorías.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="section-title">Ventas por hora</p>
    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>Ventas</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesByHour as $row)
                <tr>
                    <td>{{ $row->hour }}:00</td>
                    <td>{{ (int)$row->qty }}</td>
                    <td>${{ number_format((float)$row->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No hay ventas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!empty($qrBase64))
    <div style="margin-top: 20px; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
        <p style="font-weight: 700; margin-bottom: 8px;">QR del reporte</p>
        <img src="{{ $qrBase64 }}" alt="QR" style="width: 140px; height: 140px;">
        <p style="font-size: 10px; color: #666; word-break: break-all; margin-top: 6px;">{{ $reportUrl }}</p>
    </div>
    @endif

</body>
</html>
