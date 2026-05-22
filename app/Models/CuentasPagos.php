<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentasPagos extends Model
{
    protected $table = 'cuentas_pagos';
    public $timestamps = false;

    protected $fillable = [
        'pago_id',
        'cuenta_id',
        'concepto',
        'fecha_registro',
        'cantidad',
        'monto',
        'concepto_id',
    ];

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuentas::class, 'cuenta_id', 'id');
    }
}
