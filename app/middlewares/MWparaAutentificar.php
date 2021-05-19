<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require_once './models/AutentificadorJWT.php';

class MWparaAutentificar
{
	public function VerificarUsuario(Request $request, RequestHandler $handler) 
	{
         
		$objDelaRespuesta= new stdclass();
		$objDelaRespuesta->respuesta="";
		$response = $handler->handle($request);
	   
		if($request->getMethod()=="GET")
		{
		    $response->getBody()->write('<p>NO necesita credenciales para los get </p>'); 
		}
		else
		{
			$response->getBody()->write('<p>verifico credenciales</p>');
			$parametros = $request->getParsedBody();
			if(isset($parametros['token']))
			{
				$token = $parametros['token'];
			}
			try 
			{
				AutentificadorJWT::verificarToken($token);
				$objDelaRespuesta->esValido=true;      
			}
			catch (Exception $e) {      
				$objDelaRespuesta->excepcion=$e->getMessage();
				$objDelaRespuesta->esValido=false;     
			}

			if($objDelaRespuesta->esValido)
			{						
				if($request->getMethod()=="POST")
				{		
					$payload=AutentificadorJWT::ObtenerData($token);
					if($payload->tipo=="Socio")
					{
						$response->getBody()->write('<p>Hola Socio!</p>');
						$objDelaRespuesta->respuesta="Bienvenido";
					}		           	
					else
					{	
						$objDelaRespuesta->respuesta="Solo socios";
					}
				}		          
			}    
			else
			{
				$response->getBody()->write('<p>no tenes habilitado el ingreso</p>');
				$objDelaRespuesta->respuesta="Solo usuarios registrados";
				$objDelaRespuesta->elToken=$token;
			}  
		}		  
		if($objDelaRespuesta->respuesta!="")
		{
			$response->getBody()->write($objDelaRespuesta);
			return $response
			->withHeader('Content-Type', 'application/json');
		}
		return $response  
		->withHeader('Content-Type', 'application/json')
		->withStatus(401);
	}
}