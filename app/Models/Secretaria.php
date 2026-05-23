<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Secretaria extends Model
{
    protected $table = 'secretarias';

    protected $fillable = [
        'nombre',
        'prefijo',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'secretaria_id', 'id');
    }

    public function cuentasPorSecretaria()
    {
        return $this->hasMany(CuentasPorSecretaria::class, 'secretaria_id', 'id');
    }

    public function cuentas()
    {
        return $this->belongsToMany(Cuentas::class, 'cuentas_por_secretaria', 'secretaria_id', 'cuenta_id');
    }
}
