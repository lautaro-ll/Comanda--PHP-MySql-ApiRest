<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $producto = $parametros['producto'];
        $descripcion = $parametros['descripcion'];
        $usuario = $parametros['usuario'];
        $precio = $parametros['precio'];

        $prd = new Producto();
        $prd->producto = $producto;
        $prd->descripcion = $descripcion;
        $prd->usuario = $usuario;
        $prd->precio = $precio;
        $prd->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $prd = $args['id'];
        $producto = Producto::obtenerProducto($prd);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $producto = $parametros['producto'];
        $descripcion = $parametros['descripcion'];
        $usuario = $parametros['usuario'];
        $precio = $parametros['precio'];
        $id = $parametros['id'];

        $prd = new Producto();
        $prd->producto = $producto;
        $prd->descripcion = $descripcion;
        $prd->usuario = $usuario;
        $prd->precio = $precio;
        $prd->nombre = $id;
        $prd->modificarProducto();

        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        Producto::borrarProducto($id);

        $payload = json_encode(array("mensaje" => "Producto borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

?>