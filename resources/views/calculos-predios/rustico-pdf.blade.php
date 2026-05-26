<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estado de Cuenta Rústico - {{ $predio->Clave_predial }}</title>
    <style>
        @page { margin: 20mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9pt; color: #1a1a1a; line-height: 1.4; }

        .header { padding: 10px 0; border-bottom: 3px double #333; margin-bottom: 16px; }
        .header-table { width: 100%; }
        .header-table td { vertical-align: middle; }
        .header-table .logo-cell { width: 100px; text-align: left; }
        .header-table .title-cell { text-align: center; }
        .header-table .logo { max-height: 80px; max-width: 100px; }
        .header h1 { font-size: 16pt; text-transform: uppercase; letter-spacing: 2px; }
        .header h3 { font-size: 10pt; font-weight: normal; color: #555; margin-top: 4px; }

        .section { margin-bottom: 14px; line-height: 1; }
        .section-title { font-size: 10pt; font-weight: bold; text-transform: uppercase; background: #e5e7eb; padding: 5px 8px; border-bottom: 2px solid #333; margin-bottom: 6px; }

        .grid-2 { width: 100%; }
        .grid-2 td { vertical-align: top; padding: 0 6px; }
        .grid-2 td:first-child { padding-left: 0; }
        .grid-2 td:last-child { padding-right: 0; }

        .field { margin-bottom: 3px; }
        .field .label { font-size: 7pt; color: #888; text-transform: uppercase; display: block; }
        .field .value { font-size: 9pt; font-weight: 500; display: block; }

        table.data { width: 100%; border-collapse: collapse; font-size: 7.5pt; }
        table.data th, table.data td { border: 1px solid #999; padding: 3px 5px; text-align: right; }
        table.data th { background: #e5e7eb; text-align: center; font-size: 7.5pt; font-weight: bold; }
        table.data td:first-child, table.data td:nth-child(7) { text-align: center; }
        table.data .total-row td { font-weight: bold; background: #f3f4f6; }

        .footer { text-align: center; margin-top: 24px; padding-top: 10px; border-top: 1px solid #ccc; font-size: 7pt; color: #888; }

        .page-break { page-break-before: always; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <div style="padding: 30px 30px 0;">

    {{-- ENCABEZADO --}}
    @php $logoBase64 = base64_encode(file_get_contents(public_path('img/logo_guindo.png'))); @endphp
    <div class="section">
        <table class="header-table">
            <tr>
                <td class="logo-cell" width="20%">
                    <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo" alt="Logo">
                </td>
                <td class="title-cell" width="60%" >
                    <h2>MUNICIPIO DE GUADALUPE, ZACATECAS</h2>
                    <h3> DEPARTAMENTO DE CATASTRO </h3>   
                    <h3>Clave Catastral: {{ $predio->Clave_predial }}</h3>
                </td>
                <td style="font-size: 7.5pt; padding: 2px;" width="20%"><b>Generado por: {{ auth()->user()->name }}</b> </td>
            </tr>
        </table>
        <table class="header-table" style="margin-top: 8px;">
            <tr>
                <td width="30%" style="font-size: 7.5pt; padding: 2px;">&nbsp;</td>
                <td width="40%" style="font-size: 14.5pt; padding: 2px; text-align: center;"><b>Estado de Cuenta</b> </td>
                <td width="30%" style="font-size: 6.5pt; padding: 2px; text-align: right;"> <b>{{ \Carbon\Carbon::parse(now())->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY, h:mm a') }}</b> </td>
            </tr>
        </table>
    </div>

    {{-- DATOS DEL CONTRIBUYENTE --}}
    <div class="section">
        <table class="grid-2">
            <tr>
                <td width="12%" style="font-size: 7.5pt; background: #803047; color: white; padding: 2px;"><b>Cuenta:</b><br><b>Contribuyente:</b></td>
                <td style="font-size: 7.5pt; padding: 2px;"><b>{{ $predio->contribuyente->cuenta ?? '—' }} </b> <br> <b>{{ $predio->contribuyente->nombre_completo ?? $predio->contribuyente->nombre ?? '—' }}</b></td>
            </tr>
        </table>
    </div>

    {{-- UBICACIÓN --}}
    <div class="section">
        <table class="grid-2" style="border-collapse: collapse;">
            <tr>
                    <td width="12%" style="font-size: 6.5pt; border: 1px solid black; padding: 3px; text-align: right;"><b>Clave Predial:</b><br><b>Ubicación:</b></td>
                    <td width="65%" style="font-size: 6.5pt; border: 1px solid black; padding: 3px;"> <b>{{ $predio->clavePredial?->clave_predial_completa ?? '—' }}</b> <br> <b>{{ $predio->ubicacion_completa }}</b> </td>
                    <td width="10%" style="font-size: 6.5pt; border: 1px solid black; padding: 3px; text-align: right;"><b>ZONA:</b> <br> <b>Tipo Pred:</b></td>
                    <td width="10%" style="font-size: 6.5pt; border: 1px solid black; padding: 3px;"> <b>{{ '—' }}</b> <br> <b>{{ $predio->tipoPredio->Tipo_predio ?? '—' }}</b></td>
            </tr>  
        </table>
        <table class="grid-2" style="border-collapse: collapse;">
            <tr>
                <td width="12%" style="font-size: 6.5pt;  border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; padding: 2px;"><b>Superficie:</b></td>
                <td width="45%" style="font-size: 6.5pt;  border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; padding: 2px;"><b>{{ number_format($predio->superficie ?? 0, 4) }} m² ({{ number_format(($predio->superficie ?? 0) / 10000, 4) }} ha)</b></td>
                <td width="15%" style="font-size: 6.5pt;  border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; padding: 2px; text-align: right;"><b>Construccion:</b></td>
                <td width="35%" style="font-size: 6.5pt;  border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; padding: 2px;"><b>{{ number_format($predio->construccion ?? 0, 2) }} m²</b></td>
            </tr>
        </table>
    </div>

    {{-- CÁLCULOS PREDIALES RÚSTICOS --}}
    <div class="section">
        @php $tipos = array_unique(array_column($calculos, 'tipo_calculo')); @endphp
        <div style="font-size: 7.5pt; font-weight: bold; margin-bottom: 4px;">Tipo de Cálculo: {{ implode(', ', $tipos) }}</div>
        <table class="grid-2">
            <thead style="background: #803047; color: white; font-size: 6.5pt;">
                <tr style="background: #803047; color: white;">
                    <th>Año</th>
                    <th>UMA</th>
                    <th>Ha</th>
                    <th>Subtotal</th>
                    <th>Recargos</th>
                    <th>Actualiz.</th>
                    <th>Cobranza</th>
                    <th>Multa</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calculos as $c)
                <tr style="font-size: 6.5pt;">
                    <td>{{ $c['anho'] }}</td>
                    <td>${{ number_format($c['uma'], 2) }}</td>
                    <td>{{ number_format($c['hectareas'], 4) }}</td>
                    <td>${{ number_format($c['subtotal'], 2) }}</td>
                    <td>${{ number_format($c['recargos'], 2) }}</td>
                    <td>${{ number_format($c['actualizacion'], 2) }}</td>
                    <td>${{ number_format($c['cobranza'], 2) }}</td>
                    <td>${{ number_format($c['multa'], 2) }}</td>
                    <td>${{ number_format($c['total'], 2) }}</td>
                </tr>
                @endforeach
                @if (count($calculos) > 0)
                @php
                    $coll = collect($calculos);
                    $subtotalRustico = $coll->sum('subtotal');
                    $recargosRustico = $coll->sum('recargos');
                    $actualizacionRustico = $coll->sum('actualizacion');
                    $cobranzaRustico = $coll->sum('cobranza');
                    $multaRustico = $coll->sum('multa');
                    $totalRustico = $coll->sum('total');
                @endphp
                <tr class="total-row" style="font-size: 6.5pt;">
                    <td><b>Total</b></td>
                    <td></td>
                    <td></td>
                    <td><b>${{ number_format($subtotalRustico, 2) }}</b></td>
                    <td><b>${{ number_format($recargosRustico, 2) }}</b></td>
                    <td><b>${{ number_format($actualizacionRustico, 2) }}</b></td>
                    <td><b>${{ number_format($cobranzaRustico, 2) }}</b></td>
                    <td><b>${{ number_format($multaRustico, 2) }}</b></td>
                    <td><b>${{ number_format($totalRustico, 2) }}</b></td>
                </tr>
                <tr>
                    <td colspan="9" style="padding-top: 8px; font-size: 6.5pt;">
                        <b>Años pendientes de pago en predio:</b> {{ count($calculos) }} &nbsp;&nbsp;&nbsp;
                        <b>SubTotal Predio:</b> ${{ number_format($subtotalRustico, 2) }} &nbsp;&nbsp;&nbsp;
                        <b>Recargos:</b> ${{ number_format($recargosRustico, 2) }} &nbsp;&nbsp;&nbsp;
                        <b>Actualización:</b> ${{ number_format($actualizacionRustico, 2) }} &nbsp;&nbsp;&nbsp;
                        <b>Cobranza:</b> ${{ number_format($cobranzaRustico, 2) }} &nbsp;&nbsp;&nbsp;
                        <b>Multa:</b> ${{ number_format($multaRustico, 2) }} &nbsp;&nbsp;&nbsp;
                        <b>Total Predio:</b> ${{ number_format($totalRustico, 2) }}
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p style="font-size: 6.5pt; font-style: italic;"><strong>Estado de cuenta informativo, el costo real puede variar al realizar el pago en caja </strong></p>   
<br>
                 Estado de Cuenta generado el {{ \Carbon\Carbon::parse(now())->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY, h:mm a') }} &mdash; MUNICIPIO DE GUADALUPE, ZACATECAS
    </div>

    </div>

</body>
</html>
