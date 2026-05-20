<?php

namespace App\Http\Controllers;

use App\Models\Cajero;
use App\Models\Cajas;
use App\Models\User;
use Illuminate\Http\Request;

class CajeroController extends Controller
{
    public function index()
    {
        $cajeros = Cajero::with('usuario', 'caja')->orderBy('id_cajero')->paginate(10);
        return view('cajeros.index', compact('cajeros'));
    }

    public function create()
    {
        $usuarios = User::orderBy('name')->get();
        $cajas = Cajas::where('status', 1)->orderBy('nombre')->get();
        return view('cajeros.create', compact('usuarios', 'cajas'));
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
            'created' => now(),
        ]);

        return redirect()->route('cajeros.index')->with('success', 'Cajero asignado exitosamente.');
    }

    public function show(Cajero $cajero)
    {
        $cajero->load('usuario', 'caja');
        return view('cajeros.show', compact('cajero'));
    }

    public function edit(Cajero $cajero)
    {
        $usuarios = User::orderBy('name')->get();
        $cajas = Cajas::where('status', 1)->orderBy('nombre')->get();
        return view('cajeros.edit', compact('cajero', 'usuarios', 'cajas'));
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
