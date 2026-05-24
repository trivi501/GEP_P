<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\OrdenPago;
use App\Models\Cuentas;
use App\Models\FormaPago;
use App\Models\Secretaria;
use Illuminate\Http\Request;

class OrdenPagoController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search_folio', 'search_nombre', 'search_secretaria', 'search_fecha', 'search_monto', 'search_estatus', 'search_usuario']);

        $userSecretariaId = auth()->user()->secretaria_id;

        $ordenes = OrdenPago::with('cuentasOrdenesPago.cuenta', 'user', 'secretaria')
            ->where('secretaria_id', $userSecretariaId)
            ->when($filters['search_folio'] ?? null, fn($q, $v) => $q->where('folio', 'like', "%{$v}%"))
            ->when($filters['search_nombre'] ?? null, fn($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
            ->when($filters['search_secretaria'] ?? null, fn($q, $v) => $q->whereHas('secretaria', fn($q) => $q->where('nombre', 'like', "%{$v}%")))
            ->when($filters['search_fecha'] ?? null, fn($q, $v) => $q->where('fecha', $v))
            ->when($filters['search_usuario'] ?? null, fn($q, $v) => $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$v}%")))
            ->when($filters['search_estatus'] ?? null, function ($q, $v) {
                if ($v === 'pagado') $q->where('pagado', true);
                elseif ($v === 'pendiente') $q->where('pagado', false);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return Inertia::render('OrdenesPago/Index', compact('ordenes', 'filters', 'userSecretariaId'));
    }

    public function create()
    {
        $cuentas = Cuentas::orderBy('descripcion')->get();
        $secretarias = Secretaria::orderBy('nombre')->get();
        $userSecretariaId = auth()->user()->secretaria_id;
        $hoy = now()->format('Y-m-d');

        return Inertia::render('OrdenesPago/Create', compact('cuentas', 'secretarias', 'userSecretariaId', 'hoy'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'monto' => 'nullable|numeric|min:0',
            'cuentas' => 'nullable|array',
            'cuentas.*.IdCuenta' => 'required_with:cuentas|exists:cuentas,id',
            'cuentas.*.monto' => 'required_with:cuentas|numeric|min:0',
            'cuentas.*.cantidad' => 'required_with:cuentas|numeric|min:0',
        ]);

        $montoCalculado = 0;
        if (!empty($validated['cuentas'])) {
            foreach ($validated['cuentas'] as $c) {
                $montoCalculado += (float) ($c['monto'] ?? 0) * (float) ($c['cantidad'] ?? 0);
            }
        }

        $secretariaId = auth()->user()->secretaria_id;
        $prefijo = Secretaria::where('id', $secretariaId)->value('prefijo') ?? 'GEN';
        $year = now()->format('Y');
        $ultimoFolio = OrdenPago::whereYear('created_at', $year)
            ->whereHas('secretaria', fn($q) => $q->where('prefijo', $prefijo))
            ->count();
        $folio = strtoupper($prefijo) . '-' . $year . '-' . str_pad($ultimoFolio + 1, 4, '0', STR_PAD_LEFT);

        $orden = OrdenPago::create([
            'folio' => $folio,
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'monto' => $montoCalculado,
            'fecha' => now()->format('Y-m-d'),
            'fecha_vencimiento' => now()->addDays(15)->format('Y-m-d'),
            'secretaria_id' => auth()->user()->secretaria_id,
            'userid' => auth()->id(),
        ]);

        if (!empty($validated['cuentas'])) {
            foreach ($validated['cuentas'] as $c) {
                $orden->cuentasOrdenesPago()->create([
                    'IdCuenta' => $c['IdCuenta'],
                    'monto' => $c['monto'],
                    'cantidad' => $c['cantidad'],
                    'descuento' => 0,
                    'created' => now(),
                ]);
            }
        }

        return redirect()->route('ordenes-pago.index')->with('success', 'Orden de pago creada exitosamente.');
    }

    public function show(OrdenPago $ordenPago)
    {
        $ordenPago->load('cuentasOrdenesPago.cuenta', 'user', 'secretaria', 'pagos');
        return Inertia::render('OrdenesPago/Show', ['ordenPago' => $ordenPago]);
    }

    public function edit(OrdenPago $ordenPago)
    {
        $ordenPago->load('cuentasOrdenesPago.cuenta', 'secretaria');

        $cuentas = Cuentas::orderBy('descripcion')->get();
        $secretarias = Secretaria::orderBy('nombre')->get();
        $userSecretariaId = auth()->user()->secretaria_id;
        $hoy = now()->format('Y-m-d');

        return Inertia::render('OrdenesPago/Edit', compact('ordenPago', 'cuentas', 'secretarias', 'userSecretariaId', 'hoy'));
    }

    public function update(Request $request, OrdenPago $ordenPago)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'monto' => 'nullable|numeric|min:0',
            'cuentas' => 'nullable|array',
            'cuentas.*.IdCuenta' => 'required_with:cuentas|exists:cuentas,id',
            'cuentas.*.monto' => 'required_with:cuentas|numeric|min:0',
            'cuentas.*.cantidad' => 'required_with:cuentas|numeric|min:0',
        ]);

        $montoCalculado = 0;
        if (!empty($validated['cuentas'])) {
            foreach ($validated['cuentas'] as $c) {
                $montoCalculado += (float) ($c['monto'] ?? 0) * (float) ($c['cantidad'] ?? 0);
            }
        }

        $ordenPago->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'monto' => $montoCalculado,
            'fecha' => now()->format('Y-m-d'),
            'secretaria_id' => $ordenPago->secretaria_id,
        ]);

        $ordenPago->cuentasOrdenesPago()->delete();

        if (!empty($validated['cuentas'])) {
            foreach ($validated['cuentas'] as $c) {
                $ordenPago->cuentasOrdenesPago()->create([
                    'IdCuenta' => $c['IdCuenta'],
                    'monto' => $c['monto'],
                    'cantidad' => $c['cantidad'],
                    'descuento' => 0,
                    'created' => now(),
                ]);
            }
        }

        return redirect()->route('ordenes-pago.index')->with('success', 'Orden de pago actualizada exitosamente.');
    }

    public function destroy(OrdenPago $ordenPago)
    {
        $ordenPago->delete();
        return redirect()->route('ordenes-pago.index')->with('success', 'Orden de pago eliminada exitosamente.');
    }

    public function ordenesPgoCajas(Request $request)
    {
        $filters = $request->only(['search_folio', 'search_nombre', 'search_secretaria', 'search_fecha', 'search_monto', 'search_estatus', 'search_usuario']);

        $userSecretariaId = auth()->user()->secretaria_id;

        $ordenes = OrdenPago::with('cuentasOrdenesPago.cuenta', 'user', 'secretaria')
            ->when($filters['search_folio'] ?? null, fn($q, $v) => $q->where('folio', 'like', "%{$v}%"))
            ->when($filters['search_nombre'] ?? null, fn($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
            ->when($filters['search_secretaria'] ?? null, fn($q, $v) => $q->whereHas('secretaria', fn($q) => $q->where('nombre', 'like', "%{$v}%")))
            ->when($filters['search_fecha'] ?? null, fn($q, $v) => $q->where('fecha', $v))
            ->when($filters['search_usuario'] ?? null, fn($q, $v) => $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$v}%")))
            ->when($filters['search_estatus'] ?? null, function ($q, $v) {
                if ($v === 'pagado') $q->where('pagado', true);
                elseif ($v === 'pendiente') $q->where('pagado', false);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        $formasPago = FormaPago::where('activo', 1)->orderBy('Descripción')->get();

        return Inertia::render('OrdenesPago/OrdenesPgoCajas', compact('ordenes', 'formasPago', 'filters', 'userSecretariaId'));
    }
}
