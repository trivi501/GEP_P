<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatEstadoFisicoPredio extends Model
{
    protected $table = 'cat_estado_fisico_predio';
    protected $primaryKey = 'id_estado_fisico';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = ['id_estado_fisico', 'U_ESTFIS', 'DESCRIPCION', 'activo'];
}
