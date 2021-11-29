<?php
require_once './models/Criptomoneda.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';
use Slim\Routing\Route;

class CriptomonedaController extends Criptomoneda implements IApiUsable
{
    public function CargarUno($request, $response, $args){
        try {
            $parametros = $request->getParsedBody();
            if(isset($parametros["precio"]) && isset($parametros["nombre"]) && isset($_FILES["archivo"]) && isset($parametros["nacionalidad"])){
                $criptomoneda = new Criptomoneda();
                $criptomoneda->precio = $parametros['precio'];
                $criptomoneda->nombre = $parametros['nombre'];
                $criptomoneda->foto = $_FILES['archivo'];
                $criptomoneda->nacionalidad = $parametros['nacionalidad'];
                $respuesta = $criptomoneda->crearCriptomoneda();
                $payload = json_encode(array("mensaje" => "Criptomoneda cargada con exito", "id" => $respuesta));
                $response->getBody()->write($payload);
                return $response
                  ->withHeader('Content-Type', 'application/json');
            }else{
                throw new Exception("Parametros invalidos");
            }
        } catch (\Throwable $th) {
            $response->getBody()->write(json_encode(array("mensaje" => "ERROR, ".$th->getMessage())));
            return $response
            ->withHeader('Content-Type', 'application/json');
        }
    }

    public function TraerTodos($request, $response, $args){
        try {
            $parametros = $request->getQueryParams();
            if(isset($parametros["nacionalidad"])){
                $lista = Criptomoneda::obtenerPorNacionalidad($parametros["nacionalidad"]);
            }else{
                $lista = Criptomoneda::obtenerTodos();
            }
            $payload = json_encode(array("listaCriptomonedas" => $lista));
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $th) {
            $response->getBody()->write(json_encode(array("mensaje" => "ERROR, ".$th->getMessage())));
            return $response
            ->withHeader('Content-Type', 'application/json');
        }
    }

    public function TraerUna($request, $response, $args){
        try {
            $criptomoneda = Criptomoneda::obtenerUna($args["id"]);
            if($criptomoneda){
                $payload = json_encode(array("Criptomoneda" => $criptomoneda));
                    $response->getBody()->write($payload);
                    return $response
                        ->withHeader('Content-Type', 'application/json');
            }else{
                throw new Exception("No se encontró una criptomoneda con ese id");
            }
        } catch (\Throwable $th) {
            $response->getBody()->write(json_encode(array("mensaje" => "ERROR, ".$th->getMessage())));
            return $response
            ->withHeader('Content-Type', 'application/json');
        }
    }

    public function BorrarUno($request, $response, $args){
        try {
            $criptomoneda = Criptomoneda::obtenerUna($args["id"]);
            if($criptomoneda){
                Criptomoneda::borrarUna($args["id"]);
                $payload = json_encode(array("mensaje" => "Criptomoneda borrada con exito"));
                $response->getBody()->write($payload);
                return $response
                  ->withHeader('Content-Type', 'application/json');
            }else{
                throw new Exception("No se encontró una criptomoneda con ese id");
                
            }
        } catch (\Throwable $th) {
            $response->getBody()->write(json_encode(array("mensaje" => "ERROR, ".$th->getMessage())));
            return $response
            ->withHeader('Content-Type', 'application/json');
        }
    }

    public function ModificarUno($request, $response, $args){
        try {
            $criptomoneda = Criptomoneda::obtenerUna($args["id"]);
            if($criptomoneda){
                $parametros = $request->getParsedBody();
                $nuevaCriptomoneda = new Criptomoneda();
                $nuevaCriptomoneda->id = $criptomoneda["id"];
                $nuevaCriptomoneda->precio = isset($parametros["precio"]) ? $parametros["precio"] : $criptomoneda["precio"];
                $nuevaCriptomoneda->nombre = isset($parametros["nombre"]) ? $parametros["nombre"] : $criptomoneda["nombre"];
                $nuevaCriptomoneda->foto = isset($_FILES["archivo"]) ? $_FILES["archivo"] : $criptomoneda["foto"];
                $nuevaCriptomoneda->nacionalidad = isset($parametros["nacionalidad"]) ? $parametros["nacionalidad"] : $criptomoneda["nacionalidad"];
                $nuevaCriptomoneda->estado = isset($parametros["estado"]) ? $parametros["estado"] : $criptomoneda["estado"];
                $nuevaCriptomoneda->modificarCriptomoneda();
                $payload = json_encode(array("mensaje" => "Criptomoneda modificada con exito"));
                $response->getBody()->write($payload);
                return $response
                  ->withHeader('Content-Type', 'application/json');
            }else{
                throw new Exception("No se encontró una criptomoneda con ese id");
                
            }
        } catch (\Throwable $th) {
            $response->getBody()->write(json_encode(array("mensaje" => "ERROR, ".$th->getMessage())));
            return $response
            ->withHeader('Content-Type', 'application/json');
        }
    }
}