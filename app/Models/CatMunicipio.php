<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatMunicipio extends Model
{
    protected $table = 'cat_municipio';
    protected $primaryKey = 'id_municipio';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = ['id_municipio', 'id_estado', 'nombre_municipio', 'activo'];

    public function estado()
    {
        return $this->belongsTo(CatEstado::class, 'id_estado', 'id_estado');
    }
}
