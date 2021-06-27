<?php
require_once './models/Usuario.php';
require_once './models/Acceso.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Usuario as Usuario;
use \App\Models\Acceso as Acceso;

class UsuarioController implements IApiUsable
{
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
              $token = AutentificadorJWT::CrearToken(array('alias' => $usuario->alias, 'nombre' => $usuario->nombre, 'cargo' => $usuario->cargo, 'id' => $usuario->id));
              $registro = new Acceso();
              $registro->usuario_id = $usuario->id;
              $registro->save();
              $payload = json_encode(array("token" => $token));
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

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
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
    $u = new Usuario();
    $usuario = $u->find($id);
    $payload = json_encode($usuario);

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

        $u = new Usuario();
        $usuario = $u -> find($id);

        $usuario->cargo = $cargo;
        $usuario->nombre = $nombre;
        $usuario->alias = $alias;
        $usuario->clave = $clave;
        $usuario->habilitado = $habilitado;
        $usuario->save();
    
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

  public function TraerIngresos($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (isset($parametros['desde']) && isset($parametros['hasta'])) {
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $lista = Acceso::whereBetween('horario', [$desde, $hasta])->get();
        $payload = json_encode(array("listaAccesos" => $lista));
    
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

/*
7- De los empleados:
a- Los días y horarios que se Ingresaron al sistema. -/
e- Posibilidad de dar de alta a nuevos, suspenderlos o borrarlos. -/
*/

}

?>