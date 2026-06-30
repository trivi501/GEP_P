<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenPago extends Model
{
    protected $table = 'ordenes_pago';

    protected $fillable = [
        'folio',
        'nombre',
        'descripcion',
        'monto',
        'descuento_porcentaje',
        'pagado',
        'fecha_pago',
        'fecha',
        'fecha_vencimiento',
        'secretaria_id',
        'userid',
    ];

    public function cuentasOrdenesPago()
    {
        return $this->hasMany(CuentaOrdenPago::class, 'orden_pago_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userid', 'id');
    }

    public function secretaria()
    {
        return $this->belongsTo(Secretaria::class, 'secretaria_id', 'id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'orden_pago_id', 'id');
    }
}
