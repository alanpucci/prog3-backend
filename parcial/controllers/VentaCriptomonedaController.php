<?php
require_once './models/Venta.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';
use Slim\Routing\Route;

class VentaCriptomonedaController extends Venta implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        try {
            $parametros = $request->getParsedBody();
            if(isset($parametros["cantidad"]) && isset($parametros["criptomoneda"]) && isset($_FILES["archivo"]) && isset($parametros["mail"])){
                $venta = new Venta();
                $venta->cantidad = $parametros['cantidad'];
                $venta->criptomoneda = $parametros['criptomoneda'];
                $venta->foto = $_FILES['archivo'];
                $venta->vendedor = $parametros['mail'];
                $respuesta = $venta->crearVenta();
                $payload = json_encode(array("mensaje" => "Venta cargada con exito", "id" => $respuesta));
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

    public function TraerTodosEntreFechas($request, $response, $args){
        try {
            $lista = Venta::obtenerTodosEntreFechas();
            $payload = json_encode(array("listaVentas" => $lista));
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