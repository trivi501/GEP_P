<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PagoFormaPago extends Model
{
    protected $table = 'tb_pago_forma_pago';
    protected $primaryKey = 'id_tb_pago_forma_pago';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_tb_pagos_master',
        'id_f4_c_formapago',
        'monto',
        'vinculado',
    ];

    public function pagoMaster()
    {
        return $this->belongsTo(PagosMaster::class, 'id_tb_pagos_master', 'id_pago_guid');
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'id_f4_c_formapago', 'id');
    }
}
