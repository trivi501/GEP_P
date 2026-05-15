<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calle extends Model
{
    protected $table = 'cat_calle';
    protected $primaryKey = 'id_calle';
    public $timestamps = false;

    protected $fillable = ['id_calle', 'CALLE', 'ID_COLONIA', 'fecha_alta', 'id_usuario', 'Activo'];

    public function predios()
    {
        return $this->hasMany(Predio::class, 'id_calle', 'id_calle');
    }
}
