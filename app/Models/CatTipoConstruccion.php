<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatTipoConstruccion extends Model
{
    protected $table = 'cat_tipo_construccion';
    protected $primaryKey = 'id_tipo_construccion';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_construccion',
        'tipo',
        'descripcion',
        'activo',
    ];

    public function tasasConstruccion()
    {
        return $this->hasMany(CatTasaXConstruccion::class, 'id_tipo_construccion', 'id_tipo_construccion');
    }

    public function nivelesConstruidos()
    {
        return $this->hasMany(NivelConstruidoPredioUrbano::class, 'id_tipo_construccion', 'id_tipo_construccion');
    }

    public function vinculacionesConstruccion()
    {
        return $this->hasMany(CatastroVinculacionConstruccion::class, 'id_tipo_construccion', 'id_tipo_construccion');
    }
}
