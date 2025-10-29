<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Domain\User\UserController;
use Core\Router;
use Core\Controllers;

//Controllers
$userControllers = new UserController();

//utils
$url = $_SERVER['REQUEST_URI'];
$router = new Router($url);

//e navigateur fait automatiquement une requête pour /favicon.ico et notre router n'implemente pas encore cette(je sais pas si on le fera)
// Donc j'ai implemebté cette solution temporaire pour ne pas avoir de message d'erreur dans la console
if ($_SERVER['REQUEST_URI'] === '/favicon.ico') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_URI'] === '/.well-known/appspecific/com.chrome.devtools.json') {
    http_response_code(204);
    exit;
}

//routes
$router->get('/test', [$userControllers::class, 'user'], 'user');


//initialisation de notre controllers(il doit étre a la fin du fichier)
$router->run();


