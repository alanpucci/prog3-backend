<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/EmpleadoController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/EncuestaController.php';
require_once './controllers/ComandaController.php';
require_once './controllers/PDFController.php';
require_once './middlewares/Verificadora.php';
require_once './db/AccesoDatos.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
date_default_timezone_set('America/Argentina/Buenos_Aires');

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// peticiones
$app->post('/login',  \EmpleadoController::class . ':Login');

$app->group('/empleados', function (RouteCollectorProxy $group) {
    $group->get('[/]', \EmpleadoController::class . ':TraerTodos')->add(\Verificadora::class . ':ValidarToken');
    $group->post('[/]', \EmpleadoController::class . ':CargarUno')->add(\Verificadora::class . ':ValidarSocio');
    $group->put('/{id}', \EmpleadoController::class . ':ModificarUno')->add(\Verificadora::class . ':ValidarSocio');
  });

$app->post('/fotos', \ComandaController::class . ':SacarFoto')->add(\Verificadora::class . ':ValidarMozo');

$app->post('/cargarCSV', \ComandaController::class . ':CargarCSV')->add(\Verificadora::class . ':ValidarEmpleado');

$app->post('/guardarCSV', \ComandaController::class . ':GuardarCSV')->add(\Verificadora::class . ':ValidarEmpleado');

$app->group('/encuestas', function (RouteCollectorProxy $group) {
  $group->post('[/]', \EncuestaController::class . ':CargarUno');
  $group->get('[/]', \EncuestaController::class . ':TraerMejores')->add(\Verificadora::class . ':ValidarSocio');
});

$app->post('/pdf', \PDFController::class . ':CrearPDF')->add(\Verificadora::class . ':ValidarSocio');

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos')->add(\Verificadora::class . ':ValidarEmpleado');
  $group->post('[/]', \ProductoController::class . ':CargarUno')->add(\Verificadora::class . ':ValidarEmpleado');
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos')->add(\Verificadora::class . ':ValidarEmpleado');
  $group->get('/{id}', \MesaController::class . ':ConsultarDemora');
  $group->post('[/]', \MesaController::class . ':CargarUno')->add(\Verificadora::class . ':ValidarEmpleado');
  $group->put('/{id}', \MesaController::class . ':CerrarMesa')->add(\Verificadora::class . ':ValidarSocio');
});

$app->get('/mesaUsada', \MesaController::class . ':MesaUsada')->add(\Verificadora::class . ':ValidarSocio');


$app->group('/comandas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ComandaController::class . ':TraerTodos')->add(\Verificadora::class . ':ValidarEmpleado');
  $group->post('[/]', \ComandaController::class . ':CargarUno')->add(\Verificadora::class . ':ValidarMozo');
  $group->put('/{id}', \ComandaController::class . ':ModificarUno')->add(\Verificadora::class . ':ValidarEmpleado');
  $group->put('[/]', \ComandaController::class . ':ModificarListos')->add(\Verificadora::class . ':ValidarMozo');
});

// Run app
$app->run();

