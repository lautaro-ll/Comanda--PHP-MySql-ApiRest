<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigoIdentificacion = $parametros['codigoIdentificacion'];
        $codigoPedido = $parametros['codigoPedido'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        $estado = $parametros['estado'];

        $msa = new Mesa();
        $msa->codigoIdentificacion = $codigoIdentificacion;
        $msa->codigoPedido = $codigoPedido;
        $msa->tiempoEstimado = $tiempoEstimado;
        $msa->estado = $estado;
        $msa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

        $id = $parametros['id'];
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

        $codigoIdentificacion = $parametros['codigoIdentificacion'];
        $codigoPedido = $parametros['codigoPedido'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        $estado = $parametros['estado'];
        $id = $parametros['id'];

        $msa = new Mesa();
        $msa->codigoIdentificacion = $codigoIdentificacion;
        $msa->codigoPedido = $codigoPedido;
        $msa->tiempoEstimado = $tiempoEstimado;
        $msa->estado = $estado;
        $msa->nombre = $id;
        $msa->modificarMesa();

        $payload = json_encode(array("mensaje" => "Mesa modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        Mesa::borrarMesa($id);

        $payload = json_encode(array("mensaje" => "Mesa borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

?>