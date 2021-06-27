<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id_usuarios';
    protected $table = 'usuarios';
    public $incrementing = true;
    //public $timestamps = false;

    const DELETED_AT = 'fecha_baja';
    const CREATED_AT = 'fecha_alta';
    const UPDATED_AT = 'fecha_modificado';

    protected $fillable = [
        'cargo', 'nombre', 'alias', 'clave', 'habilitado'
    ];

}

?>