<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Secretaria;
use App\Models\Cuentas;
use App\Models\User;
use Illuminate\Http\Request;

class SecretariaController extends Controller
{
    public function index()
    {
        $secretarias = Secretaria::with('cuentas')->orderBy('nombre')->paginate(10);
        return Inertia::render('Secretarias/Index', compact('secretarias'));
    }

    public function create()
    {
        $cuentas = Cuentas::orderBy('descripcion')->get();
        return Inertia::render('Secretarias/Create', compact('cuentas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'cuentas' => 'nullable|array',
            'cuentas.*' => 'exists:cuentas,id',
        ]);

        $secretaria = Secretaria::create(['nombre' => $validated['nombre']]);

        if (!empty($validated['cuentas'])) {
            $secretaria->cuentas()->attach($validated['cuentas']);
        }

        return redirect()->route('secretarias.index')->with('success', 'Secretaría creada exitosamente.');
    }

    public function show(Secretaria $secretaria)
    {
        $secretaria->load('cuentas', 'users');
        return Inertia::render('Secretarias/Show', compact('secretaria'));
    }

    public function edit(Secretaria $secretaria)
    {
        $secretaria->load('cuentas');
        $cuentas = Cuentas::orderBy('descripcion')->get();
        return Inertia::render('Secretarias/Edit', compact('secretaria', 'cuentas'));
    }

    public function update(Request $request, Secretaria $secretaria)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'cuentas' => 'nullable|array',
            'cuentas.*' => 'exists:cuentas,id',
        ]);

        $secretaria->update(['nombre' => $validated['nombre']]);
        $secretaria->cuentas()->sync($validated['cuentas'] ?? []);

        return redirect()->route('secretarias.index')->with('success', 'Secretaría actualizada exitosamente.');
    }

    public function destroy(Secretaria $secretaria)
    {
        $secretaria->delete();
        return redirect()->route('secretarias.index')->with('success', 'Secretaría eliminada exitosamente.');
    }
}
