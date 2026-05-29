<?php

namespace App\Http\Controllers;

use App\Models\Predio;
use App\Models\Contribuyente;
use App\Models\CatUma;
use App\Models\Inpc;
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
        Cache::put("pdf_predios_{$token}", $request->predios, now()->addMinutes(10));
        Cache::put("pdf_status_{$token}", 'processing', now()->addMinutes(10));
        Cache::put("pdf_total_{$token}", count($request->predios), now()->addMinutes(10));

        $phpBinary = PHP_BINARY;
        $artisanPath = base_path('artisan');
        $execAvailable = function_exists('exec')
            && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions') ?? '')));

        if ($execAvailable) {
            if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
                exec("start /B {$phpBinary} {$artisanPath} pdf:estado-cuenta {$token} > NUL 2>&1");
            } else {
                exec("{$phpBinary} {$artisanPath} pdf:estado-cuenta {$token} > /dev/null 2>&1 &");
            }
        } else {
            Cache::put("pdf_status_{$token}", 'error', now()->addMinutes(10));
            Cache::put("pdf_error_{$token}", 'exec() no disponible — configure un worker de cola', now()->addMinutes(10));
        }

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

        return view('estado-cuenta-masivo.progress', [
            'token' => $token,
            'status' => 'processing',
            'totalPredios' => Cache::get("pdf_total_{$token}"),
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
