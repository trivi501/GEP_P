<?php

namespace App\Console\Commands;

use App\Models\Predio;
use App\Models\CatUma;
use App\Http\Controllers\CalculosPrediosController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateEstadoCuentaPdf extends Command
{
    protected $signature = 'pdf:estado-cuenta {token}';
    protected $description = 'Generate estado de cuenta masivo PDF for a given token';

    public function handle(): int
    {
        set_time_limit(600);
        ini_set('memory_limit', '1024M');

        $token = $this->argument('token');
        $predioIds = Cache::get("pdf_predios_{$token}");

        if (!$predioIds) {
            $this->error("Token {$token} not found or expired");
            return 1;
        }

        try {
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $predios = Predio::with([
                'contribuyente.domicilio', 'contribuyente.tipoContribuyente',
                'tipoPredio', 'regimenPropiedad', 'estadoRenta', 'estadoImpuesto',
                'tituloPropiedad', 'calle', 'colonia', 'clavePredial',
                'datosUrbano.zonaUrbana', 'datosUrbano.formaPredio', 'datosUrbano.usoPredio',
                'datosUrbano.estadoFisico', 'datosUrbano.pavimento',
                'nivelesConstruidos.tipoConstruccion',
                'nivelesConstruidos.usoConstruccion',
                'medidasYColindancias.orientacion',
            ])->whereIn('id_predio', $predioIds)->get();

            $descuentos = \App\Models\Descuento::whereIn('idPredio', $predioIds)
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

            $path = "$tempDir/estado_cuenta_masivo_{$token}.pdf";
            $pdf->save($path);

            Cache::put("pdf_path_{$token}", $path, 600);
            Cache::put("pdf_status_{$token}", 'ready', 600);

            $this->info("PDF generated successfully for token {$token}");
            return 0;
        } catch (\Throwable $e) {
            Cache::put("pdf_status_{$token}", 'error', 600);
            Cache::put("pdf_error_{$token}", $e->getMessage(), 600);
            $this->error($e->getMessage());
            return 1;
        }
    }

    private function getCalculosRusticos($predio, $umas = null)
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
