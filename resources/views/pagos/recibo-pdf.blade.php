<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo de Pago - {{ $pago->folio }}</title>
    <style>
        @page { margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9pt; color: #1a1a1a; line-height: 1.4; }

        .header { padding: 10px 0; border-bottom: 3px double #333; margin-bottom: 16px; text-align: center; }
        .header h1 { font-size: 16pt; text-transform: uppercase; letter-spacing: 2px; }
        .header h2 { font-size: 13pt; margin-top: 4px; }
        .header h3 { font-size: 10pt; font-weight: normal; color: #555; margin-top: 4px; }

        .folio-box { text-align: right; margin-bottom: 12px; }
        .folio-box .folio { font-size: 14pt; font-weight: bold; color: #803047; }

        .section { margin-bottom: 14px; }
        .section-title { font-size: 10pt; font-weight: bold; text-transform: uppercase; background: #e5e7eb; padding: 5px 8px; border-bottom: 2px solid #333; margin-bottom: 6px; }

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 3px 5px; font-size: 8pt; }
        .info-table .label { font-weight: bold; width: 30%; color: #555; text-transform: uppercase; font-size: 7pt; }
        .info-table .value { width: 70%; }

        table.conceptos { width: 100%; border-collapse: collapse; font-size: 8pt; }
        table.conceptos th { background: #803047; color: white; padding: 5px 8px; text-align: left; font-size: 8pt; }
        table.conceptos th.monto { text-align: right; }
        table.conceptos td { padding: 4px 8px; border-bottom: 1px solid #ddd; }
        table.conceptos td.monto { text-align: right; }
        table.conceptos .total-row td { font-weight: bold; font-size: 10pt; border-top: 2px solid #333; padding-top: 6px; }

        .footer { text-align: center; margin-top: 24px; padding-top: 10px; border-top: 1px solid #ccc; font-size: 7pt; color: #888; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 3px; font-size: 7pt; font-weight: bold; text-transform: uppercase; }
        .badge-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        .amount-box { text-align: center; margin: 16px 0; }
        .amount-box .amount { font-size: 22pt; font-weight: bold; color: #155724; }
        .amount-box .label { font-size: 8pt; color: #888; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Municipio de Guadalupe, Zacatecas</h1>
        <h2>Departamento de Catastro</h2>
        <h3>Recibo de Pago</h3>
    </div>

    <div class="folio-box">
        <div class="folio">{{ $pago->folio }}</div>
        <span class="badge badge-success">{{ ucfirst($pago->estatus) }}</span>
    </div>

    <div class="section">
        <table class="info-table">
            <tr>
                <td class="label">Contribuyente</td>
                <td class="value">{{ $pago->nombre }}</td>
            </tr>
            <tr>
                <td class="label">RFC</td>
                <td class="value">{{ $pago->rfc }}</td>
            </tr>
            <tr>
                <td class="label">Fecha</td>
                <td class="value">{{ \Carbon\Carbon::parse($pago->fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY, h:mm a') }}</td>
            </tr>
            @if ($pago->descripcion)
            <tr>
                <td class="label">Descripción</td>
                <td class="value">{{ $pago->descripcion }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">Conceptos</div>
        <table class="conceptos">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th class="monto">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pago->cuentasPagos as $c)
                <tr>
                    <td>{{ $c->concepto }}</td>
                    <td class="monto">${{ number_format($c->monto, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total</td>
                    <td class="monto">${{ number_format($pago->monto, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="amount-box">
        <div class="label">Total Pagado</div>
        <div class="amount">${{ number_format($pago->monto, 2) }}</div>
    </div>

    <div class="footer">
        <p>Recibo generado el {{ \Carbon\Carbon::parse(now())->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY, h:mm a') }}</p>
        <p>Municipio de Guadalupe, Zacatecas &mdash; Departamento de Catastro</p>
    </div>

</body>
</html>