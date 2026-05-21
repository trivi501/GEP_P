<?php

namespace App\Http\Controllers;

use App\Models\Cajero;
use App\Models\HistorialCaja;
use App\Models\Predio;
use App\Models\FormaPago;
use App\Models\Pago;
use App\Models\CuentasPagos;
use App\Services\CalculoPredialUrbanoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagosController extends Controller
{
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

        return view('pagos.index', compact('historial', 'cajero', 'cajaAbierta'));
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

            $idsHistorial = HistorialCaja::where('cajero_id', $cajero->id_cajero)
                ->pluck('id');

            $pagos = Pago::with('historialCaja.caja', 'predio')
                ->whereIn('id_historial_caja', $idsHistorial)
                ->orderBy('fecha', 'desc')
                ->paginate(15);
        }

        return view('pagos.historial', compact('pagos', 'cajero', 'cajaAbierta'));
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

    public function cobrar()
    {
        $cajero = Cajero::with('caja')->where('usuario_id', auth()->id())->first();

        $cajaAbierta = HistorialCaja::where('cajero_id', $cajero?->id_cajero)
            ->whereNull('datetime_cierre')
            ->first();

        if (!$cajaAbierta) {
            return redirect()->route('pagos.index')->with('error', 'No tienes una caja abierta.');
        }

        $formasPago = FormaPago::where('activo', 1)->orderBy('Descripción')->get();

        return view('pagos.cobrar', compact('cajaAbierta', 'formasPago'));
    }

    public function searchPredio(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $like = '%' . $q . '%';
        $fulltextTerms = '+' . implode('* +', explode(' ', $q)) . '*';

        $ids = DB::table('tb_predio')
            ->whereIn('id_tipo_predio', [5, 6])
            ->where(function ($query) use ($fulltextTerms, $like) {
                $query->whereRaw('MATCH (Clave_predial) AGAINST (? IN BOOLEAN MODE)', [$fulltextTerms])
                    ->orWhereExists(function ($sub) use ($fulltextTerms, $like) {
                        $sub->select(DB::raw(1))
                            ->from('tb_contribuyentes')
                            ->whereColumn('tb_predio.id_contribuyente', 'tb_contribuyentes.id_contribuyente')
                            ->where(function ($w) use ($fulltextTerms, $like) {
                                $w->whereRaw('MATCH (cuenta) AGAINST (? IN BOOLEAN MODE)', [$fulltextTerms])
                                    ->orWhereRaw('MATCH (nombre_completo, nombre_moral) AGAINST (? IN BOOLEAN MODE)', [$fulltextTerms]);
                            });
                    })
                    ->orWhereExists(function ($sub) use ($fulltextTerms) {
                        $sub->select(DB::raw(1))
                            ->from('tb_clave_predial')
                            ->whereColumn('tb_predio.id_clave_predial', 'tb_clave_predial.id_clave_predial')
                            ->whereRaw('MATCH (clave_predial_completa) AGAINST (? IN BOOLEAN MODE)', [$fulltextTerms]);
                    });
            })
            ->take(10)
            ->pluck('id_predio');

        if ($ids->isEmpty()) {
            $ids = DB::table('tb_predio')
                ->whereIn('id_tipo_predio', [5, 6])
                ->where(function ($query) use ($like) {
                    $query->where('Clave_predial', 'like', $like);
                })
                ->take(10)
                ->pluck('id_predio');

            if ($ids->count() < 10) {
                $moreIds = DB::table('tb_predio')
                    ->whereIn('id_tipo_predio', [5, 6])
                    ->whereNotIn('id_predio', $ids)
                    ->whereExists(function ($sub) use ($like) {
                        $sub->select(DB::raw(1))
                            ->from('tb_contribuyentes')
                            ->whereColumn('tb_predio.id_contribuyente', 'tb_contribuyentes.id_contribuyente')
                            ->where(function ($w) use ($like) {
                                $w->where('cuenta', 'like', $like)
                                    ->orWhere('nombre_completo', 'like', $like)
                                    ->orWhere('nombre_moral', 'like', $like);
                            });
                    })
                    ->take(10 - $ids->count())
                    ->pluck('id_predio');
                $ids = $ids->merge($moreIds);
            }

            if ($ids->count() < 10) {
                $moreIds = DB::table('tb_predio')
                    ->whereIn('id_tipo_predio', [5, 6])
                    ->whereNotIn('id_predio', $ids)
                    ->whereExists(function ($sub) use ($like) {
                        $sub->select(DB::raw(1))
                            ->from('tb_clave_predial')
                            ->whereColumn('tb_predio.id_clave_predial', 'tb_clave_predial.id_clave_predial')
                            ->where('clave_predial_completa', 'like', $like);
                    })
                    ->take(10 - $ids->count())
                    ->pluck('id_predio');
                $ids = $ids->merge($moreIds);
            }
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
            'nivelesConstruidos.tipoConstruccion',
            'medidasYColindancias.orientacion',
        ])->find($request->id);

        if (!$predio) {
            return response()->json(null, 404);
        }

        $total = 0;
        $conceptos = [];
        $predial_ant = 0;
        $aseoPublico = 0;
        $recargos = 0;
        $actualizacion = 0;
        $total_ant = 0; 

        if ($predio->datosUrbano) {
            try {
                $calculosController = new CalculosPrediosController(app(CalculoPredialUrbanoService::class));
                $calculosAnuales = $calculosController->getCalculosAnuales($predio);
                
                foreach ($calculosAnuales as $calculo) {
                    if($calculo['anho'] < date('Y')) {
                        $predial_ant += $calculo['entero'];
                        $aseoPublico += $calculo['aseo_publico'] ?? 0;
                        $recargos += $calculo['recargos'] ?? 0;
                        $actualizacion += $calculo['actualizacion'] ?? 0;
                        $total_ant += ($calculo['entero'] ?? 0) + ($calculo['aseo_publico'] ?? 0) + ($calculo['recargos'] ?? 0) + ($calculo['actualizacion'] ?? 0);
                    }else {
                        $aseoPublico += $calculo['aseo_publico'] ?? 0; // Solo sumamos los años anteriores al actual
                        $recargos += $calculo['recargos'] ?? 0;
                        $actualizacion += $calculo['actualizacion'] ?? 0;
                        $total += ($calculo['entero'] ?? 0) + ($calculo['aseo_publico'] ?? 0) + ($calculo['recargos'] ?? 0) + ($calculo['actualizacion'] ?? 0);
                    }
                }
                
                $ultimo = end($calculosAnuales);
                $conceptos[] = ['concepto' => 'Predial Anterior', 'monto' => round($predial_ant, 2)];
                $conceptos[] = ['concepto' => 'Impuesto Predial Actual', 'monto' => round($calculo['entero'], 2)];
                $conceptos[] = ['concepto' => 'Aseo Público', 'monto' => round($aseoPublico, 2)];

                if (!empty($ultimo['recargos']) && $ultimo['recargos'] > 0) {
                    $conceptos[] = ['concepto' => 'Recargos', 'monto' => round($recargos, 2)];
                }
                if (!empty($ultimo['actualizacion']) && $ultimo['actualizacion'] > 0) {
                    $conceptos[] = ['concepto' => 'Actualización', 'monto' => round($actualizacion, 2)];
                }
                if (!empty($ultimo['descuento']) && $ultimo['descuento'] > 0) {
                    $conceptos[] = ['concepto' => 'Descuento por Prono pago', 'monto' => round($ultimo['descuento'], 2)];
                }

                $total = round($total + $total_ant, 2);
            } catch (\Exception $e) {
                $conceptos[] = ['concepto' => 'Error al calcular: ' . $e->getMessage(), 'monto' => 0];
            }
        } else {
            $conceptos[] = ['concepto' => 'Sin datos urbanos', 'monto' => 0];
            $conceptos[] = ['concepto' => 'Sin cálculo disponible', 'monto' => 0];
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

            foreach ($validated['conceptos'] as $c) {
                CuentasPagos::create([
                    'pago_id' => $pago->id,
                    'cuenta_id' => null,
                    'concepto' => $c['concepto'],
                    'fecha_registro' => now(),
                    'cantidad' => 1,
                    'monto' => $c['monto'],
                    'concepto_id' => null,
                ]);
            }

            $cajaAbierta->increment('total_ingreso', $validated['monto']);

            $pdf = Pdf::loadView('pagos.recibo-pdf', ['pago' => $pago]);
            $pdfDir = public_path('pagos/recibos');
            if (!is_dir($pdfDir)) {
                mkdir($pdfDir, 0755, true);
            }
            $pdfPath = "pagos/recibos/recibo-{$pago->folio}.pdf";
            $pdf->save(public_path($pdfPath));

            $pago->update(['url_file' => $pdfPath]);

            DB::table('tb_predio')
                ->where('id_predio', $validated['id_predio'])
                ->update(['año_ultimo_pago' => date('Y')]);

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

    public function recibo($id)
    {
        $pago = Pago::with('cuentasPagos')->findOrFail($id);

        if ($pago->url_file && file_exists(public_path($pago->url_file))) {
            return response()->file(public_path($pago->url_file));
        }

        $pdf = Pdf::loadView('pagos.recibo-pdf', compact('pago'));

        return $pdf->stream("recibo-{$pago->folio}.pdf");
    }
}
