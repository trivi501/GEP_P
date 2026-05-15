<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoContribuyente extends Model
{
    protected $table = 'cat_tipo_contribuyente';
    protected $primaryKey = 'id_tipo_contribuyente';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_contribuyente',
        'area_contribuyente',
        'activo',
        'descripcion',
        'id_adscripcion',
    ];

    public function contribuyentes()
    {
        return $this->hasMany(Contribuyente::class, 'id_tipo_contribuyente', 'id_tipo_contribuyente');
    }
}
