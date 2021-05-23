<?php

error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './middlewares/MWparaAutentificar.php';


/*
 Habilitar CORS por mw
 JWT por mw

 SOLICITUDES POR USUARIO

 MOZO
 GenerarPedido()-> "codigo-pedido"
 CancelarPedido()
 CambiarEstadoMesa(codigoIdentificacion, nuevoEstado)

 BARTENDER-CERVECERO-COCINERO
 TraerPedidosPendientesSegunTipoUsuario()
 TomarPedido()
 PedidoListo()

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
$app = AppFactory::create();

// Middleware

// Routes
//$app->add(\MWparaCORS::class . ':HabilitarCORS8080');

$app->post('/login', \UsuarioController::class . ':Validar');

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/u/{id}', \UsuarioController::class . ':TraerUno');
  $group->get('/t/{cargos}', \UsuarioController::class . ':TraerTodosPorCargo');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
})->add(\MWparaAutentificar::class . ':VerificarUsuario');

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{producto}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
})->add(\MWparaAutentificar::class . ':VerificarUsuario');

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{mesa}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \MesaController::class . ':CargarUno');
  //$group->post('/estado', \MesaController::class . ':CambiarEstado'); //CambiarEstadoMesa ->MOZO
})->add(\MWparaAutentificar::class . ':VerificarUsuario');

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/{pedido}', \PedidoController::class . ':TraerUno');
  $group->post('[/]', \PedidoController::class . ':CargarUno'); //GenerarPedido ->MOZO
  //group->post('/pendientes/{cargo}', \PedidoController::class . ':TraerPendientes'); //TraerPedidosPendientesSegunTipoUsuario() -> BARTENDER-CERVECERO-COCINERO
  //$group->post('/estado', \PedidoController::class . ':ModificarUno'); //PedidoListo() -> BARTENDER-CERVECERO-COCINERO
});

$app->get('[/]', function (Request $request, Response $response) {
  $response->getBody()->write("TP Comanda - Lemos Lautaro Lucas");
  return $response;
});

//TomarPedido() -> BARTENDER-CERVECERO-COCINERO

$app->run();

?>