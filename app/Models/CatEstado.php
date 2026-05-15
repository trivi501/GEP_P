<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatEstado extends Model
{
    protected $table = 'cat_estado';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    protected $fillable = ['id_estado', 'nombre_estado', 'activo'];

    public function municipios()
    {
        return $this->hasMany(CatMunicipio::class, 'id_estado', 'id_estado');
    }
}
