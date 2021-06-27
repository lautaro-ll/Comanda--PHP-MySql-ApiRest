<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encuesta extends Model
{
    //use SoftDeletes;

    protected $primaryKey = 'id_encuestas';
    protected $table = 'encuestas';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'codigo_mesa', 'codigo_pedido', 'calif_mesa', 'calif_resto', 'calif_mozo', 'calif_cocinero', 'experiencia'
    ];

}
