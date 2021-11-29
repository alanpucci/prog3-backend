<?php
require_once './models/PDF.php';
require_once './models/Venta.php';

class PDFController extends PDF
{
    public function CrearPDF($request, $response, $args){
      try {
          $lista = Venta::obtenerTodos();
          PDF::fabricarPDF($lista);
          $payload = json_encode(array("mensaje" => "PDF creado exitosamente"));
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