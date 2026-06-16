<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatPoblacion extends Model
{
    protected $table = 'cat_poblacion';
    protected $primaryKey = 'id_poblacion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['id_poblacion', 'POBLACION', 'id_usuario', 'fecha_alta', 'Activo', 'numero'];
}
