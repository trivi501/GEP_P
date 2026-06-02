<?php

namespace App\Jobs;

use App\Models\Predio;
use App\Models\CatUma;
use App\Models\Descuento;
use App\Http\Controllers\CalculosPrediosController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class GenerateEstadoCuentaChunk implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable;

    public $timeout = 600;
    public $maxExceptions = 1;

    protected array $predioIds;
    protected string $token;
    protected int $chunkIndex;

    public function __construct(array $predioIds, string $token, int $chunkIndex)
    {
        $this->predioIds = $predioIds;
        $this->token = $token;
        $this->chunkIndex = $chunkIndex;
    }

    public function handle(): void
    {
        $predios = Predio::with([
            'contribuyente.domicilio', 'contribuyente.tipoContribuyente',
            'tipoPredio', 'regimenPropiedad', 'estadoRenta', 'estadoImpuesto',
            'tituloPropiedad', 'calle', 'colonia', 'clavePredial',
            'datosUrbano.zonaUrbana', 'datosUrbano.formaPredio', 'datosUrbano.usoPredio',
            'datosUrbano.estadoFisico', 'datosUrbano.pavimento',
            'nivelesConstruidos.tipoConstruccion',
            'nivelesConstruidos.usoConstruccion',
            'medidasYColindancias.orientacion',
        ])->whereIn('id_predio', $this->predioIds)->get();

        $descuentos = Descuento::whereIn('idPredio', $this->predioIds)
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
                        $totalDescuento += $descuentoAnual;
                    }
                }
                unset($c);
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

        $tempDir = public_path('recibos');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $path = "{$tempDir}/chunk_{$this->token}_{$this->chunkIndex}.pdf";
        $pdf->save($path);

        Cache::increment("pdf_progress_{$this->token}");
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
