<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatCalle extends Model
{
    protected $table = 'cat_calle';
    protected $primaryKey = 'id_calle';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['id_calle', 'CALLE', 'ID_COLONIA', 'fecha_alta', 'id_usuario', 'Activo'];

    public function getRouteKeyName()
    {
        return 'id_calle';
    }

    public function colonia()
    {
        return $this->belongsTo(CatColonia::class, 'ID_COLONIA', 'id_colonia');
    }
}
