<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatOrientacion extends Model
{
    protected $table = 'cat_orientacion';
    protected $primaryKey = 'id_orientacion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['id_orientacion', 'ORIENTA', 'descripcion', 'activo'];
}
