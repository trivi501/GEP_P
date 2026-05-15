<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContribuyenteDetalleAltaBaja extends Model
{
    protected $table = 'tb_contribuyente_detalle_alta_baja';
    protected $primaryKey = 'id_tb_contribuyente_detalle_alta_baja';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tb_contribuyente_detalle_alta_baja',
        'id_tb_contribuyente',
        'id_usuario',
        'fecha_movimiento',
        'motivo_movimiento',
        'movimiento',
    ];

    public function contribuyente()
    {
        return $this->belongsTo(Contribuyente::class, 'id_tb_contribuyente', 'id_contribuyente');
    }
}
