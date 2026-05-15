<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPredio extends Model
{
    protected $table = 'cat_tipo_predio';
    protected $primaryKey = 'id_tipo_predio';
    public $timestamps = false;

    protected $fillable = ['id_tipo_predio', 'id_tipo_predio_cat_anterior', 'Tipo_predio', 'activo'];

    public function predios()
    {
        return $this->hasMany(Predio::class, 'id_tipo_predio', 'id_tipo_predio');
    }
}
