<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribuyente extends Model
{
    protected $table = 'tb_contribuyentes';
    protected $primaryKey = 'id_contribuyente';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_contribuyente',
        'nombre',
        'primer_apellido',
        'segundo_apellido',
        'curp_contribuyente',
        'telefono',
        'correo_electronico',
        'fecha_alta',
        'id_user_registra',
        'id_domicilio',
        'id_tipo_contribuyente',
        'rfc',
        'activo',
        'id_tipo_persona',
        'nombre_moral',
        'cuenta',
        'exento',
        'nombre_completo',
        'nivel_gobierno',
        'id_cat_persona_genero',
    ];

    public function tipoContribuyente()
    {
        return $this->belongsTo(TipoContribuyente::class, 'id_tipo_contribuyente', 'id_tipo_contribuyente');
    }

    public function domicilio()
    {
        return $this->belongsTo(Domicilio::class, 'id_domicilio', 'id_domicilio');
    }

    public function datosFacturacion()
    {
        return $this->hasMany(DatosFacturacionContribuyente::class, 'id_contribuyente', 'id_contribuyente');
    }

    public function detallesAltaBaja()
    {
        return $this->hasMany(ContribuyenteDetalleAltaBaja::class, 'id_tb_contribuyente', 'id_contribuyente');
    }

    public function pagosPredial()
    {
        return $this->hasMany(ContribuyentePagoPredial::class, 'id_contribuyente_predio', 'id_contribuyente');
    }

    public function predios()
    {
        return $this->hasMany(Predio::class, 'id_contribuyente', 'id_contribuyente');
    }
}
