<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredioAltaMasiva extends Model
{
    protected $table = 'tb_predio_alta_masiva';
    protected $primaryKey = 'id_tb_predio_alta_masiva';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tb_predio_alta_masiva', 'fecha_movimiento', 'fecha_aviso',
        'causa', 'notaria', 'numero_acta', 'volumen', 'notas_acta',
    ];

    public function detalles()
    {
        return $this->hasMany(PredioAltaMasivaDetalle::class, 'id_tb_predio_alta_masiva', 'id_tb_predio_alta_masiva');
    }
}
