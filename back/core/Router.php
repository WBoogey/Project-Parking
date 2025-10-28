<?php

namespace Core;

use Exception;

//Gestionnaire principal des routes qui organise et dispatche les requêtes HTTP.
class Router{

  private array $routes;
  private string $url;
  private array $namedList;

  public function __construct($url){
    $this->url = trim($url,'/');
  }

  public function get(string $path, array $action , ?string $name){
    return $this->add('GET', $path, $action , $name);
  }

  public function post(string $path, array $action , ?string $name){
    return $this->add('POST', $path, $action , $name);
  }

  // Méthode générique pour ajouter une route.
  public function add(string $method, string $path , array $action , ?string $name){
    $route = new Route($path, $action);
    $this->routes[$method][] = $route;
    if($name){
      $this->namedList[$name] = $route;
    }

  }

  //Déso pour la docs merdique je l'améliore plut-tard, traite la requête courante et exécute la route correspondante.
  public function run(){
    if(!isset($this->routes[$_SERVER['REQUEST_METHOD']])){
        throw new Exception('REQUEST_METHOD does not exist');
    }
    foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route){
        if($route->match($this->url)){
            return $route->execute();
        }
    }
    throw new Exception('No matching routes');
  }

}