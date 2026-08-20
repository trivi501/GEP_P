<?php

use App\Http\Controllers\Api\PagoApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('pagos', [PagoApiController::class, 'index']);
    Route::get('pagos/{folio}', [PagoApiController::class, 'show']);
});
