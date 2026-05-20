<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagosMaster extends Model
{
    protected $table = 'tb_pagos_master';
    protected $primaryKey = 'id_pago_guid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_pago_guid',
        'folio_pago',
        'anio_pago',
        'folio_recibo',
        'id_contribuyente',
        'id_forma_de_pago',
        'fecha_pago',
        'notas',
        'cta_banco',
        'timbrado',
        'id_factura',
        'id_tipo_pago',
        'sub_total_pago',
        'total_descuento',
        'total_pago',
        'id_usuario_registra',
        'fecha_registro',
        'cantidad_recibida',
        'area_usuario_registra',
        'id_session_pago',
        'id_cat_estado_pago',
        'folio_completo',
        'contribuyente',
        'control_recibo',
    ];

    public function contribuyente()
    {
        return $this->belongsTo(Contribuyente::class, 'id_contribuyente', 'id_contribuyente');
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'id_forma_de_pago', 'id');
    }

    public function formasPagoDetalle()
    {
        return $this->hasMany(PagoFormaPago::class, 'id_tb_pagos_master', 'id_pago_guid');
    }

    public function tipoPago()
    {
        return $this->belongsTo(TipoContribuyente::class, 'id_tipo_pago', 'id_tipo_contribuyente');
    }
}
