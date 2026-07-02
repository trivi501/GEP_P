<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConvenioMaster extends Model
{
    protected $table = 'tb_convenio_master';
    protected $primaryKey = 'id_convenio';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_convenio',
        'folio_convenio',
        'anio_convenio',
        'fecha_convenio',
        'descripcion_convenio',
        'total_plazos',
        'monto_convenio',
        'id_periodicidad',
        'fecha_vence',
        'id_usuario',
        'activo',
        'fecha_registro',
        'pagado',
        'id_contribuyente',
        'id_cat_estado_convenio',
        'pagado_pagos',
        'pagado_monto',
        'vencido_pagos',
        'vencido_monto',
        'fecha_proximo_pago',
        'id_origen_convenio',
        'dom_contrib_convenio',
    ];

    protected function casts(): array
    {
        return [
            'fecha_convenio' => 'datetime',
            'fecha_vence' => 'datetime',
            'fecha_registro' => 'datetime',
            'fecha_proximo_pago' => 'date',
            'monto_convenio' => 'decimal:2',
            'pagado_monto' => 'decimal:2',
            'vencido_monto' => 'decimal:2',
            'activo' => 'boolean',
            'pagado' => 'boolean',
        ];
    }

    public function contribuyente()
    {
        return $this->belongsTo(Contribuyente::class, 'id_contribuyente', 'id_contribuyente');
    }

    public function estadoConvenio()
    {
        return $this->belongsTo(CatEstadoConvenio::class, 'id_cat_estado_convenio', 'id_cat_estado_convenio');
    }

    public function periodicidad()
    {
        return $this->belongsTo(CatPeriodicidadConvenio::class, 'id_periodicidad', 'id_periodicidad');
    }

    public function pagos()
    {
        return $this->hasMany(ConvenioPago::class, 'id_convenio', 'id_convenio');
    }

    public function detalle()
    {
        return $this->hasMany(ConvenioDetalle::class, 'id_convenio', 'id_convenio');
    }
}
