<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte diario {{ $date }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        .title { font-size: 18px; font-weight: 700; margin: 0; }
        .meta { color: #444; margin: 6px 0 12px; }

        /* ✅ Cards en columna, alineadas a la izquierda */
        .cards { width: 100%; margin-bottom: 10px; }
        .card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
            width: 100%;
            text-align: left;
            margin-bottom: 8px; /* separación vertical */
        }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border-bottom: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }

        .qrbox { margin-top: 14px; }
        img { width: 140px; height: 140px; }
        .small { font-size: 10px; color: #666; word-break: break-all; }
    </style>
</head>
<body>

    <p class="title">Reporte diario — Paletería 🍧</p>
    <p class="meta">Fecha: <b>{{ $date }}</b></p>

    <div class="cards">
        <!-- 🔼 Arriba -->
        <div class="card">
            <div>Ventas del día</div>
            <div style="font-size:22px; font-weight:700;">
                {{ $salesCount }}
            </div>
        </div>

        <!-- 🔽 Abajo -->
        <div class="card">
            <div>Total del día</div>
            <div style="font-size:22px; font-weight:700;">
                ${{ number_format($total, 2) }}
            </div>
        </div>
    </div>

    <h3 style="margin:10px 0 6px;">Resumen por producto</h3>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byProduct as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ (int)$row->qty }}</td>
                    <td>${{ number_format((float)$row->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Hoy no hay ventas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!empty($qrBase64))
    <div class="qrbox">
        <p><b>QR del reporte</b></p>
        <img src="{{ $qrBase64 }}" alt="QR">
        <p class="small">{{ $privateUrl }}</p>
    </div>
    @endif

</body>
</html>
