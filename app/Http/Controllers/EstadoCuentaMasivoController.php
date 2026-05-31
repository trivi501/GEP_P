<?php

namespace App\Http\Controllers;

use App\Models\Predio;
use App\Models\Contribuyente;
use App\Models\CatUma;
use App\Models\Descuento;
use App\Http\Controllers\CalculosPrediosController;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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

        $token = Str::random(40);
        $ttl = now()->addMinutes(60);

        Cache::put("pdf_status_{$token}", 'processing', $ttl);
        Cache::put("pdf_total_{$token}", count($request->predios), $ttl);

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

        $descuentos = Descuento::whereIn('idPredio', $request->predios)
            ->where('activo', true)
            ->where(function ($q) {
                $q->whereNull('fecha_expiracion')->orWhere('fecha_expiracion', '>=', now()->toDateString());
            })
            ->get()
            ->keyBy('idPredio');

        $umas = CatUma::where('activo', 1)->get()->keyBy('anio');
        $calculosController = app(CalculosPrediosController::class);

        $data = [];
        $granTotal = 0;
        $totalPredios = 0;

        foreach ($predios as $predio) {
            $esRustico = str_contains($predio->tipoPredio?->Tipo_predio ?? '', 'RÚSTICO')
                || str_contains($predio->tipoPredio?->Tipo_predio ?? '', 'RUSTICO')
                || str_contains($predio->tipoPredio?->Tipo_predio ?? '', 'MINA');

            if ($esRustico) {
                $calculos = $this->getCalculosRusticos($predio, $umas);
            } else {
                $calculos = $calculosController->getCalculosAnuales($predio);
            }

            $subtotalPredio = collect($calculos)->sum('total');

            $totalDescuento = 0;
            $descuento = $descuentos->get($predio->id_predio);

            if ($descuento) {
                $descMulta = 0;
                $descActualizacion = 0;
                $descCobranza = 0;

                foreach ($calculos as &$c) {
                    if (!empty($c['multa']) && $descuento->multas > 0) {
                        $descMulta += $c['multa'] * (float) $descuento->multas / 100;
                    }
                    if (!empty($c['actualizacion']) && $descuento->actualizaciones > 0) {
                        $descActualizacion += $c['actualizacion'] * (float) $descuento->actualizaciones / 100;
                    }
                    if (!empty($c['cobranza']) && $descuento->gastos_cobranza > 0) {
                        $descCobranza += $c['cobranza'] * (float) $descuento->gastos_cobranza / 100;
                    }
                }
                unset($c);

                $totalDescuento = round($descMulta + $descActualizacion + $descCobranza, 2);
            }

            $subtotalConDescuento = round($subtotalPredio - $totalDescuento, 2);
            $granTotal += $subtotalConDescuento;
            $totalPredios++;

            $data[] = [
                'predio' => $predio,
                'calculos' => $calculos,
                'subtotal' => $subtotalConDescuento,
                'esRustico' => $esRustico,
                'descuento' => $totalDescuento > 0 ? $totalDescuento : null,
            ];
        }

        $pdf = Pdf::loadView('estado-cuenta-masivo.pdf', compact('data', 'granTotal', 'totalPredios'));
        $pdf->setPaper('a4');

        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $path = "{$tempDir}/estado_cuenta_masivo_{$token}.pdf";
        $pdf->save($path);

        Cache::put("pdf_path_{$token}", $path, $ttl);
        Cache::put("pdf_status_{$token}", 'ready', $ttl);

        return redirect()->route('estado-cuenta-masivo.progress', ['token' => $token]);
    }

    public function progress($token)
    {
        $status = Cache::get("pdf_status_{$token}");
        $error = Cache::get("pdf_error_{$token}");

        if (!$status) {
            abort(404, 'Token no válido o expirado');
        }

        if ($status === 'ready') {
            $path = Cache::get("pdf_path_{$token}");
            $filename = basename($path);
            return view('estado-cuenta-masivo.progress', [
                'token' => $token,
                'status' => 'ready',
                'filename' => $filename,
            ]);
        }

        return view('estado-cuenta-masivo.progress', [
            'token' => $token,
            'status' => $status === 'error' ? 'error' : 'processing',
            'error' => $error,
        ]);
    }

    public function download($token)
    {
        $status = Cache::get("pdf_status_{$token}");
        $path = Cache::get("pdf_path_{$token}");

        if ($status !== 'ready' || !$path || !file_exists($path)) {
            abort(404, 'PDF no disponible');
        }

        $cleanupPath = $path;
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"',
        ])->deleteFileAfterSend(true);
    }

    private function getCalculosRusticos($predio, $umas = null): array
    {
        $valorUma = $umas?->get(now()->year)?->valor ?? CatUma::where('anio', now()->year)->where('activo', 1)->first()?->valor ?? 0;
        $hectareas = $predio->superficie ?? 0;
        $tipoPredio = $predio->tipoPredio?->Tipo_predio ?? '';
        $esMina = str_contains($tipoPredio, 'MINA');

        $anhoInicio = ($predio->año_ultimo_pago ?? now()->year) + 1;
        $anhoActual = now()->year;

        $calculos = [];
        while ($anhoInicio <= $anhoActual) {
            $umaAnual = $umas?->get($anhoInicio)?->valor ?? CatUma::where('anio', $anhoInicio)->where('activo', 1)->first()?->valor ?? $valorUma;

            if ($esMina) {
                $subtotal = $hectareas * (11 * $umaAnual);
            } elseif ($predio->datosRustico?->valor_catastral_casa) {
                $subtotal = ($predio->datosRustico->valor_catastral_casa ?? 0) * 0.015;
            } elseif ($predio->datosRustico?->valor_catastral_superficie_riego) {
                $subtotal = ($hectareas * 6.40) * (2 * $umaAnual) + (2 * $umaAnual);
            } elseif ($predio->datosRustico?->valor_catastral_superficie_temporal) {
                if ($hectareas < 20) {
                    $subtotal = (3 * $umaAnual) + ($hectareas * 3);
                } else {
                    $subtotal = (2 * $umaAnual) + ($hectareas * 6.40);
                }
            } else {
                if ($hectareas < 20) {
                    $subtotal = ($hectareas * 3) + (3 * $umaAnual) + (2 * $umaAnual);
                } else {
                    $subtotal = ($hectareas * 6.40) + (2 * $umaAnual) + (2 * $umaAnual);
                }
            }

            $calculos[] = [
                'anho' => $anhoInicio,
                'uma' => $umaAnual,
                'hectareas' => $hectareas,
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ];

            $anhoInicio++;
        }

        return $calculos;
    }
}
