<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (isset($parametros['tipo']) && isset($parametros['producto']) && isset($parametros['tipoUsuario']) && isset($parametros['precio'])) {
        $tipo = $parametros['tipo'];
        $producto = $parametros['producto'];
        $tipoUsuario = $parametros['tipoUsuario'];
        $precio = $parametros['precio'];
    
        $nuevoProducto = new Producto();
        $nuevoProducto->tipo = $tipo;
        $nuevoProducto->producto = $producto;
        $nuevoProducto->tipoUsuario = $tipoUsuario;
        $nuevoProducto->precio = $precio;
        $nuevoProducto->crearProducto();
    
        $payload = json_encode(array("mensaje" => "Producto creado con exito"));
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
    $producto = Producto::obtenerProducto($id);
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
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (isset($parametros['tipo']) && isset($parametros['producto']) && isset($parametros['tipoUsuario']) && isset($parametros['precio']) && isset($parametros['id'])) {
        $tipo = $parametros['tipo'];
        $producto = $parametros['producto'];
        $tipoUsuario = $parametros['tipoUsuario'];
        $precio = $parametros['precio'];
        $id = $parametros['id'];
    
        $nuevoProducto = new Producto();
        $nuevoProducto->tipo = $tipo;
        $nuevoProducto->producto = $producto;
        $nuevoProducto->tipoUsuario = $tipoUsuario;
        $nuevoProducto->precio = $precio;
        $nuevoProducto->nombre = $id;
        $nuevoProducto->modificarProducto();
    
        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
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
        Producto::borrarProducto($id);
    
        $payload = json_encode(array("mensaje" => "Producto borrado con exito"));
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