<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Pedido as Pedido;

class PedidoController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && ($parametros['accesoEmpleado']=="socio") || ($parametros['accesoEmpleado']=="mozo")) {
      if (isset($parametros['cliente']) && isset($parametros['foto']) && isset($parametros['codigoPedido']) && isset($parametros['idMesa']) && isset($parametros['idProducto']) && isset($parametros['precio']) && isset($parametros['idMozo'])) {
        $cliente = $parametros['cliente'];
        $foto = $parametros['foto'];
        $codigoPedido = $parametros['codigoPedido'];
        $idMesa = $parametros['idMesa'];
        $idProducto = $parametros['idProducto'];
        $precio = $parametros['precio'];
        $idMozo = $parametros['idMozo'];

        $nuevoPedido = new Pedido();
        $nuevoPedido->cliente = $cliente;
        $nuevoPedido->foto = $foto;
        $nuevoPedido->codigo_pedido = $codigoPedido;
        $nuevoPedido->mesa_id = $idMesa;
        $nuevoPedido->producto_id = $idProducto;
        $nuevoPedido->precio = $precio;
        $nuevoPedido->mozo_id = $idMozo;
        $nuevoPedido->estado = "pendiente";
        $nuevoPedido->save();
    
        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Faltan datos"));
      }
    } else {
      $payload = json_encode(array("mensaje" => "Usuario no autorizado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $id = $args['id'];
    $p = new Pedido();
    $pedido = $p->find($id);
    $payload = json_encode($pedido);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::all();
    $payload = json_encode(array("listaPedido" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args) //En Preparacion() Listo para servir() -> BARTENDER-CERVECERO-COCINERO 
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado'])) { //CUALQUIER EMPLEADO ES VALIDO
      $accesoEmpleado = $parametros['accesoEmpleado'];
      if (isset($parametros['id']) && isset($parametros['estado']) && $accesoEmpleado=="Mozo" && $parametros['estado']=="Cancelar") { //CancelarPedido -> MOZO
        $estado = $parametros['estado'];
        $id = $parametros['id'];
    
        $p = new Pedido();
        $pedido = $p->find($id);
        $pedido->obtenerPedido($id);
        $pedido->estado = $estado;
        $pedido->save();
    
        $payload = json_encode(array("mensaje" => "Pedido cancelado con exito"));
      } else if (isset($parametros['id']) && isset($parametros['estado']) && ($accesoEmpleado=="Bartender" || $accesoEmpleado=="Cervecero" || $accesoEmpleado=="Cocinero") && $parametros['estado']=="En Preparacion") {
        $estado = $parametros['estado'];
        $id = $parametros['id'];
    
        $p = new Pedido();
        $pedido = $p->find($id);
        $pedido->estado = $estado;
        $pedido->tiempoEstimado = new DateTime("NOW");
        $pedido->tiempoEstimado->format("Y-m-d H:i:s");
        $pedido->save();
    
        $payload = json_encode(array("mensaje" => "Pedido seleccionado en preparación"));
      } else if(isset($parametros['id']) && isset($parametros['estado']) && ($accesoEmpleado=="Bartender" || $accesoEmpleado=="Cervecero" || $accesoEmpleado=="Cocinero") && $parametros['estado']=="Listo para servir") {
        $estado = $parametros['estado'];
        $id = $parametros['id'];
    
        $p = new Pedido();
        $pedido = $p->find($id);
        $pedido->estado = $estado;
        $pedido->tiempoFinalizado = new DateTime("NOW");
        $pedido->tiempoFinalizado->format("Y-m-d H:i:s");
        $pedido->save();
    
        $payload = json_encode(array("mensaje" => "Pedido seleccionado listo para servir"));        
      }
      else {
        $payload = json_encode(array("mensaje" => "Faltan datos o son erróneos"));
      }
    } else {
      $payload = json_encode(array("mensaje" => "Usuario no autorizado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (isset($parametros['id'])) {
        $id = $parametros['id'];
        $p = new Pedido();
        $p->find($id)->delete();
        
        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Faltan datos"));
      }
    } else {
      $payload = json_encode(array("mensaje" => "Usuario no autorizado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPendientes($request, $response, $args)
  {
    $cargo = $args['cargo'];
    $lista = Pedido::join('productos', 'pedidos.producto_id', '=', 'productos.id')
                ->where('productos.tipo_usuario',$cargo)->get();

    $payload = json_encode(array("listaPedido" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTiempo($request, $response, $args)
  {
    $pedido = $args['pedido'];
    $p = new Pedido();
    $lista = $p->where('codigo_pedido',$pedido)->get();

    $tiempoEstimado = 0;
    for($i=0; $i<sizeof($lista); $i++)
    {
        if($lista[$i]["estado"]=="en preparacion" && $tiempoEstimado < $lista[$i]["tiempo_estimado"]) {
          $tiempoEstimado = $lista[$i]["tiempo_estimado"];
        }
    }
    $payload = json_encode(array("tiempoEstimado" => $tiempoEstimado));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

}

?> 
