<?php

namespace App\Http\Controllers;

use App\Models\PredioCalculoGeneral;
use App\Models\{CatZonaPredio, CatUma, CatTasaXBaldioUrbano, Inpc };
use App\Services\CalculoPredialUrbanoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CalculosPrediosController extends Controller
{
    public function __construct(
        protected CalculoPredialUrbanoService $calculoPredialUrbanoService
    ) {}

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $calculos = PredioCalculoGeneral::with('predio.contribuyente')
                ->when($request->filled('id_predio'), fn($q) => $q->where('id_predio', $request->id_predio))
                ->when($request->filled('año'), fn($q) => $q->where('año', $request->año))
                ->orderBy('año', 'desc')
                ->paginate(20);
            return response()->json($calculos);
        }

        $predio = null;
        $calculos = [];
        if ($request->filled('id_predio')) {
            $predio = \App\Models\Predio::with('contribuyente', 'datosUrbano.zonaUrbana', 'nivelesConstruidos.tipoConstruccion', 'nivelesConstruidos.usoConstruccion')->find($request->id_predio);
            if ($predio) {
                $calculos = $this->getCalculosAnuales($predio);
            }
        }

        return Inertia::render('CalculosPredios/Index', compact('calculos', 'predio'));
    }

    public function pdf(Request $request)
    {
        $predio = \App\Models\Predio::with([
            'contribuyente.domicilio', 'contribuyente.tipoContribuyente',
            'tipoPredio', 'regimenPropiedad', 'estadoRenta', 'estadoImpuesto',
            'tituloPropiedad', 'calle', 'colonia', 'clavePredial',
            'datosUrbano.zonaUrbana', 'datosUrbano.formaPredio', 'datosUrbano.usoPredio',
            'datosUrbano.estadoFisico', 'datosUrbano.pavimento',
            'nivelesConstruidos.tipoConstruccion',
            'nivelesConstruidos.usoConstruccion',
            'medidasYColindancias.orientacion',
        ])->findOrFail($request->id_predio);

        $calculos = $this->getCalculosAnuales($predio);

        $descuento = \App\Models\Descuento::where('idPredio', $predio->id_predio)->where('activo', 1)->first();
        if ($descuento) {
            foreach ($calculos as &$c) {
                $descuentoAnual = 0;
                if (!empty($c['multa']) && $descuento->multas > 0) {
                    $descuentoAnual += $c['multa'] * (float) $descuento->multas / 100;
                }
                if (!empty($c['actualizacion']) && $descuento->actualizaciones > 0) {
                    $descuentoAnual += $c['actualizacion'] * (float) $descuento->actualizaciones / 100;
                }
                if (!empty($c['recargos']) && $descuento->recargos > 0) {
                    $descuentoAnual += $c['recargos'] * (float) $descuento->recargos / 100;
                }
                if (!empty($c['cobranza']) && $descuento->gastos_cobranza > 0) {
                    $descuentoAnual += $c['cobranza'] * (float) $descuento->gastos_cobranza / 100;
                }
                if ($descuentoAnual > 0) {
                    $descuentoAnual = round($descuentoAnual, 2);
                    $c['descuento'] = round(($c['descuento'] ?? 0) + $descuentoAnual, 2);
                    $c['total'] = round($c['total'] - $descuentoAnual, 2);
                }
            }
            unset($c);
        }

        $pdf = Pdf::loadView('calculos-predios.pdf', compact('predio', 'calculos'));
        $pdf->setPaper('a4');

        return $pdf->stream("Estado de Cuenta {$predio->Clave_predial}.pdf");
    }

    public function getCalculosAnuales(\App\Models\Predio $predio): array
    {
        $zonas = [
            'ZONA I'              => 0.0131,
            'ZONA II'             => 0.0151,
            'ZONA III'            => 0.0162,
            'ZONA IV'             => 0.0237,
            'ZONA V'              => 0.0340,
            'ZONA VI'             => 0.0406,
            'ZONA VII'            => 0.0622,
            'ZONA VIII'           => 0.0725,
            'ZONA Urbano-Rural'   => 0.0013,
            'ZONA Industrial'     => 0.0622,
        ];

        $zonas_2016 = [
            'ZONA I'              => 0.0013,
            'ZONA II'             => 0.0026,
            'ZONA III'            => 0.0054,
            'ZONA IV'             => 0.0079,
            'ZONA V'              => 0.0166,
            'ZONA VI'             => 0.0254,
            'ZONA VII'            => 0.0399,
            'ZONA VIII'           => 0.0399,
            'ZONA Urbano-Rural'   => 0.0399,
            'ZONA Industrial'     => 0.0399,
        ];
        $productos = [
            'A' => 0.0522,
            'B' => 0.0384,
            'C' => 0.0197,
            'D' => 0.0119,
        ];

         $habitacional = [
            'A' => 0.0384,
            'B' => 0.0261,
            'C' => 0.0119,
            'D' => 0.0072,
        ];

        $factor_baldio = [
            'ZONA I'              => 0,
            'ZONA II'             => 1,
            'ZONA III'            => 1,
            'ZONA IV'             => 1.5,
            'ZONA V'              => 1.5,
            'ZONA VI'             => 2,
            'ZONA VII'            => 2,
            'ZONA VIII'           => 2,
            'ZONA Urbano-Rural'   => 0,
            'ZONA Industrial'     => 2,
        ];

        $zona = $predio->datosUrbano->zonaUrbana->descripcion ?? null;
        $factorZona= null;
        $baldio = $predio->datosUrbano->Baldio ?? 0;
        $nivel = $predio->nivelesConstruidos->first();
        $producto = $nivel?->tipoConstruccion?->tipo ?? null;
        $usoConstruccion = $nivel?->usoConstruccion?->USO;
        $anhoInicio = $predio->año_ultimo_pago ?? now()->year;
        $anhoActual = now()->year;
        $superficie = $predio->datosUrbano->superficie_terreno_metros_cuadrados ?? 0;
        $construccionM2 = $predio->construccion ?? 0;

        $resultados = [];

        $anhoInicio = $anhoInicio + 1;
        $primerAnioCalculado = $anhoInicio;
        while ($anhoInicio <= $anhoActual) {
            if($anhoInicio <= 2018){
                $factorZona = $zonas_2016[$zona] ?? 0;
            } else {
                $factorZona = $zonas[$zona] ?? 0;
            }
            $inpc_in = null;
            $inpc_fin = null;
            $factorActualizacion = 0;

            if ($anhoInicio <= $anhoActual && now()->month > 3) {
                $inpc_in = Inpc::where('year', $anhoInicio)->where('month', 3)->first();
                $inpc_fin = Inpc::orderBy('id', 'desc')->first();
                if ($inpc_in && $inpc_fin) {
                    $factorActualizacion = $inpc_fin->value / $inpc_in->value;
                    $factorActualizacion = max(1, $factorActualizacion) - 1;
                }
            }

            $uma = CatUma::where('anio', $anhoInicio)->where('activo', 1)->first();
            $valorUma = $uma?->valor ?? 0;

            $impTerreno = $superficie * $factorZona * $valorUma;

            $imp_baldio = 0;
            if ($baldio == 1) {
                $imp_baldio = $impTerreno * ($factor_baldio[$zona] ?? 0);
            }

            $impuestoConstruccion = 0;
            if ($construccionM2 > 0 && $producto) {
                $factor = ($usoConstruccion === 'P' ? $productos : $habitacional)[$producto] ?? 0;
                $impuestoConstruccion = $construccionM2 * $factor * $valorUma;
                $cuotaMinima = 2 * $valorUma;
            }

            $cuotaMinima = 2 * $valorUma;
            $entero = $impTerreno + $imp_baldio + $impuestoConstruccion + $cuotaMinima;

            $aseoPublico = match (true) {
                in_array($zona, ['ZONA I', 'ZONA II', 'ZONA III', 'ZONA IV']) => $entero * 0.105,
                in_array($zona, ['ZONA V', 'ZONA VI', 'ZONA VII', 'ZONA VIII']) => $entero * 0.21,
                default                                                       => 0,
            };

            $recargos = 0;
            if ($anhoInicio < $anhoActual) {
                $meses = (12 - 3) + ($anhoActual - $anhoInicio - 1) * 12 + now()->month;
                $recargos = $entero * 0.027 * $meses;
            } elseif ($anhoInicio == $anhoActual && now()->month > 3) {
                $meses = now()->month - 3;
                $recargos = $entero * 0.027 * $meses;
            }

            $actualizacion = $factorActualizacion * $entero;
            $cobranza = 0;
            $ultimoPago = $predio->año_ultimo_pago;
            if ($ultimoPago && ($ultimoPago + 1) == $anhoActual) {
                $cobranza = 0;
            } elseif ($primerAnioCalculado > ($anhoActual - 5)) {
                if ($anhoInicio == $primerAnioCalculado) {
                    $cobranza = 678.84;
                }
            } else {
                if ($anhoInicio == ($anhoActual - 5)) {
                    $cobranza = 678.84;
                }
            }
            $multa = $anhoInicio < $anhoActual ? 3750 : 0;

            $descuento = 0;
            if ($anhoInicio == $anhoActual) {
                $descuento = match (now()->month) {
                    1 => $entero * 0.15,
                    2 => $entero * 0.10,
                    3 => $entero * 0.05,
                    default => 0,
                };
            }


            $total = $entero + $aseoPublico + $recargos + $actualizacion + $cobranza + $multa - $descuento;

            $resultados[] = [
                'anho'                => $anhoInicio,
                'uma'                 => $valorUma,
                'superficie'          => $superficie,
                'baldio'          => $imp_baldio,
                'imp_baldio'      => $imp_baldio,
                'imp_terreno'         => $superficie * $factorZona * $valorUma,
                'imp_terreno_baldio'  => $impTerreno,
                'zona'                => $zona,
                'imp_construccion'    => $impuestoConstruccion,
                'construccion'        => $construccionM2,
                'total'               => $total,
                'cm'                  => $cuotaMinima,
                'entero'              => $entero,
                'aseo_publico'        => $aseoPublico,
                'actualizacion'       => $actualizacion,
                'recargos'            => $recargos,
                'descuento'           => $descuento,
                'multa'               => $multa,
                'cobranza'            => $cobranza,
            ];

            $anhoInicio++;
        }

        return $resultados;
    }

    public function pdfRustico(Request $request)
    {
        $predio = \App\Models\Predio::with([
            'contribuyente', 'tipoPredio', 'datosRustico',
        ])->findOrFail($request->id_predio);

        $uma = \App\Models\CatUma::where('anio', now()->year)->where('activo', 1)->first();
        $valorUma = $uma?->valor ?? 0;
        $hectareas = ($predio->superficie ?? 0) ;
        $tipoPredio = $predio->tipoPredio?->Tipo_predio ?? '';
        $esMina = str_contains($tipoPredio, 'MINA');

        $anhoInicio = $predio->año_ultimo_pago ?? now()->year;
        $anhoActual = now()->year;

        $calculos = [];

        $anhoInicio = $anhoInicio + 1;
        $primerAnioCalculado = $anhoInicio;
        while ($anhoInicio <= $anhoActual) {
            $umaAnual = \App\Models\CatUma::where('anio', $anhoInicio)->where('activo', 1)->first();
            $valorUmaAnual = $umaAnual?->valor ?? $valorUma;

            if ($esMina) {
                $tipoCalculo = 'Minas';
                $subtotal = $hectareas * (11 * $valorUmaAnual);
            } elseif ($predio->datosRustico?->valor_catastral_casa) {
                $tipoCalculo = 'Planta Metalúrgica';
                $subtotal = ($predio->datosRustico->valor_catastral_casa ?? 0) * 0.015;
            } elseif ($predio->datosRustico?->valor_catastral_superficie_riego) {
                $tipoCalculo = 'Riego por gravedad';
                $subtotal = ($hectareas * 6.40) * (2 * $valorUmaAnual) + (2 * $umaAnual);
            } elseif ($predio->datosRustico?->valor_catastral_superficie_temporal) {
                if ($hectareas < 20) {
                    $tipoCalculo = 'Temporal menor a 20 ha';
                    $subtotal = (3 * $valorUmaAnual) + ($hectareas * 3);
                } else {
                    $tipoCalculo = 'Temporal mayor a 20 ha';
                    $subtotal = (2 * $valorUmaAnual) + ($hectareas * 6.40);
                }
            } else {
                $tipoCalculo = 'Riego por bombeo';
                if($hectareas < 20) {
                    $subtotal = ($hectareas * 3) + (3 * $valorUmaAnual) + (2 * $valorUmaAnual);
                } else {
                    $subtotal = ($hectareas * 6.40) + (2 * $valorUmaAnual) + (2 * $valorUmaAnual);
                }
            }

            $meses = 0;
            if ($anhoInicio < $anhoActual) {
                $meses = (12 - 3) + ($anhoActual - $anhoInicio - 1) * 12 + now()->month;
            } elseif ($anhoInicio == $anhoActual && now()->month > 3) {
                $meses = now()->month - 3;
            }
            $recargos = $subtotal * 0.027 * $meses;

            $actualizacion = 0;
            $inpc_in = \App\Models\Inpc::where('year', $anhoInicio)->where('month', 3)->first();
            $inpc_fin = \App\Models\Inpc::orderBy('id', 'desc')->first();
            if ($inpc_in && $inpc_fin) {
                $factorActualizacion = max(1, $inpc_fin->value / $inpc_in->value) - 1;
                $actualizacion = $factorActualizacion * $subtotal;
            }

            $cobranza = 0;
            $ultimoPago = $predio->año_ultimo_pago;
            if ($ultimoPago && ($ultimoPago + 1) == $anhoActual) {
                $cobranza = 0;
            } elseif ($primerAnioCalculado > ($anhoActual - 5)) {
                if ($anhoInicio == $primerAnioCalculado) {
                    $cobranza = 678.84;
                }
            } else {
                if ($anhoInicio == ($anhoActual - 5)) {
                    $cobranza = 678.84;
                }
            }

            $multa = $anhoInicio < $anhoActual ? 1875 : 0;

            $descuento = 0;
            if ($anhoInicio == $anhoActual) {
                $descuento = match (now()->month) {
                    1 => $subtotal * 0.15,
                    2 => $subtotal * 0.10,
                    3 => $subtotal * 0.05,
                    default => 0,
                };
            }

            $descuentoDb = 0;
            $descuentoModel = \App\Models\Descuento::where('idPredio', $predio->id_predio)
                ->where('activo', true)
                ->where(function ($q) {
                    $q->whereNull('fecha_expiracion')->orWhere('fecha_expiracion', '>=', now()->toDateString());
                })
                ->first();

            if ($descuentoModel) {
                if ($descuentoModel->multas > 0 && $multa > 0) {
                    $descuentoDb += $multa * (float) $descuentoModel->multas / 100;
                }
                if ($descuentoModel->actualizaciones > 0 && $actualizacion > 0) {
                    $descuentoDb += $actualizacion * (float) $descuentoModel->actualizaciones / 100;
                }
                if ($descuentoModel->recargos > 0 && $recargos > 0) {
                    $descuentoDb += $recargos * (float) $descuentoModel->recargos / 100;
                }
                if ($descuentoModel->gastos_cobranza > 0 && $cobranza > 0) {
                    $descuentoDb += $cobranza * (float) $descuentoModel->gastos_cobranza / 100;
                }
            }

            $descuentoTotal = $descuento + $descuentoDb;
            $total = $subtotal + $recargos + $actualizacion + $cobranza + $multa - $descuentoTotal;

            $calculos[] = [
                'anho'          => $anhoInicio,
                'uma'           => $valorUmaAnual,
                'hectareas'     => $hectareas,
                'tipo_calculo'  => $tipoCalculo,
                'subtotal'      => $subtotal,
                'recargos'      => $recargos,
                'actualizacion' => $actualizacion,
                'cobranza'      => $cobranza,
                'multa'         => $multa,
                'descuento'     => $descuentoTotal,
                'total'         => $total,
            ];

            $anhoInicio++;
        }

        $pdf = Pdf::loadView('calculos-predios.rustico-pdf', compact('predio', 'calculos'));
        $pdf->setPaper('a4');

        return $pdf->stream("Estado_Cuenta_Rustico_{$predio->Clave_predial}.pdf");
    }
}
