<?php
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Pedido as Pedido;
use \App\Models\Producto as Producto;
use \App\Models\Mesa as Mesa;

class PedidoController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $uploadedFiles = $request->getUploadedFiles();

    if(isset($parametros['accesoEmpleado']) && ($parametros['accesoEmpleado']=="socio") || ($parametros['accesoEmpleado']=="mozo")) {
      if (isset($parametros['cliente']) && isset($parametros['idMesa']) && isset($parametros['idProducto']) && isset($parametros['idUsuarioRegistrado'])) {
        $cliente = $parametros['cliente'];
        $idMesa = $parametros['idMesa']; 
        $idProducto = $parametros['idProducto'];
        $idMozo = $parametros['idUsuarioRegistrado'];

        $foto = $uploadedFiles['foto'];
        $destino = "./";
        $ruta = $destino.$idMesa."-".$cliente."-".$foto->getClientMediaType();
        if ($foto->getError() === UPLOAD_ERR_OK) {
          $foto->moveTo($ruta);
        }
        
        $m = new Mesa();
        $mesa = $m->find($idMesa);
        $lista = $m->all();
        $ok = false;

        if ($mesa->estado == "con cliente comiendo" || $mesa->estado == "cerrada") {
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $nuevoCodigo = substr(str_shuffle($permitted_chars.$permitted_chars.$permitted_chars.$permitted_chars.$permitted_chars),0,5);
            for($i=0;$i<sizeof($lista);$i++) {
              if($lista[$i]->codigo_pedido == $nuevoCodigo) {
                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $nuevoCodigo = substr(str_shuffle($permitted_chars.$permitted_chars.$permitted_chars.$permitted_chars.$permitted_chars),0,5);
                $i=0;
              }
            }
          $mesa->codigo_pedido = $nuevoCodigo;
          $mesa->estado = "con cliente esperando pedido";
          $ok = true;
        }
        else if ($mesa->estado == "con cliente esperando pedido") {
          $ok = true;
        } 

        if($ok) {
          $mesa->save();
  
          $nuevoPedido = new Pedido();
          $nuevoPedido->cliente = $cliente;
          $nuevoPedido->foto = $ruta;
          $nuevoPedido->codigo_pedido = $mesa->codigo_pedido;
          $nuevoPedido->mesa_id = $idMesa;
          $nuevoPedido->producto_id = $idProducto;
  
          $p = new Producto();
          $producto = $p->find($idProducto);
          $nuevoPedido->precio = $producto->precio;
  
          $nuevoPedido->mozo_id = $idMozo;
          $nuevoPedido->estado = "pendiente";
          $nuevoPedido->save();
  
      
          $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
        }
        else {
          $payload = json_encode(array("mensaje" => "Codigo inexistente o Falta resolver un pedido anterior"));
        }
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
    $p = new Pedido();
    $pedido = $p->find($id);
    $payload = json_encode($pedido);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::all();
    $payload = json_encode(array("listaPedido" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args) //En Preparacion() Listo para servir() -> BARTENDER-CERVECERO-COCINERO 
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado'])) { //CUALQUIER EMPLEADO ES VALIDO
      $accesoEmpleado = $parametros['accesoEmpleado'];
      if (isset($parametros['id']) && isset($parametros['estado']) && $accesoEmpleado=="mozo" && $parametros['estado']=="Cancelar") { //CancelarPedido -> MOZO
        $estado = $parametros['estado'];
        $id = $parametros['id'];
    
        $p = new Pedido();
        $pedido = $p->find($id);
        $pedido->obtenerPedido($id);
        $pedido->estado = $estado;
        $pedido->save();
    
        $payload = json_encode(array("mensaje" => "Pedido cancelado con exito"));
      } else if (isset($parametros['id']) && isset($parametros['estado']) && isset($parametros['idUsuarioRegistrado']) && 
      ($accesoEmpleado=="bartender" || $accesoEmpleado=="cervecero" || $accesoEmpleado=="cocinero") && $parametros['estado']=="En preparacion") {
        $estado = $parametros['estado'];
        $id = $parametros['id'];
        $usuario_id = $parametros['idUsuarioRegistrado'];
    
        $p = new Pedido();
        $pedido = $p->find($id);
        $pedido->estado = $estado;
        $pedido->usuario_id = $usuario_id;
        $pedido->tiempo_aceptado = new DateTime("NOW");
        $pedido->tiempo_aceptado->format("Y-m-d H:i:s");
        $p = new Producto();
        $producto = $p->find($pedido->producto_id);
        $demora = explode(":", $producto->demora);
        $now = new DateTime("NOW");
        $now->add(new DateInterval("PT".$demora[0]."H".$demora[1]."M".$demora[2]."S"));
        $pedido->tiempo_estimado = $now;
        $pedido->tiempo_estimado->format("Y-m-d H:i:s");

        $pedido->save();
    
        $payload = json_encode(array("mensaje" => "Pedido seleccionado en preparación"));
      } else if(isset($parametros['id']) && isset($parametros['estado']) && 
      ($accesoEmpleado=="bartender" || $accesoEmpleado=="cervecero" || $accesoEmpleado=="cocinero") && $parametros['estado']=="Listo para servir") {
        $estado = $parametros['estado'];
        $id = $parametros['id'];
    
        $p = new Pedido();
        $pedido = $p->find($id);
        $pedido->estado = $estado;
        $pedido->tiempo_finalizado = new DateTime("NOW");
        $pedido->tiempo_finalizado->format("Y-m-d H:i:s");
        $pedido->save();
    
        $payload = json_encode(array("mensaje" => "Pedido seleccionado listo para servir"));        
      } else if(isset($parametros['codigo_pedido']) && isset($parametros['estado']) && $accesoEmpleado=="mozo" && $parametros['estado']=="Servido") {
        $estado = $parametros['estado'];
        $codigo = $parametros['codigo_pedido'];

        $p = new Pedido();
        $lista = $p->where('codigo_pedido',$codigo)->get();
        $now = new DateTime("NOW");
        foreach($lista as $pedido) {
          $pedido->estado = $estado;
          $pedido->tiempo_entregado = $now;
          $pedido->tiempo_entregado->format("Y-m-d H:i:s");
          $pedido->save();
        }

        $m = new Mesa();
        $mesa = $m->find($lista[0]->mesa_id);
        $mesa->estado = "con cliente comiendo";
        $mesa->save();
    
        $payload = json_encode(array("mensaje" => "Pedido servido"));        
      }
      else {
        $payload = json_encode(array("mensaje" => "Faltan datos o son erróneos"));
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
        $p = new Pedido();
        $p->find($id)->delete();
        
        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));
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

  public function TraerPendientes($request, $response, $args)
  {
    $cargo = $args['cargo'];
    $lista = Pedido::join('productos', 'pedidos.producto_id', '=', 'productos.id')
                ->where('productos.tipo_usuario',$cargo)->get();

    $payload = json_encode(array("listaPedido" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTiempo($request, $response, $args)
  {
    $pedido = $args['pedido'];
    $p = new Pedido();
    $lista = $p->where('codigo_pedido',$pedido)->get();

    $tiempoEstimado = 0;
    for($i=0; $i<sizeof($lista); $i++)
    {
        if($lista[$i]["estado"]=="en preparacion" && $tiempoEstimado < $lista[$i]["tiempo_estimado"]) {
          $tiempoEstimado = $lista[$i]["tiempo_estimado"];
        }
    }
    $payload = json_encode(array("tiempoEstimado" => $tiempoEstimado));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  //Cantidad de operaciones de todos por sector.
  public function OperacionesPorSector($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (isset($parametros['desde']) && isset($parametros['hasta'])) {
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $lista = Pedido::join('productos', 'pedidos.producto_id', '=', 'productos.id')
        ->whereBetween('tiempo_pedido', [$desde, $hasta])
        ->orderby('productos.tipo_usuario','DESC')
        ->get();
        if(isset($lista)) {
          $cargo = $lista[0]["tipo_usuario"];
          $c=0;
  
          foreach($lista as $line) {
            if($cargo == $line["tipo_usuario"]) {
              $c++;
            }
            else {
              $resultado[$cargo]=$c;
              $cargo = $line["tipo_usuario"];
              $c=1;
            }
          }
          $resultado[$cargo]=$c;

          $payload = json_encode(array("Lista" => $resultado));
        } else {
          $payload = json_encode(array("mensaje" => "No hay datos"));
        }

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
  //Cantidad de operaciones de todos por sector, listada por cada empleado.
  public function OperacionesPorSectorYEmpleado($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (isset($parametros['desde']) && isset($parametros['hasta'])) {
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $lista = Pedido::join('productos', 'pedidos.producto_id', '=', 'productos.id')
        ->join('usuarios', 'pedidos.usuario_id', '=', 'usuarios.id')
        ->whereBetween('tiempo_pedido', [$desde, $hasta])
        ->orderby('pedidos.usuario_id','DESC')
        ->orderby('productos.tipo_usuario','DESC')
        ->get();

        if(isset($lista)) {
          $cargo = $lista[0]["tipo_usuario"];
          $usuario_id = $lista[0]["usuario_id"];
          $nombre = $lista[0]["nombre"];
          $c=0;

          foreach($lista as $line) {
            if($cargo == $line["tipo_usuario"]) {
              if($usuario_id == $line["usuario_id"]) {
                $c++;
              }
              else {
                $resultado[$cargo][$nombre]=$c;
                $usuario_id = $line["usuario_id"];
                $nombre = $line["nombre"];
                $c=1;
              }
            }
            else {
              $resultado[$cargo][$nombre]=$c;

              $cargo = $line["tipo_usuario"];
              $usuario_id = $line["usuario_id"];
              $nombre = $line["nombre"];
              $c=1;
            }
          }
          $resultado[$cargo][$nombre]=$c;

          $payload = json_encode(array("Lista" => $resultado));
        } else {
          $payload = json_encode(array("mensaje" => "No hay datos"));
        }

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
  //Cantidad de operaciones de cada uno por separado.
  public function CantidadDeOperacionesPorEmpleado($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      if (isset($parametros['desde']) && isset($parametros['hasta'])) {
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $lista = Pedido::join('productos', 'pedidos.producto_id', '=', 'productos.id')
                ->join('usuarios', 'pedidos.usuario_id', '=', 'usuarios.id')
                ->whereBetween('tiempo_pedido', [$desde, $hasta])
                ->orderby('usuario_id','DESC')
                ->get();

        if(isset($lista)) {
          $cargo = $lista[0]["usuario_id"];
          $nombre = $lista[0]["nombre"];
          $c=0;
  
          foreach($lista as $line) {
            if($cargo == $line["usuario_id"]) {
              $c++;
            }
            else {
              $resultado[$nombre]=$c;
              $cargo = $line["usuario_id"];
              $nombre = $line["nombre"];
              $c=1;
            }
          }
          $resultado[$nombre]=$c;

          $payload = json_encode(array("Lista" => $resultado));
        } else {
          $payload = json_encode(array("mensaje" => "No hay datos"));
        }
    
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

  

//8- De las pedidos:

//a- Lo que más se vendió.
public function MasVendido($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
    if (isset($parametros['desde']) && isset($parametros['hasta'])) {
      $desde = $parametros['desde'];
      $hasta = $parametros['hasta'];

      $lista = Pedido::join('productos', 'pedidos.producto_id', '=', 'productos.id')
              ->whereBetween('tiempo_pedido', [$desde, $hasta])
              ->orderby('producto_id','DESC')
              ->get();

      if(isset($lista)) {
        $id = $lista[0]["producto_id"];
        $nombre = $lista[0]["producto"];
        $c=0;
        $max=1;

        foreach($lista as $line) {
          if($id == $line["producto_id"]) {
            $c++;
            if($max < $c) {
              $max = $c;
            }
          }
          else {
            $resultado[$nombre]=$c;
            $id = $line["producto_id"];
            $nombre = $line["producto"];
            $c=1;
          }
        }
        $resultado[$nombre]=$c;
        $resultadoFinal=array();
        foreach($resultado as $producto => $cantidad) {
          if($cantidad == $max) {
            array_push($resultadoFinal, $producto);
          }
        }

        $payload = json_encode(array("Mas vendido:" => $resultadoFinal));
      } else {
        $payload = json_encode(array("mensaje" => "No hay datos"));
      }
  
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
//b- Lo que menos se vendió.
public function MenosVendido($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
    if (isset($parametros['desde']) && isset($parametros['hasta'])) {
      $desde = $parametros['desde'];
      $hasta = $parametros['hasta'];

      $lista = Pedido::join('productos', 'pedidos.producto_id', '=', 'productos.id')
              ->whereBetween('tiempo_pedido', [$desde, $hasta])
              ->orderby('producto_id','DESC')
              ->get();

      if(isset($lista)) {
        $id = $lista[0]["producto_id"];
        $nombre = $lista[0]["producto"];
        $c=0;
        $min=9999999999;

        foreach($lista as $line) {
          if($id == $line["producto_id"]) {
            $c++;
          }
          else {
            $resultado[$nombre]=$c;
            if($min > $c) {
              $min = $c;
            }
            $id = $line["producto_id"];
            $nombre = $line["producto"];
            $c=1;
          }
        }
        $resultado[$nombre]=$c;
        if($min > $c) {
          $min = $c;
        }

        $resultadoFinal=array();
        foreach($resultado as $producto => $cantidad) {
          if($cantidad == $min) {
            array_push($resultadoFinal, $producto);
          }
        }

        $payload = json_encode(array("Menos vendido:" => $resultadoFinal));
      } else {
        $payload = json_encode(array("mensaje" => "No hay datos"));
      }
  
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
//c- Los que no se entregaron en el tiempo estipulado.
public function FueraDeTiempo($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
    if (isset($parametros['desde']) && isset($parametros['hasta'])) {
      $desde = $parametros['desde'];
      $hasta = $parametros['hasta'];

      $lista = Pedido::join('productos', 'pedidos.producto_id', '=', 'productos.id')
              ->whereBetween('tiempo_pedido', [$desde, $hasta])
              ->where('pedidos.tiempo_estimado', '<', 'pedidos.tiempo_finalizado')
              ->get();

        $payload = json_encode(array("Lista:" => $lista));

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
//d- Los cancelados.
public function Cancelados($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
    if (isset($parametros['desde']) && isset($parametros['hasta'])) {
      $desde = $parametros['desde'];
      $hasta = $parametros['hasta'];

      $lista = Pedido::join('productos', 'pedidos.producto_id', '=', 'productos.id')
              ->whereBetween('tiempo_pedido', [$desde, $hasta])
              ->where('estado', '=', 'cancelado')
              ->get();

        $payload = json_encode(array("Lista:" => $lista));
           
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
//9- De las mesas:
//a- La más usada.
public function MesaMasUsada($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
    if (isset($parametros['desde']) && isset($parametros['hasta'])) {
      $desde = $parametros['desde'];
      $hasta = $parametros['hasta'];

      $lista = Pedido::join('mesas', 'pedidos.mesa_id', '=', 'mesas.id')
              ->whereBetween('tiempo_pedido', [$desde, $hasta])
              ->orderby('mesa_id','DESC')
              ->get();

      if(isset($lista)) {
        $id = $lista[0]["mesa_id"];
        $nombre = $lista[0]["codigo_identificacion"];
        $c=0;
        $max=1;

        foreach($lista as $line) {
          if($id == $line["mesa_id"]) {
            $c++;
            if($max < $c) {
              $max = $c;
            }
          }
          else {
            $resultado[$nombre]=$c;
            $id = $line["mesa_id"];
            $nombre = $line["codigo_identificacion"];
            $c=1;
          }
        }
        $resultado[$nombre]=$c;
        $resultadoFinal=array();
        foreach($resultado as $mesa => $cantidad) {
          if($cantidad == $max) {
            array_push($resultadoFinal, $mesa);
          }
        }

        $payload = json_encode(array("Mas usada:" => $resultadoFinal));
      } else {
        $payload = json_encode(array("mensaje" => "No hay datos"));
      }
  
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
//b- La menos usada.
public function MesaMenosUsada($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
    if (isset($parametros['desde']) && isset($parametros['hasta'])) {
      $desde = $parametros['desde'];
      $hasta = $parametros['hasta'];

      $lista = Pedido::join('mesas', 'pedidos.mesa_id', '=', 'mesas.id')
              ->whereBetween('tiempo_pedido', [$desde, $hasta])
              ->orderby('mesa_id','DESC')
              ->get();

      if(isset($lista)) {
        $id = $lista[0]["mesa_id"];
        $nombre = $lista[0]["codigo_identificacion"];
        $c=0;
        $min=9999999999;

        foreach($lista as $line) {
          if($id == $line["mesa_id"]) {
            $c++;
          }
          else {
            $resultado[$nombre]=$c;
            if($min > $c) {
              $min = $c;
            }
            $id = $line["mesa_id"];
            $nombre = $line["codigo_identificacion"];
            $c=1;
          }
        }
        $resultado[$nombre]=$c;
        if($min > $c) {
          $min = $c;
        }

        $resultadoFinal=array();
        foreach($resultado as $mesa => $cantidad) {
          if($cantidad == $min) {
            array_push($resultadoFinal, $mesa);
          }
        }

        $payload = json_encode(array("Menos usada:" => $resultadoFinal));
      } else {
        $payload = json_encode(array("mensaje" => "No hay datos"));
      }
  
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
//c- La que más facturó.
public function MesaQueMasFacturo($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
    if (isset($parametros['desde']) && isset($parametros['hasta'])) {
      $desde = $parametros['desde'];
      $hasta = $parametros['hasta'];

      $lista = Pedido::join('mesas', 'pedidos.mesa_id', '=', 'mesas.id')
              ->where('pedidos.estado','=','servido')
              ->whereBetween('tiempo_pedido', [$desde, $hasta])
              ->orderby('mesa_id','DESC')
              ->get();

      if(isset($lista)) {
        $id = $lista[0]["mesa_id"];
        $nombre = $lista[0]["codigo_identificacion"];
        $c=0;
        $max=0;

        foreach($lista as $line) {
          if($id == $line["mesa_id"]) {
            $c += $line["precio"];
            if($max < $c) {
              $max = $c;
            }
          }
          else {
            $resultado[$nombre]=$c;
            $id = $line["mesa_id"];
            $nombre = $line["codigo_identificacion"];
            $c = $line["precio"];
          }
        }
        $resultado[$nombre]=$c;

        $resultadoFinal=array();
        foreach($resultado as $mesa => $cantidad) {
          if($cantidad == $max) {
            array_push($resultadoFinal, $mesa);
          }
        }

        $payload = json_encode(array("Mesa con mayor facturación:" => $resultadoFinal));
      } else {
        $payload = json_encode(array("mensaje" => "No hay datos"));
      }
  
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
//d- La que menos facturó.
public function MesaQueMenosFacturo($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
    if (isset($parametros['desde']) && isset($parametros['hasta'])) {
      $desde = $parametros['desde'];
      $hasta = $parametros['hasta'];

      $lista = Pedido::join('mesas', 'pedidos.mesa_id', '=', 'mesas.id')
              ->where('pedidos.estado','=','servido')
              ->whereBetween('tiempo_pedido', [$desde, $hasta])
              ->orderby('mesa_id','DESC')
              ->get();

      if(isset($lista)) {
        $id = $lista[0]["mesa_id"];
        $nombre = $lista[0]["codigo_identificacion"];
        $c=0;
        $min=9999999999;

        foreach($lista as $line) {
          if($id == $line["mesa_id"]) {
            $c += $line["precio"];
          }
          else {
            $resultado[$nombre]=$c;
            if($min > $c) {
              $min = $c;
            }
            $id = $line["mesa_id"];
            $nombre = $line["codigo_identificacion"];
            $c = $line["precio"];
          }
        }
        $resultado[$nombre]=$c;
        if($min > $c) {
          $min = $c;
        }

        $resultadoFinal=array();
        foreach($resultado as $mesa => $cantidad) {
          if($cantidad == $min) {
            array_push($resultadoFinal, $mesa);
          }
        }

        $payload = json_encode(array("Menos con menor facturación:" => $resultadoFinal));
      } else {
        $payload = json_encode(array("mensaje" => "No hay datos"));
      }
  
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
//e- La/s que tuvo la factura con el mayor importe.
public function MesaConMayorFactura($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
    if (isset($parametros['desde']) && isset($parametros['hasta'])) {
      $desde = $parametros['desde'];
      $hasta = $parametros['hasta'];

      $lista = Pedido::join('mesas', 'pedidos.mesa_id', '=', 'mesas.id')
              ->where('pedidos.estado','=','servido')
              ->whereBetween('tiempo_pedido', [$desde, $hasta])
              ->orderby('pedidos.codigo_pedido','DESC')
              ->get();

      if(isset($lista)) {
        $id = $lista[0]["codigo_pedido"];
        $nombre = $lista[0]["codigo_identificacion"];
        $c=0;
        $max=0;

        foreach($lista as $line) {
          if($id == $line["codigo_pedido"]) {
            $c += $line["precio"];
            if($max < $c) {
              $max = $c;
            }
          }
          else {
            $resultado[$nombre]=$c;
            $id = $line["codigo_pedido"];
            $nombre = $line["codigo_identificacion"];
            $c = $line["precio"];
          }
        }
        $resultado[$nombre]=$c;

        $resultadoFinal=array();
        foreach($resultado as $mesa => $cantidad) {
          if($cantidad == $max) {
            array_push($resultadoFinal, $mesa);
          }
        }

        $payload = json_encode(array("Mesa con mayor facturación:" => $resultadoFinal));
      } else {
        $payload = json_encode(array("mensaje" => "No hay datos"));
      }
  
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
//f- La/s que tuvo la factura con el menor importe.
public function MesaConMenorFactura($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
    if (isset($parametros['desde']) && isset($parametros['hasta'])) {
      $desde = $parametros['desde'];
      $hasta = $parametros['hasta'];

      $lista = Pedido::join('mesas', 'pedidos.mesa_id', '=', 'mesas.id')
              ->where('pedidos.estado','=','servido')
              ->whereBetween('tiempo_pedido', [$desde, $hasta])
              ->orderby('pedidos.codigo_pedido','DESC')
              ->get();

      if(isset($lista)) {
        $id = $lista[0]["codigo_pedido"];
        $nombre = $lista[0]["codigo_identificacion"];
        $c=0;
        $min=9999999999;

        foreach($lista as $line) {
          if($id == $line["codigo_pedido"]) {
            $c += $line["precio"];
          }
          else {
            $resultado[$nombre]=$c;
            if($min > $c) {
              $min = $c;
            }
            $id = $line["codigo_pedido"];
            $nombre = $line["codigo_identificacion"];
            $c = $line["precio"];
          }
        }
        $resultado[$nombre]=$c;
        if($min > $c) {
          $min = $c;
        }

        $resultadoFinal=array();
        foreach($resultado as $mesa => $cantidad) {
          if($cantidad == $min) {
            array_push($resultadoFinal, $mesa);
          }
        }

        $payload = json_encode(array("Menos con menor facturación:" => $resultadoFinal));
      } else {
        $payload = json_encode(array("mensaje" => "No hay datos"));
      }
  
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
//g- Lo que facturó entre dos fechas dadas.
public function Facturacion($request, $response, $args)
{
  $parametros = $request->getParsedBody();
  if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
    if (isset($parametros['desde']) && isset($parametros['hasta'])) {
      $desde = $parametros['desde'];
      $hasta = $parametros['hasta'];

      $lista = Pedido::select('precio')
              ->where('estado','=','servido')
              ->whereBetween('tiempo_pedido', [$desde, $hasta])
              ->get();

      if(isset($lista)) {
        $resultado = 0;
        foreach($lista as $line) {
            $resultado += $line["precio"];
            }
        }

        $payload = json_encode(array("Facturación:" => $resultado));
  
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
