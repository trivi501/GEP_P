<?php

namespace App\Http\Controllers;

use App\Models\Cuentas;
use App\Models\Conac;
use Illuminate\Http\Request;

class CuentasController extends Controller
{
    public function index()
    {
        $cuentas = Cuentas::with('conac', 'cuentaMayor')->orderBy('id')->paginate(10);
        return view('cuentas.index', compact('cuentas'));
    }

    public function create()
    {
        $conacs = Conac::orderBy('nombre')->get();
        $cuentasMayor = Cuentas::orderBy('cuenta')->get();
        return view('cuentas.create', compact('conacs', 'cuentasMayor'));
    }

    public function store(Request $request)
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

        Cuentas::create($validated);

        return redirect()->route('cuentas.index')->with('success', 'Cuenta creada exitosamente.');
    }

    public function show(Cuentas $cuenta)
    {
        $cuenta->load('conac', 'cuentaMayor');
        return view('cuentas.show', compact('cuenta'));
    }

    public function edit(Cuentas $cuenta)
    {
        $conacs = Conac::orderBy('nombre')->get();
        $cuentasMayor = Cuentas::orderBy('cuenta')->get();
        return view('cuentas.edit', compact('cuenta', 'conacs', 'cuentasMayor'));
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
