<?php

use App\Http\Controllers\ClavePredialController;
use App\Http\Controllers\ContribuyenteController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PredioController;
use App\Http\Controllers\CajasController;
use App\Http\Controllers\CajeroController;
use App\Http\Controllers\CalculosPrediosController;
use App\Http\Controllers\CuentasController;
use App\Http\Controllers\SecretariaController;
use App\Http\Controllers\OrdenPagoController;
use App\Http\Controllers\PagosController;
use App\Http\Controllers\EstadoCuentaMasivoController;
use App\Http\Controllers\MultiPagosController;
use App\Http\Controllers\CorteCajaController;
use App\Http\Controllers\DescuentosController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('superadmin')->group(function () {
        Route::resource('permissions', PermissionController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });
    Route::get('contribuyentes/search', [ContribuyenteController::class, 'search'])->name('contribuyentes.search');
    Route::resource('contribuyentes', ContribuyenteController::class);
    Route::post('clave-predial/store', [ClavePredialController::class, 'store'])->name('clave-predial.store');
    Route::get('predios/clave-predial-search', [PredioController::class, 'searchClavePredial'])->name('predios.clave-predial-search');
    Route::get('predios/calle-search', [PredioController::class, 'searchCalle'])->name('predios.calle-search');
    Route::get('predios/colonia-search', [PredioController::class, 'searchColonia'])->name('predios.colonia-search');
    Route::get('predios/{predio}/pdf', [PredioController::class, 'pdf'])->name('predios.pdf');
    Route::get('predios/{predio}/cedula', [PredioController::class, 'cedula'])->name('predios.cedula');
    Route::post('predios/{predio}/prescripcion', [PredioController::class, 'prescripcion'])->name('predios.prescripcion');
    Route::resource('predios', PredioController::class);
    Route::resource('cajas', CajasController::class);
    Route::resource('cajeros', CajeroController::class);
    Route::resource('cuentas', CuentasController::class);
    Route::get('pagos', [PagosController::class, 'index'])->name('pagos.index');
    Route::post('pagos', [PagosController::class, 'store'])->name('pagos.store');
    Route::get('pagos/cobrar', [PagosController::class, 'cobrar'])->name('pagos.cobrar');
    Route::get('pagos/search-predio', [PagosController::class, 'searchPredio'])->name('pagos.search-predio');
    Route::get('pagos/get-calculo', [PagosController::class, 'getCalculo'])->name('pagos.get-calculo');
    Route::post('pagos/guardar', [PagosController::class, 'guardar'])->name('pagos.guardar');
    Route::get('pagos/historial', [PagosController::class, 'historial'])->name('pagos.historial');
    Route::get('pagos/recibo/{id}', [PagosController::class, 'recibo'])->name('pagos.recibo');
    Route::post('pagos/cerrar', [PagosController::class, 'cerrar'])->name('pagos.cerrar');
    Route::get('pagos/corte-pdf/{historialCaja}', [PagosController::class, 'cortePdf'])->name('pagos.corte-pdf');
    Route::post('pagos/{pago}/cancelar', [PagosController::class, 'cancelar'])->name('pagos.cancelar');
    Route::get('pagos/caja-general', [PagosController::class, 'cajaGeneralIndex'])->name('pagos.caja-general');
    Route::get('pagos/caja-general/pagar/{ordenPago}', [PagosController::class, 'cajaGeneralPagar'])->name('pagos.caja-general.pagar');
    Route::post('pagos/caja-general/guardar', [PagosController::class, 'cajaGeneralGuardar'])->name('pagos.caja-general.guardar');
    Route::get('pagos/caja-general/{pago}', [PagosController::class, 'cajaGeneralShow'])->name('pagos.caja-general.show');
    Route::post('pagos/caja-general/{pago}/cancelar', [PagosController::class, 'cajaGeneralCancelar'])->name('pagos.caja-general.cancelar');
    Route::get('pagos/pagos-generales', [PagosController::class, 'pagosGenerales'])->name('pagos.pagos-generales');
    Route::get('calculos-predios', [CalculosPrediosController::class, 'index'])->name('calculos-predios.index');
    Route::get('calculos-predios/pdf', [CalculosPrediosController::class, 'pdf'])->name('calculos-predios.pdf');
    Route::get('calculos-predios/pdf-rustico', [CalculosPrediosController::class, 'pdfRustico'])->name('calculos-predios.pdf-rustico');
    Route::post('calculos-predios/calculo-predial-urbano', [CalculosPrediosController::class, 'calculoPredialUrbano'])->name('calculos-predios.calculo-predial-urbano');

    Route::get('corte-cajas/{corteCaja}/pdf', [CorteCajaController::class, 'pdf'])->name('corte-cajas.pdf');
    Route::resource('corte-cajas', CorteCajaController::class)->parameters(['corte-cajas' => 'corteCaja']);
    Route::resource('secretarias', SecretariaController::class);
    Route::resource('ordenes-pago', OrdenPagoController::class)->parameters(['ordenes-pago' => 'ordenPago']);
    Route::get('ordenes-pgo-cajas', [OrdenPagoController::class, 'ordenesPgoCajas'])->name('ordenes-pgo-cajas.index');

    Route::get('estado-cuenta-masivo', [EstadoCuentaMasivoController::class, 'index'])->name('estado-cuenta-masivo.index');
    Route::post('estado-cuenta-masivo/search', [EstadoCuentaMasivoController::class, 'search'])->name('estado-cuenta-masivo.search');
    Route::post('estado-cuenta-masivo/pdf', [EstadoCuentaMasivoController::class, 'pdf'])->name('estado-cuenta-masivo.pdf');
    Route::get('estado-cuenta-masivo/progress/{token}', [EstadoCuentaMasivoController::class, 'progress'])->name('estado-cuenta-masivo.progress');
    Route::get('estado-cuenta-masivo/download/{token}', [EstadoCuentaMasivoController::class, 'download'])->name('estado-cuenta-masivo.download');

    Route::get('multi-pagos-predial', [MultiPagosController::class, 'index'])->name('multi-pagos.index');
    Route::post('multi-pagos-predial/search', [MultiPagosController::class, 'search'])->name('multi-pagos.search');
    Route::post('multi-pagos-predial/get-calculo', [MultiPagosController::class, 'getCalculo'])->name('multi-pagos.get-calculo');
    Route::post('multi-pagos-predial/pagar', [MultiPagosController::class, 'pagar'])->name('multi-pagos.pagar');

    Route::get('descuentos', [DescuentosController::class, 'index'])->name('descuentos.index');
    Route::get('descuentos/search-predio', [DescuentosController::class, 'searchPredio'])->name('descuentos.search-predio');
    Route::post('descuentos', [DescuentosController::class, 'store'])->name('descuentos.store');
    Route::put('descuentos/{descuento}', [DescuentosController::class, 'update'])->name('descuentos.update');
    Route::delete('descuentos/{descuento}', [DescuentosController::class, 'destroy'])->name('descuentos.destroy');

    Route::get('support-tickets', [SupportTicketController::class, 'index'])->name('support-tickets.index');
    Route::get('support-tickets/create', [SupportTicketController::class, 'create'])->name('support-tickets.create');
    Route::post('support-tickets', [SupportTicketController::class, 'store'])->name('support-tickets.store');
    Route::get('support-tickets/{supportTicket}', [SupportTicketController::class, 'show'])->name('support-tickets.show');
    Route::get('notifications', [SupportTicketController::class, 'notifications'])->name('notifications.index');
    Route::put('notifications/{notification}/read', [SupportTicketController::class, 'markNotification'])->name('notifications.read');
    Route::put('support-tickets/{supportTicket}', [SupportTicketController::class, 'update'])->name('support-tickets.update');
    Route::post('support-tickets/{supportTicket}/comment', [SupportTicketController::class, 'comment'])->name('support-tickets.comment');
});

require __DIR__.'/auth.php';
