<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Cajero;
use App\Models\Cajas;
use App\Models\User;
use Illuminate\Http\Request;

class CajeroController extends Controller
{
    public function index()
    {
        $cajeros = Cajero::with('usuario', 'caja')->orderBy('id_cajero')->paginate(10);
        return Inertia::render('Cajeros/Index', compact('cajeros'));
    }

    public function create()
    {
        $usuarios = User::orderBy('name')->get();
        $cajas = Cajas::orderBy('nombre')->get();
        return Inertia::render('Cajeros/Create', compact('usuarios', 'cajas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'caja_id' => 'required|exists:cajas,id',
            'status' => 'nullable|integer',
        ]);

        Cajero::create([
            'usuario_id' => $validated['usuario_id'],
            'caja_id' => $validated['caja_id'],
            'status' => $validated['status'] ?? 1,
        ]);

        return redirect()->route('cajeros.index')->with('success', 'Cajero creado exitosamente.');
    }

    public function show(Cajero $cajero)
    {
        $cajero->load('usuario', 'caja');
        return Inertia::render('Cajeros/Show', compact('cajero'));
    }

    public function edit(Cajero $cajero)
    {
        $cajero->load('usuario', 'caja');
        $usuarios = User::orderBy('name')->get();
        $cajas = Cajas::orderBy('nombre')->get();
        return Inertia::render('Cajeros/Edit', compact('cajero', 'usuarios', 'cajas'));
    }

    public function update(Request $request, Cajero $cajero)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'caja_id' => 'required|exists:cajas,id',
            'status' => 'nullable|integer',
        ]);

        $cajero->update([
            'usuario_id' => $validated['usuario_id'],
            'caja_id' => $validated['caja_id'],
            'status' => $validated['status'] ?? 1,
        ]);

        return redirect()->route('cajeros.index')->with('success', 'Cajero actualizado exitosamente.');
    }

    public function destroy(Cajero $cajero)
    {
        $cajero->delete();

        return redirect()->route('cajeros.index')->with('success', 'Cajero eliminado exitosamente.');
    }
}
