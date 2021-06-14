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
  
  public function CargarCsv($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(isset($parametros['accesoEmpleado']) && $parametros['accesoEmpleado']=="socio") {
      var_dump("accesoOK");
      
      if (isset($_FILES["archivo"])) {
        $file = $_FILES["archivo"];
        $lista = ProductoController::RetornarArrayDelCSV($file["tmp_name"]);
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
      if(($archivo = fopen($file_name,"r")) !== FALSE) {
          $i = 0;
          while (($datos = fgetcsv($archivo, 1000, ",")) !== FALSE) {
              if(count($datos)==4) {
                $nuevoProducto = new Producto();
                $nuevoProducto->tipo = $datos[0];
                $nuevoProducto->producto = $datos[1];
                $nuevoProducto->tipoUsuario = $datos[2];
                $nuevoProducto->precio = $datos[3];
              }
              $listado[$i] = $nuevoProducto;
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
    if(!file_exists("productos.csv") || is_writable("productos.csv")) 
    {
      $lista = Producto::all()->toArray();;

      $archivo = fopen("productos.csv", "w");
      for($i=0;$i<sizeof($lista);$i++) {
        $tipo = $lista[$i]['tipo'];
        $producto = $lista[$i]['producto'];
        $tipoUsuario = $lista[$i]['tipoUsuario'];
        $precio = $lista[$i]['precio'];
        fwrite($archivo, "$tipo, $producto, $tipoUsuario, $precio\n");
      }
      fclose($archivo);
      
      $payload = json_encode(array("mensaje" => "Productos guardados con exito"));
    }
    else {
      $payload = json_encode(array("mensaje" => "No se pudieron guardar los datos"));
    }

    $response->getBody()->write($payload);
    $response->header('Content-Type: application/csv');
    $response->header('Content-Disposition: attachment; filename="productos.csv";');
    return $response;
  }



//EXPORTAR A CSV

function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
    // open raw memory as file so no temp files needed, you might run out of memory though
    $f = fopen('php://memory', 'w'); 
    // loop over the input array
    foreach ($array as $line) { 
        // generate csv lines from the inner arrays
        fputcsv($f, $line, $delimiter); 
    }
    // reset the file pointer to the start of the file
    fseek($f, 0);
    // tell the browser it's going to be a csv file
    header('Content-Type: application/csv');
    // tell the browser we want to save it instead of displaying it
    header('Content-Disposition: attachment; filename="'.$filename.'";');
    // make php send the generated csv lines to the browser
    fpassthru($f);
}
/*
And you can use it like this:

array_to_csv_download(array(
  array(1,2,3,4), // this array is going to be the first row
  array(1,2,3,4)), // this array is going to be the second row
  "numbers.csv"
);
*/

//EXPORTAR A PDF

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
      $stringHTML = "<ul>";
      foreach($listado as $producto)
      {
        $stringHTML .= "<li>".$producto["tipo"]."</li>";
        $stringHTML .= "<li>".$producto["producto"]."</li>";
        $stringHTML .= "<li>".$producto["tipoUsuario"]."</li>";
        $stringHTML .= "<li>".$producto["precio"]."</li>";
        $stringHTML .= "<br>";
      }
      $stringHTML .= "</ul>";
    }
    return $stringHTML;
}


}
?>