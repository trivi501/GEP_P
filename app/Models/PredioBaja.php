<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredioBaja extends Model
{
    protected $table = 'tb_predio_de_baja';
    protected $primaryKey = 'id_predio';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_predio', 'Clave_predial', 'id_colonia', 'id_calle', 'ubicacion',
        'codigo_postal', 'Numero_exterior', 'Numero_interior',
        'Referencia_entre_calle1', 'Referncia_entre_calle2',
        'id_zona_catastral', 'valor_catastral', 'valor_fiscal',
        'id_estaus_cobro_predial', 'id_estado_renta', 'id_regimen_propiedad',
        'numero_de_escritura', 'id_titulo_propiedad', 'fecha_de_alta',
        'año_ultimo_pago', 'id_clave_predial', 'fecha_registro',
        'latidud', 'longitud', 'id_tipo_predio', 'id_usuario',
        'id_contribuyente', 'observaciones_baja', 'id_usuario_baja',
        'fecha_baja_predio', 'id_cat_motivo_baja_predio',
    ];
}
