<?php

namespace Core;

use Exception;

// Classe responsable de la gestion des routes individuelles, incluant la correspondance d'URL et l'exécution des actions.
class Route{

  private string $path;
  private array $action;

  public function __construct($path , $action){
    $this->path = trim($path, '/');
    $this->action = $action;
  }

  public function match($url): bool{
    $path_replace = preg_replace('#:([\w]+)#', '[^/]+' , $this->path);
    $regex = preg_match("#^{$path_replace}$#i", $url, $matches);
    if($regex){
      return true;
    }else{
      return false;
    }
  }

  public  function execute(){
    [$controller , $method] = $this->action;
    if(!class_exists($controller) && !method_exists($controller, $method)){
      throw new Exception("Controller {$controller} or method {$method} not found");
    }
    $newClass = new $controller;
    return $newClass->$method();
  }

}