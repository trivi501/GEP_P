<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    protected $table = 'descuentos';

    protected $fillable = [
        'idPredio',
        'idUser',
        'multas',
        'actualizaciones',
        'gastos_cobranza',
        'fecha_expiracion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'multas' => 'decimal:2',
            'actualizaciones' => 'decimal:2',
            'gastos_cobranza' => 'decimal:2',
            'fecha_expiracion' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function predio()
    {
        return $this->belongsTo(Predio::class, 'idPredio', 'id_predio');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }
}
