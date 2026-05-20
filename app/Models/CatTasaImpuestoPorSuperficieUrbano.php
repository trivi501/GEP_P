<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatTasaImpuestoPorSuperficieUrbano extends Model
{
    protected $table = 'cat_tasa_impuesto_por_superficie_urbano';
    protected $primaryKey = 'id_cat_tasa_impuesto_por_superficie_urbano';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_cat_tasa_impuesto_por_superficie_urbano',
        'ANIO',
        'id_zona_urbana',
        'tasa',
        'fecha_alta',
    ];
}
