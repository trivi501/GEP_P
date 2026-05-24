<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialCaja extends Model
{
    protected $table = 'historial_caja';
    public $timestamps = false;

    protected $fillable = [
        'fondo',
        'total_ingreso',
        'datetime_apertura',
        'datetime_cierre',
        'cajero_id',
        'caja_id',
        'cortecaja_id',
    ];

    public function cajero()
    {
        return $this->belongsTo(Cajero::class, 'cajero_id', 'id_cajero');
    }

    public function caja()
    {
        return $this->belongsTo(Cajas::class, 'caja_id', 'id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_historial_caja', 'id');
    }
}
