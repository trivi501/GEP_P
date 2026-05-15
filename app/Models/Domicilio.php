<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domicilio extends Model
{
    protected $table = 'tb_domicilio';
    protected $primaryKey = 'id_domicilio';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_domicilio',
        'id_pais',
        'id_estado',
        'id_municipio',
        'localidad',
        'colonia',
        'id_tipo_vialidad',
        'nombre_vialidad',
        'num_interior',
        'num_exterior',
        'codigo_postal',
        'activo',
        'domicilio_completo',
    ];

    public function pais()
    {
        return $this->belongsTo(CatPais::class, 'id_pais', 'id_pais');
    }

    public function estado()
    {
        return $this->belongsTo(CatEstado::class, 'id_estado', 'id_estado');
    }

    public function municipio()
    {
        return $this->belongsTo(CatMunicipio::class, 'id_municipio', 'id_municipio');
    }
}
