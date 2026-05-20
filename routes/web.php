<?php

use App\Http\Controllers\ClavePredialController;
use App\Http\Controllers\ContribuyenteController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PredioController;
use App\Http\Controllers\CalculosPrediosController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::get('contribuyentes/search', [ContribuyenteController::class, 'search'])->name('contribuyentes.search');
    Route::resource('contribuyentes', ContribuyenteController::class);
    Route::post('clave-predial/store', [ClavePredialController::class, 'store'])->name('clave-predial.store');
    Route::get('predios/clave-predial-search', [PredioController::class, 'searchClavePredial'])->name('predios.clave-predial-search');
    Route::get('predios/calle-search', [PredioController::class, 'searchCalle'])->name('predios.calle-search');
    Route::get('predios/colonia-search', [PredioController::class, 'searchColonia'])->name('predios.colonia-search');
    Route::get('predios/{predio}/pdf', [PredioController::class, 'pdf'])->name('predios.pdf');
    Route::resource('predios', PredioController::class);
    Route::get('calculos-predios', [CalculosPrediosController::class, 'index'])->name('calculos-predios.index');
    Route::get('calculos-predios/pdf', [CalculosPrediosController::class, 'pdf'])->name('calculos-predios.pdf');
    Route::get('calculos-predios/pdf-rustico', [CalculosPrediosController::class, 'pdfRustico'])->name('calculos-predios.pdf-rustico');
    Route::post('calculos-predios/calculo-predial-urbano', [CalculosPrediosController::class, 'calculoPredialUrbano'])->name('calculos-predios.calculo-predial-urbano');
    Route::get('caja', [CajaController::class, 'index'])->name('caja.index');
    Route::get('caja/create', [CajaController::class, 'create'])->name('caja.create');
    Route::post('caja', [CajaController::class, 'store'])->name('caja.store');
    Route::get('caja/search-contribuyente', [CajaController::class, 'searchContribuyente'])->name('caja.search-contribuyente');
    Route::get('caja/search-clave-catastral', [CajaController::class, 'searchClaveCatastral'])->name('caja.search-clave-catastral');
    Route::get('caja/get-contribuyente', [CajaController::class, 'getContribuyente'])->name('caja.get-contribuyente');
    Route::get('caja/calcular-urbano', [CajaController::class, 'calcularUrbano'])->name('caja.calcular-urbano');
    Route::get('caja/calcular-rustico', [CajaController::class, 'calcularRustico'])->name('caja.calcular-rustico');
});

require __DIR__.'/auth.php';
