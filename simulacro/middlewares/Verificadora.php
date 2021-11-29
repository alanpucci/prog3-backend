<?php

require_once './middlewares/AutentificadorJWT.php';
use GuzzleHttp\Psr7\Response;

class Verificadora{

    public function ValidarVendedor($request, $handler){
        try {
            $header = $request->getHeaderLine('Authorization');
            if(!empty($header)){
                $token = trim(explode("Bearer", $header)[1]);
                $data = AutentificadorJWT::ObtenerData($token);
                if($data == "vendedor"){
                    return $handler->handle($request);
                }
                throw new Exception("Usuario no autorizado");
            }else{
                throw new Exception("Token vacío");
            }
        } catch (\Throwable $th) {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, ".$th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
    }

    public function ValidarProveedor($request, $handler){
        try {
            $header = $request->getHeaderLine('Authorization');
            if(!empty($header)){
                $token = trim(explode("Bearer", $header)[1]);
                $data = AutentificadorJWT::ObtenerData($token);
                if($data == "proveedor"){
                    return $handler->handle($request);
                }
                throw new Exception("Usuario no autorizado");
            }else{
                throw new Exception("Token vacío");
            }
        } catch (\Throwable $th) {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, ".$th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
    }

    public function ValidarAdmin($request, $handler){
        try {
            $header = $request->getHeaderLine('Authorization');
            if(!empty($header)){
                $token = trim(explode("Bearer", $header)[1]);
                $data = AutentificadorJWT::ObtenerData($token);
                if($data == "admin"){
                    return $handler->handle($request);
                }
                throw new Exception("Usuario no autorizado");
            }else{
                throw new Exception("Token vacío");
            }
        } catch (\Throwable $th) {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, ".$th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
    }

    public function ValidarRol($request, $handler){
        try {
            $header = $request->getHeaderLine('Authorization');
            if(!empty($header)){
                $token = trim(explode("Bearer", $header)[1]);
                $data = AutentificadorJWT::ObtenerData($token);
                if($data == "proveedor" || $data == "vendedor"){
                    return $handler->handle($request);
                }
                throw new Exception("Usuario no autorizado");
            }else{
                throw new Exception("Token vacío");
            }
        } catch (\Throwable $th) {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, ".$th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
    }

    public function ValidarUsuario($request, $handler){
        try {
            $header = $request->getHeaderLine('Authorization');
            if(!empty($header)){
                $token = trim(explode("Bearer", $header)[1]);
                AutentificadorJWT::VerificarToken($token);
                return $handler->handle($request);
            }else{
                throw new Exception("Token vacío");
            }
        } catch (\Throwable $th) {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, ".$th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
    }
}

?>