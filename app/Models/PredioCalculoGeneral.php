<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredioCalculoGeneral extends Model
{
    protected $table = 'tb_predio_calculo_general';
    protected $primaryKey = 'id_tb_predio_calculo_general';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tb_predio_calculo_general', 'id_predio', 'año', 'id_contribuyente',
        'cuenta', 'contribuyente', 'Clave_predial', 'Tipo_predio', 'Zona',
        'Ubicacion', 'superficie_solar', 'superficie_agostadero',
        'superficie_temporal', 'superficie_riego', 'superficie_urbano',
        'Superficie_texto', 'Superficie_construccion_texto', 'Total',
        'id_zona_predio', 'id_tipo_predio', 'valor_uma', 'factor_superficie',
    ];

    public function predio()
    {
        return $this->belongsTo(Predio::class, 'id_predio', 'id_predio');
    }
}
