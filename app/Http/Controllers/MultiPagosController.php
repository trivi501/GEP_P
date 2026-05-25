<?php

namespace App\Http\Controllers;

use App\Models\Predio;
use App\Models\Contribuyente;
use App\Models\Pago;
use App\Models\CuentasPagos;
use App\Models\Cajero;
use App\Models\HistorialCaja;
use App\Models\CalculoPredialUrbanoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MultiPagosController extends Controller
{
    private function getDescuentosPredio(string $idPredio): array
    {
        $descuento = \App\Models\Descuento::where('idPredio', $idPredio)
            ->where(function ($q) {
                $q->whereNull('fecha_expiracion')
                  ->orWhere('fecha_expiracion', '>=', now()->toDateString());
            })
            ->first();

        if (!$descuento) {
            return [
                'aplica' => false,
                'multas_pct' => 0,
                'actualizaciones_pct' => 0,
                'cobranza_pct' => 0,
                'descuento_multas' => 0,
                'descuento_actualizaciones' => 0,
                'descuento_cobranza' => 0,
                'total_descuento' => 0,
            ];
        }

        return [
            'aplica' => true,
            'multas_pct' => (float) $descuento->multas,
            'actualizaciones_pct' => (float) $descuento->actualizaciones,
            'cobranza_pct' => (float) $descuento->gastos_cobranza,
            'descuento_multas' => 0,
            'descuento_actualizaciones' => 0,
            'descuento_cobranza' => 0,
            'total_descuento' => 0,
        ];
    }
    public function index()
    {
        $formasPago = \App\Models\FormaPago::where('activo', 1)->orderBy('Descripción')->get();
        return Inertia::render('MultiPagosPredial/Index', compact('formasPago'));
    }

    public function search(Request $request)
    {
        $request->validate(['cuenta' => 'required|string']);

        $contribuyentes = Contribuyente::where('cuenta', 'like', '%' . $request->cuenta . '%')
            ->where('activo', 1)
            ->get();

        $anhoActual = date('Y');

        $predios = Predio::with([
            'contribuyente', 'tipoPredio', 'colonia', 'calle',
            'datosUrbano.zonaUrbana',
        ])
            ->whereIn('id_contribuyente', $contribuyentes->pluck('id_contribuyente'))
            ->where(function ($q) use ($anhoActual) {
                $q->whereNull('año_ultimo_pago')
                  ->orWhere('año_ultimo_pago', '<', $anhoActual);
            })
            ->get()
            ->map(fn($p) => [
                'id' => $p->id_predio,
                'Clave_predial' => $p->Clave_predial,
                'cuenta' => $p->contribuyente?->cuenta ?? '—',
                'contribuyente' => $p->contribuyente?->nombre_completo ?? '—',
                'colonia' => $p->colonia?->COLONIA ?? '—',
                'tipo_predio' => $p->tipoPredio?->Tipo_predio ?? '—',
                'ubicacionPredio' => trim(($p->calle?->nombre ?? '') . ' #' . ($p->Numero_exterior ?? '') . ($p->Numero_interior ? ' Int ' . $p->Numero_interior : '') . ', ' . ($p->colonia?->COLONIA ?? '')),
                'año_ultimo_pago' => $p->año_ultimo_pago ?? '—',
                'superficie' => (float) $p->superficie,
                'terreno' => (float) ($p->datosUrbano?->valor_catastral_terreno ?? $p->valor_catastral ?? 0),
                'construccion' => (float) ($p->construccion ?? 0),
                'id_contribuyente' => $p->id_contribuyente,
                'rfc' => $p->contribuyente?->rfc ?? '',
            ]);

        return response()->json([
            'contribuyentes' => $contribuyentes,
            'predios' => $predios,
        ]);
    }

    public function getCalculo(Request $request)
    {
        $request->validate(['id' => 'required|string']);

        $predio = Predio::with([
            'contribuyente', 'tipoPredio',
            'datosUrbano.zonaUrbana',
            'datosRustico',
            'nivelesConstruidos.tipoConstruccion',
        ])->find($request->id);

        if (!$predio) {
            return response()->json(null, 404);
        }

        $calculosController = app(CalculosPrediosController::class);
        $conceptos = [];
        $total = 0;

        $esRustico = $predio->datosRustico
            || str_contains(mb_strtoupper($predio->tipoPredio?->Tipo_predio ?? ''), 'RÚSTICO')
            || str_contains(mb_strtoupper($predio->tipoPredio?->Tipo_predio ?? ''), 'RUSTICO')
            || str_contains(mb_strtoupper($predio->tipoPredio?->Tipo_predio ?? ''), 'MINA');

        if ($esRustico) {
            $uma = \App\Models\CatUma::where('anio', now()->year)->where('activo', 1)->first();
            $valorUma = $uma?->valor ?? 0;
            $hectareas = $predio->superficie ?? 0;
            $esMina = str_contains($predio->tipoPredio?->Tipo_predio ?? '', 'MINA');
            $anhoInicio = ($predio->año_ultimo_pago ?? now()->year) + 1;
            $anhoActual = now()->year;
            $totalRustico = 0;

            while ($anhoInicio <= $anhoActual) {
                $umaAnual = \App\Models\CatUma::where('anio', $anhoInicio)->where('activo', 1)->first();
                $valorUmaAnual = $umaAnual?->valor ?? $valorUma;
                $subtotal = 0;

                if ($esMina) {
                    $subtotal = $hectareas * (11 * $valorUmaAnual);
                } elseif ($predio->datosRustico?->valor_catastral_casa) {
                    $subtotal = ($predio->datosRustico->valor_catastral_casa ?? 0) * 0.015;
                } elseif ($predio->datosRustico?->valor_catastral_superficie_riego) {
                    $subtotal = ($hectareas * 6.40) * (2 * $valorUmaAnual) + (2 * $umaAnual);
                } elseif ($predio->datosRustico?->valor_catastral_superficie_temporal) {
                    $subtotal = $hectareas < 20
                        ? (3 * $valorUmaAnual) + ($hectareas * 3)
                        : (2 * $valorUmaAnual) + ($hectareas * 6.40);
                } else {
                    $subtotal = $hectareas < 20
                        ? ($hectareas * 3) + (3 * $valorUmaAnual) + (2 * $valorUmaAnual)
                        : ($hectareas * 6.40) + (2 * $valorUmaAnual) + (2 * $valorUmaAnual);
                }

                $totalRustico += $subtotal;
                $anhoInicio++;
            }

            $conceptos[] = ['concepto' => 'Predial Rústico', 'monto' => round($totalRustico, 2)];
            $total = round($totalRustico, 2);
        } elseif ($predio->datosUrbano) {
            $calculosAnuales = $calculosController->getCalculosAnuales($predio);
            $predial_ant = 0;
            $aseoPublico_ant = 0;
            $aseoPublico_act = 0;
            $recargos_ant = 0;
            $recargos_act = 0;
            $actualizacion_ant = 0;
            $actualizacion_act = 0;
            $total_ant = 0;
            $multa_total = 0;
            $cobranza_total = 0;

            foreach ($calculosAnuales as $calculo) {
                if ($calculo['anho'] < date('Y')) {
                    $predial_ant += $calculo['entero'];
                    $aseoPublico_ant += $calculo['aseo_publico'] ?? 0;
                    $recargos_ant += $calculo['recargos'] ?? 0;
                    $actualizacion_ant += $calculo['actualizacion'] ?? 0;
                    $total_ant += ($calculo['entero'] ?? 0) + ($calculo['aseo_publico'] ?? 0) + ($calculo['recargos'] ?? 0) + ($calculo['actualizacion'] ?? 0);
                } else {
                    $aseoPublico_act += $calculo['aseo_publico'] ?? 0;
                    $recargos_act += $calculo['recargos'] ?? 0;
                    $actualizacion_act += $calculo['actualizacion'] ?? 0;
                    $total += ($calculo['entero'] ?? 0) + ($calculo['aseo_publico'] ?? 0) + ($calculo['recargos'] ?? 0) + ($calculo['actualizacion'] ?? 0);
                }
                $multa_total += $calculo['multa'] ?? 0;
                $cobranza_total += $calculo['cobranza'] ?? 0;
            }

            $ultimo = end($calculosAnuales);
            $conceptos[] = ['concepto' => 'Predial Anterior', 'monto' => round($predial_ant, 2)];
            $conceptos[] = ['concepto' => 'Impuesto Predial Actual', 'monto' => round($ultimo['entero'], 2)];
            if ($aseoPublico_ant > 0) $conceptos[] = ['concepto' => 'Aseo Público Anterior', 'monto' => round($aseoPublico_ant, 2)];
            if ($aseoPublico_act > 0) $conceptos[] = ['concepto' => 'Aseo Público Actual', 'monto' => round($aseoPublico_act, 2)];
            if ($recargos_ant > 0) $conceptos[] = ['concepto' => 'Recargos Anteriores', 'monto' => round($recargos_ant, 2)];
            if ($recargos_act > 0) $conceptos[] = ['concepto' => 'Recargos Actual', 'monto' => round($recargos_act, 2)];
            if ($actualizacion_ant > 0) $conceptos[] = ['concepto' => 'Actualización Anterior', 'monto' => round($actualizacion_ant, 2)];
            if ($actualizacion_act > 0) $conceptos[] = ['concepto' => 'Actualización Actual', 'monto' => round($actualizacion_act, 2)];
            if ($multa_total > 0) $conceptos[] = ['concepto' => 'Multa', 'monto' => round($multa_total, 2)];
            if ($cobranza_total > 0) $conceptos[] = ['concepto' => 'Gastos de Ejecución Predial Urbano', 'monto' => round($cobranza_total, 2)];
            if (!empty($ultimo['descuento']) && $ultimo['descuento'] > 0) {
                $conceptos[] = ['concepto' => 'Descuento por Pronto Pago', 'monto' => round($ultimo['descuento'], 2)];
            }

            $total = round($total + $total_ant + $multa_total + $cobranza_total, 2);
        }

        $descInfo = $this->getDescuentosPredio($request->id);
        if ($descInfo['aplica']) {
            $descuentoMulta = 0;
            $descuentoActualizacion = 0;
            $descuentoCobranza = 0;

            foreach ($conceptos as &$c) {
                if ($c['concepto'] === 'Multa' && $descInfo['multas_pct'] > 0) {
                    $descuentoMulta = round($c['monto'] * $descInfo['multas_pct'] / 100, 2);
                }
                if (in_array($c['concepto'], ['Actualización Anterior', 'Actualización Actual']) && $descInfo['actualizaciones_pct'] > 0) {
                    $descuentoActualizacion += round($c['monto'] * $descInfo['actualizaciones_pct'] / 100, 2);
                }
                if ($c['concepto'] === 'Gastos de Ejecución Predial Urbano' && $descInfo['cobranza_pct'] > 0) {
                    $descuentoCobranza = round($c['monto'] * $descInfo['cobranza_pct'] / 100, 2);
                }
            }
            unset($c);

            $totalDescuento = $descuentoMulta + $descuentoActualizacion + $descuentoCobranza;
            if ($totalDescuento > 0) {
                $conceptos[] = ['concepto' => 'Descuentos', 'monto' => -$totalDescuento];
                $total = round($total - $totalDescuento, 2);
            }
        }

        return response()->json([
            'conceptos' => $conceptos,
            'total' => $total,
            'es_rustico' => $esRustico ?? false,
        ]);
    }

    public function pagar(Request $request)
    {
        $validated = $request->validate([
            'predios' => 'required|array|min:1',
            'predios.*.id' => 'required|string',
            'predios.*.id_contribuyente' => 'required|string',
            'predios.*.monto' => 'required|numeric|min:0',
            'predios.*.descuento' => 'nullable|numeric|min:0',
            'predios.*.conceptos' => 'required|array|min:1',
            'predios.*.conceptos.*.concepto' => 'required|string',
            'predios.*.conceptos.*.monto' => 'required|numeric',
            'predios.*.nombre' => 'required|string|max:255',
            'predios.*.rfc' => 'nullable|string|max:20',
            'predios.*.tipo_pago' => 'required|string|max:100',
            'predios.*.descripcion' => 'nullable|string',
            'formas_pagos' => 'required|array|min:1',
            'formas_pagos.*.forma_pago_id' => 'required|integer|exists:f4_c_formapago,id',
            'formas_pagos.*.monto' => 'required|numeric|min:0.01',
        ]);

        $cajero = Cajero::with('caja')->where('usuario_id', auth()->id())->first();
        if (!$cajero) {
            return response()->json(['error' => 'No tienes un cajero asignado.'], 400);
        }
        $cajaAbierta = HistorialCaja::where('cajero_id', $cajero->id_cajero)
            ->whereNull('datetime_cierre')->first();
        if (!$cajaAbierta) {
            return response()->json(['error' => 'No tienes una caja abierta.'], 400);
        }

        $cuentasList = DB::table('cuentas')
            ->select('id', DB::raw("TRIM(REPLACE(REPLACE(descripcion, '\r', ''), '\n', '')) as descripcion_clean"))
            ->get();

        DB::beginTransaction();
        try {
            $pagosCreados = [];
            $montoTotal = 0;

            foreach ($validated['predios'] as $item) {
                $ultimoFolio = Pago::max('id') ?? 0;
                $folio = 'PAG-' . str_pad($ultimoFolio + 1, 6, '0', STR_PAD_LEFT);

                $esRustico = ($item['tipo_pago'] ?? '') === 'predial_rustico';

                $pago = Pago::create([
                    'monto' => $item['monto'],
                    'descuento' => $item['descuento'] ?? 0,
                    'folio' => $folio,
                    'fecha' => now(),
                    'estatus' => 'pagado',
                    'forma_pago' => '',
                    'tipo_pago' => $item['tipo_pago'],
                    'nombre' => $item['nombre'],
                    'rfc' => $item['rfc'] ?? '',
                    'descripcion' => $item['descripcion'] ?? null,
                    'id_predio' => $item['id'],
                    'id_contribuyente' => $item['id_contribuyente'],
                    'id_historial_caja' => $cajaAbierta->id,
                    'id_usuario' => auth()->id(),
                    'anio_pago' => date('Y'),
                    'im' => null,
                    'url_file' => null,
                ]);

                $conceptCuentaMapping = $esRustico ? [
                    'Predial Rústico' => fn($list) => $list->first(fn($c) =>
                        str_contains($c->descripcion_clean, 'RÚSTICO') || str_contains($c->descripcion_clean, 'RUSTICO')
                    ),
                    'Gastos de Ejecución Predial Urbano' => fn($list) => $list->first(fn($c) =>
                        str_contains($c->descripcion_clean, 'COBRANZA') || str_contains($c->descripcion_clean, 'EJECUCIÓN') || str_contains($c->descripcion_clean, 'EJECUCION')
                    ),
                    'Descuentos' => fn($list) => $list->first(fn($c) =>
                        str_contains($c->descripcion_clean, 'DESCUENTO')
                    ),
                ] : [
                    'Predial Anterior' => fn($list) => $list->first(fn($c) => str_contains($c->descripcion_clean, 'ANTERIORES')),
                    'Impuesto Predial Actual' => fn($list) => $list->first(fn($c) => $c->descripcion_clean === 'PREDIAL URBANO AÑO ACTUAL'),
                    'Aseo Público Anterior' => fn($list) => $list->first(fn($c) => str_contains($c->descripcion_clean, 'S.A.P.') && str_contains($c->descripcion_clean, 'REZAGO')),
                    'Aseo Público Actual' => fn($list) => $list->first(fn($c) => str_contains($c->descripcion_clean, 'S.A.P. - URBANO ACTUAL')),
                    'Recargos Anteriores' => fn($list) => $list->first(fn($c) => str_contains($c->descripcion_clean, 'RECARGOS PREDIAL URBANO')),
                    'Recargos Actual' => fn($list) => $list->first(fn($c) => str_contains($c->descripcion_clean, 'RECARGOS PREDIAL URBANO')),
                    'Actualización Anterior' => fn($list) => $list->first(fn($c) => str_contains($c->descripcion_clean, 'ACTUALIZACIONES PREDIAL URBANO')),
                    'Actualización Actual' => fn($list) => $list->first(fn($c) => str_contains($c->descripcion_clean, 'ACTUALIZACIONES PREDIAL URBANO')),
                    'Multa' => fn($list) => $list->first(fn($c) =>
                        str_contains($c->descripcion_clean, 'MULTA')
                    ),
                    'Gastos de Ejecución Predial Urbano' => fn($list) => $list->first(fn($c) =>
                        str_contains($c->descripcion_clean, 'COBRANZA') || str_contains($c->descripcion_clean, 'EJECUCIÓN') || str_contains($c->descripcion_clean, 'EJECUCION')
                    ),
                    'Descuentos' => fn($list) => $list->first(fn($c) =>
                        str_contains($c->descripcion_clean, 'DESCUENTO')
                    ),
                ];

                foreach ($item['conceptos'] as $c) {
                    $concepto = trim($c['concepto']);
                    $cuentaId = null;
                    if (isset($conceptCuentaMapping[$concepto])) {
                        $match = $conceptCuentaMapping[$concepto]($cuentasList);
                        if ($match) $cuentaId = $match->id;
                    }
                    CuentasPagos::create([
                        'pago_id' => $pago->id,
                        'cuenta_id' => $cuentaId,
                        'concepto' => $c['concepto'],
                        'fecha_registro' => now(),
                        'cantidad' => 1,
                        'monto' => $c['monto'],
                        'concepto_id' => null,
                    ]);
                }

                $fpCadaIds = [];
                foreach ($validated['formas_pagos'] as $fp) {
                    $fpc = \App\Models\FormasPagosCada::create([
                        'pago_id' => $pago->id,
                        'forma_pago_id' => $fp['forma_pago_id'],
                        'monto' => $fp['monto'],
                    ]);
                    $fpCadaIds[] = $fpc->id;
                }

                $pago->update(['forma_pago' => implode(',', $fpCadaIds)]);

                $montoTotal += $item['monto'];

                $predioData = DB::table('tb_predio')->where('id_predio', $item['id'])->first();
                \App\Models\IncidenciaPago::create([
                    'pago_id' => $pago->id,
                    'id_predio' => $item['id'],
                    'año_ultimo_pago_anterior' => $predioData?->año_ultimo_pago,
                    'ultimo_bimestre_pago_anterior' => $predioData?->ultimo_bimestre_pago,
                    'snapshot' => $predioData ? (array) $predioData : [],
                ]);

                DB::table('tb_predio')->where('id_predio', $item['id'])->update([
                    'año_ultimo_pago' => date('Y'),
                    'ultimo_bimestre_pago' => DB::raw('CEIL(MONTH(CURDATE()) / 2)'),
                ]);

                $qrBase64 = $this->generarQrBase64(route('pagos.recibo', $pago->id));
                $pdf = Pdf::loadView('pagos.recibo-pdf', compact('pago', 'qrBase64'));
                $pdfDir = public_path('recibos');
                if (!is_dir($pdfDir)) mkdir($pdfDir, 0755, true);
                $pdfPath = "recibos/recibo-{$pago->folio}.pdf";
                $pdf->save(public_path($pdfPath));
                $pago->update(['url_file' => $pdfPath]);

                $pagosCreados[] = $pago;
            }

            $cajaAbierta->increment('total_ingreso', $montoTotal);

            $pagosIds = collect($pagosCreados)->pluck('id');
            $pagosConRelaciones = Pago::with(['predio.contribuyente', 'predio.datosUrbano.zonaUrbana', 'predio.tipoPredio', 'cuentasPagos.cuenta', 'incidencia'])
                ->whereIn('id', $pagosIds)
                ->get();

            $pdfMulti = Pdf::loadView('multi-pagos-predial.recibos', [
                'pagos' => $pagosConRelaciones,
            ]);
            $pdfMultiName = 'multi-pago-' . now()->format('Ymd-His') . '.pdf';
            $pdfMultiPath = "recibos/{$pdfMultiName}";
            $pdfMulti->save(public_path($pdfMultiPath));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($pagosCreados) . ' pago(s) registrados exitosamente.',
                'folios' => collect($pagosCreados)->pluck('folio'),
                'folios_ids' => collect($pagosCreados)->map(fn($p) => ['folio' => $p->folio, 'id' => $p->id])->values(),
                'pdf_url' => asset($pdfMultiPath),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar pagos: ' . $e->getMessage()], 500);
        }
    }

    private function generarQrBase64(string $url): string
    {
        $qrPng = @file_get_contents('https://chart.googleapis.com/chart?chs=80x80&cht=qr&chl=' . urlencode($url));
        return $qrPng !== false ? base64_encode($qrPng) : '';
    }
}
