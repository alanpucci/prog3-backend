<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args){
      try {
        $parametros = $request->getParsedBody();
        $usr = new Usuario();
        $usr->mail = $parametros['mail'];
        $usr->tipo = $parametros['tipo'];
        $usr->clave = $parametros['clave'];
        $respuesta = $usr->crearUsuario();
        $payload = json_encode(array("mensaje" => "Usuario creado con exito", "id" => $respuesta));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
      } catch (\Throwable $th) {
        $response->getBody()->write(json_encode(array("mensaje" => "ERROR, ".$th->getMessage())));
        return $response
        ->withHeader('Content-Type', 'application/json');
      }
    }

    public function Login($request, $response, $args){
      try {
        $parametros = $request->getParsedBody();
        $usuario = new Usuario();
        $usuario->mail = $parametros["mail"];
        $usuario->clave = $parametros["clave"];
        $respuesta = $usuario->validarUsuario();
        if(!empty($respuesta)){
          $payload = json_encode(array("mensaje" => "Inicio de sesión exitoso", "token" => AutentificadorJWT::CrearToken($respuesta), "tipo" => $respuesta));
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');
        }else{
          throw new Exception("Usuario inválido");
        }
      } catch (\Throwable $th) {
        $response->getBody()->write(json_encode(array("mensaje" => "ERROR, ".$th->getMessage())));
        return $response
          ->withHeader('Content-Type', 'application/json');
      }
    }

    public function TraerPorCriptomoneda($request, $response, $args){
      try {
          $parametros = $request->getQueryParams();
            $criptomoneda = isset($parametros["criptomoneda"]) ? $parametros["criptomoneda"] : "Ethereum";
            $lista = Usuario::obtenerPorCriptomoneda($criptomoneda);
            $payload = json_encode(array("listaUsuarios" => $lista));
            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
      } catch (\Throwable $th) {
          $response->getBody()->write(json_encode(array("mensaje" => "ERROR, ".$th->getMessage())));
          return $response
          ->withHeader('Content-Type', 'application/json');
      }
  }

}