<?php

namespace App\Http\Controllers;

use App\Models\CatPoblacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatPoblacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:poblaciones-index')->only('index');
        $this->middleware('permission:poblaciones-create')->only(['create', 'store']);
        $this->middleware('permission:poblaciones-edit')->only(['edit', 'update']);
        $this->middleware('permission:poblaciones-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = CatPoblacion::when($request->filled('search'), fn($q) => $q->where('POBLACION', 'like', '%' . $request->search . '%'));
        $poblaciones = $query->orderBy('POBLACION')->paginate(15)->withQueryString();
        return Inertia::render('Poblaciones/Index', compact('poblaciones'));
    }

    public function create()
    {
        return Inertia::render('Poblaciones/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'POBLACION' => 'required|string|max:80',
            'numero' => 'nullable|string|max:3',
            'Activo' => 'nullable|boolean',
        ]);

        $ultimoId = CatPoblacion::max('id_poblacion') ?? 0;
        $validated['id_poblacion'] = $ultimoId + 1;
        $validated['fecha_alta'] = now();
        $validated['id_usuario'] = auth()->id();

        CatPoblacion::create($validated);

        return redirect()->route('poblaciones.index')->with('success', 'Población creada exitosamente.');
    }

    public function edit(CatPoblacion $poblacion)
    {
        return Inertia::render('Poblaciones/Edit', compact('poblacion'));
    }

    public function update(Request $request, CatPoblacion $poblacion)
    {
        $validated = $request->validate([
            'POBLACION' => 'required|string|max:80',
            'numero' => 'nullable|string|max:3',
            'Activo' => 'nullable|boolean',
        ]);

        $poblacion->update($validated);

        return redirect()->route('poblaciones.index')->with('success', 'Población actualizada exitosamente.');
    }

    public function destroy(CatPoblacion $poblacion)
    {
        $poblacion->delete();
        return redirect()->route('poblaciones.index')->with('success', 'Población eliminada exitosamente.');
    }
}
