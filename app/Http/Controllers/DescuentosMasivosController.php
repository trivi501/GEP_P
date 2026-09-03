<?php

namespace App\Http\Controllers;

use App\Models\Contribuyente;
use App\Models\Descuento;
use App\Models\Predio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DescuentosMasivosController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:crear-descuentos');
    }

    public function index()
    {
        return Inertia::render('DescuentosMasivos/Index');
    }

    public function search(Request $request)
    {
        $request->validate(['cuenta' => 'required|string']);

        $contribuyentes = Contribuyente::where('cuenta', 'like', '%' . $request->cuenta . '%')
            ->where('activo', 1)
            ->get();

        $predios = Predio::with(['contribuyente', 'tipoPredio', 'colonia'])
            ->whereIn('id_contribuyente', $contribuyentes->pluck('id_contribuyente'))
            ->where('Clave_predial', 'not like', 'B%')
            ->where(function ($q) {
                $q->whereNull('año_ultimo_pago')->orWhere('año_ultimo_pago', '<>', now()->year);
            })
            ->orderBy('Clave_predial')
            ->get();

        $conDescuentoActivo = Descuento::where('activo', true)
            ->whereIn('idPredio', $predios->pluck('id_predio'))
            ->pluck('idPredio')
            ->all();

        return response()->json([
            'contribuyentes' => $contribuyentes,
            'predios' => $predios->map(fn ($p) => [
                'id' => $p->id_predio,
                'Clave_predial' => $p->Clave_predial,
                'cuenta' => $p->contribuyente?->cuenta ?? '—',
                'contribuyente' => $p->contribuyente?->nombre_completo ?? $p->contribuyente?->nombre_moral ?? '—',
                'colonia' => $p->colonia?->COLONIA ?? '—',
                'tipo_predio' => $p->tipoPredio?->Tipo_predio ?? '—',
                'año_ultimo_pago' => $p->año_ultimo_pago ?? '—',
                'tiene_descuento_activo' => in_array($p->id_predio, $conDescuentoActivo),
            ])->values(),
        ]);
    }

    public function aplicar(Request $request)
    {
        $validated = $request->validate([
            'predios' => 'required|array|min:1',
            'predios.*' => 'string',
            'multas' => 'required|numeric|min:0|max:100',
            'actualizaciones' => 'required|numeric|min:0|max:100',
            'recargos' => 'required|numeric|min:0|max:100',
            'aseo_publico' => 'required|numeric|min:0|max:100',
            'gastos_cobranza' => 'required|numeric|min:0|max:100',
            'fecha_expiracion' => 'nullable|date',
            'activo' => 'boolean',
        ]);

        $userId = auth()->id();
        $activo = $validated['activo'] ?? true;

        DB::transaction(function () use ($validated, $userId, $activo) {
            Descuento::whereIn('idPredio', $validated['predios'])
                ->where('activo', true)
                ->update(['activo' => false]);

            $now = now();
            $rows = array_map(fn ($idPredio) => [
                'idPredio' => $idPredio,
                'idUser' => $userId,
                'multas' => $validated['multas'],
                'actualizaciones' => $validated['actualizaciones'],
                'recargos' => $validated['recargos'],
                'aseo_publico' => $validated['aseo_publico'],
                'gastos_cobranza' => $validated['gastos_cobranza'],
                'fecha_expiracion' => $validated['fecha_expiracion'] ?? null,
                'activo' => $activo,
                'created_at' => $now,
                'updated_at' => $now,
            ], $validated['predios']);

            Descuento::insert($rows);
        });

        return response()->json([
            'success' => true,
            'aplicados' => count($validated['predios']),
        ]);
    }
}
