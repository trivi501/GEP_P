<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cajero extends Model
{
    protected $table = 'cajeros';
    protected $primaryKey = 'id_cajero';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'caja_id',
        'status',
        'created',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    public function caja()
    {
        return $this->belongsTo(Cajas::class, 'caja_id', 'id');
    }
}
