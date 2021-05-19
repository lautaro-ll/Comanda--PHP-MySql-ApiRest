<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require_once './models/AutentificadorJWT.php';

class MWparaAutentificar
{
	public function VerificarUsuario(Request $request, RequestHandler $handler) 
	{         
		$response = $handler->handle($request);
		
		if($request->getMethod()=="GET")
		{
			$response->getBody()->write(json_encode(array("mensaje" => "NO necesita credenciales para los get")));
			$body = $response->getBody();
			var_dump($body);
		}
		else
		{
			$parametros = $request->getParsedBody();
			$tipo=$parametros['tipo'];
			var_dump($tipo);
			if($tipo=="socio")
			{
				$response = json_encode($parametros);
			}
			else
			{
				$response = json_encode(array("mensaje" => "NO tenes habilitado el ingreso"));
			}  
		}
		return $response;  
	}
}