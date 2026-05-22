<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidenciaPago extends Model
{
    protected $table = 'incidencias_pagos';

    protected $fillable = [
        'pago_id',
        'id_predio',
        'año_ultimo_pago_anterior',
        'ultimo_bimestre_pago_anterior',
        'snapshot',
    ];

    protected function casts(): array
    {
        return [
            'snapshot' => 'array',
        ];
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id', 'id');
    }
}