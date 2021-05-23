<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['tipo']) && isset($parametros['nombre']) && isset($parametros['usuario']) && isset($parametros['clave']))
        {
          $tipo = $parametros['tipo'];
          $nombre = $parametros['nombre'];
          $usuario = $parametros['usuario'];
          $clave = $parametros['clave'];
  
          $usr = new Usuario();
          $usr->tipo = $tipo;
          $usr->nombre = $nombre;
          $usr->usuario = $usuario;
          $usr->clave = $clave;
          $usr->crearUsuario();
  
          $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        }
        else {
          $payload = json_encode(array("mensaje" => "Faltan datos"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $usr = $args['id'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosPorTipo($request, $response, $args)
    {
        $tipo = $args['tipo'];
        $lista = Usuario::obtenerUsuariosPorTipo($tipo);
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $tipo = $parametros['tipo'];
        $nombre = $parametros['nombre'];
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $id = $parametros['id'];

        $usr = new Usuario();
        $usr->tipo = $tipo;
        $usr->nombre = $nombre;
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $id->nombre = $id;
        $usr->modificarUsuario();

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        Usuario::borrarUsuario($id);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function Validar($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      if(isset($parametros['usuario']) && isset($parametros['clave']))
      {
        $usuarioIngresado = $parametros['usuario'];
        $claveIngresada = $parametros['clave'];
        $arrayUsuarios = Usuario::obtenerTodos();
        if(!is_null($arrayUsuarios)) 
        {
          foreach($arrayUsuarios as $usuario)
          {
            if($usuario->usuario == $usuarioIngresado) 
            {
                if($usuario->clave == $claveIngresada) 
                {
                  // Ej OK =>	user: "socio1", clave: "1234"
                  $token= AutentificadorJWT::CrearToken(array('usuario' => $usuario->usuario,'nombre' => $usuario->nombre, 'tipo' => $usuario->tipo)); 
                  $payload = json_encode($token);
                  break;
                }
                else {
                  $payload = json_encode(array("mensaje" => "Error en la clave"));
                  break;
                }
            }
            else {
              $payload = json_encode(array("mensaje" => "Usuario no registrado"));
              break;
            }
          }
        }
        else {
          $payload = json_encode(array("mensaje" => "Error de base de datos"));
        }
      } 
      else {
        $payload = json_encode(array("mensaje" => "Faltan datos"));
      }
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}
