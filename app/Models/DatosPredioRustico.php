<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatosPredioRustico extends Model
{
    protected $table = 'tb_datos_predio_rustico';
    protected $primaryKey = 'id_predio';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_predio',
        'id_grupo_rustico',
        'id_tipo_acceso',
        'servicio_electricidad',
        'tiene_pozo_de_agua',
        'tiene_presa',
        'tiene_bordo',
        'id_tipo_pedregosidad',
        'valor_catastral_superficie_riego',
        'valor_catastral_superficie_temporal',
        'valor_catastral_superficie_agostadero',
        'valor_catastral_superficie_solar',
        'valor_catastral_casa',
    ];

    public function predio()
    {
        return $this->belongsTo(Predio::class, 'id_predio', 'id_predio');
    }
}
