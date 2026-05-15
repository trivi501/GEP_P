<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegimenPropiedad extends Model
{
    protected $table = 'cat_regimen_propiedad';
    protected $primaryKey = 'id_regimen_propiedad';
    public $timestamps = false;

    protected $fillable = ['id_regimen_propiedad', 'ID_REG_cat_anterior', 'REGIMEN', 'activo'];

    public function predios()
    {
        return $this->hasMany(Predio::class, 'id_regimen_propiedad', 'id_regimen_propiedad');
    }
}
