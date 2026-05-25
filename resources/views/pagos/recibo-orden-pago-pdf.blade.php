<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo de Pago - {{ $pago->folio }}</title>
    <style>
        @page { margin: 10mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #1a1a1a;
            line-height: 1.4;
            background-image: url("{{ public_path('img/formatoRecibo.jpg') }}");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }

        .header { padding: 4px 0; border-bottom: 2px double #333; margin-bottom: 6px; text-align: center; }
        .header h1 { font-size: 11pt; text-transform: uppercase; letter-spacing: 1px; margin: 0; }
        .header h2 { font-size: 9pt; margin: 0; }

        .section { margin-bottom: 4px; margin-top: 135px; margin-right: 10px; margin-left: 10px; line-height: 1; }
        .section-title { font-size: 6pt; margin-right: 20px; margin-left: 20px; text-align: center; font-weight: bold; text-transform: uppercase; background: #e5e7eb; padding: 2px 4px; border-bottom: 1px solid #333; margin-bottom: 2px; }

        .info-table { width: 100%; border-collapse: collapse; border-spacing: 0; font-size: 7pt; }
        .info-table td { padding: 0; font-size: 6.5pt; }
        .info-table .label { font-weight: bold; width: 30%; color: #555; text-transform: uppercase; font-size: 5.5pt; }
        .info-table .value { width: 70%; }

        table.conceptos { width: 90%; margin: 0 auto; border-collapse: collapse; border-spacing: 0; font-size: 6.5pt; }
        table.conceptos th { background: #803047; color: white; padding: 0.5px 2px; text-align: left; font-size: 6pt; }
        table.conceptos th.monto { text-align: right; }
        table.conceptos td { padding: 0.5px 2px; border-bottom: 0.5px solid #ddd; font-size: 6pt; }
        table.conceptos td.monto { text-align: right; }
        table.conceptos .total-row td { font-weight: bold; font-size: 7pt; border-top: 0.5px solid #333; padding-top: 1px; }

        .footer { text-align: center; margin-top: 24px; padding-top: 10px; border-top: 1px solid #ccc; font-size: 7pt; color: #000000; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 3px; font-size: 7pt; font-weight: bold; text-transform: uppercase; }
        .badge-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        .recibo-original{
            position: absolute;
            left: 10px;
            right: 10px;
            top: 10px;
        }

        .recibo-copia{
            position: absolute;
            left: 10px;
            right: 10px;
            top: 570px;
        }

        .footer_recibo_original{
            position: absolute;
            left: 10px;
            right: 10px;
            bottom: 10px;
            top: 480px;
        }

        .footer_recibo_copia{
            position: absolute;
            left: 10px;
            right: 10px;
            bottom: 10px;
            top: 1040px;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 48pt;
            font-weight: bold;
            color: rgba(200, 0, 0, 0.3);
            border: 6px double rgba(200, 0, 0, 0.3);
            padding: 20px 40px;
            z-index: 1000;
            text-transform: uppercase;
            letter-spacing: 4px;
        }
    </style>
</head>
<body>
    @if ($pago->estatus === 'cancelado')
        <div class="watermark">Cancelado</div>
    @endif
    @php
        $ordenPago = $pago->ordenPago;
    @endphp
    <div class="recibo-original">
        <div class="section">
            <table width="100%">
                <tr>
                    <td class="label" style="font-size: 9pt;" width="80%">Contribuyente</td>
                    <td class="label" style="font-size: 9pt; text-align: right;" width="20%">{{ $pago->folio }}</td>
                </tr>
                <tr>
                    <td class="value" width="80%" style="font-weight: bold; font-size: 9pt;">{{ $ordenPago?->nombre ?? $pago->nombre ?? '—' }}</td>
                    <td width="30%" style="text-align: right; font-size: 7pt;">Fecha: {{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
            <table class="info-table">
                <tr>
                    <table>
                        <tr>
                            <td class="label" style="width:120px">No. Orden</td>
                            <td class="label" style="width:100px">Secretaría</td>
                        </tr>
                        <tr>
                            <td class="value" style="width:120px; font-size: 10pt;"><b>{{ $ordenPago?->folio ?? '—' }}</b></td>
                            <td class="value" style="width:100px; font-size: 10pt;"><b>{{ $ordenPago?->secretaria?->nombre ?? '—' }}</b></td>
                        </tr>
                    </table>
                </tr>
                <tr>
                    <table>
                        <tr>
                            <td class="label" style="width:120px">Folio Orden</td>
                        </tr>
                        <tr>
                            <td class="value" style="width:120px">{{ $ordenPago?->folio ?? '—' }}</td>
                        </tr>
                    </table>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="label" style="width:100%">Descripción</td>
                </tr>
                <tr>
                    <td class="value" style="width:100%; font-size: 9pt;"><b>{{ $ordenPago?->descripcion ?? '—' }}</b></td>
                </tr>
            </table>
        </div>

        <div class="section-title">Conceptos</div>
        <table class="conceptos">
            <thead>
                <tr>
                    <th>Indetec</th>
                    <th>Concepto</th>
                    <th>Cant.</th>
                    <th class="monto">Importe</th>
                    <th class="monto">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pago->cuentasPagos as $c)
                <tr>
                    <td>{{ $c->cuenta?->indetec ?? '—' }}</td>
                    <td>{{ $c->concepto }}</td>
                    <td>{{ $c->cantidad }}</td>
                    <td class="monto">${{ number_format($c->cantidad > 0 ? $c->monto / $c->cantidad : $c->monto, 2) }}</td>
                    <td class="monto">${{ number_format($c->monto, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4">Total</td>
                    <td class="monto">${{ number_format($pago->monto, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 8px; text-align: right; font-size: 7pt; margin-right: 25px;">
            <div>TOTAL ${{ number_format($pago->monto, 2) }}</div>
        </div>


    </div>

    <div class="recibo-copia">
               <div class="section">
            <table width="100%">
                <tr>
                    <td class="label" style="font-size: 9pt;" width="80%">Contribuyente</td>
                    <td class="label" style="font-size: 9pt; text-align: right;" width="20%">{{ $pago->folio }}</td>
                </tr>
                <tr>
                    <td class="value" width="80%" style="font-weight: bold; font-size: 9pt;">{{ $ordenPago?->nombre ?? $pago->nombre ?? '—' }}</td>
                    <td width="30%" style="text-align: right; font-size: 7pt;">Fecha: {{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
            <table class="info-table">
                <tr>
                    <table>
                        <tr>
                            <td class="label" style="width:120px">No. Orden</td>
                            <td class="label" style="width:100px">Secretaría</td>
                        </tr>
                        <tr>
                            <td class="value" style="width:120px; font-size: 10pt;"><b>{{ $ordenPago?->folio ?? '—' }}</b></td>
                            <td class="value" style="width:100px; font-size: 10pt;"><b>{{ $ordenPago?->secretaria?->nombre ?? '—' }}</b></td>
                        </tr>
                    </table>
                </tr>
                <tr>
                    <table>
                        <tr>
                            <td class="label" style="width:120px">Folio Orden</td>
                        </tr>
                        <tr>
                            <td class="value" style="width:120px">{{ $ordenPago?->folio ?? '—' }}</td>
                        </tr>
                    </table>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="label" style="width:100%">Descripción</td>
                </tr>
                <tr>
                    <td class="value" style="width:100%; font-size: 9pt;"><b>{{ $ordenPago?->descripcion ?? '—' }}</b></td>
                </tr>
            </table>
        </div>

        <div class="section-title">Conceptos</div>
        <table class="conceptos">
            <thead>
                <tr>
                    <th>Indetec</th>
                    <th>Concepto</th>
                    <th>Cant.</th>
                    <th class="monto">Importe</th>
                    <th class="monto">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pago->cuentasPagos as $c)
                <tr>
                    <td>{{ $c->cuenta?->indetec ?? '—' }}</td>
                    <td>{{ $c->concepto }}</td>
                    <td>{{ $c->cantidad }}</td>
                    <td class="monto">${{ number_format($c->cantidad > 0 ? $c->monto / $c->cantidad : $c->monto, 2) }}</td>
                    <td class="monto">${{ number_format($c->monto, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4">Total</td>
                    <td class="monto">${{ number_format($pago->monto, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 8px; text-align: right; font-size: 7pt; margin-right: 25px;">
            <div>TOTAL ${{ number_format($pago->monto, 2) }}</div>
        </div>
    </div>
</body>
</html>