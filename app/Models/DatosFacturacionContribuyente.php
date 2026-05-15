<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatosFacturacionContribuyente extends Model
{
    protected $table = 'tb_datos_facturacion_contribuyente';
    protected $primaryKey = 'id_datos_facturacion';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_datos_facturacion',
        'id_contribuyente',
        'rfc_facturacion',
        'razon_social',
        'id_domicilio_facturacion',
        'correo',
        'fecha_alta',
        'id_f4_c_regimenfiscal',
        'CP_DomicilioFiscal_contribuyente',
    ];

    public function contribuyente()
    {
        return $this->belongsTo(Contribuyente::class, 'id_contribuyente', 'id_contribuyente');
    }

    public function domicilioFacturacion()
    {
        return $this->belongsTo(Domicilio::class, 'id_domicilio_facturacion', 'id_domicilio');
    }
}
