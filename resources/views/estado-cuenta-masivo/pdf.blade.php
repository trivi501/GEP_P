<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estado de Cuenta Masivo</title>
    <style>
        @page { margin: 8mm 10mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 8pt; color: #1a1a1a; line-height: 1.3; padding: 8mm 10mm; }

        .header { padding: 8px 0; border-bottom: 3px double #333; margin-bottom: 12px; }
        .header-table { width: 100%; }
        .header-table td { vertical-align: middle; }
        .header-table .logo-cell { width: 100px; text-align: left; }
        .header-table .title-cell { text-align: center; }
        .header-table .logo { max-height: 100px; max-width: 120px; }
        .header h1 { font-size: 13pt; text-transform: uppercase; letter-spacing: 1px; }
        .header h3 { font-size: 8pt; font-weight: normal; color: #555; margin-top: 3px; }

        .section { margin-bottom: 10px; line-height: 1; }

        .grid-2 { width: 100%; }
        .grid-2 td { vertical-align: top; padding: 0 4px; }
        .grid-2 td:first-child { padding-left: 0; }
        .grid-2 td:last-child { padding-right: 0; }

        table.data { width: 100%; border-collapse: collapse; font-size: 6.5pt; }
        table.data th, table.data td { border: 1px solid #999; padding: 2px 3px; text-align: right; }
        table.data th { background: #803047; color: white; text-align: center; font-size: 6.5pt; font-weight: bold; }
        table.data td:first-child { text-align: center; }
        table.data .total-row td { font-weight: bold; background: #f3f4f6; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; padding: 4px 12mm 2px; border-top: 1px solid #ccc; font-size: 6pt; color: #888; background: white; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .predio-info { font-size: 7pt; margin-bottom: 4px; }
        .predio-info b { font-size: 7.5pt; }
        .predio-divider { border-top: 2px solid #333; margin: 10px 0; }

        .gran-total { font-size: 10pt; font-weight: bold; text-align: right; padding: 8px; background: #e5e7eb; margin-top: 12px; }
    </style>
</head>
<body>

    @php $logoBase64 = base64_encode(file_get_contents(public_path('img/logo_guindo.png'))); @endphp

    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell" width="25%">
                    <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo" alt="Logo">
                </td>
                <td class="title-cell" width="50%">
                    <h2>MUNICIPIO DE GUADALUPE, ZACATECAS</h2>
                    <h3>DEPARTAMENTO DE CATASTRO</h3>
                    <h3 style="font-size: 11pt; margin-top: 4px;"><b>Estado de Cuenta Masivo</b></h3>
                </td>
                <td style="font-size: 7.5pt; padding: 4px;" width="25%">
                    <b>Generado por:</b> {{ auth()->user()?->name ?? 'Sistema' }}<br>
                    <b>Fecha:</b> {{ now()->format('d/m/Y') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p style="font-size: 6pt; font-style: italic;">
            <strong>Estado de cuenta informativo, el costo real puede variar al realizar el pago en caja.</strong>
        </p>
    </div>

    @php
        $counter = 0;
        $firstPredio = $data[0]['predio'] ?? null;
        $contribuyente = $firstPredio?->contribuyente;
    @endphp

    @if($contribuyente)
    <div class="section" style="margin-bottom: 14px; padding: 8px; border: 1px solid #999; background: #f9f9f9;">
        <table style="width: 100%;">
            <tr>
                <td style="font-size: 8pt; padding: 2px 6px;"><b>Cuenta:</b> {{ $contribuyente->cuenta ?? '—' }}</td>
                <td style="font-size: 8pt; padding: 2px 6px;"><b>Contribuyente:</b> {{ $contribuyente->nombre_completo ?? '—' }}</td>
            </tr>
        </table>
    </div>
    @endif

    @foreach($data as $item)
        @php
            $predio = $item['predio'];
            $calculos = $item['calculos'];
            $esRustico = $item['esRustico'];
            $counter++;
        @endphp

        @if($counter > 1)
            <hr style="border: none; border-top: 2px solid #333; margin: 14px 0;">
        @endif

        <div class="section">
            <table style="width:100%; background: #803047; color: white; padding: 4px 6px; margin-bottom: 6px;">
                <tr>
                    <td style="font-size: 8pt;"><b>Predio #{{ $counter }} — {{ $predio->Clave_predial }}</b></td>
                </tr>
            </table>

            <table style="width: 100%;">
                <tr>
                    <td style="vertical-align: top; padding: 0 4px; width: 40%;">
                        <div class="predio-info"><b>Ubicación:</b> {{ $predio->ubicacion_completa }}</div>
                    </td>
                    <td style="vertical-align: top; padding: 0 4px; width: 25%;">
                        <div class="predio-info"><b>Clave Predial:</b> {{ $predio->clavePredial?->clave_predial_completa ?? '—' }}</div>
                        <div class="predio-info"><b>Zona:</b> {{ $predio->datosUrbano->zonaUrbana->descripcion ?? '—' }}</div>
                    </td>
                    <td style="vertical-align: top; padding: 0 4px; width: 35%;">
                        <div class="predio-info"><b>Tipo:</b> {{ $predio->tipoPredio->Tipo_predio ?? '—' }}</div>
                        @if(!$esRustico)
                            <div class="predio-info"><b>Terreno:</b> {{ number_format($predio->datosUrbano->superficie_terreno_metros_cuadrados ?? 0, 2) }} m²</div>
                            <div class="predio-info"><b>Construcción:</b> {{ number_format($predio->construccion ?? 0, 2) }} m²</div>
                        @else
                            <div class="predio-info"><b>Superficie:</b> {{ number_format($predio->superficie ?? 0, 4) }} m²</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        @if(!$esRustico)
            <div class="section">
                <table class="data">
                    <thead>
                        <tr>
                            <th>Año</th>
                            <th>Terreno</th>
                            <th>Construcción</th>
                            <th>Baldío</th>
                            <th>C.M.</th>
                            <th>Entero</th>
                            <th>A.P.</th>
                            <th>Recargos</th>
                            <th>Actualización</th>
                            <th>Cobranza</th>
                            <th>Multa</th>
                            <th>Descuento</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calculos as $c)
                        <tr>
                            <td>{{ $c['anho'] }}</td>
                            <td>${{ number_format($c['imp_terreno'], 2) }}</td>
                            <td>${{ number_format($c['imp_construccion'], 2) }}</td>
                            <td>${{ ($c['baldio'] ?? 0) ? number_format($c['baldio'], 2) : '0.00' }}</td>
                            <td>${{ number_format($c['cm'], 2) }}</td>
                            <td>${{ number_format($c['entero'], 2) }}</td>
                            <td>${{ number_format($c['aseo_publico'] ?? 0, 2) }}</td>
                            <td>${{ number_format($c['recargos'] ?? 0, 2) }}</td>
                            <td>${{ number_format($c['actualizacion'] ?? 0, 2) }}</td>
                            <td>${{ number_format($c['cobranza'] ?? 0, 2) }}</td>
                            <td>${{ number_format($c['multa'] ?? 0, 2) }}</td>
                            <td>${{ number_format($c['descuento'] ?? 0, 2) }}</td>
                            <td><b>${{ number_format($c['total'], 2) }}</b></td>
                        </tr>
                        @endforeach
                        @php
                            $coll = collect($calculos);
                            $subtotalPredio = $coll->sum('total');
                        @endphp
                        <tr class="total-row">
                            <td><b>Total Predio</b></td>
                            <td><b>${{ number_format($coll->sum('imp_terreno'), 2) }}</b></td>
                            <td><b>${{ number_format($coll->sum('imp_construccion'), 2) }}</b></td>
                            <td><b>${{ number_format($coll->sum(fn($r) => $r['baldio'] ?? 0), 2) }}</b></td>
                            <td><b>${{ number_format($coll->sum('cm'), 2) }}</b></td>
                            <td><b>${{ number_format($coll->sum('entero'), 2) }}</b></td>
                            <td><b>${{ number_format($coll->sum('aseo_publico'), 2) }}</b></td>
                            <td><b>${{ number_format($coll->sum('recargos'), 2) }}</b></td>
                            <td><b>${{ number_format($coll->sum('actualizacion'), 2) }}</b></td>
                            <td><b>${{ number_format($coll->sum(fn($r) => $r['cobranza'] ?? 0), 2) }}</b></td>
                            <td><b>${{ number_format($coll->sum(fn($r) => $r['multa'] ?? 0), 2) }}</b></td>
                            <td><b>${{ number_format($coll->sum('descuento'), 2) }}</b></td>
                            <td><b>${{ number_format($subtotalPredio, 2) }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        @else
            <div class="section">
                <table class="data">
                    <thead>
                        <tr>
                            <th>Año</th>
                            <th>UMA</th>
                            <th>Hectáreas</th>
                            <th>Subtotal</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calculos as $c)
                        <tr>
                            <td>{{ $c['anho'] }}</td>
                            <td>${{ number_format($c['uma'], 2) }}</td>
                            <td>{{ number_format($c['hectareas'], 4) }}</td>
                            <td>${{ number_format($c['subtotal'], 2) }}</td>
                            <td>${{ number_format($c['total'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="total-row">
                            <td><b>Total Predio</b></td>
                            <td></td>
                            <td></td>
                            <td><b>${{ number_format($subtotalPredio, 2) }}</b></td>
                            <td><b>${{ number_format($subtotalPredio, 2) }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        @endif
    @endforeach

    <div class="predio-divider"></div>

    <div class="gran-total">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left; font-size: 8pt;">
                    <b>Total de Predios:</b> {{ $totalPredios }}
                </td>
                <td style="text-align: right; font-size: 11pt;">
                    <b>TOTAL: ${{ number_format($granTotal, 2) }}</b>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
