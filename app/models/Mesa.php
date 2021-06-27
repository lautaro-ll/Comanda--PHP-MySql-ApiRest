<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model
{
    //use SoftDeletes;

    protected $primaryKey = 'id_mesas';
    protected $table = 'mesas';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'codigo_identificacion', 'codigo_pedido', 'estado'
    ];

}
