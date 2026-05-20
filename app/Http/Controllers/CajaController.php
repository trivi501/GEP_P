<?php

namespace App\Http\Controllers;

use App\Models\PagosMaster;
use App\Models\PagoFormaPago;
use App\Models\Predio;
use App\Models\Contribuyente;
use App\Models\FormaPago;
use App\Models\CatUma;
use App\Models\Inpc;
use App\Models\CatTipoConstruccion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CajaController extends Controller
{
    public function index()
    {
        $pagos = PagosMaster::with('contribuyente', 'formaPago')
            ->orderBy('fecha_registro', 'desc')
            ->paginate(20);

        return view('caja.index', compact('pagos'));
    }

    public function create()
    {
        $formasPago = FormaPago::where('activo', 1)->get();
        return view('caja.create', compact('formasPago'));
    }

    public function searchContribuyente(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $contribuyentes = Contribuyente::where('activo', 1)
            ->where(function ($query) use ($q) {
                $query->where('nombre_completo', 'like', $q . '%')
                      ->orWhere('nombre_completo', 'like', '%' . $q . '%')
                      ->orWhere('cuenta', 'like', $q . '%');
            })
            ->orderByRaw('CASE WHEN nombre_completo LIKE ? THEN 0 WHEN cuenta LIKE ? THEN 1 ELSE 2 END', [$q . '%', $q . '%'])
            ->orderBy('nombre_completo')
            ->limit(20)
            ->get(['id_contribuyente', 'nombre_completo', 'cuenta']);

        return response()->json($contribuyentes);
    }

    public function searchClaveCatastral(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $predios = Predio::with('contribuyente', 'tipoPredio', 'clavePredial')
            ->where('Clave_predial', 'like', $q . '%')
            ->orWhere('Clave_predial', 'like', '%' . $q . '%')
            ->limit(20)
            ->get();

        $results = $predios->map(function ($predio) {
            return [
                'id_predio' => $predio->id_predio,
                'clave_predial' => $predio->clavePredial?->clave_predial_completa ?? $predio->Clave_predial,
                'id_contribuyente' => $predio->id_contribuyente,
                'contribuyente' => $predio->contribuyente?->nombre_completo ?? '—',
                'cuenta' => $predio->contribuyente?->cuenta ?? '—',
                'tipo_predio' => $predio->tipoPredio?->Tipo_predio ?? '—',
            ];
        });

        return response()->json($results);
    }

    public function getContribuyente(Request $request)
    {
        $contribuyente = Contribuyente::with('predios.tipoPredio', 'predios.clavePredial')
            ->findOrFail($request->id);

        return response()->json($contribuyente);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_contribuyente' => 'required|exists:tb_contribuyentes,id_contribuyente',
            'sub_total_pago' => 'required|numeric|min:0',
            'total_descuento' => 'required|numeric|min:0',
            'total_pago' => 'required|numeric|min:0',
            'notas' => 'nullable|string|max:1000',
            'formas_pago' => 'nullable|array',
            'formas_pago.id_f4_c_formapago' => 'nullable|array',
            'formas_pago.id_f4_c_formapago.*' => 'nullable|integer|exists:f4_c_formapago,id',
            'formas_pago.monto' => 'nullable|array',
            'formas_pago.monto.*' => 'nullable|numeric|min:0',
        ]);

        $contribuyente = Contribuyente::findOrFail($validated['id_contribuyente']);

        $ultimoFolio = PagosMaster::where('anio_pago', now()->year)
            ->max('folio_pago') ?? 0;

        $formasPagoInput = $request->input('formas_pago', []);
        $metodos = [];
        $totalFormas = 0;
        if (isset($formasPagoInput['id_f4_c_formapago'])) {
            foreach ($formasPagoInput['id_f4_c_formapago'] as $i => $idForma) {
                if ($idForma && ($formasPagoInput['monto'][$i] ?? 0) > 0) {
                    $monto = (float) $formasPagoInput['monto'][$i];
                    $metodos[] = [
                        'id_f4_c_formapago' => (int) $idForma,
                        'monto' => $monto,
                    ];
                    $totalFormas += $monto;
                }
            }
        }

        $pago = PagosMaster::create([
            'id_pago_guid' => Str::uuid(),
            'folio_pago' => $ultimoFolio + 1,
            'anio_pago' => now()->year,
            'folio_recibo' => !empty($metodos) ? now()->year . '-' . ($ultimoFolio + 1) : null,
            'id_contribuyente' => $validated['id_contribuyente'],
            'id_forma_de_pago' => $metodos[0]['id_f4_c_formapago'] ?? null,
            'fecha_pago' => now(),
            'notas' => $validated['notas'],
            'sub_total_pago' => $validated['sub_total_pago'],
            'total_descuento' => $validated['total_descuento'],
            'total_pago' => $validated['total_pago'],
            'id_usuario_registra' => auth()->user()->id ?? auth()->user()->email,
            'fecha_registro' => now(),
            'contribuyente' => $contribuyente->nombre_completo,
        ]);

        // Save payment method details
        $nextId = (PagoFormaPago::max('id_tb_pago_forma_pago') ?? 0) + 1;
        foreach ($metodos as $metodo) {
            PagoFormaPago::create([
                'id_tb_pago_forma_pago' => $nextId++,
                'id_tb_pagos_master' => $pago->id_pago_guid,
                'id_f4_c_formapago' => $metodo['id_f4_c_formapago'],
                'monto' => $metodo['monto'],
                'vinculado' => 0,
            ]);
        }

        return redirect()->route('caja.index')
            ->with('success', "Pago registrado exitosamente. Folio: {$pago->folio_pago}");
    }

    public function calcularUrbano(Request $request)
    {
        $predio = Predio::with([
            'datosUrbano.zonaUrbana', 'nivelesConstruidos.tipoConstruccion', 'nivelesConstruidos.usoConstruccion',
        ])->findOrFail($request->id_predio);

        $calculos = app(CalculosPrediosController::class)->getCalculosAnuales($predio);

        return response()->json(['calculos' => $calculos]);
    }

    public function calcularRustico(Request $request)
    {
        $predio = Predio::with('tipoPredio', 'datosRustico')->findOrFail($request->id_predio);
        $uma = CatUma::where('anio', now()->year)->where('activo', 1)->first();
        $valorUma = $uma?->valor ?? 0;
        $hectareas = ($predio->superficie ?? 0) / 10000;
        $tipoPredio = $predio->tipoPredio?->Tipo_predio ?? '';
        $esMina = str_contains($tipoPredio, 'MINA');

        $anhoInicio = $predio->año_ultimo_pago ?? now()->year;
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
                $subtotal = $hectareas * (1.1390 * $valorUmaAnual);
            } elseif ($predio->datosRustico?->valor_catastral_superficie_temporal) {
                $subtotal = $hectareas < 20
                    ? (3 * $valorUmaAnual) + ($hectareas * 3)
                    : (2 * $valorUmaAnual) + ($hectareas * 6.40);
            } else {
                $subtotal = $hectareas * (0.8342 * $valorUmaAnual);
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

        return response()->json(['calculos' => $calculos]);
    }

    public function show($id)
    {
        $pago = PagosMaster::with('contribuyente', 'formaPago')
            ->findOrFail($id);

        return view('caja.show', compact('pago'));
    }
}
