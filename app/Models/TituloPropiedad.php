<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TituloPropiedad extends Model
{
    protected $table = 'cat_titulo_propiedad';
    protected $primaryKey = 'id_titulo_propiedad';
    public $timestamps = false;

    protected $fillable = ['id_titulo_propiedad', 'TIT_PROP', 'DESCRIPCION', 'activo'];

    public function predios()
    {
        return $this->hasMany(Predio::class, 'id_titulo_propiedad', 'id_titulo_propiedad');
    }
}
