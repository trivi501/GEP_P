<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedidasYColindancias extends Model
{
    protected $table = 'tb_medidas_y_colindancias';
    protected $primaryKey = 'id_medida_colindacion';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_medida_colindacion', 'id_predio', 'id_orientacion',
        'medida_en_metros', 'colinda_con', 'fecha_alta', 'id_usuario',
    ];

    public function orientacion()
    {
        return $this->belongsTo(CatOrientacion::class, 'id_orientacion', 'id_orientacion');
    }
}
