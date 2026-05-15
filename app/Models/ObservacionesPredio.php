<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObservacionesPredio extends Model
{
    protected $table = 'tb_observaciones_predio';
    protected $primaryKey = 'id_observacion';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_observacion', 'id_predio', 'observacion', 'fecha_registro', 'id_usuario',
    ];

    public function predio()
    {
        return $this->belongsTo(Predio::class, 'id_predio', 'id_predio');
    }
}
