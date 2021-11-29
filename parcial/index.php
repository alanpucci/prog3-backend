<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/CriptomonedaController.php';
require_once './controllers/VentaCriptomonedaController.php';
require_once './controllers/PDFController.php';
require_once './middlewares/Verificadora.php';
require_once './db/AccesoDatos.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->setBasePath('/parcial');

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();
// peticiones
$app->post('/login', \UsuarioController::class . ':Login');

$app->get('/pdf', \PDFController::class . ':CrearPDF');

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerPorCriptomoneda')->add(\Verificadora::class . ':ValidarAdmin');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
  });

$app->group('/criptomonedas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \CriptomonedaController::class . ':TraerTodos');
    $group->get('/{id}', \CriptomonedaController::class . ':TraerUna')->add(\Verificadora::class . ':ValidarUsuario');
    $group->post('[/]', \CriptomonedaController::class . ':CargarUno')->add(\Verificadora::class . ':ValidarAdmin');
    $group->delete('/{id}', \CriptomonedaController::class . ':BorrarUno')->add(\Verificadora::class . ':ValidarAdmin');
    $group->post('/{id}', \CriptomonedaController::class . ':ModificarUno')->add(\Verificadora::class . ':ValidarAdmin');
  });

$app->group('/ventas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \VentaCriptomonedaController::class . ':TraerTodosEntreFechas')->add(\Verificadora::class . ':ValidarAdmin');
    $group->post('[/]', \VentaCriptomonedaController::class . ':CargarUno')->add(\Verificadora::class . ':ValidarUsuario');
  });

// Run app
$app->run();

