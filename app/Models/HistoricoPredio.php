<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoPredio extends Model
{
    protected $table = 'tb_historico_predios';
    protected $primaryKey = 'id_historico';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_historico', 'id_predio', 'campo_modificado', 'valor_anterior',
        'valor_nuevo', 'id_usuario_modifica', 'fecha_modificacion', 'tipo_operacion',
    ];

    public function predio()
    {
        return $this->belongsTo(Predio::class, 'id_predio', 'id_predio');
    }

    public function usuarioModifica()
    {
        return $this->belongsTo(User::class, 'id_usuario_modifica', 'id');
    }
}
