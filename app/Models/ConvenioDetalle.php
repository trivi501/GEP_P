<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConvenioDetalle extends Model
{
    protected $table = 'tb_convenio_detalle';
    protected $primaryKey = 'id_detalle_convenio';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_detalle_convenio',
        'id_convenio',
        'consecutivo',
        'fecha_vence',
        'id_pago',
        'fecha_pago',
        'monto',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_vence' => 'datetime',
            'fecha_pago' => 'datetime',
            'monto' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    public function convenio()
    {
        return $this->belongsTo(ConvenioMaster::class, 'id_convenio', 'id_convenio');
    }
}
