<?php

namespace App\Http\Controllers;

use App\Models\Predio;
use App\Models\Contribuyente;
use App\Models\TipoPredio;
use App\Models\RegimenPropiedad;
use App\Models\EstadoRenta;
use App\Models\EstadoImpuesto;
use App\Models\TituloPropiedad;
use App\Models\Calle;
use App\Models\CatColonia;
use App\Models\CatOrientacion;
use App\Models\ClavePredial;
use App\Models\MedidasYColindancias;
use App\Models\HistoricoPredio;
use App\Models\ObservacionesPredio;
use App\Models\DatosPredioUrbano;
use App\Models\CatZonaPredio;
use App\Models\CatFormaPredio;
use App\Models\CatUsoPredioUrbano;
use App\Models\CatEstadoFisicoPredio;
use App\Models\CatPavimento;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PredioController extends Controller
{
    public function searchClavePredial(Request $request)
    {
        $q = $request->get('q', '');
        $claves = ClavePredial::where('clave_predial_completa', 'like', '%' . $q . '%')
            ->orWhere('id_clave_predial', 'like', '%' . $q . '%')
            ->orderBy('clave_predial_completa')
            ->limit(20)
            ->get();

        return response()->json($claves);
    }

    public function searchCalle(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }
        $calles = Calle::where('Activo', 1)
            ->where(function ($query) use ($q) {
                $query->where('CALLE', 'like', $q . '%')
                      ->orWhere('CALLE', 'like', '%' . $q . '%');
            })
            ->orderBy('CALLE')
            ->limit(20)
            ->get(['id_calle', 'CALLE']);

        return response()->json($calles);
    }

    public function searchColonia(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }
        $colonias = CatColonia::where('Activo', 1)
            ->where(function ($query) use ($q) {
                $query->where('COLONIA', 'like', $q . '%')
                      ->orWhere('COLONIA', 'like', '%' . $q . '%');
            })
            ->orderBy('COLONIA')
            ->limit(20)
            ->get(['id_colonia', 'COLONIA']);

        return response()->json($colonias);
    }

    public function index(Request $request)
    {
        $query = Predio::with('contribuyente', 'tipoPredio', 'colonia', 'estadoImpuesto', 'calle', 'datosUrbano')
            ->select(['id_predio', 'Clave_predial', 'id_contribuyente', 'id_tipo_predio', 'id_colonia', 'id_calle', 'ubicacion', 'codigo_postal', 'Numero_exterior', 'Numero_interior', 'superficie', 'construccion', 'valor_catastral', 'año_ultimo_pago', 'ultimo_bimestre_pago', 'id_estaus_cobro_predial'])
            ->when($request->filled('Clave_predial'), fn($q) => $q->where('Clave_predial', 'like', '%' . $request->Clave_predial . '%'))
            ->when($request->filled('ubicacion'), fn($q) => $q->where('ubicacion', 'like', '%' . $request->ubicacion . '%'))
            ->when($request->filled('contribuyente'), fn($q) => $q->whereHas('contribuyente', fn($q) => $q->where('nombre_completo', 'like', '%' . $request->contribuyente . '%')))
            ->when($request->filled('cuenta'), fn($q) => $q->whereHas('contribuyente', fn($q) => $q->where('cuenta', 'like', '%' . $request->cuenta . '%')))
            ->when($request->filled('tipo_predio'), fn($q) => $q->whereHas('tipoPredio', fn($q) => $q->where('Tipo_predio', 'like', '%' . $request->tipo_predio . '%')))
            ->when($request->filled('estado'), fn($q) => $q->whereHas('estadoImpuesto', fn($q) => $q->where('DESCRIPCION', 'like', '%' . $request->estado . '%')))
            ->when($request->filled('colonia'), fn($q) => $q->whereHas('colonia', fn($q) => $q->where('COLONIA', 'like', '%' . $request->colonia . '%')))
            ->when($request->filled('search_global'), fn($q) => $q->where(function($sub) use ($request) {
                $s = $request->search_global;
                $sub->where('Clave_predial', 'like', "%{$s}%")
                    ->orWhere('ubicacion', 'like', "%{$s}%")
                    ->orWhere('año_ultimo_pago', 'like', "%{$s}%")
                    ->orWhere('ultimo_bimestre_pago', 'like', "%{$s}%")
                    ->orWhereRaw("CAST(superficie AS CHAR) LIKE ?", ["%{$s}%"])
                    ->orWhereHas('contribuyente', fn($q) => $q->where('nombre_completo', 'like', "%{$s}%")->orWhere('cuenta', 'like', "%{$s}%"))
                    ->orWhereHas('colonia', fn($q) => $q->where('COLONIA', 'like', "%{$s}%"))
                    ->orWhereHas('tipoPredio', fn($q) => $q->where('Tipo_predio', 'like', "%{$s}%"))
                    ->orWhereHas('estadoImpuesto', fn($q) => $q->where('DESCRIPCION', 'like', "%{$s}%"));
            }));

        $sortColumn = $request->get('sort_column', 'Clave_predial');
        $sortDirection = strtolower($request->get('sort_direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $directColumns = ['Clave_predial', 'ubicacion', 'año_ultimo_pago', 'ultimo_bimestre_pago', 'superficie'];

        if (in_array($sortColumn, $directColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $subQueryMap = [
                'contribuyente' => [Contribuyente::class, 'nombre_completo', 'id_contribuyente'],
                'cuenta' => [Contribuyente::class, 'cuenta', 'id_contribuyente'],
                'colonia' => [CatColonia::class, 'COLONIA', 'id_colonia'],
                'tipo_predio' => [TipoPredio::class, 'Tipo_predio', 'id_tipo_predio'],
                'estado' => [EstadoImpuesto::class, 'DESCRIPCION', 'id_estaus_cobro_predial'],
            ];

            if (isset($subQueryMap[$sortColumn])) {
                [$model, $column, $localKey] = $subQueryMap[$sortColumn];
                $query->orderBy(
                    $model::select($column)->whereColumn($localKey, 'tb_predio.' . $localKey),
                    $sortDirection
                );
            } else {
                $query->orderBy('Clave_predial', 'asc');
            }
        }

        if ($request->wantsJson()) {
            $predios = $query->paginate(10);
            $data = $predios->map(fn($p) => [
                'id' => $p->id_predio,
                'Clave_predial' => $p->Clave_predial,
                'cuenta' => $p->contribuyente?->cuenta ?? '—',
                'contribuyente' => $p->contribuyente?->nombre_completo ?? '—',
                'colonia' => $p->colonia?->COLONIA ?? '—',
                'tipo_predio' => $p->tipoPredio?->Tipo_predio ?? '—',
                'estado' => $p->estadoImpuesto?->DESCRIPCION ?? '—',
                'ubicacion' => $p->ubicacion ?? '—',
                'año_ultimo_pago' => $p->año_ultimo_pago ?? '—',
                'ultimo_bimestre_pago' => $p->ultimo_bimestre_pago ?? '—',
                'superficie' => (float) $p->superficie,
                'ubicacionPredio' => trim(($p->calle?->nombre ?? '') . ' #' . ($p->Numero_exterior ?? '') . ($p->Numero_interior ? ' Int ' . $p->Numero_interior : '') . ', ' . ($p->colonia?->COLONIA ?? '')),
                'terreno' => (float) ($p->datosUrbano?->valor_catastral_terreno ?? $p->valor_catastral ?? 0),
                'construccion' => (float) ($p->construccion ?? 0),
            ]);
            return response()->json([
                'data' => $data,
                'total' => $predios->total(),
                'current_page' => $predios->currentPage(),
                'last_page' => $predios->lastPage(),
            ]);
        }

        $predios = $query->paginate(10)->withQueryString();
        $prediosData = $predios->map(fn($p) => [
            'id' => $p->id_predio,
            'Clave_predial' => $p->Clave_predial,
            'cuenta' => $p->contribuyente?->cuenta ?? '—',
            'contribuyente' => $p->contribuyente?->nombre_completo ?? '—',
            'colonia' => $p->colonia?->COLONIA ?? '—',
            'tipo_predio' => $p->tipoPredio?->Tipo_predio ?? '—',
            'estado' => $p->estadoImpuesto?->DESCRIPCION ?? '—',
            'ubicacion' => $p->ubicacion ?? '—',
            'año_ultimo_pago' => $p->año_ultimo_pago ?? '—',
            'ultimo_bimestre_pago' => $p->ultimo_bimestre_pago ?? '—',
            'superficie' => (float) $p->superficie,
            'ubicacionPredio' => trim(($p->calle?->nombre ?? '') . ' #' . ($p->Numero_exterior ?? '') . ($p->Numero_interior ? ' Int ' . $p->Numero_interior : '') . ', ' . ($p->colonia?->COLONIA ?? '')),
            'terreno' => (float) ($p->datosUrbano?->valor_catastral_terreno ?? $p->valor_catastral ?? 0),
            'construccion' => (float) ($p->construccion ?? 0),
        ])->values();

        return Inertia::render('Predios/Index', [
            'predios' => $predios,
            'prediosData' => $prediosData,
            'search_global' => $request->search_global ?? '',
        ]);
    }

    public function create()
    {
        $tiposPredio = TipoPredio::where('activo', 1)->orderBy('Tipo_predio')->get();
        $regimenesPropiedad = RegimenPropiedad::where('activo', 1)->orderBy('REGIMEN')->get();
        $estadosRenta = EstadoRenta::where('activo', 1)->orderBy('DESCRIPCION')->get();
        $estadosImpuesto = EstadoImpuesto::where('activo', 1)->orderBy('DESCRIPCION')->get();
        $titulosPropiedad = TituloPropiedad::where('activo', 1)->orderBy('DESCRIPCION')->get();
        $colonias = CatColonia::where('Activo', 1)->orderBy('COLONIA')->get();
        $calles = Calle::where('Activo', 1)->orderBy('CALLE')->get();
        $orientaciones = CatOrientacion::where('activo', 1)->orderBy('id_orientacion')->get();
        $zonasUrbana = CatZonaPredio::where('activo', 1)->orderBy('descripcion')->get();
        $formasPredio = CatFormaPredio::where('activo', 1)->orderBy('descripcion')->get();
        $usosPredioUrbano = CatUsoPredioUrbano::where('activo', 1)->orderBy('descripcion')->get();
        $estadosFisicos = CatEstadoFisicoPredio::where('activo', 1)->orderBy('DESCRIPCION')->get();
        $pavimentos = CatPavimento::where('activo', 1)->orderBy('DESCRIPCION')->get();
        return Inertia::render('Predios/Create', compact('tiposPredio', 'regimenesPropiedad', 'estadosRenta', 'estadosImpuesto', 'titulosPropiedad', 'colonias', 'calles', 'orientaciones', 'zonasUrbana', 'formasPredio', 'usosPredioUrbano', 'estadosFisicos', 'pavimentos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Clave_predial' => 'required|string|max:25|unique:tb_predio,Clave_predial',
            'id_contribuyente' => 'required|exists:tb_contribuyentes,id_contribuyente',
            'id_tipo_predio' => 'required|exists:cat_tipo_predio,id_tipo_predio',
            'id_colonia' => 'nullable|exists:cat_colonia,id_colonia',
            'id_calle' => 'nullable|exists:cat_calle,id_calle',
            'ubicacion' => 'nullable|string|max:65',
            'codigo_postal' => 'nullable|string|max:10',
            'Numero_exterior' => 'nullable|string|max:15',
            'Numero_interior' => 'nullable|string|max:15',
            'Referencia_entre_calle1' => 'nullable|string|max:250',
            'Referncia_entre_calle2' => 'nullable|string|max:250',
            'id_regimen_propiedad' => 'nullable|exists:cat_regimen_propiedad,id_regimen_propiedad',
            'id_estado_renta' => 'nullable|exists:cat_estado_renta,id_estado_renta',
            'id_estaus_cobro_predial' => 'nullable|exists:cat_estado_impuesto,id_estaus_cobro_predial',
            'id_titulo_propiedad' => 'nullable|exists:cat_titulo_propiedad,id_titulo_propiedad',
            'id_clave_predial' => 'nullable|exists:tb_clave_predial,id_clave_predial',
            'numero_de_escritura' => 'nullable|string|max:50',
            'valor_catastral' => 'nullable|numeric',
            'valor_fiscal' => 'nullable|numeric',
            'superficie' => 'nullable|numeric',
            'construccion' => 'nullable|numeric',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'año_ultimo_pago' => 'nullable|integer|min:1900|max:2100',
            'observacion' => 'nullable|string|max:5000',
            'id_zona_urbana' => 'nullable|exists:cat_zona_predio,id_zona_urbana',
            'numero_de_pisos_construidos' => 'nullable|integer|min:0|max:255',
            'superficie_terreno_metros_cuadrados' => 'nullable|numeric',
            'Frente_metros' => 'nullable|numeric',
            'Fondo_metros' => 'nullable|numeric',
            'Baldio' => 'nullable|boolean',
            'id_forma_predio' => 'nullable|exists:cat_tipo_forma_predio,id_forma_predio',
            'id_uso_predio' => 'nullable|exists:cat_uso_predio_urbano,id_uso_predio',
            'id_estado_fisico' => 'nullable|exists:cat_estado_fisico_predio,id_estado_fisico',
            'servicio_agua' => 'nullable|boolean',
            'servicio_drenaje' => 'nullable|boolean',
            'servicio_energia_electrica' => 'nullable|boolean',
            'servicio_alumbrado' => 'nullable|boolean',
            'id_pavimientacion' => 'nullable|exists:cat_pavimento,id_pavimientacion',
            'cuenta_con_banqueta' => 'nullable|boolean',
            'valor_catastral_terreno' => 'nullable|numeric',
            'valor_catastral_construido' => 'nullable|numeric',
        ]);

        $validated['id_predio'] = (string) Str::uuid();
        $validated['fecha_de_alta'] = now();
        $validated['fecha_registro'] = now();
        $validated['id_usuario'] = auth()->user()->id ?? null;

        $urbanoFields = ['id_zona_urbana', 'numero_de_pisos_construidos', 'superficie_terreno_metros_cuadrados', 'Frente_metros', 'Fondo_metros', 'Baldio', 'id_forma_predio', 'id_uso_predio', 'id_estado_fisico', 'servicio_agua', 'servicio_drenaje', 'servicio_energia_electrica', 'servicio_alumbrado', 'id_pavimientacion', 'cuenta_con_banqueta', 'valor_catastral_terreno', 'valor_catastral_construido'];

        Predio::create($validated);

        $booleanos = ['Baldio', 'servicio_agua', 'servicio_drenaje', 'servicio_energia_electrica', 'servicio_alumbrado', 'cuenta_con_banqueta'];
        $urbanoData = ['id_predio' => $validated['id_predio']];
        foreach ($urbanoFields as $f) {
            if ($request->has($f) && !is_null($request->$f) && $request->$f !== '') {
                $urbanoData[$f] = in_array($f, $booleanos) ? $request->boolean($f) : $request->$f;
            }
        }
        if (count($urbanoData) > 1) {
            DatosPredioUrbano::create($urbanoData);
        }

        if (!empty($validated['observacion'])) {
            ObservacionesPredio::create([
                'id_observacion' => (string) Str::uuid(),
                'id_predio' => $validated['id_predio'],
                'observacion' => $validated['observacion'],
                'fecha_registro' => now(),
                'id_usuario' => auth()->user()->id ?? null,
            ]);
            HistoricoPredio::create([
                'id_historico' => (string) Str::uuid(),
                'id_predio' => $validated['id_predio'],
                'campo_modificado' => 'observacion',
                'valor_anterior' => null,
                'valor_nuevo' => $validated['observacion'],
                'id_usuario_modifica' => auth()->user()->id ?? null,
                'fecha_modificacion' => now(),
                'tipo_operacion' => 'CREATE',
            ]);
        }

        if ($request->has('medidas')) {
            foreach ($request->medidas as $medida) {
                if (!empty($medida['id_orientacion'])) {
                    MedidasYColindancias::create([
                        'id_medida_colindacion' => (string) Str::uuid(),
                        'id_predio' => $validated['id_predio'],
                        'id_orientacion' => $medida['id_orientacion'],
                        'medida_en_metros' => $medida['medida_en_metros'] ?? null,
                        'colinda_con' => $medida['colinda_con'] ?? null,
                        'fecha_alta' => now(),
                        'id_usuario' => auth()->user()->id ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('predios.show', $validated['id_predio'])
            ->with('success', 'Predio creado exitosamente.');
    }

    public function show(Predio $predio)
    {
        $predio->load('contribuyente.domicilio', 'contribuyente.tipoContribuyente', 'tipoPredio', 'regimenPropiedad', 'estadoRenta', 'estadoImpuesto', 'tituloPropiedad', 'calle', 'colonia', 'clavePredial', 'calculosGenerales', 'medidasYColindancias.orientacion', 'anotaciones', 'historico.usuarioModifica', 'observaciones', 'datosUrbano.zonaUrbana', 'datosUrbano.formaPredio', 'datosUrbano.usoPredio', 'datosUrbano.estadoFisico', 'datosUrbano.pavimento');
        return Inertia::render('Predios/Show', compact('predio'));
    }

    public function edit(Predio $predio)
    {
        $predio->load('clavePredial', 'medidasYColindancias', 'observaciones', 'datosUrbano');
        $tiposPredio = TipoPredio::where('activo', 1)->orderBy('Tipo_predio')->get();
        $regimenesPropiedad = RegimenPropiedad::where('activo', 1)->orderBy('REGIMEN')->get();
        $estadosRenta = EstadoRenta::where('activo', 1)->orderBy('DESCRIPCION')->get();
        $estadosImpuesto = EstadoImpuesto::where('activo', 1)->orderBy('DESCRIPCION')->get();
        $titulosPropiedad = TituloPropiedad::where('activo', 1)->orderBy('DESCRIPCION')->get();
        $colonias = CatColonia::where('Activo', 1)->orderBy('COLONIA')->get();
        $calles = Calle::where('Activo', 1)->orderBy('CALLE')->get();
        $orientaciones = CatOrientacion::where('activo', 1)->orderBy('id_orientacion')->get();
        $zonasUrbana = CatZonaPredio::where('activo', 1)->orderBy('descripcion')->get();
        $formasPredio = CatFormaPredio::where('activo', 1)->orderBy('descripcion')->get();
        $usosPredioUrbano = CatUsoPredioUrbano::where('activo', 1)->orderBy('descripcion')->get();
        $estadosFisicos = CatEstadoFisicoPredio::where('activo', 1)->orderBy('DESCRIPCION')->get();
        $pavimentos = CatPavimento::where('activo', 1)->orderBy('DESCRIPCION')->get();
        return Inertia::render('Predios/Edit', compact('predio', 'tiposPredio', 'regimenesPropiedad', 'estadosRenta', 'estadosImpuesto', 'titulosPropiedad', 'colonias', 'calles', 'orientaciones', 'zonasUrbana', 'formasPredio', 'usosPredioUrbano', 'estadosFisicos', 'pavimentos'));
    }

    public function update(Request $request, Predio $predio)
    {
        $validated = $request->validate([
            'Clave_predial' => 'required|string|max:25|unique:tb_predio,Clave_predial,' . $predio->id_predio . ',id_predio',
            'id_contribuyente' => 'required|exists:tb_contribuyentes,id_contribuyente',
            'id_tipo_predio' => 'required|exists:cat_tipo_predio,id_tipo_predio',
            'id_colonia' => 'nullable|exists:cat_colonia,id_colonia',
            'id_calle' => 'nullable|exists:cat_calle,id_calle',
            'ubicacion' => 'nullable|string|max:65',
            'codigo_postal' => 'nullable|string|max:10',
            'Numero_exterior' => 'nullable|string|max:15',
            'Numero_interior' => 'nullable|string|max:15',
            'Referencia_entre_calle1' => 'nullable|string|max:250',
            'Referncia_entre_calle2' => 'nullable|string|max:250',
            'id_regimen_propiedad' => 'nullable|exists:cat_regimen_propiedad,id_regimen_propiedad',
            'id_estado_renta' => 'nullable|exists:cat_estado_renta,id_estado_renta',
            'id_estaus_cobro_predial' => 'nullable|exists:cat_estado_impuesto,id_estaus_cobro_predial',
            'id_titulo_propiedad' => 'nullable|exists:cat_titulo_propiedad,id_titulo_propiedad',
            'id_clave_predial' => 'nullable|exists:tb_clave_predial,id_clave_predial',
            'numero_de_escritura' => 'nullable|string|max:50',
            'valor_catastral' => 'nullable|numeric',
            'valor_fiscal' => 'nullable|numeric',
            'superficie' => 'nullable|numeric',
            'construccion' => 'nullable|numeric',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'año_ultimo_pago' => 'nullable|integer|min:1900|max:2100',
            'observacion' => 'nullable|string|max:5000',
            'id_zona_urbana' => 'nullable|exists:cat_zona_predio,id_zona_urbana',
            'numero_de_pisos_construidos' => 'nullable|integer|min:0|max:255',
            'superficie_terreno_metros_cuadrados' => 'nullable|numeric',
            'Frente_metros' => 'nullable|numeric',
            'Fondo_metros' => 'nullable|numeric',
            'Baldio' => 'nullable|boolean',
            'id_forma_predio' => 'nullable|exists:cat_tipo_forma_predio,id_forma_predio',
            'id_uso_predio' => 'nullable|exists:cat_uso_predio_urbano,id_uso_predio',
            'id_estado_fisico' => 'nullable|exists:cat_estado_fisico_predio,id_estado_fisico',
            'servicio_agua' => 'nullable|boolean',
            'servicio_drenaje' => 'nullable|boolean',
            'servicio_energia_electrica' => 'nullable|boolean',
            'servicio_alumbrado' => 'nullable|boolean',
            'id_pavimientacion' => 'nullable|exists:cat_pavimento,id_pavimientacion',
            'cuenta_con_banqueta' => 'nullable|boolean',
            'valor_catastral_terreno' => 'nullable|numeric',
            'valor_catastral_construido' => 'nullable|numeric',
        ]);

        $old = $predio->toArray();
        $predio->update($validated);

        $urbanoFields = ['id_zona_urbana', 'numero_de_pisos_construidos', 'superficie_terreno_metros_cuadrados', 'Frente_metros', 'Fondo_metros', 'Baldio', 'id_forma_predio', 'id_uso_predio', 'id_estado_fisico', 'servicio_agua', 'servicio_drenaje', 'servicio_energia_electrica', 'servicio_alumbrado', 'id_pavimientacion', 'cuenta_con_banqueta', 'valor_catastral_terreno', 'valor_catastral_construido'];
        $booleanos = ['Baldio', 'servicio_agua', 'servicio_drenaje', 'servicio_energia_electrica', 'servicio_alumbrado', 'cuenta_con_banqueta'];
        $oldUrbano = $predio->datosUrbano;
        $urbanoData = [];
        foreach ($urbanoFields as $f) {
            if ($request->has($f) && !is_null($request->$f) && $request->$f !== '') {
                $urbanoData[$f] = in_array($f, $booleanos) ? $request->boolean($f) : $request->$f;
            }
        }
        if (!empty($urbanoData)) {
            if ($oldUrbano) {
                $oldUrbano->update($urbanoData);
            } else {
                $urbanoData['id_predio'] = $predio->id_predio;
                DatosPredioUrbano::create($urbanoData);
            }
            $this->compararUrbano($predio, $oldUrbano?->toArray() ?? [], $urbanoData);
        }

        if (!empty($validated['observacion'])) {
            $obsAnterior = $predio->observaciones()->latest('fecha_registro')->value('observacion');
            if ($obsAnterior !== $validated['observacion']) {
                ObservacionesPredio::create([
                    'id_observacion' => (string) Str::uuid(),
                    'id_predio' => $predio->id_predio,
                    'observacion' => $validated['observacion'],
                    'fecha_registro' => now(),
                    'id_usuario' => auth()->user()->id ?? null,
                ]);
                HistoricoPredio::create([
                    'id_historico' => (string) Str::uuid(),
                    'id_predio' => $predio->id_predio,
                    'campo_modificado' => 'observacion',
                    'valor_anterior' => $obsAnterior,
                    'valor_nuevo' => $validated['observacion'],
                    'id_usuario_modifica' => auth()->user()->id ?? null,
                    'fecha_modificacion' => now(),
                    'tipo_operacion' => 'UPDATE',
                ]);
            }
        }

        $medidasViejas = $predio->medidasYColindancias()->get()->toArray();
        $this->guardarHistorico($predio, $old, $validated);

        if ($request->has('medidas')) {
            $this->compararMedidas($predio, $medidasViejas, $request->medidas);
            $predio->medidasYColindancias()->delete();
            foreach ($request->medidas as $medida) {
                if (!empty($medida['id_orientacion'])) {
                    MedidasYColindancias::create([
                        'id_medida_colindacion' => (string) Str::uuid(),
                        'id_predio' => $predio->id_predio,
                        'id_orientacion' => $medida['id_orientacion'],
                        'medida_en_metros' => $medida['medida_en_metros'] ?? null,
                        'colinda_con' => $medida['colinda_con'] ?? null,
                        'fecha_alta' => now(),
                        'id_usuario' => auth()->user()->id ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('predios.show', $predio)
            ->with('success', 'Predio actualizado exitosamente.');
    }

    private function compararMedidas(Predio $predio, array $viejas, array $nuevas)
    {
        $nuevasFiltradas = array_filter($nuevas, fn($m) => !empty($m['id_orientacion']));
        $viejasStr = '';
        foreach ($viejas as $m) {
            $viejasStr .= ($m['id_orientacion'] ?? '?') . ': ' . ($m['medida_en_metros'] ?? 0) . 'm -> ' . ($m['colinda_con'] ?? '?') . "\n";
        }
        $nuevasStr = '';
        foreach ($nuevasFiltradas as $m) {
            $nuevasStr .= ($m['id_orientacion'] ?? '?') . ': ' . ($m['medida_en_metros'] ?? 0) . 'm -> ' . ($m['colinda_con'] ?? '?') . "\n";
        }

        if ($viejasStr !== $nuevasStr) {
            HistoricoPredio::create([
                'id_historico' => (string) Str::uuid(),
                'id_predio' => $predio->id_predio,
                'campo_modificado' => 'medidas_y_colindancias',
                'valor_anterior' => $viejasStr ?: null,
                'valor_nuevo' => $nuevasStr ?: null,
                'id_usuario_modifica' => auth()->user()->id ?? null,
                'fecha_modificacion' => now(),
                'tipo_operacion' => 'UPDATE',
            ]);
        }
    }

    private function compararUrbano(Predio $predio, array $old, array $new)
    {
        $tracked = [
            'id_zona_urbana', 'numero_de_pisos_construidos', 'superficie_terreno_metros_cuadrados',
            'Frente_metros', 'Fondo_metros', 'Baldio', 'id_forma_predio', 'id_uso_predio',
            'id_estado_fisico', 'servicio_agua', 'servicio_drenaje', 'servicio_energia_electrica',
            'servicio_alumbrado', 'id_pavimientacion', 'cuenta_con_banqueta',
            'valor_catastral_terreno', 'valor_catastral_construido',
        ];

        foreach ($tracked as $campo) {
            $oldVal = $old[$campo] ?? null;
            $newVal = $new[$campo] ?? null;

            if ($oldVal != $newVal) {
                HistoricoPredio::create([
                    'id_historico' => (string) Str::uuid(),
                    'id_predio' => $predio->id_predio,
                    'campo_modificado' => $campo,
                    'valor_anterior' => !is_null($oldVal) ? (string) $oldVal : null,
                    'valor_nuevo' => !is_null($newVal) ? (string) $newVal : null,
                    'id_usuario_modifica' => auth()->user()->id ?? null,
                    'fecha_modificacion' => now(),
                    'tipo_operacion' => 'UPDATE',
                ]);
            }
        }
    }

    private function guardarHistorico(Predio $predio, array $old, array $new)
    {
        $tracked = [
            'Clave_predial', 'id_contribuyente', 'id_tipo_predio', 'id_colonia',
            'id_calle', 'ubicacion', 'codigo_postal', 'Numero_exterior',
            'Numero_interior', 'Referencia_entre_calle1', 'Referncia_entre_calle2',
            'id_regimen_propiedad', 'id_estado_renta', 'id_estaus_cobro_predial',
            'id_titulo_propiedad', 'id_clave_predial', 'numero_de_escritura',
            'valor_catastral', 'valor_fiscal', 'superficie', 'construccion',
            'latitud', 'longitud',
            'año_ultimo_pago',
        ];

        foreach ($tracked as $campo) {
            $oldVal = $old[$campo] ?? null;
            $newVal = $new[$campo] ?? null;

            if ($oldVal != $newVal) {
                HistoricoPredio::create([
                    'id_historico' => (string) Str::uuid(),
                    'id_predio' => $predio->id_predio,
                    'campo_modificado' => $campo,
                    'valor_anterior' => !is_null($oldVal) ? (string) $oldVal : null,
                    'valor_nuevo' => !is_null($newVal) ? (string) $newVal : null,
                    'id_usuario_modifica' => auth()->user()->id ?? null,
                    'fecha_modificacion' => now(),
                    'tipo_operacion' => 'UPDATE',
                ]);
            }
        }
    }

    public function pdf(Predio $predio)
    {
        $predio->load('contribuyente.domicilio', 'contribuyente.tipoContribuyente', 'tipoPredio', 'regimenPropiedad', 'estadoRenta', 'estadoImpuesto', 'tituloPropiedad', 'calle', 'colonia', 'clavePredial', 'medidasYColindancias.orientacion', 'datosUrbano.zonaUrbana', 'datosUrbano.formaPredio', 'datosUrbano.usoPredio', 'datosUrbano.estadoFisico', 'datosUrbano.pavimento');

        $pdf = Pdf::loadView('predios.pdf', compact('predio'));
        $pdf->setPaper('letter');

        return $pdf->stream("predio_{$predio->Clave_predial}.pdf");
    }

    public function cedula(Predio $predio)
    {
        $predio->load('contribuyente', 'tipoPredio', 'calle', 'colonia', 'clavePredial', 'datosUrbano');

        $pdf = Pdf::loadView('predios.cedula', compact('predio'));
        $pdf->setPaper('letter');

        return $pdf->stream("cedula_{$predio->Clave_predial}.pdf");
    }

    public function destroy(Predio $predio)
    {
        $predio->delete();

        return redirect()->route('predios.index')
            ->with('success', 'Predio eliminado exitosamente.');
    }
}
