<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/UsuarioController.php';
require_once './middlewares/Credenciales.php';
require_once './middlewares/Verificadora.php';
require_once './db/AccesoDatos.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->setBasePath('/public');

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("hola alumnos de los lunes!");
    return $response;
});

// peticiones
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
  });

$app->group('/credenciales', function (RouteCollectorProxy $group){
  $group->get('[/]', function (Request $request, Response $response, $args){
    $response->getBody()->write("Metodo GET");
    return $response;
  });
  $group->post('[/]', function (Request $request, Response $response, $args){
    $response->getBody()->write("Metodo POST");
    return $response;
  })->add(\Credenciales::class . ':ValidarPost');
});

$app->group('/json', function (RouteCollectorProxy $group){
  $group->get('[/]', function (Request $request, Response $response, $args){
    $payload = json_encode(array("mensaje" => "API => GET", "status" => 200));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  });
  $group->post('[/]', function (Request $request, Response $response, $args){
    $payload = json_encode(array("mensaje" => "API => POST", "status" => 200));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  })->add(\Credenciales::class . ':ValidarPostJSON');
});

$app->group('/json_bd', function (RouteCollectorProxy $group){
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->post('[/]', \UsuarioController::class . ':TraerTodos')->add(\Verificadora::class . ':VerificarUsuario');
});

// Run app
$app->run();

