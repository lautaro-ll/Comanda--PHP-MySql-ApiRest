<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Usuario as Usuario;

class UsuarioController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    var_dump("CARGARUNO");
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") { var_dump("ACCESO");
      if ((isset($parametros['cargo']) && isset($parametros['nombre']) && isset($parametros['alias']) && isset($parametros['clave']) && isset($parametros['habilitado']))) {
        $cargo = $parametros['cargo'];
        $nombre = $parametros['nombre'];
        $alias = $parametros['alias'];
        $clave = $parametros['clave'];
        $habilitado = $parametros['habilitado'];
  
        $nuevoUsuario = new Usuario();
        $nuevoUsuario->cargo = $cargo;
        $nuevoUsuario->nombre = $nombre;
        $nuevoUsuario->alias = $alias;
        $nuevoUsuario->clave = $clave;
        $nuevoUsuario->habilitado = $habilitado;
        $nuevoUsuario->save();
  
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
    $user = new Usuario();
    $usuario = $user->find($id);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodosPorCargo($request, $response, $args)
  {
    $cargo = $args['cargo'];
    $user = new Usuario();
    $lista = $user->where('cargo',$cargo)->get();
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
      if (($parametros['cargo']) && isset($parametros['nombre']) && isset($parametros['alias']) && isset($parametros['clave']) && isset($parametros['habilitado'])) {
        $cargo = $parametros['cargo'];
        $nombre = $parametros['nombre'];
        $alias = $parametros['alias'];
        $clave = $parametros['clave'];
        $id = $parametros['id'];
        $habilitado = $parametros['habilitado'];

        $user = new Usuario();
        $usuarioSolicitado = $user -> find($id);

        $usuarioSolicitado->cargo = $cargo;
        $usuarioSolicitado->nombre = $nombre;
        $usuarioSolicitado->alias = $alias;
        $usuarioSolicitado->clave = $clave;
        $usuarioSolicitado->habilitado = $habilitado;
        $usuarioSolicitado->save();
    
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

        $user = new Usuario();
        $user->find($id)->delete();

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
      $arrayUsuarios = Usuario::all();
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