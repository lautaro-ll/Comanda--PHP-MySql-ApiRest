<?php
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Encuesta as Encuesta;

class EncuestaController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {  
    $parametros = $request->getParsedBody();
    if (isset($parametros['codigo-mesa']) && isset($parametros['cliente']) && isset($parametros['calif-mesa']) && isset($parametros['calif-resto']) && isset($parametros['calif-mozo']) && isset($parametros['calif-cocinero']) && isset($parametros['experiencia'])) {
      $codigoMesa = $parametros['codigo-mesa'];
      $cliente = $parametros['cliente'];
      $mesa = $parametros['calif-mesa'];
      $resto = $parametros['calif-resto'];
      $mozo = $parametros['calif-mozo'];
      $cocinero = $parametros['calif-cocinero'];
      $experiencia = $parametros['experiencia'];

      $nuevaEncuesta = new Encuesta();
      $nuevaEncuesta->codigo_mesa = $codigoMesa;
      $nuevaEncuesta->cliente = $cliente;
      $nuevaEncuesta->calif_mesa = $mesa;
      $nuevaEncuesta->calif_resto = $resto;
      $nuevaEncuesta->calif_mozo = $mozo;
      $nuevaEncuesta->calif_cocinero = $cocinero;
      $nuevaEncuesta->experiencia = $experiencia;
      $nuevaEncuesta->save();
  
      $payload = json_encode(array("mensaje" => "Encuesta cargada con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Faltan datos"));
    }
  
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  public function TraerUno($request, $response, $args)
  {
    $id = $args['id'];
    $e = new Encuesta();
    $encuesta = $e->find($id);
    $payload = json_encode($encuesta);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Encuesta::all();
    $payload = json_encode(array("listaEncuesta" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

	public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (isset($parametros['id']) && isset($parametros['codigo-mesa']) && isset($parametros['cliente']) && isset($parametros['calif-mesa']) && isset($parametros['calif-resto']) && isset($parametros['calif-mozo']) && isset($parametros['calif-cocinero']) && isset($parametros['experiencia']) && isset($parametros['id'])) {
      $codigoMesa = $parametros['codigo-mesa'];
      $cliente = $parametros['cliente'];
      $mesa = $parametros['calif-mesa'];
      $resto = $parametros['calif-resto'];
      $mozo = $parametros['calif-mozo'];
      $cocinero = $parametros['calif-cocinero'];
      $experiencia = $parametros['experiencia'];
      $id = $parametros['id'];

      $e = new Encuesta();
      $encuesta = $e->find($id);
      $encuesta->codigo_mesa = $codigoMesa;
      $encuesta->cliente = $cliente;
      $encuesta->calif_mesa = $mesa;
      $encuesta->calif_resto = $resto;
      $encuesta->calif_mozo = $mozo;
      $encuesta->calif_cocinero = $cocinero;
      $encuesta->experiencia = $experiencia;
      $encuesta->save();

        $payload = json_encode(array("mensaje" => "Encuesta modificada con exito"));
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

	public function BorrarUno($request, $response, $args) {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (isset($parametros['id'])) {
        $id = $parametros['id'];
      $e = new Encuesta();
      $e->find($id)->delete;
    
        $payload = json_encode(array("mensaje" => "Encuesta borrada con exito"));
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
  
  //9- De las mesas:
  //h- Mejores comentarios.
  public function MejoresComentarios($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      $lista = Encuesta::orderby('codigo_mesa','DESC')
      ->get();

      if(isset($lista)) {
        $mesa = $lista[0]["codigo_mesa"];
        $c=0;
        $s=0;

        foreach($lista as $line) {
          if($mesa == $line["codigo_mesa"]) {
            $s += $line["calif_mesa"];
            $s += $line["calif_resto"];
            $s += $line["calif_mozo"];
            $s += $line["calif_cocinero"];
            $c++;
          }
          else {
            $promedioGeneral = $s / (4* $c);
            if($promedioGeneral > 5) {
              $resultado[$mesa]=$promedioGeneral;
            }
            $mesa = $line["codigo_mesa"];
            $c=1;
            $s = $line["calif_mesa"];
            $s += $line["calif_resto"];
            $s += $line["calif_mozo"];
            $s += $line["calif_cocinero"];
          }
        }
        $promedioGeneral = $s / (4* $c);
        if($promedioGeneral > 5) {
          $resultado[$mesa]=$promedioGeneral;
        }

        $payload = json_encode(array("Lista" => $resultado));
      } else {
        $payload = json_encode(array("mensaje" => "No hay datos"));
      }
  } else {
    $payload = json_encode(array("mensaje" => "Usuario no autorizado"));
  }
  

  $response->getBody()->write($payload);
  return $response
    ->withHeader('Content-Type', 'application/json');
}
  //i- Peores comentarios.
  public function PeoresComentarios($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {

      $lista = Encuesta::orderby('codigo_mesa','DESC')
      ->get();

      if(isset($lista)) {
        $mesa = $lista[0]["codigo_mesa"];
        $c=0;
        $s=0;

        foreach($lista as $line) {
          if($mesa == $line["codigo_mesa"]) {
            $s += $line["calif_mesa"];
            $s += $line["calif_resto"];
            $s += $line["calif_mozo"];
            $s += $line["calif_cocinero"];
            $c++;
          }
          else {
            $promedioGeneral = $s / (4* $c);
            if($promedioGeneral < 5) {
              $resultado[$mesa]=$promedioGeneral;
            }
            $mesa = $line["codigo_mesa"];
            $c=1;
            $s = $line["calif_mesa"];
            $s += $line["calif_resto"];
            $s += $line["calif_mozo"];
            $s += $line["calif_cocinero"];
          }
        }
        $promedioGeneral = $s / (4* $c);
        if($promedioGeneral < 5) {
          $resultado[$mesa]=$promedioGeneral;
        }

        $payload = json_encode(array("Lista" => $resultado));
      } else {
        $payload = json_encode(array("mensaje" => "No hay datos"));
      }
  
  } else {
    $payload = json_encode(array("mensaje" => "Usuario no autorizado"));
  }
  

  $response->getBody()->write($payload);
  return $response
    ->withHeader('Content-Type', 'application/json');
}
}
?>

