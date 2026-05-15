<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnotacionesPredio extends Model
{
    protected $table = 'tb_anotaciones_predio';
    protected $primaryKey = 'id_anotacion';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_anotacion', 'id_predio', 'fecha_registro', 'id_area_realiza_anotacion',
        'usuario_realiza_anotacion', 'nota', 'id_status_anotacion', 'contestacion',
        'usuario_contesta', 'fecha_contestacion', 'usuario_termina', 'fecha_termina', 'activo',
    ];

    public function predio()
    {
        return $this->belongsTo(Predio::class, 'id_predio', 'id_predio');
    }
}
