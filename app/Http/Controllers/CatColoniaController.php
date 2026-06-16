<?php

namespace App\Http\Controllers;

use App\Models\CatColonia;
use App\Models\CatPoblacion;
use App\Models\CatZonaPredio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatColoniaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:colonias-index')->only('index');
        $this->middleware('permission:colonias-create')->only(['create', 'store']);
        $this->middleware('permission:colonias-edit')->only(['edit', 'update']);
        $this->middleware('permission:colonias-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = CatColonia::when($request->filled('search'), fn($q) => $q->where('COLONIA', 'like', '%' . $request->search . '%'));
        $colonias = $query->orderBy('COLONIA')->paginate(15)->withQueryString();
        return Inertia::render('Colonias/Index', compact('colonias'));
    }

    public function create()
    {
        $poblaciones = CatPoblacion::where('Activo', 1)->orderBy('POBLACION')->get();
        $zonasPredio = CatZonaPredio::where('activo', 1)->orderBy('descripcion')->get();
        return Inertia::render('Colonias/Create', compact('poblaciones', 'zonasPredio'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'COLONIA' => 'required|string|max:120',
            'id_poblacion' => 'nullable|integer',
            'id_cat_zona_predio' => 'nullable|integer',
            'Activo' => 'nullable|boolean',
        ]);

        $ultimoId = CatColonia::max('id_colonia') ?? 0;
        $validated['id_colonia'] = $ultimoId + 1;
        $validated['fecha_alta'] = now();
        $validated['ID_USUARIO'] = auth()->id();

        CatColonia::create($validated);

        return redirect()->route('colonias.index')->with('success', 'Colonia creada exitosamente.');
    }

    public function show(CatColonia $colonia)
    {
        $colonia->load('calles');
        return Inertia::render('Colonias/Show', ['colonia' => $colonia]);
    }

    public function edit(CatColonia $colonia)
    {
        $poblaciones = CatPoblacion::where('Activo', 1)->orderBy('POBLACION')->get();
        $zonasPredio = CatZonaPredio::where('activo', 1)->orderBy('descripcion')->get();
        return Inertia::render('Colonias/Edit', compact('colonia', 'poblaciones', 'zonasPredio'));
    }

    public function update(Request $request, CatColonia $colonia)
    {
        $validated = $request->validate([
            'COLONIA' => 'required|string|max:120',
            'id_poblacion' => 'nullable|integer',
            'id_cat_zona_predio' => 'nullable|integer',
            'Activo' => 'nullable|boolean',
        ]);

        $colonia->update($validated);

        return redirect()->route('colonias.index')->with('success', 'Colonia actualizada exitosamente.');
    }

    public function destroy(CatColonia $colonia)
    {
        $colonia->delete();
        return redirect()->route('colonias.index')->with('success', 'Colonia eliminada exitosamente.');
    }
}
