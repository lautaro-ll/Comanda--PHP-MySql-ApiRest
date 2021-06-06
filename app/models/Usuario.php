<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'usuarios';
    public $incrementing = true;
    //public $timestamps = false;

    const DELETED_AT = 'fecha-baja';
    const CREATED_AT = 'fecha-alta';

    protected $fillable = [
        'cargo', 'nombre', 'alias', 'clave', 'habilitado'
    ];

}

?>