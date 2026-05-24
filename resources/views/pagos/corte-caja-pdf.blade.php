<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Corte de Caja #{{ $historialCaja->id }}</title>
    <style>
        @page { size: A4; margin: 0; }
        body { padding: 15mm 12mm; margin: 0; font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9pt; color: #000; line-height: 1.4; }
        .page { width: 100%; }
        .header {
            text-align: center;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
            margin-bottom: 12px;
        }
        .header h1 { font-size: 13pt; text-transform: uppercase; margin-bottom: 2px; }
        .header p { font-size: 7.5pt; color: #000; }
        .info-grid {
            width: 100%;
            margin-bottom: 12px;
        }
        .info-grid td {
            padding: 2px 4px;
            font-size: 8pt;
        }
        .info-grid .label {
            font-weight: bold;
            width: 30%;
            text-transform: uppercase;
            font-size: 7pt;
        }
        .section-title {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            margin-bottom: 6px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data th {
            border-bottom: 1.5px solid #000;
            padding: 4px 6px;
            text-align: left;
            font-size: 7.5pt;
            text-transform: uppercase;
        }
        table.data th.right { text-align: right; }
        table.data td {
            padding: 3px 6px;
            border-bottom: 0.5px solid #000;
            font-size: 7.5pt;
        }
        table.data td.right { text-align: right; }
        table.data .total-row td {
            font-weight: bold;
            font-size: 8pt;
            border-top: 1.5px solid #000;
            padding-top: 4px;
        }
        .summary-box {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 12px;
            text-align: center;
        }
        .summary-box .amount {
            font-size: 16pt;
            font-weight: bold;
        }
        .summary-box .label {
            font-size: 7.5pt;
            text-transform: uppercase;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #000;
            font-size: 7pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Corte de Caja</h1>
        <p>{{ \Carbon\Carbon::parse($historialCaja->datetime_apertura)->format('d/m/Y') }}</p>
    </div>

    <table class="info-grid">
        <tr>
            <td class="label">Cajero</td>
            <td>{{ $historialCaja->cajero?->usuario?->name ?? '—' }}</td>
            <td class="label">Caja</td>
            <td>{{ $historialCaja->caja?->nombre ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Apertura</td>
            <td>{{ \Carbon\Carbon::parse($historialCaja->datetime_apertura)->format('d/m/Y H:i') }}</td>
            <td class="label">Cierre</td>
            <td>{{ $historialCaja->datetime_cierre ? \Carbon\Carbon::parse($historialCaja->datetime_cierre)->format('d/m/Y H:i') : '—' }}</td>
        </tr>
        <tr>
            <td class="label">Fondo</td>
            <td>${{ number_format($historialCaja->fondo, 2) }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <div class="section-title">Resumen por Método de Pago</div>
    <table class="data">
        <thead>
            <tr>
                <th>Método</th>
                <th class="right">Pagos</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pagosPorForma as $item)
            <tr>
                <td>{{ $item['forma'] }}</td>
                <td class="right">{{ $item['count'] }}</td>
                <td class="right">${{ number_format($item['total'], 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center;">Sin pagos</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td>Total</td>
                <td class="right">{{ $totalRecibos }}</td>
                <td class="right">${{ number_format($totalIngresos, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Recibos</div>
    <table class="data">
        <thead>
            <tr>
                <th>Folio</th>
                <th>Nombre</th>
                <th class="right">Monto</th>
                <th>Método</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pagos as $pago)
            <tr>
                <td>{{ $pago->folio }}</td>
                <td>{{ $pago->nombre ?: '—' }}</td>
                <td class="right">${{ number_format($pago->monto, 2) }}</td>
                <td>
                    {{ $pago->formasPagosCada->map(fn($f) => $f->formaPago?->Descripción)->filter()->implode(', ') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Sin pagos</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <div class="label">Total de Ingresos</div>
        <div class="amount">${{ number_format($totalIngresos, 2) }}</div>
        <div style="margin-top: 4px; font-size: 8pt;">{{ $totalRecibos }} recibo(s)</div>
    </div>

</body>
</html>