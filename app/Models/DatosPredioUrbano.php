<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatosPredioUrbano extends Model
{
    protected $table = 'tb_datos_predio_urbano';
    protected $primaryKey = 'id_predio';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_predio', 'id_zona_urbana', 'numero_de_pisos_construidos',
        'superficie_terreno_metros_cuadrados', 'Frente_metros', 'Fondo_metros',
        'Baldio', 'id_forma_predio', 'id_uso_predio', 'id_estado_fisico',
        'servicio_agua', 'servicio_drenaje', 'servicio_energia_electrica',
        'servicio_alumbrado', 'id_pavimientacion', 'cuenta_con_banqueta',
        'valor_catastral_terreno', 'valor_catastral_construido',
    ];

    public function predio()
    {
        return $this->belongsTo(Predio::class, 'id_predio', 'id_predio');
    }

    public function zonaUrbana()
    {
        return $this->belongsTo(CatZonaPredio::class, 'id_zona_urbana', 'id_zona_urbana');
    }

    public function formaPredio()
    {
        return $this->belongsTo(CatFormaPredio::class, 'id_forma_predio', 'id_forma_predio');
    }

    public function usoPredio()
    {
        return $this->belongsTo(CatUsoPredioUrbano::class, 'id_uso_predio', 'id_uso_predio');
    }

    public function estadoFisico()
    {
        return $this->belongsTo(CatEstadoFisicoPredio::class, 'id_estado_fisico', 'id_estado_fisico');
    }

    public function pavimento()
    {
        return $this->belongsTo(CatPavimento::class, 'id_pavimientacion', 'id_pavimientacion');
    }
}
