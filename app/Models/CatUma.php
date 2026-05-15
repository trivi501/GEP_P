<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatUma extends Model
{
    protected $table = 'cat_uma';
    protected $primaryKey = 'id_cat_uma';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_cat_uma',
        'anio',
        'valor',
        'activo',
        'fecha_alta',
        'id_usuario_alta',
        'minimo_urbano',
        'minimo_rustico',
    ];
}
