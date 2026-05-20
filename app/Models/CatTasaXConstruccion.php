<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatTasaXConstruccion extends Model
{
    protected $table = 'cat_tasa_x_construccion';
    protected $primaryKey = 'id_cat_taza_x_contruccion';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_cat_taza_x_contruccion',
        'fecha_alta',
        'activo',
        'factor',
        'fecha_alta_registro',
        'id_tipo_construccion',
        'id_uso_construccion',
        'año',
    ];
}
