<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Usuario as Usuario;

class UsuarioController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if ((isset($parametros['cargo']) && isset($parametros['nombre']) && isset($parametros['alias']) && isset($parametros['clave']))) {
        $cargo = $parametros['cargo'];
        $nombre = $parametros['nombre'];
        $alias = $parametros['alias'];
        $clave = $parametros['clave'];
  
        $nuevoUsuario = new Usuario();
        $nuevoUsuario->cargo = $cargo;
        $nuevoUsuario->nombre = $nombre;
        $nuevoUsuario->alias = $alias;
        $nuevoUsuario->clave = $clave;
        $nuevoUsuario->crearUsuario();
  
        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
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
    $usuario = Usuario::obtenerUsuario($id);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodosPorCargo($request, $response, $args)
  {
    $cargo = $args['cargo'];
    $lista = Usuario::obtenerPorCargo($cargo);
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::all();
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (($parametros['cargo']) && isset($parametros['nombre']) && isset($parametros['alias']) && isset($parametros['clave'])) {
        $cargo = $parametros['cargo'];
        $nombre = $parametros['nombre'];
        $alias = $parametros['alias'];
        $clave = $parametros['clave'];
        $id = $parametros['id'];
    
        $nuevoUsuario = new Usuario();
        $nuevoUsuario->cargo = $cargo;
        $nuevoUsuario->nombre = $nombre;
        $nuevoUsuario->alias = $alias;
        $nuevoUsuario->clave = $clave;
        $id->nombre = $id;
        $nuevoUsuario->modificarUsuario();
    
        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
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
      if (isset($parametros['id'])) {
        $id = $parametros['id'];
        Usuario::borrarUsuario($id);
        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
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

  public function Validar($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if (isset($parametros['alias']) && isset($parametros['clave'])) {
      $usuarioIngresado = $parametros['alias'];
      $claveIngresada = $parametros['clave'];
      $arrayUsuarios = Usuario::obtenerTodos();
      if (!is_null($arrayUsuarios)) {
        foreach ($arrayUsuarios as $usuario) {
          if ($usuario->alias == $usuarioIngresado) {
            if ($usuario->clave == $claveIngresada) {
              // Ej OK =>	user: "socio1", clave: "1234"
              $token = AutentificadorJWT::CrearToken(array('alias' => $usuario->alias, 'nombre' => $usuario->nombre, 'cargo' => $usuario->cargo));
              $payload = json_encode($token);
              break;
            } else {
              $payload = json_encode(array("mensaje" => "Error en la clave"));
            }
          } else {
            $payload = json_encode(array("mensaje" => "Usuario no registrado"));
          }
        }
      } else {
        $payload = json_encode(array("mensaje" => "Error de base de datos"));
      }
    } else {
      $payload = json_encode(array("mensaje" => "Faltan datos"));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}

?>