<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatFormaPredio extends Model
{
    protected $table = 'cat_tipo_forma_predio';
    protected $primaryKey = 'id_forma_predio';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = ['id_forma_predio', 'descripcion', 'activo'];
}
