<?php

use App\Domain\User\UserController;
use Core\Router;

//Controllers
$userControllers = new UserController();

//utils
$url = $_SERVER["REQUEST_URI"];
$router = new Router($url);

//routes
$router->get("/test", [$userControllers::class, "user"], "user");

//initialisation de notre controllers(il doit étre a la fin du fichier)
$router->run();
