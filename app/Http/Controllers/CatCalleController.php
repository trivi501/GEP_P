<?php

namespace App\Http\Controllers;

use App\Models\CatCalle;
use App\Models\CatColonia;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatCalleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:calles-index')->only('index');
        $this->middleware('permission:calles-create')->only(['create', 'store']);
        $this->middleware('permission:calles-edit')->only(['edit', 'update']);
        $this->middleware('permission:calles-delete')->only('destroy');
    }

    public function index()
    {
        $calles = CatCalle::with('colonia')->orderBy('CALLE')->paginate(15);
        return Inertia::render('Calles/Index', compact('calles'));
    }

    public function create()
    {
        $colonias = CatColonia::where('Activo', 1)->orderBy('COLONIA')->get();
        return Inertia::render('Calles/Create', compact('colonias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'CALLE' => 'required|string|max:60',
            'ID_COLONIA' => 'required|exists:cat_colonia,id_colonia',
            'Activo' => 'nullable|boolean',
        ]);

        $ultimoId = CatCalle::max('id_calle') ?? 0;
        $validated['id_calle'] = $ultimoId + 1;
        $validated['fecha_alta'] = now();
        $validated['id_usuario'] = auth()->id();

        CatCalle::create($validated);

        return redirect()->route('calles.index')->with('success', 'Calle creada exitosamente.');
    }

    public function show(CatCalle $calle)
    {
        $calle->load('colonia');
        return Inertia::render('Calles/Show', ['calle' => $calle]);
    }

    public function edit(CatCalle $calle)
    {
        $colonias = CatColonia::where('Activo', 1)->orderBy('COLONIA')->get();
        return Inertia::render('Calles/Edit', ['calle' => $calle, 'colonias' => $colonias]);
    }

    public function update(Request $request, CatCalle $calle)
    {
        $validated = $request->validate([
            'CALLE' => 'required|string|max:60',
            'ID_COLONIA' => 'required|exists:cat_colonia,id_colonia',
            'Activo' => 'nullable|boolean',
        ]);

        $calle->update($validated);

        return redirect()->route('calles.index')->with('success', 'Calle actualizada exitosamente.');
    }

    public function destroy(CatCalle $calle)
    {
        $calle->delete();
        return redirect()->route('calles.index')->with('success', 'Calle eliminada exitosamente.');
    }
}
