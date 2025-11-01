<?php

use App\Domain\User\UserController;

// Le router est passé depuis index.php
// Ce fichier contient toutes les routes API

// Controllers
$userController = new UserController();

// Routes User
$router->get("/api/", [$userController::class, "user"], "api.users.index");

// Routes de test
$router->get("/api/test", [$userController::class, "user"], "api.test");
