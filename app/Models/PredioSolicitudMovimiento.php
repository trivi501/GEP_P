<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredioSolicitudMovimiento extends Model
{
    protected $table = 'tb_predio_solicitud_movimiento';
    protected $primaryKey = 'id_tb_predio_solicitud_movimiento';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tb_predio_solicitud_movimiento', 'id_tb_predio', 'id_tb_usuarios',
        'id_cat_predio_solicitud_movimiento', 'id_cat_estado_impuesto_anterior',
        'fecha_solicitud', 'id_cat_predio_solicitud_movimiento_estado', 'motivo',
    ];

    public function detalles()
    {
        return $this->hasMany(PredioSolicitudMovimientoDetalle::class, 'id_tb_predio_solicitud_movimiento', 'id_tb_predio_solicitud_movimiento');
    }

    public function predio()
    {
        return $this->belongsTo(Predio::class, 'id_tb_predio', 'id_predio');
    }
}
