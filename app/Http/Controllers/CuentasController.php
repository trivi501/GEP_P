<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Cuentas;
use App\Models\Conac;
use App\Exports\CuentasExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class CuentasController extends Controller
{
    public function index()
    {
        $cuentas = Cuentas::with('conac', 'cuentaMayor')
            ->withSum('cuentasPagos', 'monto')
            ->orderBy('id')
            ->paginate(10);

        return Inertia::render('Cuentas/Index', compact('cuentas'));
    }

    public function exportarExcel(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        return Excel::download(
            new CuentasExport($request->fecha_inicio, $request->fecha_fin),
            'reporte-cuentas-' . $request->fecha_inicio . '-al-' . $request->fecha_fin . '.xlsx'
        );
    }

    public function create()
    {
        $conacs = Conac::all();
        $cuentasMayor = Cuentas::whereNull('cuentaMayor_id')->orderBy('cuenta')->get();
        return Inertia::render('Cuentas/Create', compact('conacs', 'cuentasMayor'));
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

        $validated['importe'] = $validated['importe'] ?? 0;

        Cuentas::create($validated);

        return redirect()->route('cuentas.index')->with('success', 'Cuenta creada exitosamente.');
    }

    public function show(Cuentas $cuenta)
    {
        $cuenta->load('cuentaMayor', 'conac');
        return Inertia::render('Cuentas/Show', compact('cuenta'));
    }

    public function edit(Cuentas $cuenta)
    {
        $conacs = Conac::all();
        $cuentasMayor = Cuentas::whereNull('cuentaMayor_id')->orderBy('cuenta')->get();
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

        $validated['importe'] = $validated['importe'] ?? 0;

        $cuenta->update($validated);

        return redirect()->route('cuentas.index')->with('success', 'Cuenta actualizada exitosamente.');
    }

    public function destroy(Cuentas $cuenta)
    {
        $cuenta->delete();
        return redirect()->route('cuentas.index')->with('success', 'Cuenta eliminada exitosamente.');
    }
}
