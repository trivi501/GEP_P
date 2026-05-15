<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredioAltaMasivaDetalleColindancia extends Model
{
    protected $table = 'tb_predio_alta_masiva_detalle_colindancias';
    protected $primaryKey = 'id_tb_predio_alta_masiva_detalle_colindancias';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tb_predio_alta_masiva_detalle_colindancias',
        'id_tb_predio_alta_masiva_detalle', 'id_cat_orientacion',
        'medida_en_metros', 'colinda_con', 'orden', 'id_calle',
    ];

    public function detalle()
    {
        return $this->belongsTo(PredioAltaMasivaDetalle::class, 'id_tb_predio_alta_masiva_detalle', 'id_tb_predio_alta_masiva_detalle');
    }
}
