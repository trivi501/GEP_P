<?php

namespace App\Http\Controllers;

use App\Models\Cajero;
use App\Models\HistorialCaja;
use App\Models\Predio;
use App\Models\FormaPago;
use App\Models\Pago;
use App\Models\OrdenPago;
use App\Models\CuentasPagos;
use App\Models\FormasPagosCada;
use App\Services\CalculoPredialUrbanoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PagosController extends Controller
{
    private function getDescuentosPredio(string $idPredio): array
    {
        $descuento = \App\Models\Descuento::where('idPredio', $idPredio)
            ->where('activo', true)
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
        $cajero = Cajero::with('caja')->where('usuario_id', auth()->id())->first();

        if (!$cajero) {
            $historial = HistorialCaja::whereRaw('1 = 0')->paginate(10);
            $cajaAbierta = null;
        } else {
            $cajaAbierta = HistorialCaja::where('cajero_id', $cajero->id_cajero)
                ->whereNull('datetime_cierre')
                ->first();

            $historial = HistorialCaja::with('caja', 'cajero.usuario')
                ->where('cajero_id', $cajero->id_cajero)
                ->orderBy('datetime_apertura', 'desc')
                ->paginate(10);
        }

        return Inertia::render('Pagos/Index', compact('historial', 'cajero', 'cajaAbierta'));
    }

    public function historial()
    {
        $cajero = Cajero::with('caja')->where('usuario_id', auth()->id())->first();

        if (!$cajero) {
            $pagos = Pago::whereRaw('1 = 0')->paginate(10);
            $cajaAbierta = null;
        } else {
            $cajaAbierta = HistorialCaja::where('cajero_id', $cajero->id_cajero)
                ->whereNull('datetime_cierre')
                ->first();

            if ($cajaAbierta) {
                $pagos = Pago::with('historialCaja.caja', 'predio', 'formasPagosCada.formaPago')
                    ->where('id_historial_caja', $cajaAbierta->id)
                    ->orderBy('fecha', 'desc')
                    ->paginate(15);
            } else {
                $pagos = Pago::whereRaw('1 = 0')->paginate(15);
            }
        }

        return Inertia::render('Pagos/Historial', compact('pagos', 'cajero', 'cajaAbierta'));
    }

    public function store(Request $request)
    {
        $cajero = Cajero::with('caja')->where('usuario_id', auth()->id())->first();

        if (!$cajero) {
            return redirect()->route('pagos.index')->with('error', 'No tienes un cajero asignado.');
        }

        $cajaAbierta = HistorialCaja::where('cajero_id', $cajero->id_cajero)
            ->whereNull('datetime_cierre')
            ->first();

        if ($cajaAbierta) {
            return redirect()->route('pagos.index')->with('error', 'Ya tienes una caja abierta. Ciérrala antes de abrir una nueva.');
        }

        $cajaAbiertaMismaCaja = HistorialCaja::where('caja_id', $cajero->caja_id)
            ->whereNull('datetime_cierre')
            ->where('cajero_id', '!=', $cajero->id_cajero)
            ->first();

        if ($cajaAbiertaMismaCaja) {
            return redirect()->route('pagos.index')->with('error', 'La caja ya está abierta por otro cajero. Ciérrela primero.');
        }

        $validated = $request->validate([
            'fondo' => 'required|numeric|min:0',
        ]);

        HistorialCaja::create([
            'fondo' => $validated['fondo'],
            'total_ingreso' => 0,
            'datetime_apertura' => now(),
            'datetime_cierre' => null,
            'cajero_id' => $cajero->id_cajero,
            'caja_id' => $cajero->caja_id,
        ]);

        return redirect()->route('pagos.index')->with('success', 'Caja abierta exitosamente.');
    }

    public function cobrar(Request $request)
    {
        $cajero = Cajero::with('caja')->where('usuario_id', auth()->id())->first();

        $cajaAbierta = HistorialCaja::where('cajero_id', $cajero?->id_cajero)
            ->whereNull('datetime_cierre')
            ->first();

        if (!$cajaAbierta) {
            return redirect()->route('pagos.index')->with('error', 'No tienes una caja abierta.');
        }

        $formasPago = FormaPago::where('activo', 1)->orderBy('Descripción')->get();

        $predioId = $request->query('id_predio');

        return Inertia::render('Pagos/Cobrar', compact('cajaAbierta', 'formasPago', 'predioId'));
    }

    public function searchPredio(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $prefix = $q . '%';
        $like = '%' . $q . '%';
        $terms = array_filter(explode(' ', $q), fn($t) => preg_match('/[a-zA-Z0-9]/', $t));

        $ids = collect();

        if (!empty($terms) && strlen($q) >= 2) {
            $fulltextTerms = '+' . implode('* +', $terms) . '*';

            $ids = DB::table('tb_predio')
                ->join('tb_contribuyentes', 'tb_predio.id_contribuyente', '=', 'tb_contribuyentes.id_contribuyente')
                ->leftJoin('tb_clave_predial', 'tb_predio.id_clave_predial', '=', 'tb_clave_predial.id_clave_predial')
                ->whereIn('tb_predio.id_tipo_predio', [1, 2, 3, 4, 5, 6])
                ->where(function ($query) use ($fulltextTerms) {
                    $query->whereRaw('MATCH (tb_predio.Clave_predial) AGAINST (? IN BOOLEAN MODE)', [$fulltextTerms])
                        ->orWhereRaw('MATCH (tb_contribuyentes.cuenta) AGAINST (? IN BOOLEAN MODE)', [$fulltextTerms])
                        ->orWhereRaw('MATCH (tb_contribuyentes.nombre_completo, tb_contribuyentes.nombre_moral) AGAINST (? IN BOOLEAN MODE)', [$fulltextTerms])
                        ->orWhereRaw('MATCH (tb_clave_predial.clave_predial_completa) AGAINST (? IN BOOLEAN MODE)', [$fulltextTerms]);
                })
                ->take(10)
                ->pluck('tb_predio.id_predio');
        }

        if ($ids->isEmpty()) {
            $ids = DB::table('tb_predio')
                ->join('tb_contribuyentes', 'tb_predio.id_contribuyente', '=', 'tb_contribuyentes.id_contribuyente')
                ->leftJoin('tb_clave_predial', 'tb_predio.id_clave_predial', '=', 'tb_clave_predial.id_clave_predial')
                ->whereIn('tb_predio.id_tipo_predio', [1, 2, 3, 4, 5, 6])
                ->where(function ($query) use ($prefix) {
                    $query->where('tb_predio.Clave_predial', 'like', $prefix)
                        ->orWhere('tb_contribuyentes.cuenta', 'like', $prefix)
                        ->orWhere('tb_contribuyentes.nombre_completo', 'like', $prefix)
                        ->orWhere('tb_contribuyentes.nombre_moral', 'like', $prefix)
                        ->orWhere('tb_clave_predial.clave_predial_completa', 'like', $prefix);
                })
                ->take(10)
                ->pluck('tb_predio.id_predio');
        }

        if ($ids->isEmpty() && strlen($q) >= 3) {
            $ids = DB::table('tb_predio')
                ->join('tb_contribuyentes', 'tb_predio.id_contribuyente', '=', 'tb_contribuyentes.id_contribuyente')
                ->leftJoin('tb_clave_predial', 'tb_predio.id_clave_predial', '=', 'tb_clave_predial.id_clave_predial')
                ->whereIn('tb_predio.id_tipo_predio', [1, 2, 3, 4, 5, 6])
                ->where(function ($query) use ($like) {
                    $query->where('tb_predio.Clave_predial', 'like', $like)
                        ->orWhere('tb_contribuyentes.cuenta', 'like', $like)
                        ->orWhere('tb_contribuyentes.nombre_completo', 'like', $like)
                        ->orWhere('tb_contribuyentes.nombre_moral', 'like', $like)
                        ->orWhere('tb_clave_predial.clave_predial_completa', 'like', $like);
                })
                ->take(10)
                ->pluck('tb_predio.id_predio');
        }

        if ($ids->isEmpty()) {
            return response()->json([]);
        }

        $predios = DB::table('tb_predio')
            ->join('tb_contribuyentes', 'tb_predio.id_contribuyente', '=', 'tb_contribuyentes.id_contribuyente')
            ->leftJoin('tb_clave_predial', 'tb_predio.id_clave_predial', '=', 'tb_clave_predial.id_clave_predial')
            ->leftJoin('cat_calle', 'tb_predio.id_calle', '=', 'cat_calle.id_calle')
            ->leftJoin('cat_colonia', 'tb_predio.id_colonia', '=', 'cat_colonia.id_colonia')
            ->whereIn('tb_predio.id_predio', $ids)
            ->select([
                'tb_predio.id_predio',
                'tb_predio.Clave_predial',
                'tb_contribuyentes.cuenta',
                'tb_contribuyentes.nombre_completo',
                'tb_contribuyentes.nombre_moral',
                'cat_calle.CALLE as calle_nombre',
                'cat_colonia.COLONIA as colonia_nombre',
                'tb_predio.Numero_exterior',
                'tb_predio.Numero_interior',
                'tb_predio.codigo_postal',
                'tb_predio.año_ultimo_pago',
                'tb_predio.ultimo_bimestre_pago',
            ])
            ->get()
            ->map(function ($predio) {
                $domicilio = trim(($predio->calle_nombre ?? '') . ' #' . ($predio->Numero_exterior ?? '') . ($predio->Numero_interior ? ' Int.' . $predio->Numero_interior : '') . ', ' . ($predio->colonia_nombre ?? ''));
                $nombre = $predio->nombre_completo ?? $predio->nombre_moral ?? '';
                $ultimoPago = $predio->año_ultimo_pago
                    ? $predio->año_ultimo_pago . ($predio->ultimo_bimestre_pago ? ' - Bimestre ' . $predio->ultimo_bimestre_pago : '')
                    : null;
                return [
                    'id' => $predio->id_predio,
                    'clave_catastral' => $predio->Clave_predial,
                    'cuenta' => $predio->cuenta,
                    'contribuyente' => $nombre,
                    'domicilio' => $domicilio,
                    'ultimo_pago' => $ultimoPago,
                ];
            });

        return response()->json($predios);
    }

    public function getCalculo(Request $request)
    {
        $predio = Predio::with([
            'contribuyente.domicilio', 'contribuyente.tipoContribuyente',
            'tipoPredio', 'regimenPropiedad', 'estadoRenta', 'estadoImpuesto',
            'calle', 'colonia', 'clavePredial',
            'datosUrbano.zonaUrbana', 'datosUrbano.formaPredio', 'datosUrbano.usoPredio',
            'datosUrbano.estadoFisico', 'datosUrbano.pavimento',
            'datosRustico',
            'nivelesConstruidos.tipoConstruccion',
            'medidasYColindancias.orientacion',
        ])->find($request->id);

        if (!$predio) {
            return response()->json(null, 404);
        }

        $total = 0;
        $conceptos = [];
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

        if ($predio->datosUrbano) {
            try {
                $calculosController = new CalculosPrediosController(app(CalculoPredialUrbanoService::class));
                $calculosAnuales = $calculosController->getCalculosAnuales($predio);
                
                foreach ($calculosAnuales as $calculo) {
                    if($calculo['anho'] < date('Y')) {
                        $predial_ant += $calculo['entero'];
                        $aseoPublico_ant += $calculo['aseo_publico'] ?? 0;
                        $recargos_ant += $calculo['recargos'] ?? 0;
                        $actualizacion_ant += $calculo['actualizacion'] ?? 0;
                        $total_ant += ($calculo['entero'] ?? 0) + ($calculo['aseo_publico'] ?? 0) + ($calculo['recargos'] ?? 0) + ($calculo['actualizacion'] ?? 0);
                    }else {
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
                $conceptos[] = ['concepto' => 'Impuesto Predial Actual', 'monto' => round($calculo['entero'], 2)];

                if ($aseoPublico_ant > 0) {
                    $conceptos[] = ['concepto' => 'Aseo Público Anterior', 'monto' => round($aseoPublico_ant, 2)];
                }
                if ($aseoPublico_act > 0) {
                    $conceptos[] = ['concepto' => 'Aseo Público Actual', 'monto' => round($aseoPublico_act, 2)];
                }

                if ($recargos_ant > 0) {
                    $conceptos[] = ['concepto' => 'Recargos Anteriores', 'monto' => round($recargos_ant, 2)];
                }
                if ($recargos_act > 0) {
                    $conceptos[] = ['concepto' => 'Recargos Actual', 'monto' => round($recargos_act, 2)];
                }

                if ($actualizacion_ant > 0) {
                    $conceptos[] = ['concepto' => 'Actualización Anterior', 'monto' => round($actualizacion_ant, 2)];
                }
                if ($actualizacion_act > 0) {
                    $conceptos[] = ['concepto' => 'Actualización Actual', 'monto' => round($actualizacion_act, 2)];
                }

                if ($multa_total > 0) {
                    $conceptos[] = ['concepto' => 'Multa', 'monto' => round($multa_total, 2)];
                }
                if ($cobranza_total > 0) {
                    $conceptos[] = ['concepto' => 'Gastos de Ejecución Predial Urbano', 'monto' => round($cobranza_total, 2)];
                }

                if (!empty($ultimo['descuento']) && $ultimo['descuento'] > 0) {
                    $conceptos[] = ['concepto' => 'Descuento por Prono pago', 'monto' => round($ultimo['descuento'], 2)];
                }

                $total = round($total + $total_ant + $multa_total + $cobranza_total, 2);
            } catch (\Exception $e) {
                $conceptos[] = ['concepto' => 'Error al calcular: ' . $e->getMessage(), 'monto' => 0];
            }
        } elseif ($predio->datosRustico || str_contains(mb_strtoupper($predio->tipoPredio?->Tipo_predio ?? ''), 'RÚSTICO') || str_contains(mb_strtoupper($predio->tipoPredio?->Tipo_predio ?? ''), 'MINA')) {
            $predio->load('datosRustico');
            $uma = \App\Models\CatUma::where('anio', now()->year)->where('activo', 1)->first();
            $valorUma = $uma?->valor ?? 0;
            $hectareas = $predio->superficie ?? 0;
            $esMina = str_contains($predio->tipoPredio?->Tipo_predio ?? '', 'MINA');

            $anhoInicio = $predio->año_ultimo_pago ?? now()->year;
            $anhoActual = now()->year;
            $predialRustico = 0;
            $predialRusticoAnt = 0;
            $predialRusticoAct = 0;
            $recargosRustico = 0;
            $actualizacionRustico = 0;
            $multaRustico = 0;
            $cobranzaRustico = 0;
            $anhoInicio = $anhoInicio + 1;
            $cobranzaRustico = 0;

            while ($anhoInicio <= $anhoActual) {
                $umaAnual = \App\Models\CatUma::where('anio', $anhoInicio)->where('activo', 1)->first();
                $valorUmaAnual = $umaAnual?->valor ?? $valorUma;

                if ($esMina) {
                    $subtotal = $hectareas * (11 * $valorUmaAnual);
                } elseif ($predio->datosRustico?->valor_catastral_casa) {
                    $subtotal = ($predio->datosRustico->valor_catastral_casa ?? 0) * 0.015;
                } elseif ($predio->datosRustico?->valor_catastral_superficie_riego) {
                    $subtotal = ($hectareas * 6.40) * (2 * $valorUmaAnual) + (2 * $valorUmaAnual);
                } elseif ($predio->datosRustico?->valor_catastral_superficie_temporal) {
                    $subtotal = $hectareas < 20
                        ? (3 * $valorUmaAnual) + ($hectareas * 3)
                        : (2 * $valorUmaAnual) + ($hectareas * 6.40);
                } else {
                    $subtotal = $hectareas < 20
                        ? ($hectareas * 3) + (3 * $valorUmaAnual) + (2 * $valorUmaAnual)
                        : ($hectareas * 6.40) + (2 * $valorUmaAnual) + (2 * $valorUmaAnual);
                }

                if ($anhoInicio < $anhoActual) {
                    $predialRusticoAnt += $subtotal;
                } else {
                    $predialRusticoAct += $subtotal;
                }
                $predialRustico += $subtotal;
                $anhoInicio++;
            }

            if ($predialRusticoAnt > 0) {
                $conceptos[] = ['concepto' => 'Predial Rústico Anterior', 'monto' => round($predialRusticoAnt, 2)];
            }
            if ($predialRusticoAct > 0) {
                $conceptos[] = ['concepto' => 'Predial Rústico Actual', 'monto' => round($predialRusticoAct, 2)];
            }
            if ($predialRusticoAnt == 0 && $predialRusticoAct == 0 && $predialRustico > 0) {
                $conceptos[] = ['concepto' => 'Predial Rústico', 'monto' => round($predialRustico, 2)];
            }
            $total = round($predialRustico, 2);
        } else {
            $conceptos[] = ['concepto' => 'Sin datos', 'monto' => 0];
            $conceptos[] = ['concepto' => 'Sin cálculo disponible', 'monto' => 0];
        }

        $esRustico = $predio->datosRustico || str_contains(mb_strtoupper($predio->tipoPredio?->Tipo_predio ?? ''), 'RÚSTICO') || str_contains(mb_strtoupper($predio->tipoPredio?->Tipo_predio ?? ''), 'MINA');

        $descInfo = $this->getDescuentosPredio($predio->id_predio);
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
            'predio' => [
                'id' => $predio->id_predio,
                'id_contribuyente' => $predio->id_contribuyente,
                'rfc' => $predio->contribuyente?->rfc,
                'contribuyente_nombre' => $predio->contribuyente?->nombre_completo ?? $predio->contribuyente?->nombre_moral,
                'clave_catastral' => $predio->Clave_predial,
                'cuenta' => $predio->contribuyente?->cuenta,
                'contribuyente' => $predio->contribuyente?->nombre_completo ?? $predio->contribuyente?->nombre_moral,
                'domicilio' => $predio->ubicacion_completa,
                'ultimo_pago' => $predio->año_ultimo_pago
                    ? $predio->año_ultimo_pago . ($predio->ultimo_bimestre_pago ? ' - Bimestre ' . $predio->ultimo_bimestre_pago : '')
                    : 'Sin pagos',
                'tipo_predio' => $predio->tipoPredio?->Tipo_predio ?? '',
                'es_rustico' => $esRustico,
            ],
            'conceptos' => $conceptos,
            'total' => $total,
        ]);
    }

    public function guardar(Request $request)
    {
        $validated = $request->validate([
            'id_predio' => 'required',
            'id_contribuyente' => 'required',
            'monto' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'nombre' => 'required|string|max:255',
            'rfc' => 'required|string|max:20',
            'descripcion' => 'nullable|string',
            'forma_pago' => 'required|string|max:100',
            'tipo_pago' => 'required|string|max:100',
            'conceptos' => 'required|array|min:1',
            'conceptos.*.concepto' => 'required|string',
            'conceptos.*.monto' => 'required|numeric',
            'formas_pagos' => 'required|array|min:1',
            'formas_pagos.*.forma_pago_id' => 'required|integer',
            'formas_pagos.*.monto' => 'required|numeric|min:0.01',
        ]);

        $cajero = Cajero::with('caja')->where('usuario_id', auth()->id())->first();

        if (!$cajero) {
            return response()->json(['error' => 'No tienes un cajero asignado.'], 400);
        }

        $cajaAbierta = HistorialCaja::where('cajero_id', $cajero->id_cajero)
            ->whereNull('datetime_cierre')
            ->first();

        if (!$cajaAbierta) {
            return response()->json(['error' => 'No tienes una caja abierta.'], 400);
        }

        DB::beginTransaction();

        try {
            $ultimoFolio = Pago::max('id') ?? 0;
            $folio = 'PAG-' . str_pad($ultimoFolio + 1, 6, '0', STR_PAD_LEFT);

            $pago = Pago::create([
                'monto' => $validated['monto'],
                'descuento' => $validated['descuento'] ?? 0,
                'folio' => $folio,
                'fecha' => now(),
                'estatus' => 'pagado',
                'forma_pago' => $validated['forma_pago'],
                'tipo_pago' => $validated['tipo_pago'],
                'nombre' => $validated['nombre'],
                'rfc' => $validated['rfc'],
                'descripcion' => $validated['descripcion'] ?? null,
                'id_predio' => $validated['id_predio'],
                'id_contribuyente' => $validated['id_contribuyente'],
                'id_historial_caja' => $cajaAbierta->id,
                'id_usuario' => auth()->id(),
                'anio_pago' => date('Y'),
                'im' => null,
                'url_file' => null,
            ]);

            $cuentasList = DB::table('cuentas')
                ->select('id', DB::raw("TRIM(REPLACE(REPLACE(descripcion, '\r', ''), '\n', '')) as descripcion_clean"))
                ->get();

            $esRustico = ($validated['tipo_pago'] ?? '') === 'predial_rustico';

            $conceptCuentaMapping = $esRustico ? [
                'Predial Rústico Actual' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'RÚSTICO') && str_contains($c->descripcion_clean, 'ACTUAL')
                ),
                'Predial Rústico Anterior' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'RÚSTICO') && str_contains($c->descripcion_clean, 'ANTERIORES')
                ),
                'Predial Rústico' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'RÚSTICO') || str_contains($c->descripcion_clean, 'RUSTICO')
                ),
                'Gastos de Ejecución Predial Urbano' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'COBRANZA') || str_contains($c->descripcion_clean, 'EJECUCIÓN') || str_contains($c->descripcion_clean, 'EJECUCION')
                ),
                'Descuentos' => fn($list) => $list->first(fn($c) =>
                    stripos($c->descripcion_clean, 'DESCUENTO') !== false
                ),
            ] : [
                'Predial Anterior' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'ANTERIORES')
                ),
                'Impuesto Predial Actual' => fn($list) => $list->first(fn($c) =>
                    $c->descripcion_clean === 'PREDIAL URBANO AÑO ACTUAL'
                ),
                'Aseo Público Anterior' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'S.A.P.') && str_contains($c->descripcion_clean, 'REZAGO')
                ),
                'Aseo Público Actual' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'S.A.P. - URBANO ACTUAL')
                ),
                'Recargos Anteriores' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'RECARGOS PREDIAL URBANO')
                ),
                'Recargos Actual' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'RECARGOS PREDIAL URBANO')
                ),
                'Actualización Anterior' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'ACTUALIZACIONES PREDIAL URBANO')
                ),
                'Actualización Actual' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'ACTUALIZACIONES PREDIAL URBANO')
                ),
                'Multa' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'MULTA')
                ),
                'Gastos de Ejecución Predial Urbano' => fn($list) => $list->first(fn($c) =>
                    str_contains($c->descripcion_clean, 'COBRANZA') || str_contains($c->descripcion_clean, 'EJECUCIÓN') || str_contains($c->descripcion_clean, 'EJECUCION')
                ),
                'Descuentos' => fn($list) => $list->first(fn($c) =>
                    stripos($c->descripcion_clean, 'DESCUENTO') !== false
                ),
            ];

            foreach ($validated['conceptos'] as $c) {
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

            foreach ($validated['formas_pagos'] as $fp) {
                \App\Models\FormasPagosCada::create([
                    'pago_id' => $pago->id,
                    'forma_pago_id' => $fp['forma_pago_id'],
                    'monto' => $fp['monto'],
                ]);
            }

            $cajaAbierta->increment('total_ingreso', $validated['monto']);

            $predio = DB::table('tb_predio')
                ->where('id_predio', $validated['id_predio'])
                ->first();

            \App\Models\IncidenciaPago::create([
                'pago_id' => $pago->id,
                'id_predio' => $validated['id_predio'],
                'año_ultimo_pago_anterior' => $predio?->año_ultimo_pago,
                'ultimo_bimestre_pago_anterior' => $predio?->ultimo_bimestre_pago,
                'snapshot' => $predio ? (array) $predio : [],
            ]);

            \App\Models\Descuento::where('idPredio', $validated['id_predio'])
                ->where('activo', true)
                ->update(['activo' => false]);

            DB::table('tb_predio')
                ->where('id_predio', $validated['id_predio'])
                ->update([
                    'año_ultimo_pago' => date('Y'),
                    'ultimo_bimestre_pago' => DB::raw('CEIL(MONTH(CURDATE()) / 2)'),
                ]);

            $qrBase64 = $this->generarQrBase64(route('pagos.recibo', $pago->id));
            $pdf = Pdf::loadView('pagos.recibo-pdf', compact('pago', 'qrBase64'));
            $pdfDir = public_path('recibos');
            if (!is_dir($pdfDir)) {
                mkdir($pdfDir, 0755, true);
            }
            $pdfPath = "recibos/recibo-{$pago->folio}.pdf";
            $pdf->save(public_path($pdfPath));

            $pago->update(['url_file' => $pdfPath]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado exitosamente.',
                'pago_id' => $pago->id,
                'folio' => $folio,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Error al registrar el pago: ' . $e->getMessage()], 500);
        }
    }

    public function cajaGeneralIndex(Request $request)
    {
        $filters = $request->only(['search_folio', 'search_nombre', 'search_secretaria', 'search_fecha', 'search_monto', 'search_estatus']);

        $userSecretariaId = auth()->user()->secretaria_id;

        $ordenes = OrdenPago::with('secretaria', 'user', 'pagos', 'cuentasOrdenesPago.cuenta')
            ->where('secretaria_id', $userSecretariaId)
            ->when($filters['search_folio'] ?? null, fn($q, $v) => $q->where('folio', 'like', "%{$v}%"))
            ->when($filters['search_nombre'] ?? null, fn($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
            ->when($filters['search_secretaria'] ?? null, fn($q, $v) => $q->whereHas('secretaria', fn($q) => $q->where('nombre', 'like', "%{$v}%")))
            ->when($filters['search_fecha'] ?? null, fn($q, $v) => $q->where('fecha', $v))
            ->when($filters['search_estatus'] ?? null, function ($q, $v) {
                if ($v === 'pagado') $q->where('pagado', true);
                elseif ($v === 'pendiente') $q->where('pagado', false);
                elseif ($v === 'vencida') $q->where('pagado', false)->where('fecha_vencimiento', '<', now()->startOfDay());
            })
            ->orderBy('id', 'desc')
            ->paginate(15);

        $formasPago = FormaPago::where('activo', 1)->orderBy('Descripción')->get();

        return Inertia::render('Pagos/CajaGeneral/Index', compact('ordenes', 'formasPago', 'filters', 'userSecretariaId'));
    }

    public function cajaGeneralPagar(OrdenPago $ordenPago)
    {
        if ($ordenPago->pagado) {
            return redirect()->route('pagos.caja-general')->with('error', 'Esta orden ya está pagada.');
        }

        if ($ordenPago->fecha_vencimiento && now()->startOfDay()->gt(now()->parse($ordenPago->fecha_vencimiento))) {
            return redirect()->route('pagos.caja-general')->with('error', 'Esta orden está vencida.');
        }

        $ordenPago->load('cuentasOrdenesPago.cuenta', 'secretaria', 'user');
        $formasPago = FormaPago::where('activo', 1)->orderBy('Descripción')->get();

        return Inertia::render('Pagos/CajaGeneral/Pagar', compact('ordenPago', 'formasPago'));
    }

    public function cajaGeneralGuardar(Request $request)
    {
        $validated = $request->validate([
            'orden_pago_id' => 'required|exists:ordenes_pago,id',
            'formas_pagos' => 'required|array|min:1',
            'formas_pagos.*.forma_pago_id' => 'required|integer|exists:f4_c_formapago,id',
            'formas_pagos.*.monto' => 'required|numeric|min:0.01',
        ]);

        $ordenPago = OrdenPago::with('cuentasOrdenesPago.cuenta')->findOrFail($validated['orden_pago_id']);

        if ($ordenPago->pagado) {
            return response()->json(['error' => 'Esta orden ya está pagada.'], 400);
        }

        if ($ordenPago->fecha_vencimiento && now()->startOfDay()->gt(now()->parse($ordenPago->fecha_vencimiento))) {
            return response()->json(['error' => 'Esta orden está vencida y no puede pagarse.'], 400);
        }

        $idHistorialCaja = null;
        $cajero = \App\Models\Cajero::with('caja')->where('usuario_id', auth()->id())->first();
        if ($cajero) {
            $cajaAbierta = HistorialCaja::where('cajero_id', $cajero->id_cajero)
                ->whereNull('datetime_cierre')
                ->first();
            if (!$cajaAbierta) {
                return response()->json(['error' => 'No tienes una caja abierta. Abre una caja antes de realizar el pago.'], 400);
            }
            $idHistorialCaja = $cajaAbierta->id;
        }

        DB::beginTransaction();

        try {
            $ultimoFolio = Pago::max('id') ?? 0;
            $folio = 'CG-' . str_pad($ultimoFolio + 1, 6, '0', STR_PAD_LEFT);

            $pago = Pago::create([
                'monto' => $ordenPago->monto,
                'folio' => $folio,
                'fecha' => now(),
                'estatus' => 'pagado',
                'forma_pago' => '',
                'rfc' => '',
                'tipo_pago' => 'Ingresos',
                'nombre' => $ordenPago->nombre,
                'descripcion' => $ordenPago->descripcion,
                'id_predio' => '',
                'id_contribuyente' => '',
                'id_historial_caja' => $idHistorialCaja,
                'orden_pago_id' => $ordenPago->id,
                'id_usuario' => auth()->id(),
            ]);

            if ($idHistorialCaja) {
                HistorialCaja::find($idHistorialCaja)->increment('total_ingreso', $ordenPago->monto);
            }

            foreach ($ordenPago->cuentasOrdenesPago as $c) {
                CuentasPagos::create([
                    'pago_id' => $pago->id,
                    'cuenta_id' => $c->IdCuenta,
                    'concepto' => $c->cuenta?->descripcion ?? ('Cuenta #' . $c->IdCuenta),
                    'fecha_registro' => now(),
                    'cantidad' => $c->cantidad,
                    'monto' => $c->monto * $c->cantidad,
                    'concepto_id' => null,
                ]);
            }

            $fpCadaIds = [];
            foreach ($validated['formas_pagos'] as $fp) {
                $fpc = FormasPagosCada::create([
                    'pago_id' => $pago->id,
                    'forma_pago_id' => $fp['forma_pago_id'],
                    'monto' => $fp['monto'],
                ]);
                $fpCadaIds[] = $fpc->id;
            }

            $pago->update(['forma_pago' => implode(',', $fpCadaIds)]);

            $ordenPago->update([
                'pagado' => true,
                'fecha_pago' => now(),
            ]);

            DB::commit();

            try {
                $pago->load(['ordenPago.secretaria', 'ordenPago.user', 'cuentasPagos.cuenta', 'formasPagosCada.formaPago']);
                $qrBase64 = $this->generarQrBase64(route('pagos.recibo', $pago->id));
                $pdf = Pdf::loadView('pagos.recibo-orden-pago-pdf', compact('pago', 'qrBase64'));
                $pdfDir = public_path('recibos');
                if (!is_dir($pdfDir)) {
                    mkdir($pdfDir, 0755, true);
                }
                $pdfPath = "recibos/recibo-{$pago->folio}.pdf";
                $pdf->save(public_path($pdfPath));
                $pago->update(['url_file' => $pdfPath]);
            } catch (\Exception $e) {
                // PDF generation failed but payment was saved
            }

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado exitosamente.',
                'pago_id' => $pago->id,
                'folio' => $folio,
                'redirect' => route('pagos.caja-general.show', $pago->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al registrar el pago: ' . $e->getMessage()], 500);
        }
    }

    public function cajaGeneralShow(Pago $pago)
    {
        if (!$pago->orden_pago_id) {
            return redirect()->route('pagos.caja-general')->with('error', 'Este pago no pertenece a Caja General.');
        }

        $pago->load('ordenPago.cuentasOrdenesPago.cuenta', 'ordenPago.secretaria', 'ordenPago.user', 'cuentasPagos.cuenta', 'formasPagosCada.formaPago');

        return Inertia::render('Pagos/CajaGeneral/Show', compact('pago'));
    }

    public function cajaGeneralCancelar(Pago $pago)
    {
        if ($pago->estatus === 'cancelado') {
            return redirect()->back()->with('error', 'Este pago ya está cancelado.');
        }

        if (!$pago->orden_pago_id) {
            return redirect()->back()->with('error', 'Este pago no pertenece a Caja General.');
        }

        DB::beginTransaction();

        try {
            $pago->update(['estatus' => 'cancelado']);

            if ($pago->ordenPago) {
                $pago->ordenPago->update([
                    'pagado' => false,
                    'fecha_pago' => null,
                ]);
            }

            CuentasPagos::where('pago_id', $pago->id)->update(['monto' => 0]);

            DB::commit();

            try {
                $pago->load(['ordenPago.secretaria', 'ordenPago.user', 'cuentasPagos.cuenta', 'formasPagosCada.formaPago']);
                $qrBase64 = $this->generarQrBase64(route('pagos.recibo', $pago->id));
                $pdf = Pdf::loadView('pagos.recibo-orden-pago-pdf', compact('pago', 'qrBase64'));
                $pdfPath = "recibos/recibo-{$pago->folio}.pdf";
                $pdf->save(public_path($pdfPath));
                $pago->update(['url_file' => $pdfPath]);
            } catch (\Exception $e) {
                // PDF regeneration failed but cancellation was processed
            }

            return redirect()->back()->with('success', 'Pago cancelado y orden restaurada.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al cancelar el pago: ' . $e->getMessage());
        }
    }

    public function recibo($id)
    {
        $pago = Pago::with(['cuentasPagos.cuenta', 'predio.tipoPredio', 'predio.contribuyente', 'predio.datosUrbano.zonaUrbana', 'predio.calle', 'predio.colonia', 'incidencia', 'ordenPago.secretaria', 'ordenPago.user', 'formasPagosCada.formaPago'])->findOrFail($id);
        $qrBase64 = $this->generarQrBase64(route('pagos.recibo', $pago->id));

        if ($pago->url_file && file_exists(public_path($pago->url_file))) {
            return response()->file(public_path($pago->url_file));
        }

        $vista = $pago->orden_pago_id ? 'pagos.recibo-orden-pago-pdf' : 'pagos.recibo-pdf';
        $pdf = Pdf::loadView($vista, compact('pago', 'qrBase64'));

        return $pdf->stream("recibo-{$pago->folio}.pdf");
    }

    public function cancelar(Pago $pago)
    {
        if ($pago->estatus === 'cancelado') {
            return redirect()->back()->with('error', 'Este pago ya está cancelado.');
        }

        $incidencia = \App\Models\IncidenciaPago::where('pago_id', $pago->id)->first();

        if (!$incidencia) {
            return redirect()->back()->with('error', 'No se encontró la incidencia de este pago.');
        }

        DB::beginTransaction();

        try {
            DB::table('tb_predio')
                ->where('id_predio', $pago->id_predio)
                ->update([
                    'año_ultimo_pago' => $incidencia->año_ultimo_pago_anterior,
                    'ultimo_bimestre_pago' => $incidencia->ultimo_bimestre_pago_anterior,
                ]);

            $pago->update(['estatus' => 'cancelado']);

            if ($pago->ordenPago) {
                $pago->ordenPago->update([
                    'pagado' => false,
                    'fecha_pago' => null,
                ]);
            }

            if ($pago->historialCaja) {
                $pago->historialCaja->decrement('total_ingreso', $pago->monto);
            }

            \App\Models\CuentasPagos::where('pago_id', $pago->id)
                ->update(['monto' => 0]);

            $qrBase64 = $this->generarQrBase64(route('pagos.recibo', $pago->id));
            $pdf = Pdf::loadView('pagos.recibo-pdf', compact('pago', 'qrBase64'));
            $pdfPath = "recibos/recibo-{$pago->folio}.pdf";
            $pdf->save(public_path($pdfPath));

            DB::commit();

            return redirect()->back()->with('success', 'Pago cancelado y datos del predio restaurados.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error al cancelar el pago: ' . $e->getMessage());
        }
    }

    public function cerrar(Request $request)
    {
        $cajero = Cajero::with('caja')->where('usuario_id', auth()->id())->first();

        if (!$cajero) {
            return redirect()->route('pagos.index')->with('error', 'No tienes un cajero asignado.');
        }

        $cajaAbierta = HistorialCaja::where('cajero_id', $cajero->id_cajero)
            ->whereNull('datetime_cierre')
            ->first();

        if (!$cajaAbierta) {
            return redirect()->route('pagos.index')->with('error', 'No tienes una caja abierta.');
        }

        $cajaAbierta->update([
            'datetime_cierre' => now(),
        ]);

        return redirect()->route('pagos.corte-pdf', $cajaAbierta->id);
    }

    public function cortePdf(HistorialCaja $historialCaja)
    {
        $historialCaja->load('caja', 'cajero.usuario', 'pagos.formasPagosCada.formaPago');

        $pagos = $historialCaja->pagos;

        $totalRecibos = $pagos->count();
        $totalIngresos = $pagos->sum('monto');

        $pagosPorForma = $pagos->flatMap(function ($pago) {
            return $pago->formasPagosCada->map(function ($fpc) {
                return [
                    'forma_pago' => $fpc->formaPago?->Descripción ?? 'Sin método',
                    'monto' => $fpc->monto,
                ];
            });
        })->groupBy('forma_pago')->map(function ($items, $forma) {
            return [
                'forma' => $forma,
                'total' => $items->sum('monto'),
                'count' => $items->count(),
            ];
        })->values();

        $pdf = Pdf::loadView('pagos.corte-caja-pdf', compact(
            'historialCaja', 'pagos', 'totalRecibos', 'totalIngresos', 'pagosPorForma'
        ));

        return $pdf->stream("corte-caja-{$historialCaja->id}.pdf");
    }

    private function generarQrBase64(string $url): string
    {
        $qrPng = @file_get_contents('https://chart.googleapis.com/chart?chs=80x80&cht=qr&chl=' . urlencode($url));
        return $qrPng !== false ? base64_encode($qrPng) : '';
    }
}
