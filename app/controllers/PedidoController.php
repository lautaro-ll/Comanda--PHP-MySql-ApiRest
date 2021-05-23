<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && ($parametros['accesoEmpleado']=="socio") || ($parametros['accesoEmpleado']=="mozo")) {
      if (($parametros['cargo']) && isset($parametros['nombre']) && isset($parametros['alias']) && isset($parametros['clave'])) {
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
        $nuevoPedido->codigoPedido = $codigoPedido;
        $nuevoPedido->idMesa = $idMesa;
        $nuevoPedido->idProducto = $idProducto;
        $nuevoPedido->precio = $precio;
        $nuevoPedido->idMozo = $idMozo;
        $nuevoPedido->estado = "pendiente";
        $nuevoPedido->crearPedido();
    
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
    $pedido = Pedido::obtenerPedido($id);
    $payload = json_encode($pedido);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::obtenerTodos();
    $payload = json_encode(array("listaPedido" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado'])) { //CUALQUIER EMPLEADO ES VALIDO
      if (($parametros['cargo']) && isset($parametros['nombre']) && isset($parametros['alias']) && isset($parametros['clave'])) {
        $cliente = $parametros['cliente'];
        $foto = $parametros['foto'];
        $codigoPedido = $parametros['codigoPedido'];
        $idMesa = $parametros['idMesa'];
        $idProducto = $parametros['idProducto'];
        $precio = $parametros['precio'];
        $idUsuario = $parametros['idUsuario'];
        $estado = $parametros['estado'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        $tiempoFinalizado = $parametros['tiempoFinalizado'];
        $tiempoEntregado = $parametros['tiempoEntregado'];
        $id = $parametros['id'];
    
        $nuevoPedido = new Pedido();
        $nuevoPedido->cliente = $cliente;
        $nuevoPedido->foto = $foto;
        $nuevoPedido->codigoPedido = $codigoPedido;
        $nuevoPedido->idMesa = $idMesa;
        $nuevoPedido->idProducto = $idProducto;
        $nuevoPedido->precio = $precio;
        $nuevoPedido->idUsuario = $idUsuario;
        $nuevoPedido->estado = $estado;
        $nuevoPedido->tiempoEstimado = $tiempoEstimado;
        $nuevoPedido->tiempoFinalizado = $tiempoFinalizado;
        $nuevoPedido->tiempoEntregado = $tiempoEntregado;
        $nuevoPedido->nombre = $id;
        $nuevoPedido->modificarPedido();
    
        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
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

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (($parametros['cargo']) && isset($parametros['nombre']) && isset($parametros['alias']) && isset($parametros['clave'])) {
        $id = $parametros['id'];
        Pedido::borrarPedido($id);
    
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
    var_dump($cargo);
    $lista = Pedido::obtenerPorCargo($cargo);
    var_dump($lista);
    $payload = json_encode(array("listaPedido" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

}

?>