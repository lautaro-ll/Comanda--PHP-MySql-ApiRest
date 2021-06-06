<?php
require_once './models/Encuesta.php';
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController extends Encuesta implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    $id = $args['id'];
    $encuesta = Encuesta::obtenerEncuesta($id);
    $payload = json_encode($encuesta);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Encuesta::obtenerTodos();
    $payload = json_encode(array("listaEncuesta" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

	public function CargarUno($request, $response, $args)
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

	public function BorrarUno($request, $response, $args) {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (isset($parametros['id'])) {
        $id = $parametros['id'];
        Encuesta::borrarEncuesta($id);
    
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

	public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (isset($parametros['codigoMesa']) && isset($parametros['codigoPedido']) && isset($parametros['mesa']) && isset($parametros['resto']) && isset($parametros['mozo']) && isset($parametros['cocinero']) && isset($parametros['experiencia']) && isset($parametros['id'])) {
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
      $nuevaEncuesta->modificarEncuesta();

        $payload = json_encode(array("mensaje" => "Mesa modificado con exito"));
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
}
?>