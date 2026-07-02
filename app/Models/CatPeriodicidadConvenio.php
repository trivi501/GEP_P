<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatPeriodicidadConvenio extends Model
{
    protected $table = 'cat_periodicidad_convenio';
    protected $primaryKey = 'id_periodicidad';
    public $timestamps = false;

    protected $fillable = ['periodicidad', 'sumar', 'unidad', 'activo'];
}
