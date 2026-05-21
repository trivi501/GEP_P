<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';
    public $timestamps = false;

    protected $fillable = [
        'monto',
        'descuento',
        'folio',
        'fecha',
        'estatus',
        'forma_pago',
        'tipo_pago',
        'nombre',
        'rfc',
        'descripcion',
        'id_predio',
        'id_contribuyente',
        'id_historial_caja',
        'id_usuario',
        'anio_pago',
        'im',
        'url_file',
    ];

    public function cuentasPagos()
    {
        return $this->hasMany(CuentasPagos::class, 'pago_id', 'id');
    }

    public function historialCaja()
    {
        return $this->belongsTo(HistorialCaja::class, 'id_historial_caja', 'id');
    }

    public function predio()
    {
        return $this->belongsTo(Predio::class, 'id_predio', 'id_predio');
    }
}
