<?php

namespace App\Http\Controllers;

use App\Models\Cajas;
use App\Models\Cajero;
use App\Models\User;
use Illuminate\Http\Request;

class CajasController extends Controller
{
    public function index()
    {
        $cajas = Cajas::with('cajeros.usuario')->orderBy('nombre')->paginate(10);
        return view('cajas.index', compact('cajas'));
    }

    public function create()
    {
        $usuarios = User::orderBy('name')->get();
        return view('cajas.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
            'folio' => 'required|string|max:255',
            'status' => 'nullable|integer',
            'cajeros' => 'nullable|array',
            'cajeros.*' => 'exists:users,id',
        ]);

        $caja = Cajas::create([
            'nombre' => $validated['nombre'],
            'ubicacion' => $validated['ubicacion'],
            'folio' => $validated['folio'],
            'status' => $validated['status'] ?? 1,
        ]);

        if (!empty($validated['cajeros'])) {
            foreach ($validated['cajeros'] as $usuarioId) {
                Cajero::create([
                    'usuario_id' => $usuarioId,
                    'caja_id' => $caja->id,
                    'status' => 1,
                    'created' => now(),
                ]);
            }
        }

        return redirect()->route('cajas.index')->with('success', 'Caja creada exitosamente.');
    }

    public function show(Cajas $caja)
    {
        $caja->load('cajeros.usuario');
        return view('cajas.show', compact('caja'));
    }

    public function edit(Cajas $caja)
    {
        $caja->load('cajeros');
        $usuarios = User::orderBy('name')->get();
        return view('cajas.edit', compact('caja', 'usuarios'));
    }

    public function update(Request $request, Cajas $caja)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
            'folio' => 'required|string|max:255',
            'status' => 'nullable|integer',
            'cajeros' => 'nullable|array',
            'cajeros.*' => 'exists:users,id',
        ]);

        $caja->update([
            'nombre' => $validated['nombre'],
            'ubicacion' => $validated['ubicacion'],
            'folio' => $validated['folio'],
            'status' => $validated['status'] ?? 1,
        ]);

        $caja->cajeros()->delete();

        if (!empty($validated['cajeros'])) {
            foreach ($validated['cajeros'] as $usuarioId) {
                Cajero::create([
                    'usuario_id' => $usuarioId,
                    'caja_id' => $caja->id,
                    'status' => 1,
                    'created' => now(),
                ]);
            }
        }

        return redirect()->route('cajas.index')->with('success', 'Caja actualizada exitosamente.');
    }

    public function destroy(Cajas $caja)
    {
        $caja->cajeros()->delete();
        $caja->delete();

        return redirect()->route('cajas.index')->with('success', 'Caja eliminada exitosamente.');
    }
}
