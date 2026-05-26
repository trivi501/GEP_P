<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Corte de Caja #{{ $corte->id }}</title>
    <style>
        @page { size: A4; margin: 0; }
        body { padding: 10mm 8mm; margin: 0; font-family: 'Helvetica', 'Arial', sans-serif; font-size: 7pt; color: #000; line-height: 1.2; }
        .header { text-align: center; padding-bottom: 4px; border-bottom: 1.5px solid #000; margin-bottom: 6px; }
        .header h1 { font-size: 10pt; text-transform: uppercase; margin-bottom: 1px; }
        .header p { font-size: 6pt; color: #000; }
        .info-grid { width: 100%; margin-bottom: 6px; }
        .info-grid td { padding: 1px 2px; font-size: 6.5pt; }
        .info-grid .label { font-weight: bold; width: 20%; text-transform: uppercase; font-size: 6pt; }
        .section-title { font-size: 7pt; font-weight: bold; text-transform: uppercase; border-bottom: 0.5px solid #000; padding: 2px 0; margin-bottom: 3px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.data th { border-bottom: 1px solid #000; padding: 2px 3px; text-align: left; font-size: 6pt; text-transform: uppercase; }
        table.data th.right { text-align: right; }
        table.data th.center { text-align: center; }
        table.data td { padding: 1.5px 3px; border-bottom: 0.3px solid #000; font-size: 6pt; }
        table.data td.right { text-align: right; }
        table.data td.center { text-align: center; }
        table.data .total-row td { font-weight: bold; font-size: 6.5pt; border-top: 1px solid #000; padding-top: 2px; }
        .summary-box { border: 1px solid #000; padding: 6px; margin-top: 6px; text-align: center; }
        .summary-box .amount { font-size: 12pt; font-weight: bold; }
        .summary-box .label { font-size: 6pt; text-transform: uppercase; }
        .footer { text-align: center; margin-top: 10px; padding-top: 4px; border-top: 0.5px solid #000; font-size: 5.5pt; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Corte de Caja #{{ $corte->id }}</h1>
        <p>{{ \Carbon\Carbon::parse($corte->fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}</p>
    </div>

    <table class="info-grid">
        <tr>
            <td class="label">Fecha</td>
            <td>{{ $corte->fecha }}</td>
            <td class="label">Ingresos</td>
            <td>${{ number_format($corte->ingresos, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Urbano</td>
            <td>${{ number_format($corte->urbano, 2) }}</td>
            <td class="label">Rústico</td>
            <td>${{ number_format($corte->rustico, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Recibos</td>
            <td>{{ $corte->recibos_efectivos }}</td>
            <td class="label">Cancelados</td>
            <td>{{ $corte->recibos_cancelados }}</td>
        </tr>
    </table>

    <div class="section-title" style="font-size:8pt;">Resumen por Cuenta Contable (Ingresos)</div>
    <table class="data">
        <thead>
            <tr>
                <th>Cuenta</th>
                <th>Descripción</th>
                <th class="right">Monto</th>
                <th class="center">Recibos</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cuentasResumen as $item)
            <tr>
                <td>{{ $item['cuenta'] }}</td>
                <td>{{ $item['descripcion'] }}</td>
                <td class="right">${{ number_format($item['monto'], 2) }}</td>
                <td class="center">{{ $item['recibos'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Sin pagos</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2">Totales</td>
                <td class="right">${{ number_format($totales['monto'], 2) }}</td>
                <td class="center">{{ $totales['recibos'] }}</td>
            </tr>
        </tbody>
    </table>

    @if(!empty($descuentos))
    <div class="section-title">Descuentos</div>
    <table class="data">
        <thead>
            <tr>
                <th>Cuenta</th>
                <th>Descripción</th>
                <th class="right">Monto</th>
                <th class="center">Recibos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($descuentos as $item)
            <tr>
                <td>{{ $item['cuenta'] }}</td>
                <td>{{ $item['descripcion'] }}</td>
                <td class="right">${{ number_format($item['monto'], 2) }}</td>
                <td class="center">{{ $item['recibos'] }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">Total Descuentos</td>
                <td class="right">${{ number_format($totalDescuentos, 2) }}</td>
                <td class="center">—</td>
            </tr>
        </tbody>
    </table>
    @endif

    <div class="summary-box">
        <div class="label">Total de Ingresos</div>
        <div class="amount">${{ number_format($corte->ingresos, 2) }}</div>
        <div style="margin-top: 4px; font-size: 8pt;">{{ $corte->recibos_efectivos }} recibo(s) efectivos</div>
    </div>

    <div class="footer">
        Generado el {{ now()->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY, h:mm a') }}
    </div>
</body>
</html>
