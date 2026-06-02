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
        }
        .header { padding: 4px 0; border-bottom: 2px double #333; margin-bottom: 6px; text-align: center; }
        .header h1 { font-size: 11pt; text-transform: uppercase; letter-spacing: 1px; margin: 0; }
        .header h2 { font-size: 9pt; margin: 0; }
        .header h3 { font-size: 8pt; font-weight: normal; color: #555; margin: 0; }

        .folio-box { text-align: right;  }
        .folio-box .folio { font-size: 10pt; font-weight: bold; color: #803047; }

        .section { margin-bottom: 4px; margin-top: 135px; margin-right: 10px; margin-left: 10px; line-height: 1; }
        .section-title { font-size: 6pt;margin-right: 20px; margin-left: 20px; text-align: center; font-weight: bold; text-transform: uppercase; background: #e5e7eb; padding: 2px 4px; border-bottom: 1px solid #333; margin-bottom: 2px; }

        .info-table { width: 100%;  border-collapse: collapse; border-spacing: 0; font-size: 7pt;}
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

        .amount-box { text-align: center; margin: 16px 0; }
        .amount-box .amount { font-size: 22pt; font-weight: bold; color: #155724; }
        .amount-box .label { font-size: 8pt; color: #888; text-transform: uppercase; }

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
        $ultimoAno = $pago->incidencia?->año_ultimo_pago_anterior ? (int) $pago->incidencia->año_ultimo_pago_anterior : ($pago->predio?->año_ultimo_pago ? (int) $pago->predio->año_ultimo_pago : (int) date('Y') - 1);
        $anioActual = (int) date('Y');
    @endphp
    <div class="header">
       
        <h3 style="text-align: right">{{ $pago->folio }}</h3>
    </div>
    <div class="recibo-original">
        <div class="section">
            <table width="100%">
                <tr>
                    <td class="label"  style="font-size: 9pt;" width="80%">Contribuyente</td>
                    <td class="label" style="font-size: 9pt; text-align: right;" width="20%"></td>
                </tr>
                <tr>
                    <td class="value" width = "80%" style="font-weight: bold; font-size: 9pt;"><b>{{ $pago->predio?->contribuyente?->nombre ?? '—' }}</b></td>
                    <td width="30%" style="text-align: right; font-size: 7pt;">Fecha: {{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
            <table class="info-table">
                
                <tr>
                    <table>
                        <tr>
                            <td class="label" style="width:100px">Cuenta</td>
                            <td class="label" style="width:100px">Clave Catastral</td>
                            <td class="label" style="width:500px; text-align: right;">Período del pago</td>
                        <tr>
                        <tr>
                            <td class="value" style="width:100px; font-size: 10pt;"><b>{{ $pago->predio?->contribuyente?->cuenta ?? '—' }}</b></td>
                            <td class="value" style="width:100px; font-size: 10pt;"><b>{{ $pago->predio?->Clave_predial ?? '—' }}</b></td>
                            <td class="value" style="font-size: 8pt; width: 500px; text-align: right;"><b>{{ $ultimoAno + 1 == $anioActual ? $anioActual : ($ultimoAno + 1) . ' - ' . $anioActual }}</b></td>
                        </tr>
                    </table>
                    
                </tr>
                <tr>
                    <table>
                        <tr>
                            <td class="label" style="width:100px">Zona</td>
                            <td class="label" style="width:100px">Tipo Predio</td>
                            <td class="label" style="width:100px">Terreno</td>
                            <td class="label" style="width:100px">Construcción</td>
                        <tr>
                        <tr>
                            <td class="value" style="width:100px">{{ $pago->predio?->datosUrbano?->zonaUrbana?->descripcion ?? '—' }}</td>
                            <td class="value" style="width:100px">{{ $pago->predio?->tipoPredio?->Tipo_predio ?? $pago->predio?->tipoPredio?->nombre ?? '—' }}</td>
                            <td class="value" style="width:100px">{{ number_format($pago->predio?->superficie ?? 0, 2) }} m²</td>
                            <td class="value" style="width:100px">{{ number_format($pago->predio?->construccion ?? 0, 2) }} m²</td>
                        </tr>
                    </table>
                </tr>
            </table>
                    <table>
                        <tr>
                            <td class="label" style="width:100%">Ubicación</td>
                        </tr>
                        <tr>
                            <td class="value" style="width:100%" style="font-size: 9pt;"><b>{{ $pago->predio?->ubicacion_completa ?? '—' }}</b></td>
                        </tr>
                    </table>
        </div>

        <div class="section-title">Descripción</div>
            <table class="conceptos">
                <thead>
                    <tr>
                        <th>Indetec</th>
                        <th>Concepto</th>
                        <th class="monto">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pago->cuentasPagos as $c)
                    <tr>
                        <td>{{ $c->cuenta?->indetec ?? '—' }}</td>
                        <td>{{ $c->concepto }}</td>
                        <td class="monto">${{ number_format($c->monto, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2">Total</td>
                        <td class="monto">${{ number_format($pago->monto, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>


        @if (!empty($qrBase64))
        <div style="text-align: center; margin: 10px 0;">
            <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR" style="width: 80px; height: 80px;" />
            <p style="font-size: 5pt; color: #888; margin-top: 2px;">Escanea para consultar recibo</p>
        </div>
        @endif

        <div class="footer_recibo_original">
            <p style="font-size: 4pt; text-align: justify; margin: 0 10px;">
                original POR ESTE CONDUCTO SE LE HACE DE SU CONOCIMIENTOQUE APARTIR DE LA FECHA CUENTA CON UN PLAZO DE 15 (DIAS)HABILES PARA INFORME AL DEPARTAMENTODENTRO DE ESTA MUNICIPALIDAD.SI EXISTE ALGUNA MODIFICACION EN LAS CONSTRUCCIONES Y CARACTERISTICAS DEL PREDIO QUE SE DETALLA EN EL ANVERSO DE ESTE DOCUMENTO APERCIBIENDOLO.LO QUE EN EL SUPUESRO DE HACER CASO OMISO A TAL REQUERIMINTO, ESTE MUNICIPIO EN SU USO DE SUS ATRIBUCIONES Y FACULTADES CONTENIDAS EN LOSN ART.1,2 FRAC ll y ll de 3 FRAC III 4,6,7,8 FRAC IV. 13 FRAC VI 22,24,25,38,44,45,47FRAC II Y III 50,56,57, FRAC VI DE LA LEY DE CATASTRO DEL ESTADO DE ZACATECAS PROCEDERA A LLEVAR A CABO DE MANERA OFICIOSA LA ACTUALIZACION CATASTRAL DE ACUERDO ALA INFORMACION EXISTENTE EN ESTE MUNICIPIO POR LO QUE EL IMPORTE DE EL PAGO CONSIGNADO EN EL ANVERSO DE ESTE RECIBO, PODRA SER MODIFICADO EN ATENCION AL RESULTADO DE ACTUALIZACION DE LA CEDULA CATASTRAL, QUEDANDO EXPEDITO EL DERECHO DEL MUNICIPIO DE GUADALUPE ZAC.PARA REQURIR EL COMBRO DEL IMPUESTO IMPORTE DEL DINERO QUE ARROJE, LO ANTERIOR FUNDAMENTADO EN LO DISPUESTO POR LOS ARTICULOS 131 FRAC I Y 147 FRAC II DEL CODIGO FISCAL DEL ESTADO DE ZACATECAS.
            </p>
        </div>
    </div>
    <div class="recibo-copia">
        <div class="section">
            <table width="100%">
                <tr>
                    <td class="label"  style="font-size: 9pt;" width="80%">Contribuyente</td>
                    <td class="label" style="font-size: 9pt; text-align: right;" width="20%">{{ $pago->folio }}</td>
                </tr>
                <tr>
                    <td class="value" width = "80%" style="font-weight: bold; font-size: 9pt;"><b>{{ $pago->predio?->contribuyente?->nombre ?? '—' }}</b></td>
                    <td width="30%" style="text-align: right; font-size: 7pt;">Fecha: {{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
            <table class="info-table">
                
                <tr>
                    <table>
                        <tr>
                            <td class="label" style="width:100px">Cuenta</td>
                            <td class="label" style="width:100px">Clave Catastral</td>
                            <td class="label" style="width:500px; text-align: right;">Período del pago</td>
                        <tr>
                        <tr>
                            <td class="value" style="width:100px; font-size: 10pt;">{{ $pago->predio?->contribuyente?->cuenta ?? '—' }}</td>
                            <td class="value" style="width:100px; font-size: 10pt;">{{ $pago->predio?->Clave_predial ?? '—' }}</td>
                            <td class="value" style="font-size: 8pt; width: 500px; text-align: right;"><b>{{ $ultimoAno + 1 == $anioActual ? $anioActual : ($ultimoAno + 1) . ' - ' . $anioActual }}</b></td>
                        </tr>
                    </table>
                    
                </tr>
                <tr>
                    <table>
                        <tr>
                            <td class="label" style="width:100px">Zona</td>
                            <td class="label" style="width:100px">Tipo Predio</td>
                            <td class="label" style="width:100px">Terreno</td>
                            <td class="label" style="width:100px">Construcción</td>
                        <tr>
                        <tr>
                            <td class="value" style="width:100px">{{ $pago->predio?->datosUrbano?->zonaUrbana?->descripcion ?? '—' }}</td>
                            <td class="value" style="width:100px">{{ $pago->predio?->tipoPredio?->Tipo_predio ?? $pago->predio?->tipoPredio?->nombre ?? '—' }}</td>
                            <td class="value" style="width:100px">{{ number_format($pago->predio?->superficie ?? 0, 2) }} m²</td>
                            <td class="value" style="width:100px">{{ number_format($pago->predio?->construccion ?? 0, 2) }} m²</td>
                        </tr>
                    </table>
                </tr>
            </table>
        </div>

        <div class="section-title">Descripción</div>
            <table class="conceptos">
                <thead>
                    <tr>
                        <th>Indetec</th>
                        <th>Concepto</th>
                        <th class="monto">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pago->cuentasPagos as $c)
                    <tr>
                        <td>{{ $c->cuenta?->indetec ?? '—' }}</td>
                        <td>{{ $c->concepto }}</td>
                        <td class="monto">${{ number_format($c->monto, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2">Total</td>
                        <td class="monto">${{ number_format($pago->monto, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>


        @if (!empty($qrBase64))
        <div style="text-align: center; margin: 10px 0;">
            <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR" style="width: 80px; height: 80px;" />
            <p style="font-size: 5pt; color: #888; margin-top: 2px;">Escanea para consultar recibo</p>
        </div>
        @endif

        <div class="footer_recibo_copia">
            <p style="font-size: 4pt; text-align: justify; margin: 0 10px;">
                POR ESTE CONDUCTO SE LE HACE DE SU CONOCIMIENTOQUE APARTIR DE LA FECHA CUENTA CON UN PLAZO DE 15 (DIAS)HABILES PARA INFORME AL DEPARTAMENTODENTRO DE ESTA MUNICIPALIDAD.SI EXISTE ALGUNA MODIFICACION EN LAS CONSTRUCCIONES Y CARACTERISTICAS DEL PREDIO QUE SE DETALLA EN EL ANVERSO DE ESTE DOCUMENTO APERCIBIENDOLO.LO QUE EN EL SUPUESRO DE HACER CASO OMISO A TAL REQUERIMINTO, ESTE MUNICIPIO EN SU USO DE SUS ATRIBUCIONES Y FACULTADES CONTENIDAS EN LOSN ART.1,2 FRAC ll y ll de 3 FRAC III 4,6,7,8 FRAC IV. 13 FRAC VI 22,24,25,38,44,45,47FRAC II Y III 50,56,57, FRAC VI DE LA LEY DE CATASTRO DEL ESTADO DE ZACATECAS PROCEDERA A LLEVAR A CABO DE MANERA OFICIOSA LA ACTUALIZACION CATASTRAL DE ACUERDO ALA INFORMACION EXISTENTE EN ESTE MUNICIPIO POR LO QUE EL IMPORTE DE EL PAGO CONSIGNADO EN EL ANVERSO DE ESTE RECIBO, PODRA SER MODIFICADO EN ATENCION AL RESULTADO DE ACTUALIZACION DE LA CEDULA CATASTRAL, QUEDANDO EXPEDITO EL DERECHO DEL MUNICIPIO DE GUADALUPE ZAC.PARA REQURIR EL COMBRO DEL IMPUESTO IMPORTE DEL DINERO QUE ARROJE, LO ANTERIOR FUNDAMENTADO EN LO DISPUESTO POR LOS ARTICULOS 131 FRAC I Y 147 FRAC II DEL CODIGO FISCAL DEL ESTADO DE ZACATECAS.
            </p>
        </div>
    </div>
</body>
</html>