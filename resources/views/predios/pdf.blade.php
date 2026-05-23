<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Predio {{ $predio->Clave_predial }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            color: #2d3748;
            margin: 0.5in;
            padding: 0;
            line-height: 1.15;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #1a202c;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header h1 {
            font-size: 14px;
            color: #1a202c;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .header .subtitle {
            font-size: 9px;
            color: #4a5568;
            margin: 0;
        }
        .header .clave {
            font-size: 11px;
            font-weight: bold;
            color: #2b6cb0;
            margin-top: 2px;
        }

        .section {
            margin-bottom: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
            line-height: 1;
        }
        .section-title {
            font-size: 9px;
            font-weight: bold;
            color: #fff;
            background: #2d3748;
            padding: 3px 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .section-body {
            padding: 3px 8px 4px 8px;
        }

        table.info {
            width: 100%;
            border-collapse: collapse;
        }
        table.info tr {
            border-bottom: 1px dotted #e2e8f0;
        }
        table.info tr:last-child {
            border-bottom: none;
        }
        table.info td {
            padding: 1px 4px;
            vertical-align: top;
            font-size: 7.5px;
            line-height: 1.2;
        }
        table.info td.label {
            width: 110px;
            font-weight: bold;
            color: #4a5568;
            white-space: nowrap;
        }
        table.info td.value {
            color: #1a202c;
        }

        .two-col {
            width: 100%;
            border-collapse: collapse;
        }
        .two-col td {
            width: 50%;
            vertical-align: top;
        }
        .two-col td:first-child {
            padding-right: 5px;
        }
        .two-col td:last-child {
            padding-left: 5px;
        }

        table.medidas {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5px;
        }
        table.medidas thead th {
            background: #4a5568;
            color: #fff;
            padding: 2px 6px;
            text-align: left;
            font-size: 7.5px;
        }
        table.medidas tbody td {
            padding: 1px 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        table.medidas tbody tr:nth-child(even) {
            background: #f7fafc;
        }
        table.medidas tbody tr:last-child td {
            border-bottom: none;
        }

        .total-row {
            background: #edf2f7;
            font-weight: bold;
        }
        .total-row td {
            padding: 2px 6px;
            border-top: 2px solid #2d3748;
        }
        .total-row .total-label {
            text-align: right;
        }
        .total-row .total-value {
            font-size: 10px;
            color: #2b6cb0;
        }

        .footer {
            position: fixed;
            bottom: 8px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }

        .badge {
            display: inline-block;
            background: #ebf8ff;
            color: #2b6cb0;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
        }

        .value-highlight {
            font-size: 9px;
            font-weight: bold;
            color: #2d3748;
        }
    </style>
</head>
<body>
    <div class="header" style="position:relative;">
        <img src="{{ public_path('img/logo_guindo.png') }}" style="position:absolute; left:0; top:0; height:75px; width:auto;">
        <h1 style="margin-left:70px;">MUNICIPIO DE GUADALUPE, ZACATECAS</h1>
        <p class="subtitle" style="margin-left:70px;">DEPARTAMENTO DE CATASTRO</p>
        <p class="subtitle" style="margin-left:70px;">CEDULA PREDIAL URBANA</p>
        <div class="clave" style="margin-left:70px;"> CLAVE CATASTRAL {{ $predio->Clave_predial }}</div>
    </div>

    <table class="two-col">
        <tr>
            <td>
                
                <div class="section">
                    <div class="section-title">Datos del Contribuyente</div>
                    <div class="section-body">
                        <table class="info">
                            <tr><td class="label">Nombre:</td><td class="value">{{ $predio->contribuyente->nombre_completo ?? '—' }}</td></tr>
                            <tr><td class="label">Cuenta:</td><td class="value">{{ $predio->contribuyente->cuenta ?? '—' }}</td></tr>
                            <tr><td class="label">Domicilio:</td><td class="value">{{ $predio->contribuyente->domicilio->domicilio_completo ?? '—' }}</td></tr>
                        </table>
                    </div>
                </div>
            </td>
            <td>
                
                <div class="section">
                    <div class="section-title">TIPO DE PREDIO {{ $predio->tipoPredio->Tipo_predio ?? '—' }}</div>
                    <div class="section-body">
                        <table class="info">
                            <tr><td class="label">Clave Catastral:</td><td class="value">{{ $predio->clavePredial->clave_predial_completa ?? '—' }}</td></tr>
                            <tr><td class="label">Manzana:</td><td class="value">{{ $predio->clavePredial->id_manzana ?? '—' }}</td></tr>
                            <tr><td class="label">Lote:</td><td class="value">{{ $predio->clavePredial->id_lote ?? '—' }}</td></tr>
                            <tr><td class="label">Régimen Propiedad:</td><td class="value">{{ $predio->regimenPropiedad->REGIMEN ?? '—' }}</td></tr>
                            <tr><td class="label">Escritura:</td><td class="value">{{ $predio->numero_de_escritura ?? '—' }}</td></tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div class="section">
        <div class="section">
                    <div class="section-title">LOCALIZACION DEL PREDIO</div>
                    <div class="section-body">
                        <table class="info">
                            <tr><td class="label">Colonia:</td><td class="value">{{ $predio->colonia->COLONIA ?? '—' }}</td></tr>
                            <tr><td class="label">Calle:</td><td class="value">{{ $predio->calle->CALLE ?? '—' }}</td></tr>
                            <tr><td class="label">Ubicación:</td><td class="value">{{ $predio->ubicacion ?? '—' }}</td></tr>
                            <tr><td class="label">Núm. Exterior:</td><td class="value">{{ $predio->Numero_exterior ?? '—' }}</td></tr>
                            <tr><td class="label">Núm. Interior:</td><td class="value">{{ $predio->Numero_interior ?? '—' }}</td></tr>
                            <tr><td class="label">Código Postal:</td><td class="value">{{ $predio->codigo_postal ?? '—' }}</td></tr>
                            <tr><td class="label">Entre calles:</td><td class="value">{{ $predio->Referencia_entre_calle1 ?? '—' }}{{ $predio->Referncia_entre_calle2 ? ' y ' . $predio->Referncia_entre_calle2 : '' }}</td></tr>
                            <tr><td class="label">Coordenadas:</td><td class="value">{{ $predio->latitud ? $predio->latitud . ', ' . $predio->longitud : '—' }}</td></tr>
                        </table>
                    </div>
                </div>
    </div>

    <div class="section">
        <div class="section-title">Valores del Predio</div>
        <div class="section-body">
            <table class="info">
                <tr>
                    <td class="label">Valor Catastral:</td>
                    <td class="value value-highlight">$ {{ number_format($predio->valor_catastral, 2) }}</td>
                    <td class="label">Superficie:</td>
                    <td class="value">{{ number_format($predio->superficie, 4) }} m²</td>
                </tr>
                <tr>
                    <td class="label">Valor Fiscal:</td>
                    <td class="value value-highlight">$ {{ number_format($predio->valor_fiscal, 2) }}</td>
                    <td class="label">Construcción:</td>
                    <td class="value">{{ number_format($predio->construccion, 4) }} m²</td>
                </tr>
            </table>
        </div>
    </div>

    @if ($predio->datosUrbano)
    <div class="section">
        <div class="section-title">Datos Urbano</div>
        <div class="section-body">
            <table class="info">
                <tr><td class="label">Zona Urbana:</td><td class="value">{{ $predio->datosUrbano->zonaUrbana->descripcion ?? $predio->datosUrbano->id_zona_urbana ?? '—' }}</td></tr>
                <tr><td class="label">Forma del Predio:</td><td class="value">{{ $predio->datosUrbano->formaPredio->descripcion ?? $predio->datosUrbano->id_forma_predio ?? '—' }}</td></tr>
                <tr><td class="label">Uso del Predio:</td><td class="value">{{ $predio->datosUrbano->usoPredio->descripcion ?? $predio->datosUrbano->id_uso_predio ?? '—' }}</td></tr>
                <tr><td class="label">Estado Físico:</td><td class="value">{{ $predio->datosUrbano->estadoFisico->descripcion ?? $predio->datosUrbano->id_estado_fisico ?? '—' }}</td></tr>
                <tr><td class="label">Pavimentación:</td><td class="value">{{ $predio->datosUrbano->pavimento->descripcion ?? $predio->datosUrbano->id_pavimientacion ?? '—' }}</td></tr>
                <tr><td class="label">Núm. Pisos:</td><td class="value">{{ $predio->datosUrbano->numero_de_pisos_construidos ?? '—' }}</td></tr>
                <tr><td class="label">Sup. Terreno:</td><td class="value">{{ $predio->datosUrbano->superficie_terreno_metros_cuadrados ? number_format($predio->datosUrbano->superficie_terreno_metros_cuadrados, 4) . ' m²' : '—' }}</td></tr>
                <tr><td class="label">Frente:</td><td class="value">{{ $predio->datosUrbano->Frente_metros ? number_format($predio->datosUrbano->Frente_metros, 2) . ' m' : '—' }}</td></tr>
                <tr><td class="label">Fondo:</td><td class="value">{{ $predio->datosUrbano->Fondo_metros ? number_format($predio->datosUrbano->Fondo_metros, 2) . ' m' : '—' }}</td></tr>
                <tr><td class="label">Baldío:</td><td class="value">{{ $predio->datosUrbano->Baldio ? 'Sí' : 'No' }}</td></tr>
                <tr><td class="label">Servicios:</td><td class="value">
                    @php $servicios = []; @endphp
                    @if($predio->datosUrbano->servicio_agua) @php $servicios[] = 'Agua'; @endphp @endif
                    @if($predio->datosUrbano->servicio_drenaje) @php $servicios[] = 'Drenaje'; @endphp @endif
                    @if($predio->datosUrbano->servicio_energia_electrica) @php $servicios[] = 'Energía Eléctrica'; @endphp @endif
                    @if($predio->datosUrbano->servicio_alumbrado) @php $servicios[] = 'Alumbrado'; @endphp @endif
                    @if($predio->datosUrbano->cuenta_con_banqueta) @php $servicios[] = 'Banqueta'; @endphp @endif
                    {{ count($servicios) ? implode(', ', $servicios) : '—' }}
                </td></tr>
                <tr><td class="label">Valor Cat. Terreno:</td><td class="value">$ {{ number_format($predio->datosUrbano->valor_catastral_terreno, 2) }}</td></tr>
                <tr><td class="label">Valor Cat. Construido:</td><td class="value">$ {{ number_format($predio->datosUrbano->valor_catastral_construido, 2) }}</td></tr>
            </table>
        </div>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Medidas y Colindancias</div>
        <div class="section-body" style="padding:0;">
            <table class="medidas">
                <thead>
                    <tr>
                        <th style="width:70px;">Orientación</th>
                        <th style="width:80px;">Medida (m)</th>
                        <th>Colinda con</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($predio->medidasYColindancias as $m)
                        <tr>
                            <td><span class="badge">{{ $m->orientacion->descripcion ?? $m->id_orientacion }}</span></td>
                            <td>{{ number_format($m->medida_en_metros, 2) }}</td>
                            <td>{{ $m->colinda_con ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center; color:#a0aec0; padding:15px;">Sin registros de medidas y colindancias</td>
                        </tr>
                    @endforelse
                </tbody>
               
            </table>
        </div>
    </div>

    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i:s') }} — Dirección de Catastro Municipal
    </div>
</body>
</html>