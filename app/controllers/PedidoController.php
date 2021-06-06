<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
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

  public function ModificarUno($request, $response, $args) //En Preparacion() Listo para servir() -> BARTENDER-CERVECERO-COCINERO 
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado'])) { //CUALQUIER EMPLEADO ES VALIDO
      $accesoEmpleado = $parametros['accesoEmpleado'];
      if (isset($parametros['id']) && isset($parametros['estado']) && $accesoEmpleado=="Mozo" && $parametros['estado']=="Cancelar") { //CancelarPedido -> MOZO
        $estado = $parametros['estado'];
        $id = $parametros['id'];
    
        $nuevoPedido = new Pedido();
        $nuevoPedido->obtenerPedido($id);
        $nuevoPedido->estado = $estado;
        $nuevoPedido->modificarPedido();
    
        $payload = json_encode(array("mensaje" => "Pedido cancelado con exito"));
      } else if (isset($parametros['id']) && isset($parametros['estado']) && ($accesoEmpleado=="Bartender" || $accesoEmpleado=="Cervecero" || $accesoEmpleado=="Cocinero") && $parametros['estado']=="En Preparacion") {
        $estado = $parametros['estado'];
        $id = $parametros['id'];
    
        $nuevoPedido = new Pedido();
        $nuevoPedido->obtenerPedido($id);
        $nuevoPedido->estado = $estado;
        $nuevoPedido->tiempoEstimado = new DateTime("NOW");
        $nuevoPedido->tiempoEstimado->format("Y-m-d H:i:s");
        $nuevoPedido->modificarPedido();
    
        $payload = json_encode(array("mensaje" => "Pedido seleccionado en preparación"));
      } else if(isset($parametros['id']) && isset($parametros['estado']) && ($accesoEmpleado=="Bartender" || $accesoEmpleado=="Cervecero" || $accesoEmpleado=="Cocinero") && $parametros['estado']=="Listo para servir") {
        $estado = $parametros['estado'];
        $id = $parametros['id'];
    
        $nuevoPedido = new Pedido();
        $nuevoPedido->obtenerPedido($id);
        $nuevoPedido->estado = $estado;
        $nuevoPedido->tiempoFinalizado = new DateTime("NOW");
        $nuevoPedido->tiempoFinalizado->format("Y-m-d H:i:s");
        $nuevoPedido->modificarPedido();
    
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
    $lista = Pedido::obtenerPorCargo($cargo);
    $payload = json_encode(array("listaPedido" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTiempo($request, $response, $args)
  {
    $pedido = $args['pedido'];
    $lista = Pedido::obtenerPedido($pedido);

    $tiempoEstimado = 0;
    for($i=0; $i<sizeof($lista); $i++)
    {
        if($lista[$i]["estado"]=="en preparacion" && $tiempoEstimado < $lista[$i]["tiempoEstimado"]) {
          $tiempoEstimado = $lista[$i]["tiempoEstimado"];
        }
    }
    $payload = json_encode(array("tiempoEstimado" => $tiempoEstimado));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CargarEncuesta($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if (isset($parametros['codigo-mesa']) && isset($parametros['codigo-pedido']) && isset($parametros['calif-mesa']) 
    && isset($parametros['calif-resto']) && isset($parametros['calif-mozo']) && isset($parametros['calif-cocinero']) && isset($parametros['experiencia'])) {
      $codigoMesa = $parametros['codigo-mesa'];
      $codigoPedido = $parametros['codigo-pedido'];
      $mesa = $parametros['calif-mesa'];
      $resto = $parametros['calif-resto'];
      $mozo = $parametros['calif-mozo'];
      $cocinero = $parametros['calif-cocinero'];
      $experiencia = $parametros['experiencia'];

      $nuevaEncuesta = new Encuesta();
      $nuevaEncuesta->codigoMesa = $codigoMesa;
      $nuevaEncuesta->codigoPedido = $codigoPedido;
      $nuevaEncuesta->mesa = $mesa;
      $nuevaEncuesta->resto = $resto;
      $nuevaEncuesta->mozo = $mozo;
      $nuevaEncuesta->cocinero = $cocinero;
      $nuevaEncuesta->experiencia = $experiencia;
      $nuevaEncuesta->crearEncuesta();
  
      $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Faltan datos"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

}

?>