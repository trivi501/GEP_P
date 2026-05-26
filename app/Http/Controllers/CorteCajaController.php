<?php

namespace App\Http\Controllers;

use App\Models\CorteCaja;
use App\Models\HistorialCaja;
use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CorteCajaController extends Controller
{
    public function index(Request $request)
    {
        $query = CorteCaja::withCount('historialCajas')->orderBy('fecha', 'desc');

        $cortes = $query->paginate(15)->withQueryString();

        $historialCajasSinCorte = HistorialCaja::with('caja', 'cajero.usuario')
            ->whereNull('cortecaja_id')
            ->whereNotNull('datetime_cierre')
            ->orderBy('datetime_cierre', 'desc')
            ->get()
            ->map(function ($h) {
                $pagos = Pago::where('id_historial_caja', $h->id)->get();
                return [
                    'id' => $h->id,
                    'caja' => $h->caja?->nombre ?? '—',
                    'cajero' => $h->cajero?->usuario?->name ?? '—',
                    'fondo' => $h->fondo,
                    'total_ingreso' => $h->total_ingreso,
                    'datetime_apertura' => $h->datetime_apertura,
                    'datetime_cierre' => $h->datetime_cierre,
                    'urbano' => $pagos->where('tipo_pago', 'predial_urbano')->sum('monto'),
                    'rustico' => $pagos->where('tipo_pago', 'predial_rustico')->sum('monto'),
                    'recibos_efectivos' => $pagos->where('estatus', 'pagado')->count(),
                    'recibos_cancelados' => $pagos->where('estatus', 'cancelado')->count(),
                ];
            });

        return Inertia::render('CorteCajas/Index', compact('cortes', 'historialCajasSinCorte'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'ingresos' => 'required|numeric|min:0',
            'urbano' => 'required|numeric|min:0',
            'rustico' => 'required|numeric|min:0',
            'recibos_efectivos' => 'required|integer|min:0',
            'recibos_cancelados' => 'required|integer|min:0',
            'historial_ids' => 'required|array|min:1',
            'historial_ids.*' => 'integer|exists:historial_caja,id',
        ]);

        $corte = CorteCaja::create([
            'fecha' => $validated['fecha'],
            'ingresos' => $validated['ingresos'],
            'urbano' => $validated['urbano'],
            'rustico' => $validated['rustico'],
            'recibos_efectivos' => $validated['recibos_efectivos'],
            'recibos_cancelados' => $validated['recibos_cancelados'],
        ]);

        HistorialCaja::whereIn('id', $validated['historial_ids'])
            ->update(['cortecaja_id' => $corte->id]);

        $pdfUrl = route('corte-cajas.pdf', $corte->id);

        return response()->json([
            'success' => true,
            'message' => 'Corte de caja creado exitosamente.',
            'pdf_url' => $pdfUrl,
            'redirect' => route('corte-cajas.index'),
        ]);
    }

    public function update(Request $request, CorteCaja $corteCaja)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'ingresos' => 'required|numeric|min:0',
            'urbano' => 'required|numeric|min:0',
            'rustico' => 'required|numeric|min:0',
            'recibos_efectivos' => 'required|integer|min:0',
            'recibos_cancelados' => 'required|integer|min:0',
        ]);

        $corteCaja->update($validated);

        return redirect()->route('corte-cajas.index')
            ->with('success', 'Corte de caja actualizado exitosamente.');
    }

    public function destroy(CorteCaja $corteCaja)
    {
        HistorialCaja::where('cortecaja_id', $corteCaja->id)
            ->update(['cortecaja_id' => null]);

        $corteCaja->delete();

        return redirect()->route('corte-cajas.index')
            ->with('success', 'Corte de caja eliminado exitosamente.');
    }

    public function pdf(CorteCaja $corteCaja)
    {
        $historialIds = HistorialCaja::where('cortecaja_id', $corteCaja->id)->pluck('id');
        $pagoIds = Pago::whereIn('id_historial_caja', $historialIds)
            ->where('estatus', 'pagado')
            ->pluck('id');

        $cuentasResumenRaw = \App\Models\CuentasPagos::whereIn('pago_id', $pagoIds)
            ->selectRaw('cuenta_id, COUNT(DISTINCT pago_id) as recibos, SUM(monto) as monto')
            ->groupBy('cuenta_id')
            ->get()
            ->map(function ($cp) {
                $cuenta = \App\Models\Cuentas::find($cp->cuenta_id);
                return [
                    'cuenta_id' => $cp->cuenta_id,
                    'cuenta' => $cuenta?->cuenta ?? '—',
                    'descripcion' => $cuenta?->descripcion ?? 'Sin cuenta',
                    'monto' => (float) $cp->monto,
                    'recibos' => (int) $cp->recibos,
                ];
            });

        $idCuentaDescuentos = 15;
        $descuentos = collect($cuentasResumenRaw)->filter(fn($item) => $item['cuenta_id'] == $idCuentaDescuentos)->values()->toArray();
        $cuentasResumen = collect($cuentasResumenRaw)->filter(fn($item) => $item['cuenta_id'] != $idCuentaDescuentos)->sortByDesc('monto')->values()->toArray();

        $totales = [
            'monto' => collect($cuentasResumen)->sum('monto'),
            'recibos' => collect($cuentasResumen)->sum('recibos'),
        ];

        $totalDescuentos = collect($descuentos)->sum('monto');

        $corte = $corteCaja;
        $pdf = Pdf::loadView('corte-cajas.pdf', compact('corte', 'cuentasResumen', 'totales', 'descuentos', 'totalDescuentos'));
        $pdf->setPaper('a4');

        return $pdf->stream("Corte_Caja_{$corteCaja->id}.pdf");
    }
}
