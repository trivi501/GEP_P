<?php

namespace App\Services;

use App\Models\Predio;
use App\Models\PredioCalculoGeneral;
use App\Models\CatUma;
use App\Models\CatTasaImpuestoPorSuperficieUrbano;
use App\Models\CatTasaXBaldioUrbano;
use App\Models\CatTasaXConstruccion;
use App\Models\CatFactoresRequerimiento;
use Illuminate\Support\Str;

class CalculoPredialUrbanoService
{
    public function calcular(string $idPredio, ?int $año = null): PredioCalculoGeneral
    {
        $año = $año ?? (int) date('Y');

        $predio = Predio::with([
            'contribuyente',
            'tipoPredio',
            'clavePredial',
            'colonia',
            'calle',
            'datosUrbano.zonaUrbana',
        ])->findOrFail($idPredio);

        $urbano = $predio->datosUrbano;

        if (!$urbano) {
            throw new \RuntimeException("El predio {$idPredio} no tiene datos urbanos.");
        }

        $uma = CatUma::where('anio', $año)->where('activo', 1)->first();
        $valorUma = $uma?->valor ?? 0;
        $minimoUrbano = $uma?->minimo_urbano ?? 0;

        $tasaZona = CatTasaImpuestoPorSuperficieUrbano::where('ANIO', $año)
            ->where('id_zona_urbana', $urbano->id_zona_urbana)
            ->first();
        $factorSuperficie = $tasaZona?->tasa ?? 0;

        $superficie = (float) ($urbano->superficie_terreno_metros_cuadrados ?? 0);
        $valorTerreno = $superficie * $factorSuperficie * $valorUma;

        $recargoBaldio = 0;
        if ($urbano->Baldio) {
            $tasaBaldio = CatTasaXBaldioUrbano::where('ANIO', $año)
                ->where('id_zona_urbana', $urbano->id_zona_urbana)
                ->first();
            $recargoBaldio = ($tasaBaldio?->tasa ?? 0) * $valorUma;
        }

        $construccion = (float) ($predio->construccion ?? 0);
        $valorConstruido = 0;
        if ($construccion > 0) {
            $tasaConstruccion = CatTasaXConstruccion::where('año', $año)
                ->where('activo', 1)
                ->first();
            $factorConstruccion = $tasaConstruccion?->factor ?? 0;
            $valorConstruido = $construccion * $factorConstruccion * $valorUma;
        }

        $factorRequerimiento = CatFactoresRequerimiento::where('año', $año)
            ->where('activo', 1)
            ->first();
        $minimoUma = $factorRequerimiento?->uma_minimo ?? $minimoUrbano;

        $total = $valorTerreno + $recargoBaldio + $valorConstruido;

        $minimoTotal = $minimoUma * $valorUma;
        if ($total < $minimoTotal) {
            $total = $minimoTotal;
        }

        $contribuyente = $predio->contribuyente;
        $nombreContribuyente = $contribuyente
            ? (trim((string) $contribuyente->nombre_completo) ?: trim(
                ($contribuyente->nombre ?? '') . ' ' .
                ($contribuyente->primer_apellido ?? '') . ' ' .
                ($contribuyente->segundo_apellido ?? '')
            ))
            : '';

        $zonaDesc = $urbano->zonaUrbana?->descripcion ?? '';
        $tipoPredioDesc = $predio->tipoPredio?->Tipo_predio ?? '';

        $ubicacion = $predio->ubicacion ?? '';
        if ($predio->calle) {
            $ubicacion = $predio->calle->CALLE . ($predio->Numero_exterior ? ' #' . $predio->Numero_exterior : '');
        }

        $calculo = PredioCalculoGeneral::create([
            'id_tb_predio_calculo_general' => (string) Str::uuid(),
            'id_predio' => $predio->id_predio,
            'año' => $año,
            'id_contribuyente' => $contribuyente?->id_contribuyente,
            'cuenta' => $contribuyente?->cuenta,
            'contribuyente' => $nombreContribuyente,
            'Clave_predial' => $predio->Clave_predial,
            'Tipo_predio' => $tipoPredioDesc,
            'Zona' => $zonaDesc,
            'Ubicacion' => $ubicacion,
            'superficie_solar' => $superficie,
            'superficie_agostadero' => null,
            'superficie_temporal' => null,
            'superficie_riego' => null,
            'superficie_urbano' => $superficie,
            'Superficie_texto' => number_format($superficie, 2) . ' m²',
            'Superficie_construccion_texto' => $construccion > 0 ? number_format($construccion, 2) . ' m²' : null,
            'Total' => round($total, 2),
            'id_zona_predio' => $urbano->id_zona_urbana,
            'id_tipo_predio' => $predio->id_tipo_predio,
            'valor_uma' => $valorUma,
            'factor_superficie' => $factorSuperficie,
        ]);

        return $calculo;
    }
}
