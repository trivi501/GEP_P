<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelConstruidoPredioUrbano extends Model
{
    protected $table = 'tb_nivel_construido_predio_urbano';
    protected $primaryKey = 'id_nivel_construido';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_nivel_construido',
        'id_predio',
        'id_tipo_construccion',
        'id_uso_construccion',
        'calidad_construccion',
        'estado_construccion',
        'superficie_metros_cuadrados',
        'id_usuario',
        'fecha_alta',
        'activo',
    ];

    public function predio()
    {
        return $this->belongsTo(Predio::class, 'id_predio', 'id_predio');
    }

    public function tipoConstruccion()
    {
        return $this->belongsTo(CatTipoConstruccion::class, 'id_tipo_construccion', 'id_tipo_construccion');
    }

    public function usoConstruccion()
    {
        return $this->belongsTo(CatUsoConstruccion::class, 'id_uso_construccion', 'id_uso_construccion');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }
}
