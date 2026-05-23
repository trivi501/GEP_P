<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaOrdenPago extends Model
{
    protected $table = 'cuentas_ordenes_pago';
    public $timestamps = false;

    protected $fillable = [
        'orden_pago_id',
        'IdCuenta',
        'monto',
        'cantidad',
        'descuento',
        'created',
    ];

    public function ordenPago(): BelongsTo
    {
        return $this->belongsTo(OrdenPago::class, 'orden_pago_id', 'id');
    }

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuentas::class, 'IdCuenta', 'id');
    }
}
