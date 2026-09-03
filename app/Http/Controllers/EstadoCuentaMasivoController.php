<?php

namespace App\Http\Controllers;

use App\Models\Predio;
use App\Models\Contribuyente;
use App\Models\CatUma;
use App\Models\Inpc;
use App\Jobs\FinalizeEstadoCuentaPdf;
use App\Jobs\GenerateEstadoCuentaChunk;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
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
            'descuentos' => fn($q) => $q->where('activo', true)
                ->where(function ($q) {
                    $q->whereNull('fecha_expiracion')->orWhere('fecha_expiracion', '>=', now()->toDateString());
                }),
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
                'tiene_descuento' => $p->descuentos->isNotEmpty(),
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
        Cache::put("pdf_predios_{$token}", $request->predios, $ttl);
        Cache::put("pdf_status_{$token}", 'processing', $ttl);
        Cache::put("pdf_total_{$token}", count($request->predios), $ttl);

        $chunkSize = 50;
        $chunks = array_chunk($request->predios, $chunkSize);
        $totalChunks = count($chunks);

        Cache::put("pdf_total_chunks_{$token}", $totalChunks, $ttl);
        Cache::put("pdf_progress_{$token}", 0, $ttl);

        $jobs = [];
        foreach ($chunks as $index => $chunkIds) {
            $jobs[] = new GenerateEstadoCuentaChunk($chunkIds, $token, $index + 1);
        }

        Bus::batch($jobs)
            ->finally(function () use ($token, $totalChunks) {
                FinalizeEstadoCuentaPdf::dispatch($token, $totalChunks);
            })
            ->catch(function () use ($token) {
                Cache::put("pdf_status_{$token}", 'error', now()->addMinutes(60));
                Cache::put("pdf_error_{$token}", 'Falló uno o más lotes. Revisa los logs.', now()->addMinutes(60));
            })
            ->dispatch();

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

        if ($status === 'error') {
            return view('estado-cuenta-masivo.progress', [
                'token' => $token,
                'status' => 'error',
                'error' => $error,
            ]);
        }

        $totalChunks = Cache::get("pdf_total_chunks_{$token}");
        $completedChunks = Cache::get("pdf_progress_{$token}", 0);
        return view('estado-cuenta-masivo.progress', [
            'token' => $token,
            'status' => 'processing',
            'totalPredios' => Cache::get("pdf_total_{$token}"),
            'totalChunks' => $totalChunks,
            'completedChunks' => $completedChunks,
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
