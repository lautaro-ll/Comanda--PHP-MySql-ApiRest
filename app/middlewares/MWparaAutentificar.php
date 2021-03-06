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
		if ($request->getMethod() == "GET") {
			$response = $handler->handle($request);
		} else {
			$arrayConToken = $request->getHeader('Authorization');
			if(isset($arrayConToken[0])) {
				$token = explode(" ", $arrayConToken[0], 2)[1];
			}
			if (isset($token)) {
				try {
					AutentificadorJWT::verificarToken($token);
					$payload = AutentificadorJWT::ObtenerData($token);
					if (isset($payload->cargo)) 
					{
						$parsedBody = $request->getParsedBody();
						if($payload->cargo == "Socio") {
							$parsedBody["accesoEmpleado"] = "socio";
						}
						if($payload->cargo == "Cervecero") {
							$parsedBody["accesoEmpleado"] = "cervecero";
						}
						if($payload->cargo == "Bartender") {
							$parsedBody["accesoEmpleado"] = "bartender";
						}
						if($payload->cargo == "Cocinero") {
							$parsedBody["accesoEmpleado"] = "cocinero";
						}
						if($payload->cargo == "Mozo") {
							$parsedBody["accesoEmpleado"] = "mozo";
						}
						$parsedBody["idUsuarioRegistrado"] = $payload->id;
						$request = $request->withParsedBody($parsedBody);
						$response = $handler->handle($request);

					} 
					else 
					{
						$response = new Response();
						$response->getBody()->write("NO tenes habilitado el ingreso");
					}
				} catch (Exception $e) {
					$response = new Response();
					$response->getBody()->write($e->getMessage());
				}
			} else {
				$response = new Response();
				$response->getBody()->write("NO se ingreso token");
			}
		}
		return $response;
	}
}
?>