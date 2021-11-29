<?php

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Slim\Handlers\Strategies\RequestHandler;

class Credenciales{

    public function ValidarPost($request, $handler){
        $parametros = $request->getParsedBody();
        if($parametros['perfil'] == "administrador"){
            $response = $handler->handle($request);
            $response->getBody()->write($parametros['nombre']);
        }else{
            $response = new Response();
            $response->getBody()->write('No tienes habilitado el acceso.');
        }
        return $response;
    }

    public function ValidarPostJSON($request, $handler){
        $parametros = $request->getParsedBody();
        if($parametros['perfil'] == "administrador"){
            $response = $handler->handle($request);
        }else{
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, usuario sin permisos", "status" => 403));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
        return $response;
    }
}

?>