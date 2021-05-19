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
		$response = $handler->handle($request);
		$existingContent = (string) $response->getBody();
		$response = new Response();
		
		if($request->getMethod()=="GET")
		{
			$response->getBody()->write("NO necesita credenciales para los get");
		}
		else
		{
			$parametros = $request->getParsedBody();
			$tipo=$parametros['tipo'];
			var_dump($tipo);
			if($tipo=="socio")
			{
				$response->getBody()->write($existingContent);
			}
			else
			{
				$response->getBody()->write("NO tenes habilitado el ingreso");
			}  
		}
		return $response;  
	}
}