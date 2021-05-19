<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require_once './models/AutentificadorJWT.php';

class MWparaAutentificar
{
	public function VerificarUsuario(Request $request, RequestHandler $handler) {
         
		$objDelaRespuesta= new stdclass();
		$objDelaRespuesta->respuesta="";
	   
		if($request->getMethod()=="GET")
		{
			$response = $handler->handle($request);
		    $response->getBody()->write('<p>NO necesita credenciales para los get </p>'); //borrar
		}
		else
		{
			//$response->getBody()->write('<p>verifico credenciales</p>');

			//perfil=Profesor (GET, POST)
			//$datos = array('usuario' => 'rogelio@agua.com','perfil' => 'profe', 'alias' => "PinkBoy");
			
			//perfil=Administrador(todos)
			$datos = array('usuario' => 'rogelio@agua.com','perfil' => 'Administrador', 'alias' => "PinkBoy");
			
			$token= AutentificadorJWT::CrearToken($datos);

			//token vencido
			//$token="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE0OTc1Njc5NjUsImV4cCI6MTQ5NzU2NDM2NSwiYXVkIjoiNGQ5ODU5ZGU4MjY4N2Y0YzEyMDg5NzY5MzQ2OGFhNzkyYTYxNTMwYSIsImRhdGEiOnsidXN1YXJpbyI6InJvZ2VsaW9AYWd1YS5jb20iLCJwZXJmaWwiOiJBZG1pbmlzdHJhZG9yIiwiYWxpYXMiOiJQaW5rQm95In0sImFwcCI6IkFQSSBSRVNUIENEIDIwMTcifQ.GSpkrzIp2UbJWNfC1brUF_O4h8PyqykmW18vte1bhMw";

			//token error
			//$token="octavioAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE0OTc1Njc5NjUsImV4cCI6MTQ5NzU2NDM2NSwiYXVkIjoiNGQ5ODU5ZGU4MjY4N2Y0YzEyMDg5NzY5MzQ2OGFhNzkyYTYxNTMwYSIsImRhdGEiOnsidXN1YXJpbyI6InJvZ2VsaW9AYWd1YS5jb20iLCJwZXJmaWwiOiJBZG1pbmlzdHJhZG9yIiwiYWxpYXMiOiJQaW5rQm95In0sImFwcCI6IkFQSSBSRVNUIENEIDIwMTcifQ.GSpkrzIp2UbJWNfC1brUF_O4h8PyqykmW18vte1bhMw";
	
			//tomo el token del header
			/*
				$arrayConToken = $request->getHeader('token');
				$token=$arrayConToken[0];			
			*/
			//var_dump($token);
			$objDelaRespuesta->esValido=true; 
			try 
			{
				//$token="";
				AutentificadorJWT::verificarToken($token);
				$objDelaRespuesta->esValido=true;      
			}
			catch (Exception $e) {      
				//guardar en un log
				$objDelaRespuesta->excepcion=$e->getMessage();
				$objDelaRespuesta->esValido=false;     
			}

			if($objDelaRespuesta->esValido)
			{						
				if($request->getMethod()=="POST")
				{		
					// el post sirve para todos los logeados			    
					$response = $handler->handle($request);
				}
				else
				{
					$payload=AutentificadorJWT::ObtenerData($token);
					//var_dump($payload);
					// DELETE,PUT y DELETE sirve para todos los logeados y admin
					if($payload->perfil=="Administrador")
					{
						$response = $handler->handle($request);
					}		           	
					else
					{	
						$objDelaRespuesta->respuesta="Solo administradores";
					}
				}		          
			}    
			else
			{
				//   $response->getBody()->write('<p>no tenes habilitado el ingreso</p>');
				$objDelaRespuesta->respuesta="Solo usuarios registrados";
				$objDelaRespuesta->elToken=$token;

			}  
		}		  
		if($objDelaRespuesta->respuesta!="")
		{
			$nueva=$response->withJson($objDelaRespuesta, 401);  
			return $nueva;
		}
		  
		 //$response->getBody()->write('<p>vuelvo del verificador de credenciales</p>');
		 return $response;   
	}
}