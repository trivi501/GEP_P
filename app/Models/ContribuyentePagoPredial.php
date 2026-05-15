<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContribuyentePagoPredial extends Model
{
    protected $table = 'tb_contribuyente_pago_predial';
    protected $primaryKey = 'id_contribuytente_pago_predial';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_contribuyente_predio',
        'id_pago',
    ];

    public function contribuyente()
    {
        return $this->belongsTo(Contribuyente::class, 'id_contribuyente_predio', 'id_contribuyente');
    }
}
