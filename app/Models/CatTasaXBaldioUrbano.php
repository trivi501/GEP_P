<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatTasaXBaldioUrbano extends Model
{
    protected $table = 'cat_tasa_x_baldio_urbano';
    protected $primaryKey = 'id_cat_tasa_x_baldio_urbano';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_cat_tasa_x_baldio_urbano',
        'ANIO',
        'id_zona_urbana',
        'tasa',
    ];
}
