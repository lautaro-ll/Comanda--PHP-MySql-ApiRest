<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Producto as Producto;

class ProductoController implements IApiUsable
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
        $nuevoProducto->save();
    
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
    $p = new Producto();
    $producto = $p->find($id);
    $payload = json_encode($producto);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Producto::all();
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
    
        $p = new Producto();
        $producto = $p->find($id);


        $producto->tipo = $tipo;
        $producto->producto = $producto;
        $producto->tipoUsuario = $tipoUsuario;
        $producto->precio = $precio;
        $producto->save();
    
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

        $p = new Producto();
        $p->find($id)->delete();
    
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