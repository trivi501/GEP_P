<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Contribuyente;
use App\Models\TipoContribuyente;
use App\Models\Domicilio;
use App\Models\CatPais;
use App\Models\CatEstado;
use App\Models\CatMunicipio;
use App\Models\CatColonia;
use App\Models\RegimenFiscal;
use App\Models\DatosFacturacionContribuyente;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContribuyenteController extends Controller
{
    public function search(Request $request)
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

    public function index(Request $request)
    {
        $filters = $request->only(['nombre_completo', 'cuenta', 'tipo', 'telefono', 'correo_electronico', 'activo']);

        $contribuyentes = Contribuyente::with('tipoContribuyente', 'domicilio')
            ->select(['id_contribuyente', 'nombre_completo', 'cuenta', 'id_tipo_contribuyente', 'telefono', 'correo_electronico', 'activo'])
            ->when($request->filled('nombre_completo'), fn($q) => $q->where('nombre_completo', 'like', '%' . $request->nombre_completo . '%'))
            ->when($request->filled('cuenta'), fn($q) => $q->where('cuenta', 'like', '%' . $request->cuenta . '%'))
            ->when($request->filled('tipo'), fn($q) => $q->whereHas('tipoContribuyente', fn($q) => $q->where('area_contribuyente', 'like', '%' . $request->tipo . '%')))
            ->when($request->filled('telefono'), fn($q) => $q->where('telefono', 'like', '%' . $request->telefono . '%'))
            ->when($request->filled('correo_electronico'), fn($q) => $q->where('correo_electronico', 'like', '%' . $request->correo_electronico . '%'))
            ->when($request->filled('activo'), fn($q) => $q->where('activo', $request->activo))
            ->orderBy('nombre_completo')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Contribuyentes/Index', compact('contribuyentes', 'filters'));
    }

    public function create()
    {
        $tiposContribuyente = TipoContribuyente::where('activo', 1)->orderBy('area_contribuyente')->get()->map(fn($t) => ['id' => $t->id_tipo_contribuyente, 'name' => $t->area_contribuyente]);
        $paises = CatPais::where('activo', 1)->orderBy('nombre_pais')->get()->map(fn($p) => ['id' => $p->id_pais, 'name' => $p->nombre_pais]);
        $estados = CatEstado::where('activo', 1)->orderBy('nombre_estado')->get()->map(fn($e) => ['id' => $e->id_estado, 'name' => $e->nombre_estado]);
        $municipios = CatMunicipio::where('activo', 1)->orderBy('nombre_municipio')->get()->map(fn($m) => ['id' => $m->id_municipio, 'name' => $m->nombre_municipio]);
        $colonias = CatColonia::where('Activo', 1)->orderBy('COLONIA')->get()->map(fn($c) => ['id' => $c->id_colonia, 'name' => $c->COLONIA]);
        $regimenesFiscales = RegimenFiscal::where('activo', 1)->orderBy('Descripción')->get()->map(fn($r) => ['id' => $r->id, 'name' => $r->Descripción]);

        return Inertia::render('Contribuyentes/Create', compact('tiposContribuyente', 'paises', 'estados', 'municipios', 'colonias', 'regimenesFiscales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'nullable|string|max:200',
            'primer_apellido' => 'nullable|string|max:150',
            'segundo_apellido' => 'nullable|string|max:150',
            'curp_contribuyente' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:13',
            'correo_electronico' => 'nullable|email|max:100',
            'id_tipo_contribuyente' => 'required|exists:cat_tipo_contribuyente,id_tipo_contribuyente',
            'rfc' => 'nullable|string|max:15',
            'id_tipo_persona' => 'nullable|integer',
            'nombre_moral' => 'nullable|string|max:300',
            'cuenta' => 'nullable|string|max:36|unique:tb_contribuyentes,cuenta',
            'exento' => 'nullable|boolean',
            'nombre_completo' => 'nullable|string|max:500',
            'nivel_gobierno' => 'nullable|string|max:50',
            'id_pais' => 'nullable|exists:cat_pais,id_pais',
            'id_estado' => 'nullable|exists:cat_estado,id_estado',
            'id_municipio' => 'nullable|exists:cat_municipio,id_municipio',
            'colonia' => 'nullable|string|max:200',
            'nombre_vialidad' => 'nullable|string|max:200',
            'num_interior' => 'nullable|string|max:10',
            'num_exterior' => 'nullable|string|max:10',
            'codigo_postal' => 'nullable|string|max:7',
            'fact_id_regimen_fiscal' => 'nullable|exists:f4_c_regimenfiscal,id',
            'activo' => 'nullable|boolean',
        ]);

        $validated['activo'] = $validated['activo'] ?? 0;
        $validated['exento'] = $validated['exento'] ?? 0;
        $validated['id_contribuyente'] = (string) Str::uuid();
        $validated['cuenta'] = $validated['cuenta'] ?? $validated['id_contribuyente'];
        $parts = array_filter([$validated['primer_apellido'] ?? '', $validated['segundo_apellido'] ?? '', $validated['nombre'] ?? '']);
        $validated['nombre_completo'] = $validated['nombre_completo'] ?? trim(implode(' ', $parts));
        $validated['fecha_alta'] = now();
        $validated['id_user_registra'] = auth()->id();

        if ($request->filled('id_pais') || $request->filled('colonia') || $request->filled('nombre_vialidad')) {
            $domicilioId = (string) Str::uuid();
            Domicilio::create([
                'id_domicilio' => $domicilioId,
                'id_pais' => $validated['id_pais'] ?? 1,
                'id_estado' => $validated['id_estado'] ?? 1,
                'id_municipio' => $validated['id_municipio'] ?? 1,
                'colonia' => $validated['colonia'] ?? null,
                'nombre_vialidad' => $validated['nombre_vialidad'] ?? null,
                'num_interior' => $validated['num_interior'] ?? null,
                'num_exterior' => $validated['num_exterior'] ?? null,
                'codigo_postal' => $validated['codigo_postal'] ?? null,
                'activo' => 1,
            ]);
            $validated['id_domicilio'] = $domicilioId;
        }

        $contribuyente = Contribuyente::create($validated);

        if ($request->filled('fact_id_regimen_fiscal')) {
            DatosFacturacionContribuyente::create([
                'id_datos_facturacion' => (string) Str::uuid(),
                'id_contribuyente' => $contribuyente->id_contribuyente,
                'id_f4_c_regimenfiscal' => $validated['fact_id_regimen_fiscal'],
                'fecha_alta' => now(),
            ]);
        }

        return redirect()->route('contribuyentes.index')
            ->with('success', 'Contribuyente creado exitosamente.');
    }

    public function show(Contribuyente $contribuyente)
    {
        $contribuyente->load('tipoContribuyente', 'domicilio.pais', 'domicilio.estado', 'domicilio.municipio', 'datosFacturacion', 'predios');

        return Inertia::render('Contribuyentes/Show', compact('contribuyente'));
    }

    public function edit(Contribuyente $contribuyente)
    {
        $contribuyente->load('domicilio', 'datosFacturacion');

        $tiposContribuyente = TipoContribuyente::where('activo', 1)->orderBy('area_contribuyente')->get()->map(fn($t) => ['id' => $t->id_tipo_contribuyente, 'name' => $t->area_contribuyente]);
        $paises = CatPais::where('activo', 1)->orderBy('nombre_pais')->get()->map(fn($p) => ['id' => $p->id_pais, 'name' => $p->nombre_pais]);
        $estados = CatEstado::where('activo', 1)->orderBy('nombre_estado')->get()->map(fn($e) => ['id' => $e->id_estado, 'name' => $e->nombre_estado]);
        $municipios = CatMunicipio::where('activo', 1)->orderBy('nombre_municipio')->get()->map(fn($m) => ['id' => $m->id_municipio, 'name' => $m->nombre_municipio]);
        $colonias = CatColonia::where('Activo', 1)->orderBy('COLONIA')->get()->map(fn($c) => ['id' => $c->id_colonia, 'name' => $c->COLONIA]);
        $regimenesFiscales = RegimenFiscal::where('activo', 1)->orderBy('Descripción')->get()->map(fn($r) => ['id' => $r->id, 'name' => $r->Descripción]);

        return Inertia::render('Contribuyentes/Edit', compact('contribuyente', 'tiposContribuyente', 'paises', 'estados', 'municipios', 'colonias', 'regimenesFiscales'));
    }

    public function update(Request $request, Contribuyente $contribuyente)
    {
        $validated = $request->validate([
            'nombre' => 'nullable|string|max:200',
            'primer_apellido' => 'nullable|string|max:150',
            'segundo_apellido' => 'nullable|string|max:150',
            'curp_contribuyente' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:13',
            'correo_electronico' => 'nullable|email|max:100',
            'id_tipo_contribuyente' => 'required|exists:cat_tipo_contribuyente,id_tipo_contribuyente',
            'rfc' => 'nullable|string|max:15',
            'id_tipo_persona' => 'nullable|integer',
            'nombre_moral' => 'nullable|string|max:300',
            'cuenta' => 'required|string|max:36|unique:tb_contribuyentes,cuenta,' . $contribuyente->id_contribuyente . ',id_contribuyente',
            'exento' => 'nullable|boolean',
            'nombre_completo' => 'nullable|string|max:500',
            'nivel_gobierno' => 'nullable|string|max:50',
            'id_cat_persona_genero' => 'nullable|integer',
            'id_pais' => 'nullable|exists:cat_pais,id_pais',
            'id_estado' => 'nullable|exists:cat_estado,id_estado',
            'id_municipio' => 'nullable|exists:cat_municipio,id_municipio',
            'colonia' => 'nullable|string|max:200',
            'nombre_vialidad' => 'nullable|string|max:200',
            'num_interior' => 'nullable|string|max:10',
            'num_exterior' => 'nullable|string|max:10',
            'codigo_postal' => 'nullable|string|max:7',
            'domicilio_completo' => 'nullable|string|max:1500',
            'fact_rfc' => 'nullable|string|max:15',
            'fact_razon_social' => 'nullable|string|max:500',
            'fact_correo' => 'nullable|email|max:250',
            'fact_cp_domicilio_fiscal' => 'nullable|string|max:10',
            'fact_id_regimen_fiscal' => 'nullable|exists:f4_c_regimenfiscal,id',
            'activo' => 'nullable|boolean',
        ]);

        $validated['activo'] = $validated['activo'] ?? 0;
        $validated['exento'] = $validated['exento'] ?? 0;
        $parts = array_filter([$validated['primer_apellido'] ?? '', $validated['segundo_apellido'] ?? '', $validated['nombre'] ?? '']);
        $validated['nombre_completo'] = trim(implode(' ', $parts));

        $contribuyente->update($validated);

        if ($request->filled('id_pais') || $request->filled('colonia') || $request->filled('nombre_vialidad')) {
            if ($contribuyente->domicilio) {
                $contribuyente->domicilio->update([
                    'id_pais' => $validated['id_pais'] ?? 1,
                    'id_estado' => $validated['id_estado'] ?? 1,
                    'id_municipio' => $validated['id_municipio'] ?? 1,
                    'colonia' => $validated['colonia'] ?? null,
                    'nombre_vialidad' => $validated['nombre_vialidad'] ?? null,
                    'num_interior' => $validated['num_interior'] ?? null,
                    'num_exterior' => $validated['num_exterior'] ?? null,
                    'codigo_postal' => $validated['codigo_postal'] ?? null,
                    'domicilio_completo' => $validated['domicilio_completo'] ?? null,
                ]);
            } else {
                $domicilioId = (string) Str::uuid();
                Domicilio::create([
                    'id_domicilio' => $domicilioId,
                    'id_pais' => $validated['id_pais'] ?? 1,
                    'id_estado' => $validated['id_estado'] ?? 1,
                    'id_municipio' => $validated['id_municipio'] ?? 1,
                    'colonia' => $validated['colonia'] ?? null,
                    'nombre_vialidad' => $validated['nombre_vialidad'] ?? null,
                    'num_interior' => $validated['num_interior'] ?? null,
                    'num_exterior' => $validated['num_exterior'] ?? null,
                    'codigo_postal' => $validated['codigo_postal'] ?? null,
                    'domicilio_completo' => $validated['domicilio_completo'] ?? null,
                    'activo' => 1,
                ]);
                $contribuyente->update(['id_domicilio' => $domicilioId]);
            }
        }

        if ($request->filled('fact_rfc') || $request->filled('fact_razon_social')) {
            $facturacion = $contribuyente->datosFacturacion->first();
            if ($facturacion) {
                $facturacion->update([
                    'rfc_facturacion' => $validated['fact_rfc'] ?? null,
                    'razon_social' => $validated['fact_razon_social'] ?? null,
                    'correo' => $validated['fact_correo'] ?? null,
                    'CP_DomicilioFiscal_contribuyente' => $validated['fact_cp_domicilio_fiscal'] ?? null,
                    'id_f4_c_regimenfiscal' => $validated['fact_id_regimen_fiscal'] ?? null,
                ]);
            } else {
                DatosFacturacionContribuyente::create([
                    'id_datos_facturacion' => (string) Str::uuid(),
                    'id_contribuyente' => $contribuyente->id_contribuyente,
                    'rfc_facturacion' => $validated['fact_rfc'] ?? null,
                    'razon_social' => $validated['fact_razon_social'] ?? null,
                    'correo' => $validated['fact_correo'] ?? null,
                    'CP_DomicilioFiscal_contribuyente' => $validated['fact_cp_domicilio_fiscal'] ?? null,
                    'id_f4_c_regimenfiscal' => $validated['fact_id_regimen_fiscal'] ?? null,
                    'id_domicilio_facturacion' => $contribuyente->id_domicilio,
                    'fecha_alta' => now(),
                ]);
            }
        }

        return redirect()->route('contribuyentes.index')
            ->with('success', 'Contribuyente actualizado exitosamente.');
    }

    public function destroy(Contribuyente $contribuyente)
    {
        $contribuyente->update(['activo' => 0]);

        return redirect()->route('contribuyentes.index')
            ->with('success', 'Contribuyente desactivado exitosamente.');
    }
}
