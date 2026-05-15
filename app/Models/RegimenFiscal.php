<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegimenFiscal extends Model
{
    protected $table = 'f4_c_regimenfiscal';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['id', 'c_RegimenFiscal', 'Descripción', 'Física', 'Moral', 'Fecha de inicio de vigencia', 'Fecha de fin de vigencia', 'activo'];
}
