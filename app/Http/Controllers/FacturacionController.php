<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Services\PagoFacturacionDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacturacionController extends Controller
{
    private function filtered(Request $request)
    {
        return Pago::with('predio')
            ->when($request->filled('search_folio'), fn ($q) => $q->where('folio', 'like', '%' . $request->search_folio . '%'))
            ->when($request->filled('search_nombre'), fn ($q) => $q->where('nombre', 'like', '%' . $request->search_nombre . '%'))
            ->when($request->filled('search_fecha'), fn ($q) => $q->whereDate('fecha', $request->search_fecha))
            ->when($request->filled('search_tipo_pago'), fn ($q) => $q->where('tipo_pago', $request->search_tipo_pago))
            ->when($request->filled('search_estatus'), fn ($q) => $q->where('estatus', $request->search_estatus))
            ->orderBy('fecha', 'desc');
    }

    private function mapRow(Pago $pago): array
    {
        return [
            'id' => $pago->id,
            'folio' => $pago->folio,
            'fecha' => $pago->fecha,
            'nombre' => $pago->nombre,
            'rfc' => $pago->rfc,
            'tipo_pago' => $pago->tipo_pago,
            'estatus' => $pago->estatus,
            'monto' => (float) $pago->monto,
            'clave_catastral' => $pago->predio?->Clave_predial,
        ];
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search_folio', 'search_nombre', 'search_fecha', 'search_tipo_pago', 'search_estatus']);

        $pagos = $this->filtered($request)->paginate(25);

        return Inertia::render('Facturacion/Index', [
            'pagos' => $pagos->through(fn ($pago) => $this->mapRow($pago))->items(),
            'currentPage' => $pagos->currentPage(),
            'lastPage' => $pagos->lastPage(),
            'filters' => $filters,
        ]);
    }

    public function listar(Request $request)
    {
        $pagos = $this->filtered($request)->paginate(25);

        return response()->json([
            'data' => $pagos->through(fn ($pago) => $this->mapRow($pago))->items(),
            'current_page' => $pagos->currentPage(),
            'last_page' => $pagos->lastPage(),
        ]);
    }

    public function datos(string $folio)
    {
        $pago = Pago::with(PagoFacturacionDataService::eagerLoad())
            ->where('folio', $folio)
            ->first();

        if (!$pago) {
            return response()->json(['error' => 'No se encontró un pago con ese folio.'], 404);
        }

        return response()->json(PagoFacturacionDataService::build($pago));
    }
}
