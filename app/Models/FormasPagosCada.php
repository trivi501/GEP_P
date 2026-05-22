<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormasPagosCada extends Model
{
    protected $table = 'formas_pagos_cada';
    public $timestamps = false;

    protected $fillable = [
        'pago_id',
        'forma_pago_id',
        'monto',
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'forma_pago_id');
    }
}
