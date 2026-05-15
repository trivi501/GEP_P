<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaAltaContribuyente extends Model
{
    protected $table = 'cat_area_alta_contribuyente';
    protected $primaryKey = 'id_area_alta_contribuyente';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_area_alta_contribuyente',
        'id_tipo_usuario',
        'id_tipo_contribuyente',
        'activo',
    ];

    public function tipoContribuyente()
    {
        return $this->belongsTo(TipoContribuyente::class, 'id_tipo_contribuyente', 'id_tipo_contribuyente');
    }
}
