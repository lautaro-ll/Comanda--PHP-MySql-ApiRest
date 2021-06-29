<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Producto as Producto;
use Dompdf\Dompdf;

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
        $nuevoProducto->tipo_usuario = $tipoUsuario;
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
        $producto->tipo_usuario = $tipoUsuario;
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
  
  public function CargarCsv($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      
      if (isset($_FILES["archivo"])) {
        $file = $_FILES["archivo"];
        var_dump($file);
        $lista = ProductoController::RetornarArrayDelCSV($file["tmp_name"]);
        var_dump($lista);

        for($i=0;$i<sizeof($lista);$i++) {
          $lista[$i]->save();
        }
    
        $payload = json_encode(array("mensaje" => "Productos creados con exito"));
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

  static function RetornarArrayDelCSV($file_name)
  {
    $listado = array();
      if(($archivo = fopen($file_name,"r")) !== FALSE) {
          $i = 0;
          while (($datos = fgetcsv($archivo, 1000, ",")) !== FALSE) {
              if(count($datos)==5) {
                $nuevoProducto = new Producto();
                $nuevoProducto->tipo = $datos[0];
                $nuevoProducto->producto = $datos[1];
                $nuevoProducto->tipo_usuario = $datos[2];
                $nuevoProducto->precio = $datos[3];
                $nuevoProducto->demora = $datos[4];
                $listado[$i] = $nuevoProducto;

              }
              $i++;
          } 
          fclose($archivo);
          return $listado;
      }
      else {
          return null;
      }
  }    

  public function ExportarCsv($request, $response, $args)
  {
    $lista = Producto::all()->toArray();;

    $f = fopen('php://memory', 'w'); 
    $titulo = array('id_productos','tipo', 'producto', 'tipo_usuario', 'precio', 'demora');
    fputcsv($f, $titulo, ";"); 

    foreach ($lista as $line) { 
      fputcsv($f, $line, ";"); 
    }
    fseek($f, 0);
    fpassthru($f);
    $response = $response->withHeader('Content-Type', 'application/csv');
    return $response->withHeader('Content-Disposition', 'attachment; filename="productos.csv";');
  }

  public function ExportarPdf($request, $response, $args)
  {

    // instantiate and use the dompdf class
    $dompdf = new Dompdf();

    $lista = Producto::all()->toArray();;

    $stringHTML = ProductoController::DibujarListado($lista);

    $dompdf->loadHtml($stringHTML);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream();

    $response->getBody()->write($dompdf);
    return $response
      ->withHeader('Content-Type', 'application/pdf');
  }

  static function DibujarListado($listado)
  {
      if(!is_null($listado) && is_array($listado)) 
      {
        $stringHTML = "<h1>Productos</h1>";
        foreach($listado as $producto)
        {
          $stringHTML .= "<ul>";
          $stringHTML .= "<li>".$producto["tipo"]."</li>";
          $stringHTML .= "<li>".$producto["producto"]."</li>";
          $stringHTML .= "<li>".$producto["tipo_usuario"]."</li>";
          $stringHTML .= "<li>".$producto["precio"]."</li>";
          $stringHTML .= "<li>".$producto["demora"]."</li>";
          $stringHTML .= "</ul>";
          $stringHTML .= "<br>";
        }
      }
      return $stringHTML;
  }

}
?>