<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatZonaPredio extends Model
{
    protected $table = 'cat_zona_predio';
    protected $primaryKey = 'id_zona_urbana';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = ['id_zona_urbana', 'descripcion', 'activo'];
}
