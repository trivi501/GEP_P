<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConvenioPago extends Model
{
    protected $table = 'tb_convenio_pagos';
    protected $primaryKey = 'id_convenio_pago';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_convenio_pago',
        'id_convenio',
        'numero_cuota',
        'monto',
        'fecha_pago',
        'id_pago',
        'pagado',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_pago' => 'date',
            'pagado' => 'boolean',
        ];
    }

    public function convenio()
    {
        return $this->belongsTo(ConvenioMaster::class, 'id_convenio', 'id_convenio');
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'id_pago');
    }
}
