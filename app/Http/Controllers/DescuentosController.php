<?php

namespace App\Http\Controllers;

use App\Models\Descuento;
use App\Models\Predio;
use App\Models\Contribuyente;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DescuentosController extends Controller
{
    public function index(Request $request)
    {
        $query = Descuento::with(['predio.contribuyente', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $predioIds = Predio::whereHas('contribuyente', function ($q) use ($search) {
                $q->where('cuenta', 'like', "%{$search}%")
                  ->orWhere('nombre_completo', 'like', "%{$search}%")
                  ->orWhere('nombre_moral', 'like', "%{$search}%");
            })->orWhere('Clave_predial', 'like', "%{$search}%")
            ->pluck('id_predio');

            $query->whereIn('idPredio', $predioIds);
        }

        $descuentos = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $filters = $request->only(['search']);
        $id_predio = $request->get('id_predio');

        $existingDescuento = null;
        if ($id_predio) {
            $existingDescuento = Descuento::with(['predio.contribuyente', 'user'])
                ->where('idPredio', $id_predio)
                ->first();
        }

        return Inertia::render('Descuentos/Index', compact('descuentos', 'filters', 'id_predio', 'existingDescuento'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idPredio' => 'required|string',
            'multas' => 'required|numeric|min:0|max:100',
            'actualizaciones' => 'required|numeric|min:0|max:100',
            'gastos_cobranza' => 'required|numeric|min:0|max:100',
            'fecha_expiracion' => 'nullable|date',
            'activo' => 'boolean',
        ]);

        $validated['idUser'] = auth()->id();
        $validated['activo'] = $validated['activo'] ?? true;

        Descuento::create($validated);

        return redirect()->route('descuentos.index')
            ->with('success', 'Descuento creado exitosamente.');
    }

    public function update(Request $request, Descuento $descuento)
    {
        $validated = $request->validate([
            'multas' => 'required|numeric|min:0|max:100',
            'actualizaciones' => 'required|numeric|min:0|max:100',
            'gastos_cobranza' => 'required|numeric|min:0|max:100',
            'fecha_expiracion' => 'nullable|date',
            'activo' => 'boolean',
        ]);

        $descuento->update($validated);

        return redirect()->route('descuentos.index')
            ->with('success', 'Descuento actualizado exitosamente.');
    }

    public function destroy(Descuento $descuento)
    {
        $descuento->delete();

        return redirect()->route('descuentos.index')
            ->with('success', 'Descuento eliminado exitosamente.');
    }

    public function searchPredio(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) return response()->json([]);

        $predios = Predio::with('contribuyente')
            ->whereHas('contribuyente', function ($query) use ($q) {
                $query->where('cuenta', 'like', "%{$q}%")
                      ->orWhere('nombre_completo', 'like', "%{$q}%")
                      ->orWhere('nombre_moral', 'like', "%{$q}%");
            })
            ->orWhere('Clave_predial', 'like', "%{$q}%")
            ->take(10)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id_predio,
                'text' => "{$p->Clave_predial} - {$p->contribuyente?->cuenta} - {$p->contribuyente?->nombre_completo}",
            ]);

        return response()->json($predios);
    }
}
