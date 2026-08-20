<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Services\PagoFacturacionDataService;
use Illuminate\Http\Request;

class PagoApiController extends Controller
{
    public function index(Request $request)
    {
        $pagos = Pago::with(PagoFacturacionDataService::eagerLoad())
            ->when($request->query('tipo_pago'), fn ($query, $tipoPago) => $query->where('tipo_pago', $tipoPago))
            ->when($request->query('estatus'), fn ($query, $estatus) => $query->where('estatus', $estatus))
            ->when($request->query('anio_pago'), fn ($query, $anioPago) => $query->where('anio_pago', $anioPago))
            ->orderByDesc('fecha')
            ->paginate($request->integer('per_page', 25));

        return response()->json([
            'data' => $pagos->getCollection()->map(fn (Pago $pago) => PagoFacturacionDataService::build($pago))->values(),
            'meta' => [
                'current_page' => $pagos->currentPage(),
                'last_page' => $pagos->lastPage(),
                'per_page' => $pagos->perPage(),
                'total' => $pagos->total(),
            ],
        ]);
    }

    public function show(Request $request, string $folio)
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
