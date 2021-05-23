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
					if (isset($payload->cargo)) 
					{
						$parsedBody = $request->getParsedBody();
						if($payload->cargo == "Socio") {
							$parsedBody["empleado"] = "socio";
						}
						if($payload->cargo == "Cervecero") {
							$parsedBody["empleado"] = "cervecero";
						}
						if($payload->cargo == "Bartender") {
							$parsedBody["empleado"] = "bartender";
						}
						if($payload->cargo == "Cocinero") {
							$parsedBody["empleado"] = "cocinero";
						}
						if($payload->cargo == "Mozo") {
							$parsedBody["empleado"] = "mozo";
						}
						$request = $request->withParsedBody($parsedBody);
						$response = $handler->handle($request);

					} 
					else 
					{
						$response->getBody()->write("NO tenes habilitado el ingreso");
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