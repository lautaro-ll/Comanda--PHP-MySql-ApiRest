<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    //use SoftDeletes;

    protected $primaryKey = 'id_pedidos';
    protected $table = 'pedidos';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'cliente', 'foto', 'codigo_pedido', 'mesa_id', 'producto_id', 'precio', 'mozo_id', 'usuario_id', 'estado', 'tiempo_pedido', 'tiempo_aceptado', 'tiempo_estimado', 'tiempo_finalizado', 'tiempo_entregado'
    ];

}
