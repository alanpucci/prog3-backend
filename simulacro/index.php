<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/HortalizaController.php';
require_once './controllers/PDFController.php';
require_once './controllers/VentaHortalizaController.php';
require_once './middlewares/Verificadora.php';
require_once './db/AccesoDatos.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->setBasePath('/simulacro');

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();
// peticiones
$app->post('/login', \UsuarioController::class . ':Login');

$app->get('/pdf', \PDFController::class . ':CrearPDF');

$app->group('/hortalizas', function (RouteCollectorProxy $group) {
  $group->get('/{id}', \HortalizaController::class . ':TraerUna')->add(\Verificadora::class . ':ValidarUsuario');
  $group->delete('/{id}', \HortalizaController::class . ':BorrarUno')->add(\Verificadora::class . ':ValidarVendedor');
  $group->post('/{id}', \HortalizaController::class . ':ModificarUno')->add(\Verificadora::class . ':ValidarAdmin');
  $group->get('[/]', \HortalizaController::class . ':TraerTodos');
  $group->post('[/]', \HortalizaController::class . ':CargarUno')->add(\Verificadora::class . ':ValidarVendedor');
});

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos')->add(\Verificadora::class . ':ValidarProveedor');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
  });

$app->group('/ventas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \VentaHortalizaController::class . ':TraerTodosEntreFechas')->add(\Verificadora::class . ':ValidarVendedor');
    $group->post('[/]', \VentaHortalizaController::class . ':CargarUno')->add(\Verificadora::class . ':ValidarRol');
  });

// Run app
$app->run();

