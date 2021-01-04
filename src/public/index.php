<?php

use App\Database;
use App\Authorization;
use App\Session;
use \Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Twig
 */

$loader = new FilesystemLoader('../view');
$twig = new Environment($loader);
/**
 * Slim Framework
 */
$app = AppFactory::create();
$app->addBodyParsingMiddleware();

/** Middleware */
$session = new Session();
$sessionMiddleware = function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($session) {
    $session->start();
    $response = $handler->handle($request);
    $session->save();
    return $response;
};
$app->add($sessionMiddleware);

$config = include_once '../config/database.php';
$dsn = $config['dsn'];
$username = $config['username'];
$password = $config['password'];
$database = new Database($dsn, $username, $password);
$authorization = new Authorization($database, $session);

/**
 * Routing
 */
$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig, $session) {

    $body = $twig->render('index.twig', [
        'user' => $session->getData('user')
    ]);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/login', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig, $session) {

    $body = $twig->render('login.twig', [
        'message' => $session->flush('message'),
        'form'    => $session->flush('form')
    ]);
    $response->getBody()->write($body);
    return $response;
});

$app->post('/login-post', function (ServerRequestInterface $request, ResponseInterface $response) use ($authorization, $session) {
    $params = (array)$request->getParsedBody();

    try {
        $authorization->login($params['email'], $params['password']);
    } catch (\App\AuthorizationException $exception) {
        $session->setData('message', $exception->getMessage());
        $session->setData('form', $params);

        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    return $response->withHeader('Location', '/')->withStatus(302);
});

$app->get('/register', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig, $session) {

    $body = $twig->render('register.twig', [
        'message' => $session->flush('message'),
        'form' => $session->flush('form')
    ]);
    $response->getBody()->write($body);
    return $response;
});

$app->post('/register-post', function (ServerRequestInterface $request, ResponseInterface $response) use ($authorization, $session) {

    $params = (array)$request->getParsedBody();

    try {
        $authorization->register($params);
    } catch (\App\AuthorizationException $exception) {
        $session->setData('message', $exception->getMessage());
        $session->setData('form', $params);

        return $response->withHeader('Location', '/register')->withStatus(302);
    }

    return $response->withHeader('Location', '/')->withStatus(302);
});

$app->get('/logout', function (ServerRequestInterface $request, ResponseInterface $response) use ($session){
    $session->setData('user', null);
    return $response->withHeader('Location', '/')->withStatus(302);
});


$app->run();
