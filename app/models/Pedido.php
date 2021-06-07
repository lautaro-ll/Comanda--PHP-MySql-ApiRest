<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'pedidos';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'cliente', 'foto', 'codigoPedido', 'idMesa', 'idProducto', 'precio', 'idMozo', 'idUsuario', 'estado', 'tiempoEstimado', 'tiempoFinalizado', 'tiempoEntregado'
    ];

}
