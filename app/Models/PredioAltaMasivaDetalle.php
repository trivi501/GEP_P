<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredioAltaMasivaDetalle extends Model
{
    protected $table = 'tb_predio_alta_masiva_detalle';
    protected $primaryKey = 'id_tb_predio_alta_masiva_detalle';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tb_predio_alta_masiva_detalle', 'id_tb_predio_alta_masiva',
        'id_poblacion', 'id_seccion', 'id_manzana', 'id_lote', 'subLote',
        'id_colonia', 'id_calle', 'Numero_exterior',
        'superficie_terreno_metros_cuadrados', 'id_forma_predio',
        'id_contribuyente', 'id_predio_origen', 'Frente_metros',
        'Fondo_metros', 'error', 'mensaje',
    ];

    public function altaMasiva()
    {
        return $this->belongsTo(PredioAltaMasiva::class, 'id_tb_predio_alta_masiva', 'id_tb_predio_alta_masiva');
    }

    public function colindancias()
    {
        return $this->hasMany(PredioAltaMasivaDetalleColindancia::class, 'id_tb_predio_alta_masiva_detalle', 'id_tb_predio_alta_masiva_detalle');
    }
}
