<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    //use SoftDeletes;

    protected $primaryKey = 'id_productos';
    protected $table = 'productos';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'tipo', 'producto', 'tipo_usuario', 'precio', 'demora'
    ];

}