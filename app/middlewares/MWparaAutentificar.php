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

			if(isset($parametros['token']))
			{
				$token = $parametros['token'];
				try 
				{
					AutentificadorJWT::verificarToken($token);
					$payload=AutentificadorJWT::ObtenerData($token);
					if(isset($payload->tipo) && $payload->tipo=="Socio")
					{
						$response->getBody()->write($existingContent);
					}
					else
					{
						$response->getBody()->write("NO tenes habilitado el ingreso");
					}  
				}
				catch (Exception $e) {      
					$response->getBody()->write($e->getMessage());
				}
			}
			else
			{
				$response->getBody()->write("NO se ingreso token");
			}
		}
		return $response;  
	}

	/*
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
	*/
}