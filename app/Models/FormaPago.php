<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormaPago extends Model
{
    protected $table = 'f4_c_formapago';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'c_FormaPago',
        'Descripción',
        'Bancarizado',
        'Número de operación',
        'RFC del Emisor de la cuenta ordenante',
        'Cuenta Ordenante',
        'Patrón para cuenta ordenante',
        'RFC del Emisor Cuenta de Beneficiario',
        'Cuenta de Benenficiario',
        'Patrón para cuenta Beneficiaria',
        'Tipo Cadena Pago',
        'Nombre del Banco emisor de la cuenta ordenante en caso de extran',
        'activo',
    ];

    public function pagosMaster()
    {
        return $this->hasMany(PagosMaster::class, 'id_forma_de_pago', 'id');
    }
}
