<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatPavimento extends Model
{
    protected $table = 'cat_pavimento';
    protected $primaryKey = 'id_pavimientacion';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = ['id_pavimientacion', 'UE_PAVI', 'DESCRIPCION', 'activo'];
}
