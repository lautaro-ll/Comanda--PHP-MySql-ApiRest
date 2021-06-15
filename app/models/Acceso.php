<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Acceso extends Model
{
    //use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'accesos';
    public $incrementing = true;
    //public $timestamps = false;

    const CREATED_AT = 'horario';

    protected $fillable = [
        'usuario_id'
    ];

}

?>