<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Predio extends Model
{
    protected $table = 'tb_predio';
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
        'latitud', 'longitud', 'id_tipo_predio', 'id_usuario',
        'id_contribuyente', 'ultimo_bimestre_pago', 'superficie',
        'construccion', 'importe_adeudado', 'id_cat_catastro_vinculacion_estado',
        'id_tb_catastro_vinculacion_detalle', 'colindancias', 'gid_tb_cartografia_predio',
    ];

    public function contribuyente()
    {
        return $this->belongsTo(Contribuyente::class, 'id_contribuyente', 'id_contribuyente');
    }

    public function tipoPredio()
    {
        return $this->belongsTo(TipoPredio::class, 'id_tipo_predio', 'id_tipo_predio');
    }

    public function regimenPropiedad()
    {
        return $this->belongsTo(RegimenPropiedad::class, 'id_regimen_propiedad', 'id_regimen_propiedad');
    }

    public function estadoRenta()
    {
        return $this->belongsTo(EstadoRenta::class, 'id_estado_renta', 'id_estado_renta');
    }

    public function estadoImpuesto()
    {
        return $this->belongsTo(EstadoImpuesto::class, 'id_estaus_cobro_predial', 'id_estaus_cobro_predial');
    }

    public function tituloPropiedad()
    {
        return $this->belongsTo(TituloPropiedad::class, 'id_titulo_propiedad', 'id_titulo_propiedad');
    }

    public function calle()
    {
        return $this->belongsTo(Calle::class, 'id_calle', 'id_calle');
    }

    public function colonia()
    {
        return $this->belongsTo(CatColonia::class, 'id_colonia', 'id_colonia');
    }

    public function pagosPredial()
    {
        return $this->hasMany(ContribuyentePagoPredial::class, 'id_contribuyente_predio', 'id_predio');
    }

    public function calculosGenerales()
    {
        return $this->hasMany(PredioCalculoGeneral::class, 'id_predio', 'id_predio');
    }

    public function clavePredial()
    {
        return $this->belongsTo(ClavePredial::class, 'id_clave_predial', 'id_clave_predial');
    }

    public function medidasYColindancias()
    {
        return $this->hasMany(MedidasYColindancias::class, 'id_predio', 'id_predio');
    }

    public function anotaciones()
    {
        return $this->hasMany(AnotacionesPredio::class, 'id_predio', 'id_predio');
    }

    public function observaciones()
    {
        return $this->hasMany(ObservacionesPredio::class, 'id_predio', 'id_predio');
    }

    public function datosUrbano()
    {
        return $this->hasOne(DatosPredioUrbano::class, 'id_predio', 'id_predio');
    }

    public function datosRustico()
    {
        return $this->hasOne(DatosPredioRustico::class, 'id_predio', 'id_predio');
    }

    public function historico()
    {
        return $this->hasMany(HistoricoPredio::class, 'id_predio', 'id_predio');
    }

    public function nivelesConstruidos()
    {
        return $this->hasMany(NivelConstruidoPredioUrbano::class, 'id_predio', 'id_predio');
    }

    public function descuentos()
    {
        return $this->hasMany(Descuento::class, 'idPredio', 'id_predio');
    }

    public function getUbicacionCompletaAttribute()
    {
        $parts = [];
        if ($this->calle) {
            $parts[] = $this->calle->CALLE;
        }
        if ($this->Numero_exterior) {
            $parts[] = '#' . $this->Numero_exterior;
        }
        if ($this->Numero_interior) {
            $parts[] = 'Int. ' . $this->Numero_interior;
        }
        if ($this->colonia) {
            $parts[] = $this->colonia->COLONIA;
        }
        if ($this->codigo_postal) {
            $parts[] = 'C.P. ' . $this->codigo_postal;
        }
        return implode(', ', $parts) ?: $this->ubicacion ?? '—';
    }
}
