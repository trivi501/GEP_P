<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Cajas;
use App\Models\Cajero;
use App\Models\User;
use Illuminate\Http\Request;

class CajasController extends Controller
{
    public function index()
    {
        $cajas = Cajas::with('cajeros.usuario')->orderBy('nombre')->paginate(10);
        return Inertia::render('Cajas/Index', compact('cajas'));

        return Inertia::render('Cajas/Create', compact('usuarios'));

        return Inertia::render('Cajas/Show', compact('caja'));

        return Inertia::render('Cajas/Edit', compact('caja', 'usuarios'));
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
