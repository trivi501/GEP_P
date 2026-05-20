<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatUsoConstruccion extends Model
{
    protected $table = 'cat_uso_construccion';
    protected $primaryKey = 'id_uso_construccion';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_uso_construccion',
        'USO',
        'descripcion',
        'activo',
    ];

    public function tasasConstruccion()
    {
        return $this->hasMany(CatTasaXConstruccion::class, 'id_uso_construccion', 'id_uso_construccion');
    }

    public function nivelesConstruidos()
    {
        return $this->hasMany(NivelConstruidoPredioUrbano::class, 'id_uso_construccion', 'id_uso_construccion');
    }

    public function vinculacionesConstruccion()
    {
        return $this->hasMany(CatastroVinculacionConstruccion::class, 'id_uso_construccion', 'id_uso_construccion');
    }
}
