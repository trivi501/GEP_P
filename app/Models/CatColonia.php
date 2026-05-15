<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatColonia extends Model
{
    protected $table = 'cat_colonia';
    protected $primaryKey = 'id_colonia';
    public $timestamps = false;

    protected $fillable = ['id_colonia', 'COLONIA', 'fecha_alta', 'ID_USUARIO', 'Activo', 'id_poblacion', 'id_cat_zona_predio'];
}
