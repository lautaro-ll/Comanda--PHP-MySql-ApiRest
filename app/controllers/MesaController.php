<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
          if (isset($parametros['codigoIdentificacion']) && isset($parametros['codigoPedido']) && isset($parametros['estado'])) {
            $codigoIdentificacion = $parametros['codigoIdentificacion'];
            $codigoPedido = $parametros['codigoPedido'];
            $estado = $parametros['estado'];
    
            $nuevaMesa = new Mesa();
            $nuevaMesa->codigoIdentificacion = $codigoIdentificacion;
            $nuevaMesa->codigoPedido = $codigoPedido;
            $nuevaMesa->estado = $estado;
            $nuevaMesa->crearMesa();
    
            $payload = json_encode(array("mensaje" => "Mesa creado con exito"));
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
        $mesa = Mesa::obtenerMesa($id);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
          if (isset($parametros['codigoIdentificacion']) && isset($parametros['codigoPedido']) && isset($parametros['estado']) && isset($parametros['id'])) {
            $codigoIdentificacion = $parametros['codigoIdentificacion'];
            $codigoPedido = $parametros['codigoPedido'];
            $estado = $parametros['estado'];
            $id = $parametros['id'];

            $nuevaMesa = new Mesa();
            $nuevaMesa->codigoIdentificacion = $codigoIdentificacion;
            $nuevaMesa->codigoPedido = $codigoPedido;
            $nuevaMesa->estado = $estado;
            $nuevaMesa->nombre = $id;
            $nuevaMesa->modificarMesa();
    
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

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
          if (isset($parametros['id'])) {
            $id = $parametros['id'];
            Mesa::borrarMesa($id);

            $payload = json_encode(array("mensaje" => "Mesa borrado con exito"));
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

    public function CambiarEstado($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['accesoEmpleado']))
        {
          if (isset($parametros['id']) && isset($parametros['estado'])) 
          {
            $id = $parametros['id'];
            $estado = $parametros['estado'];
            $accesoEmpleado = $parametros['accesoEmpleado'];

            if($accesoEmpleado=="socio" || ($accesoEmpleado=="mozo" && ($estado=="con cliente esperando pedido" || $estado=="con cliente comiendo" || $estado=="con cliente pagando"))) {
              $nuevaMesa = new Mesa();
              $nuevaMesa->obtenerMesa($id);
              $nuevaMesa->estado = $estado;
              $nuevaMesa->ModificarMesa();
            }

            $payload = json_encode(array("mensaje" => "Estado cambiado con exito"));
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
}

?>