<?php

namespace App\Http\Controllers;

use App\Models\PredioCalculoGeneral;
use App\Models\{CatZonaPredio, CatUma, CatTasaXBaldioUrbano, Inpc };
use App\Services\CalculoPredialUrbanoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CalculosPrediosController extends Controller
{
    public function __construct(
        protected CalculoPredialUrbanoService $calculoPredialUrbanoService
    ) {}

    public function index(Request $request)
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
        $habitacional = [
            'A' => 0.0384,
            'B' => 0.0261,
            'C' => 0.0119,
            'D' => 0.0072,
        ];
        $productos = [
            'A' => 0.0522,
            'B' => 0.0384,
            'C' => 0.0197,
            'D' => 0.0119,
        ];




        $calculos = PredioCalculoGeneral::with('predio.contribuyente')
            ->when($request->filled('id_predio'), fn($q) => $q->where('id_predio', $request->id_predio))
            ->when($request->filled('año'), fn($q) => $q->where('año', $request->año))
            ->orderBy('año', 'desc')
            ->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($calculos);
        }

        $predio = null;
        if ($request->filled('id_predio')) {
            $predio = \App\Models\Predio::with('contribuyente', 'datosUrbano.zonaUrbana', 'nivelesConstruidos')->find($request->id_predio);
        }
        $anho_pago = $predio->año_ultimo_pago ?? null;

        $zona = $predio->datosUrbano->zonaUrbana->descripcion?? null;
        $tasa = $zonas[$zona] ?? null;
        $habitacional = $habitacional[$predio->datosUrbano->habitacional] ?? null;
        $producto = $predio->nivelesConstruidos->first()->tipoConstruccion->tipo ?? null;
        $factorZona = $zonas[$zona];
        $baldio = $predio->datosUrbano->Baldio;
        $factorZona = $zonas[$zona] ?? null;
        $recargoBaldio = 0;
        $factorBaldio = 0;
        $impuestoConstruccion = 0;
        $inpc_in = 0;
        $factorActualizacion = 0;
        echo '<p class="text-lg font-semibold mb-2">Clave Catastral: '.$predio->Clave_predial.'</p>';
        echo '<table class="min-w-full divide-y divide-gray-200 text-sm mb-6">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">Año</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">UMA</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">Superficie</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">INPC Inicio</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">INPC Fin</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">Factor Act.</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">Baldío</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">Factor Zona</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">Imp. Terreno</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">Imp. C/Baldío</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">Zona</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">Imp. Const.</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">Construcción</th>
                    <th class="px-4 py-2 border text-xs font-medium uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">';

        while ($anho_pago <= date('Y')) {
            if($anho_pago <= date('Y') && date('m') > 3){
                $inpc_in = Inpc::where('year', $anho_pago)->where('month', 3)->first();
                $inpc_fin = Inpc::orderBy('id', 'desc')->first();
                $factorActualizacion =  ($inpc_fin->value / $inpc_in->value) ;
                if($factorActualizacion < 1){
                    $factorActualizacion = 1;
                }else{
                    $factorActualizacion = $factorActualizacion - 1;
                }

            }
            try {
                $uma = catUma::where('anio', $anho_pago)->where('activo', 1)->first();
                $superficie = $predio->datosUrbano->superficie_terreno_metros_cuadrados ?? 0;
                
                $impTerreno = $superficie * $factorZona * $uma?->valor;
                if($baldio == 1){
                    $factorBaldio = 1;
                    if($zona == 'ZONA I' OR $zona == 'ZONA II'){
                        $factorBaldio = 2;
                    }
                    if($zona == 'ZONA III' OR $zona == 'ZONA IV'){
                        $factorBaldio = 2.5;
                    }
                    if($zona == 'ZONA V' OR $zona == 'ZONA VI'){
                        $factorBaldio = 3;
                    }
                    $impTerreno *= $factorBaldio;
                }
                if($predio->construccion > 0){
                    $factorConstruccion = $productos[$producto] ?? null;
                    $impuestoConstruccion = $predio->construccion * $factorConstruccion * $uma?->valor;
                }
                $total  = $impTerreno + $impuestoConstruccion + (2 * $uma?->valor);

                $fmt = fn($n, $d = 2) => '$' . number_format($n, $d);
                echo '<tr>
                    <td class="px-4 py-2 border text-center">'.$anho_pago.'</td>
                    <td class="px-4 py-2 border text-right">'.$fmt($uma?->valor ?? 0, 6).'</td>
                    <td class="px-4 py-2 border text-right">'.number_format($superficie, 2).' m²</td>
                    <td class="px-4 py-2 border text-right">'.($inpc_in?->value ).'</td>
                    <td class="px-4 py-2 border text-right">'.($inpc_fin?->value ).'</td>
                    <td class="px-4 py-2 border text-right">'.number_format($factorActualizacion, 6).'</td>
                    <td class="px-4 py-2 border text-center">'.($baldio ? 'Sí' : 'No').'</td>
                    <td class="px-4 py-2 border text-right">'.number_format($factorZona, 4).'</td>
                    <td class="px-4 py-2 border text-right">'.$fmt($superficie * $factorZona * $uma?->valor).'</td>
                    <td class="px-4 py-2 border text-right">'.$fmt($impTerreno).'</td>
                    <td class="px-4 py-2 border">'.$zona.'</td>
                    <td class="px-4 py-2 border text-right">'.$fmt($impuestoConstruccion).'</td>
                    <td class="px-4 py-2 border text-right">'.number_format($predio->construccion, 2).' m²</td>
                    <td class="px-4 py-2 border text-right font-bold text-lg">'.$fmt($total).'</td>
                </tr>';
                
                
                
                
                
                $anho_pago++;

            } catch (\RuntimeException $e) {
                // Si el cálculo falla, intentamos con el año anterior
                $request->merge(['año' => (int)$request->año - 1]);
            }
        }

        echo '</tbody></table>';

        DD('zona '.$zona, 'tasa: '.$tasa,'habitacional: '. $habitacional,'producto: '.$producto, 'factor zona: '.$factorZona, 'Terreno: '.$predio->construccion, 'año: '.$predio->año_ultimo_pago, 'baldio: '.$baldio);

        return view('calculos-predios.index', compact('calculos', 'predio'));
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
            'medidasYColindancias.orientacion',
        ])->findOrFail($request->id_predio);

        $calculos = $this->getCalculosAnuales($predio);

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
                in_array($zona, ['ZONA V', 'ZONA VI', 'ZONA VII'])            => $entero * 0.21,
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
            $multa = 0;

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

            $calculos[] = [
                'anho'         => $anhoInicio,
                'uma'          => $valorUmaAnual,
                'hectareas'    => $hectareas,
                'tipo_calculo' => $tipoCalculo,
                'subtotal'     => $subtotal,
                'total'        => $subtotal,
            ];

            $anhoInicio++;
        }

        $pdf = Pdf::loadView('calculos-predios.rustico-pdf', compact('predio', 'calculos'));
        $pdf->setPaper('a4');

        return $pdf->stream("Estado_Cuenta_Rustico_{$predio->Clave_predial}.pdf");
    }
}
