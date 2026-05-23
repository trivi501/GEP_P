<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentasPorSecretaria extends Model
{
    protected $table = 'cuentas_por_secretaria';

    protected $fillable = [
        'secretaria_id',
        'cuenta_id',
    ];

    public function secretaria(): BelongsTo
    {
        return $this->belongsTo(Secretaria::class, 'secretaria_id', 'id');
    }

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuentas::class, 'cuenta_id', 'id');
    }
}
