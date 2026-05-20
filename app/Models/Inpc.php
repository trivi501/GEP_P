<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inpc extends Model
{
    protected $table = 'tb_inpc';
    public $timestamps = false;

    protected $fillable = [
        'year',
        'month',
        'value',
    ];
}
