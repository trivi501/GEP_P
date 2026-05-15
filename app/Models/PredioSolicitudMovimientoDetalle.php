<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredioSolicitudMovimientoDetalle extends Model
{
    protected $table = 'tb_predio_solicitud_movimiento_detalle';
    protected $primaryKey = 'id_tb_predio_solicitud_movimiento_detalle';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tb_predio_solicitud_movimiento_detalle',
        'id_tb_predio_solicitud_movimiento', 'id_tb_usuarios',
        'id_cat_predio_solicitud_movimiento_estado', 'fecha_movimiento',
        'observaciones',
    ];

    public function solicitud()
    {
        return $this->belongsTo(PredioSolicitudMovimiento::class, 'id_tb_predio_solicitud_movimiento', 'id_tb_predio_solicitud_movimiento');
    }
}
