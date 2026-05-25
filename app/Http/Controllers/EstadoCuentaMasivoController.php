<?php

namespace App\Http\Controllers;

use App\Models\Predio;
use App\Models\Contribuyente;
use App\Models\CatUma;
use App\Models\Inpc;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use Illuminate\Http\Request;

class EstadoCuentaMasivoController extends Controller
{
    public function index()
    {
        return Inertia::render('EstadoCuentaMasivo/Index');
    }

    public function search(Request $request)
    {
        $request->validate(['cuenta' => 'required|string']);

        $contribuyentes = Contribuyente::where('cuenta', 'like', '%' . $request->cuenta . '%')
            ->where('activo', 1)
            ->get();

        $predios = Predio::with([
            'contribuyente', 'tipoPredio', 'colonia', 'calle',
            'estadoImpuesto', 'datosUrbano.zonaUrbana',
            'nivelesConstruidos.tipoConstruccion',
            'nivelesConstruidos.usoConstruccion',
        ])
            ->whereIn('id_contribuyente', $contribuyentes->pluck('id_contribuyente'))
            ->get()
            ->map(fn($p) => [
                'id' => $p->id_predio,
                'Clave_predial' => $p->Clave_predial,
                'cuenta' => $p->contribuyente?->cuenta ?? '—',
                'contribuyente' => $p->contribuyente?->nombre_completo ?? '—',
                'colonia' => $p->colonia?->COLONIA ?? '—',
                'tipo_predio' => $p->tipoPredio?->Tipo_predio ?? '—',
                'ubicacion' => $p->ubicacion ?? '—',
                'ubicacionPredio' => trim(($p->calle?->nombre ?? '') . ' #' . ($p->Numero_exterior ?? '') . ($p->Numero_interior ? ' Int ' . $p->Numero_interior : '') . ', ' . ($p->colonia?->COLONIA ?? '')),
                'año_ultimo_pago' => $p->año_ultimo_pago ?? '—',
                'superficie' => (float) $p->superficie,
                'terreno' => (float) ($p->datosUrbano?->valor_catastral_terreno ?? $p->valor_catastral ?? 0),
                'construccion' => (float) ($p->construccion ?? 0),
            ]);

        return response()->json([
            'contribuyentes' => $contribuyentes,
            'predios' => $predios,
        ]);
    }

    public function pdf(Request $request)
    {
        $request->validate(['predios' => 'required|array', 'predios.*' => 'string']);

        $predios = Predio::with([
            'contribuyente.domicilio', 'contribuyente.tipoContribuyente',
            'tipoPredio', 'regimenPropiedad', 'estadoRenta', 'estadoImpuesto',
            'tituloPropiedad', 'calle', 'colonia', 'clavePredial',
            'datosUrbano.zonaUrbana', 'datosUrbano.formaPredio', 'datosUrbano.usoPredio',
            'datosUrbano.estadoFisico', 'datosUrbano.pavimento',
            'nivelesConstruidos.tipoConstruccion',
            'nivelesConstruidos.usoConstruccion',
            'medidasYColindancias.orientacion',
        ])->whereIn('id_predio', $request->predios)->get();

        $calculosController = app(CalculosPrediosController::class);

        $data = [];
        $granTotal = 0;
        $totalPredios = 0;

        foreach ($predios as $predio) {
            $esRustico = str_contains($predio->tipoPredio?->Tipo_predio ?? '', 'RÚSTICO')
                || str_contains($predio->tipoPredio?->Tipo_predio ?? '', 'RUSTICO')
                || str_contains($predio->tipoPredio?->Tipo_predio ?? '', 'MINA');

            if ($esRustico) {
                $calculos = $this->getCalculosRusticos($predio);
            } else {
                $calculos = $calculosController->getCalculosAnuales($predio);
            }

            $subtotalPredio = collect($calculos)->sum('total');
            $granTotal += $subtotalPredio;
            $totalPredios++;

            $data[] = [
                'predio' => $predio,
                'calculos' => $calculos,
                'subtotal' => $subtotalPredio,
                'esRustico' => $esRustico,
            ];
        }

        $pdf = Pdf::loadView('estado-cuenta-masivo.pdf', compact('data', 'granTotal', 'totalPredios'));
        $pdf->setPaper('a4');

        $contribuyente = $predios->first()->contribuyente;
        $nombreArchivo = 'Estado_Cuenta_Masivo_' . ($contribuyente->cuenta ?? 'varios') . '.pdf';

        return $pdf->stream($nombreArchivo);
    }

    private function getCalculosRusticos($predio)
    {
        $uma = CatUma::where('anio', now()->year)->where('activo', 1)->first();
        $valorUma = $uma?->valor ?? 0;
        $hectareas = $predio->superficie ?? 0;
        $tipoPredio = $predio->tipoPredio?->Tipo_predio ?? '';
        $esMina = str_contains($tipoPredio, 'MINA');

        $anhoInicio = ($predio->año_ultimo_pago ?? now()->year) + 1;
        $anhoActual = now()->year;

        $calculos = [];
        while ($anhoInicio <= $anhoActual) {
            $umaAnual = CatUma::where('anio', $anhoInicio)->where('activo', 1)->first();
            $valorUmaAnual = $umaAnual?->valor ?? $valorUma;

            if ($esMina) {
                $subtotal = $hectareas * (11 * $valorUmaAnual);
            } elseif ($predio->datosRustico?->valor_catastral_casa) {
                $subtotal = ($predio->datosRustico->valor_catastral_casa ?? 0) * 0.015;
            } elseif ($predio->datosRustico?->valor_catastral_superficie_riego) {
                $subtotal = ($hectareas * 6.40) * (2 * $valorUmaAnual) + (2 * $umaAnual);
            } elseif ($predio->datosRustico?->valor_catastral_superficie_temporal) {
                if ($hectareas < 20) {
                    $subtotal = (3 * $valorUmaAnual) + ($hectareas * 3);
                } else {
                    $subtotal = (2 * $valorUmaAnual) + ($hectareas * 6.40);
                }
            } else {
                if ($hectareas < 20) {
                    $subtotal = ($hectareas * 3) + (3 * $valorUmaAnual) + (2 * $valorUmaAnual);
                } else {
                    $subtotal = ($hectareas * 6.40) + (2 * $valorUmaAnual) + (2 * $valorUmaAnual);
                }
            }

            $calculos[] = [
                'anho' => $anhoInicio,
                'uma' => $valorUmaAnual,
                'hectareas' => $hectareas,
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ];

            $anhoInicio++;
        }

        return $calculos;
    }
}
