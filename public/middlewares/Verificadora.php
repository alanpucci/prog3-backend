<?php

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Slim\Handlers\Strategies\RequestHandler;

class Verificadora{

    public function VerificarUsuario($request, $handler){
        $parametros = $request->getParsedBody();
        $usuario = Usuario::obtenerUsuarioConClave($parametros["nombre"],$parametros["clave"]);
        if(!empty($usuario)){
            $response = $handler->handle($request);
        }else{
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, correo o clave incorrectas", "status" => 403));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
        return $response;
    }
}

?>