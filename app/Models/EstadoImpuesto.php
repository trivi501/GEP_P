<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoImpuesto extends Model
{
    protected $table = 'cat_estado_impuesto';
    protected $primaryKey = 'id_estaus_cobro_predial';
    public $timestamps = false;

    protected $fillable = ['id_estaus_cobro_predial', 'ESTATUSIMP', 'DESCRIPCION', 'activo'];

    public function predios()
    {
        return $this->hasMany(Predio::class, 'id_estaus_cobro_predial', 'id_estaus_cobro_predial');
    }
}
