<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conac extends Model
{
    protected $table = 'conac';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'created',
    ];

    public function cuentasPagos()
    {
        return $this->hasMany(CuentasPagos::class, 'concepto_id', 'id');
    }
}
