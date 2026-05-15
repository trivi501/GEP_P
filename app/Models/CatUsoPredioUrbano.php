<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatUsoPredioUrbano extends Model
{
    protected $table = 'cat_uso_predio_urbano';
    protected $primaryKey = 'id_uso_predio';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = ['id_uso_predio', 'UE_USOPRE', 'descripcion', 'activo'];
}
