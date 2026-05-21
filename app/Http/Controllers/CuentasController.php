<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Cuentas;
use App\Models\Conac;
use Illuminate\Http\Request;

class CuentasController extends Controller
{
    public function index()
    {
        $cuentas = Cuentas::with('conac', 'cuentaMayor')->orderBy('id')->paginate(10);
        return Inertia::render('Cuentas/Index', compact('cuentas'));

        return Inertia::render('Cuentas/Create', compact('conacs', 'cuentasMayor'));

        return Inertia::render('Cuentas/Show', compact('cuenta'));

        return Inertia::render('Cuentas/Edit', compact('cuenta', 'conacs', 'cuentasMayor'));
    }

    public function update(Request $request, Cuentas $cuenta)
    {
        $validated = $request->validate([
            'indetec' => 'nullable|string|max:255',
            'nom_indetect' => 'nullable|string|max:255',
            'cuenta' => 'nullable|string|max:255',
            'subcuenta' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'importe' => 'nullable|numeric|min:0',
            'cuentaMayor_id' => 'nullable|exists:cuentas,id',
            'indetecMayor_id' => 'nullable|integer',
            'conac_id' => 'nullable|exists:conacs,id',
        ]);

        $cuenta->update($validated);

        return redirect()->route('cuentas.index')->with('success', 'Cuenta actualizada exitosamente.');
    }

    public function destroy(Cuentas $cuenta)
    {
        $cuenta->delete();
        return redirect()->route('cuentas.index')->with('success', 'Cuenta eliminada exitosamente.');
    }
}
