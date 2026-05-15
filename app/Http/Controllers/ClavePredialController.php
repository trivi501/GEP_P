<?php

namespace App\Http\Controllers;

use App\Models\ClavePredial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClavePredialController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_poblacion' => 'nullable|integer',
            'id_seccion' => 'nullable|integer',
            'id_manzana' => 'nullable|integer',
            'id_lote' => 'nullable|integer',
            'subLote' => 'nullable|string|max:2',
            'Parcela' => 'nullable|string|max:6',
            'id_tipo_predio' => 'required|integer',
            'prefijo' => 'nullable|string|max:6',
            'clave_predial_completa' => 'required|string|max:18|unique:tb_clave_predial,clave_predial_completa',
            'manzana_rustico' => 'nullable|string|max:3',
            'lote_rustico' => 'nullable|string|max:2',
        ]);

        $validated['id_clave_predial'] = (string) Str::uuid();

        $clavePredial = ClavePredial::create($validated);

        return response()->json($clavePredial);
    }
}
