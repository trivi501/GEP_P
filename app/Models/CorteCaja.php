<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorteCaja extends Model
{
    protected $table = 'corte_cajas';

    protected $fillable = [
        'fecha',
        'ingresos',
        'urbano',
        'rustico',
        'recibos_efectivos',
        'recibos_cancelados',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'ingresos' => 'decimal:2',
            'urbano' => 'decimal:2',
            'rustico' => 'decimal:2',
        ];
    }

    public function historialCajas()
    {
        return $this->hasMany(HistorialCaja::class, 'cortecaja_id', 'id');
    }
}
