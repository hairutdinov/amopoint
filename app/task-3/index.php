<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . "/../bootstrap.php";


use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;
use entities\PageHitCounter;

/* @var $entityManager EntityManager */

$app = AppFactory::create();

session_start();

$users = [
    'admin' => ['password' => 'password1', 'name' => 'Admin'],
    'user' => ['password' => 'password2', 'name' => 'User'],
];

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->get('/', function (Request $request, Response $response, $args) use ($entityManager, $app) {
    $htmlContent = file_get_contents(__DIR__ . '/templates/index.html');
    $response->getBody()->write($htmlContent);
    return $response;
});

$app->get('/admin', function (Request $request, Response $response, $args) use ($entityManager, $app) {
    if (!isset($_SESSION['user'])) {
        return $response
            ->withHeader('Location', 'http://localhost:8080')
            ->withStatus(302);
    }

    $htmlContent = file_get_contents(__DIR__ . '/templates/admin.html');
    $response->getBody()->write($htmlContent);
    return $response;
});


$app->get('/unique-visits-per-hour', function (Request $request, Response $response, $args) use ($entityManager) {
    $model = new PageHitCounter();
    $response->getBody()->write(json_encode($model->getUniqueVisitsByHoursForToday($entityManager), JSON_UNESCAPED_UNICODE));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(202);
});


$app->get('/visits-by-city', function (Request $request, Response $response, $args) use ($entityManager) {
    $model = new PageHitCounter();
    $response->getBody()->write(json_encode($model->getVisitsByCity($entityManager), JSON_UNESCAPED_UNICODE));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(202);
});


function authenticate($username, $password)
{
    global $users;
    if (isset($users[$username])) {
        if ($users[$username]['password'] === $password) {
            $_SESSION['user'] = $users[$username];
            return true;
        }
    }
    return false;
}

$app->post('/login', function (Request $request, Response $response, $args) {
    $parsedBody = $request->getParsedBody();
    $username = $parsedBody['username'] ?? '';
    $password = $parsedBody['password'] ?? '';

    if (authenticate($username, $password)) {
        return $response
            ->withHeader('Location', 'http://localhost:8080/admin')
            ->withStatus(302);
    }

    return $response
        ->withHeader('Location', 'http://localhost:8080')
        ->withStatus(302);
});

$app->post('/logout', function (Request $request, Response $response, $args) {
    unset($_SESSION['user']);
    return $response
        ->withHeader('Location', 'http://localhost:8080')
        ->withStatus(302);
});


$app->post('/save', function (Request $request, Response $response, $args) use ($entityManager) {
    /* @var $model PageHitCounter */

    $parsedBody = $request->getParsedBody();

    $model = new PageHitCounter();
    $model->setIp($parsedBody["ip"] ?? null)
        ->setCity($parsedBody["city"] ?? null)
        ->setDevice($parsedBody["deviceType"] ?? null);

    $entityManager->persist($model);
    $entityManager->flush();

    $response->getBody()->write(json_encode($parsedBody, JSON_UNESCAPED_UNICODE));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(201);
});

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    throw new HttpNotFoundException($request);
});

$app->run();