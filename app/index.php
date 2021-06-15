<?php

error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/EncuestaController.php';
require_once './middlewares/MWparaAutentificar.php';

$app = AppFactory::create();

// Eloquent
$container=$app->getContainer();

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['MYSQL_HOST'],
    'database'  => $_ENV['MYSQL_DB'],
    'username'  => $_ENV['MYSQL_USER'],
    'password'  => $_ENV['MYSQL_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Routes
$app->post('/login', \UsuarioController::class . ':Validar');

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/u/{id}', \UsuarioController::class . ':TraerUno');
  $group->get('/t/{cargo}', \UsuarioController::class . ':TraerTodosPorCargo');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
  $group->post('/ingresos', \UsuarioController::class . ':TraerIngresos');
  $group->post('/borrar', \UsuarioController::class . ':BorrarUno');
})->add(\MWparaAutentificar::class . ':VerificarUsuario');;

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/t/{id}', \ProductoController::class . ':TraerUno');
  $group->get('/exportarpdf', \ProductoController::class . ':ExportarPdf'); 
  $group->get('/exportarcsv', \ProductoController::class . ':ExportarCsv'); 
  $group->post('/csv', \ProductoController::class . ':CargarCsv');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
})->add(\MWparaAutentificar::class . ':VerificarUsuario');;

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{id}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \MesaController::class . ':CargarUno');
  $group->post('/estado', \MesaController::class . ':CambiarEstado'); 
})->add(\MWparaAutentificar::class . ':VerificarUsuario');;

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/u/{id}', \PedidoController::class . ':TraerUno');
  $group->get('/pendientes/{cargo}', \PedidoController::class . ':TraerPendientes'); //TraerPedidosPendientesSegunTipoUsuario() -> BARTENDER-CERVECERO-COCINERO
  $group->post('[/]', \PedidoController::class . ':CargarUno'); //GenerarPedido ->MOZO
  $group->post('/estado', \PedidoController::class . ':ModificarUno'); //PedidoListo() /  TomarPedido() -> BARTENDER-CERVECERO-COCINERO //CancelarPedido -> MOZO
  $group->post('/consulta/operaciones', \PedidoController::class . ':OperacionesPorSector');
  $group->post('/consulta/operacionesempleado', \PedidoController::class . ':OperacionesPorSectorYEmpleado');
  $group->post('/consulta/cantidadoperaciones', \PedidoController::class . ':CantidadDeOperacionesPorEmpleado');
  $group->post('/consulta/masvendido', \PedidoController::class . ':MasVendido');
  $group->post('/consulta/menosvendido', \PedidoController::class . ':MenosVendido');
  $group->post('/consulta/fueradetiempo', \PedidoController::class . ':FueraDeTiempo');
  $group->post('/consulta/cancelados', \PedidoController::class . ':Cancelados');
  $group->post('/consulta/mesamasusada', \PedidoController::class . ':MesaMasUsada');



})->add(\MWparaAutentificar::class . ':VerificarUsuario');;

$app->group('/encuestas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \EncuestaController::class . ':TraerTodos');
  $group->get('/tiempo/{pedido}', \EncuestaController::class . ':TraerTiempo'); // TraerTiempoRestante(codigo-pedido) -> "tiempo-estimado" - "tiempo-actual" -> CLIENTE
  $group->post('/nueva/{pedido}', \EncuestaController::class . ':CargarUno'); // CargarEncuesta() -> CLIENTE
});

$app->get('[/]', function (Request $request, Response $response) {
  $response->getBody()->write("TP Comanda - Lemos Lautaro Lucas");
  return $response;
});

$app->run();

/*
 Habilitar CORS por mw
 JWT por mw

 SOLICITUDES POR USUARIO

 MOZO
 GenerarPedido()-> "codigo-pedido"*
 CancelarPedido()*
 CambiarEstadoMesa(codigoIdentificacion, nuevoEstado)*

 BARTENDER-CERVECERO-COCINERO
 TraerPedidosPendientesSegunTipoUsuario()*
 TomarPedido()*
 PedidoListo()*

 SOCIO
 TraerEstadoPedidos()
 CerrarMesa(codigoIdentificacion)
 AltaEmpleado()
 BajaEmpleado()
 ModificacionEmpleado()
 AltaProducto()
 BajaProducto()
 ModificacionProducto()
 AltaMesa()
 BajaMesa()
 ModificacionMesa()

 CLIENTE
 TraerTiempoRestante(codigo-pedido) -> "tiempo-estimado" - "tiempo-actual"
 CargarEncuesta()
*/

?>
