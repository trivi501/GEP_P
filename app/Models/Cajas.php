<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cajas extends Model
{
    protected $table = 'cajas';

    protected $fillable = [
        'nombre',
        'ubicacion',
        'folio',
        'status',
    ];

    public function cajeros()
    {
        return $this->hasMany(Cajero::class, 'caja_id', 'id');
    }
}
