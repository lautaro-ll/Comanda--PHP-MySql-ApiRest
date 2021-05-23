<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;

require_once './models/AutentificadorJWT.php';

class MWparaAutentificar
{
	public function VerificarUsuario(Request $request, RequestHandler $handler)
	{
		var_dump("Verificar Usuario");
		$response = new Response();
		
		if ($request->getMethod() == "GET") {
			$response->getBody()->write("NO necesita credenciales para los get");
		} else {
			$arrayConToken = $request->getHeader('Authorization');
			$token = explode(" ", $arrayConToken[0], 2)[1];
			if (isset($token)) {
				try {
					AutentificadorJWT::verificarToken($token);
					$payload = AutentificadorJWT::ObtenerData($token);
					if (isset($payload->cargo) && $payload->cargo == "Socio") {
						$response = $handler->handle($request);
						var_dump("Verificado");
					} else {
						$response->getBody()->write("NO tenes habilitado el ingreso");
						var_dump("NO Verificado");
					}
				} catch (Exception $e) {
					$response->getBody()->write($e->getMessage());
				}
			} else {
				$response->getBody()->write("NO se ingreso token");
			}
		}
		return $response;
	}
}
?>