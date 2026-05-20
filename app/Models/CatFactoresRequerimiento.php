<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatFactoresRequerimiento extends Model
{
    protected $table = 'cat_factores_requerimiento';
    protected $primaryKey = 'id_cat_factores_requerimiento';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_cat_factores_requerimiento',
        'año',
        'factor',
        'uma_minimo',
        'id_usuario_alta',
        'fecha_alta',
        'activo',
    ];
}
