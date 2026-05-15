<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoRenta extends Model
{
    protected $table = 'cat_estado_renta';
    protected $primaryKey = 'id_estado_renta';
    public $timestamps = false;

    protected $fillable = ['id_estado_renta', 'RENTADO', 'DESCRIPCION', 'activo'];

    public function predios()
    {
        return $this->hasMany(Predio::class, 'id_estado_renta', 'id_estado_renta');
    }
}
