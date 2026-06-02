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
        'orden_pago_id',
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

    public function formasPagosCada()
    {
        return $this->hasMany(FormasPagosCada::class, 'pago_id', 'id');
    }

    public function incidencia()
    {
        return $this->hasOne(IncidenciaPago::class, 'pago_id', 'id');
    }

    public function ordenPago()
    {
        return $this->belongsTo(OrdenPago::class, 'orden_pago_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}
