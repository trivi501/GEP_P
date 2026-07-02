<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatEstadoConvenio extends Model
{
    protected $table = 'cat_estado_convenio';
    protected $primaryKey = 'id_cat_estado_convenio';
    public $timestamps = false;

    protected $fillable = ['descripcion'];
}
