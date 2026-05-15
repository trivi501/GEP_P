<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClavePredial extends Model
{
    protected $table = 'tb_clave_predial';
    protected $primaryKey = 'id_clave_predial';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_clave_predial', 'id_poblacion', 'id_seccion', 'id_manzana',
        'id_lote', 'subLote', 'Parcela', 'id_tipo_predio', 'prefijo',
        'clave_predial_completa', 'manzana_rustico', 'lote_rustico',
    ];

    public function predios()
    {
        return $this->hasMany(Predio::class, 'id_clave_predial', 'id_clave_predial');
    }
}
