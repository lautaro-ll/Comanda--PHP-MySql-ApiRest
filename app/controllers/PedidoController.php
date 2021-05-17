<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $cliente = $parametros['cliente'];
        $foto = $parametros['foto'];
        $codigoPedido = $parametros['codigoPedido'];
        $idMesa = $parametros['idMesa'];
        $idProducto = $parametros['idProducto'];
        $precio = $parametros['precio'];

        $pdd = new Pedido();
        $pdd->cliente = $cliente;
        $pdd->foto = $foto;
        $pdd->codigoPedido = $codigoPedido;
        $pdd->idMesa = $idMesa;
        $pdd->idProducto = $idProducto;
        $pdd->precio = $precio;
        $pdd->estado = "solicitado";
        $pdd->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $pdd = $args['id'];
        $pedido = Pedido::obtenerPedido($pdd);
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

        $pdd = new Pedido();
        $pdd->cliente = $cliente;
        $pdd->foto = $foto;
        $pdd->codigoPedido = $codigoPedido;
        $pdd->idMesa = $idMesa;
        $pdd->idProducto = $idProducto;
        $pdd->precio = $precio;
        $pdd->idUsuario = $idUsuario;
        $pdd->estado = $estado;
        $pdd->tiempoEstimado = $tiempoEstimado;
        $pdd->tiempoFinalizado = $tiempoFinalizado;
        $pdd->tiempoEntregado = $tiempoEntregado;
        $pdd->nombre = $id;
        $pdd->modificarPedido();

        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        Pedido::borrarPedido($id);

        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

?>