<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuentas extends Model
{
    protected $table = 'cuentas';

    protected $fillable = [
        'indetec',
        'nom_indetect',
        'cuenta',
        'subcuenta',
        'descripcion',
        'importe',
        'cuentaMayor_id',
        'indetecMayor_id',
        'conac_id',
    ];

    public function cuentaMayor()
    {
        return $this->belongsTo(Cuentas::class, 'cuentaMayor_id', 'id');
    }

    public function conac()
    {
        return $this->belongsTo(Conac::class, 'conac_id', 'id');
    }

    public function cuentasHijas()
    {
        return $this->hasMany(Cuentas::class, 'cuentaMayor_id', 'id');
    }

    public function secretarias()
    {
        return $this->belongsToMany(Secretaria::class, 'cuentas_por_secretaria', 'cuenta_id', 'secretaria_id');
    }
}
