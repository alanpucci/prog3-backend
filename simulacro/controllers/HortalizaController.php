<?php
require_once './models/Hortaliza.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';
use Slim\Routing\Route;

class HortalizaController extends Hortaliza implements IApiUsable
{
    public function CargarUno($request, $response, $args){
        try {
            $parametros = $request->getParsedBody();
            if(isset($parametros["precio"]) && isset($parametros["nombre"]) && isset($_FILES["archivo"]) && isset($parametros["clima"]) && isset($parametros["tipoUnidad"])){
                $hortaliza = new Hortaliza();
                $hortaliza->precio = $parametros['precio'];
                $hortaliza->nombre = $parametros['nombre'];
                $hortaliza->clima = $parametros['clima'];
                $hortaliza->foto = $_FILES['archivo'];
                $hortaliza->tipoUnidad = $parametros['tipoUnidad'];
                $respuesta = $hortaliza->crearHortaliza();
                $payload = json_encode(array("mensaje" => "Hortaliza cargada con exito", "id" => $respuesta));
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
            if(isset($parametros["tipoUnidad"]) || isset($parametros["clima"])){
                if(isset($parametros["tipoUnidad"])){
                    $lista = Hortaliza::obtenerTodos("tipoUnidad", $parametros["tipoUnidad"]);
                }else if(isset($parametros["clima"])){
                    $lista = Hortaliza::obtenerTodos("clima", $parametros["clima"]);
                }
                    $payload = json_encode(array("listaHortalizas" => $lista));
                        $response->getBody()->write($payload);
                        return $response
                          ->withHeader('Content-Type', 'application/json');
            }else{
                throw new Exception("No se encontró el parametro tipoUnidad o clima");
            }
        } catch (\Throwable $th) {
            $response->getBody()->write(json_encode(array("mensaje" => "ERROR, ".$th->getMessage())));
            return $response
            ->withHeader('Content-Type', 'application/json');
        }
    }

    public function TraerUna($request, $response, $args){
        try {
            $hortaliza = Hortaliza::obtenerUna($args["id"]);
            if($hortaliza){
                $payload = json_encode(array("hortaliza" => $hortaliza));
                    $response->getBody()->write($payload);
                    return $response
                        ->withHeader('Content-Type', 'application/json');
            }else{
                throw new Exception("No se encontró hortaliza con ese id");
            }
        } catch (\Throwable $th) {
            $response->getBody()->write(json_encode(array("mensaje" => "ERROR, ".$th->getMessage())));
            return $response
            ->withHeader('Content-Type', 'application/json');
        }
    }

    public function BorrarUno($request, $response, $args){
        try {
            $hortaliza = Hortaliza::obtenerUna($args["id"]);
            if($hortaliza){
                Hortaliza::borrarUna($args["id"]);
                $payload = json_encode(array("mensaje" => "Hortailiza borrada con exito"));
                $response->getBody()->write($payload);
                return $response
                  ->withHeader('Content-Type', 'application/json');
            }else{
                throw new Exception("No se encontró una hortaliza con ese id");
                
            }
        } catch (\Throwable $th) {
            $response->getBody()->write(json_encode(array("mensaje" => "ERROR, ".$th->getMessage())));
            return $response
            ->withHeader('Content-Type', 'application/json');
        }
    }

    public function ModificarUno($request, $response, $args){
        try {
            $hortaliza = Hortaliza::obtenerUna($args["id"]);
            if($hortaliza){
                $parametros = $request->getParsedBody();
                $nuevaHortaliza = new Hortaliza();
                $nuevaHortaliza->id = $hortaliza["id"];
                $nuevaHortaliza->precio = isset($parametros["precio"]) ? $parametros["precio"] : $hortaliza["precio"];
                $nuevaHortaliza->nombre = isset($parametros["nombre"]) ? $parametros["nombre"] : $hortaliza["nombre"];
                $nuevaHortaliza->clima = isset($parametros["clima"]) ? $parametros["clima"] : $hortaliza["clima"];
                $nuevaHortaliza->foto = isset($_FILES["archivo"]) ? $_FILES["archivo"] : $hortaliza["foto"];
                $nuevaHortaliza->tipoUnidad = isset($parametros["tipoUnidad"]) ? $parametros["tipoUnidad"] : $hortaliza["tipoUnidad"];
                $nuevaHortaliza->estado = isset($parametros["estado"]) ? $parametros["estado"] : $hortaliza["estado"];
                $nuevaHortaliza->modificarHortaliza();
                $payload = json_encode(array("mensaje" => "Hortaliza modificada con exito"));
                $response->getBody()->write($payload);
                return $response
                  ->withHeader('Content-Type', 'application/json');
            }else{
                throw new Exception("No se encontró una hortaliza con ese id");
                
            }
        } catch (\Throwable $th) {
            $response->getBody()->write(json_encode(array("mensaje" => "ERROR, ".$th->getMessage())));
            return $response
            ->withHeader('Content-Type', 'application/json');
        }
    }
}