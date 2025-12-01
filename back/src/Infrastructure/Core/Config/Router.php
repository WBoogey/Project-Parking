<?php

namespace App\Infrastructure\Core\Config;

use App\Infrastructure\Middleware\MiddlewareHandler;
use Exception;

/**
 * Gestionnaire principal des routes qui organise et dispatche les requêtes HTTP.
 */
class Router
{
  /**
   * @var array<string, Route[]>
   */
  private array $routes = [];

  /**
   * @var string
   */
  private string $url;

  /**
   * @var array<string, Route>
   */
  private array $namedList = [];

  /**
   * @var MiddlewareHandler|null
   */
  private ?MiddlewareHandler $middlewareHandler = null;

  /**
   * @param string $url
   */
  public function __construct(string $url)
  {
    $this->url = trim($url, "/");
  }

  /**
   * @param MiddlewareHandler $handler
   */
  public function setMiddlewareHandler(MiddlewareHandler $handler): void
  {
    $this->middlewareHandler = $handler;
  }

  /**
   * @param string $path
   * @param callable|array $action
   * @param string|null $name
   * @return void
   */
  public function get(
    string $path,
    callable|array $action,
    ?string $name = null,
  ): void {
    $this->add("GET", $path, $action, $name);
  }

  /**
   * @param string $path
   * @param callable|array $action
   * @param string|null $name
   * @return void
   */
  public function post(
    string $path,
    callable|array $action,
    ?string $name = null,
  ): void {
    $this->add("POST", $path, $action, $name);
  }

  /**
   * @param string $path
   * @param callable|array $action
   * @param string|null $name
   * @return void
   */
  public function put(
    string $path,
    callable|array $action,
    ?string $name = null,
  ): void {
    $this->add("PUT", $path, $action, $name);
  }

  /**
   * @param string $path
   * @param callable|array $action
   * @param string|null $name
   * @return void
   */
  public function delete(
    string $path,
    callable|array $action,
    ?string $name = null,
  ): void {
    $this->add("DELETE", $path, $action, $name);
  }

  /**
   * Méthode générique pour ajouter une route
   * @param string $method
   * @param string $path
   * @param callable|array $action
   * @param string|null $name
   * @return void
   */
  public function add(
    string $method,
    string $path,
    callable|array $action,
    ?string $name = null,
  ): void {
    $route = new Route($path, $action);

    if ($this->middlewareHandler !== null) {
      $route->setMiddlewareHandler($this->middlewareHandler);
    }

    $this->routes[$method][] = $route;

    if ($name) {
      $this->namedList[$name] = $route;
    }
  }

  /**
   * Traite la requête courante et exécute la route correspondante
   * @return mixed
   * @throws Exception
   */
  public function run(): mixed
  {
    if (!isset($this->routes[$_SERVER["REQUEST_METHOD"]])) {
      throw new Exception("REQUEST_METHOD does not exist");
    }

    foreach ($this->routes[$_SERVER["REQUEST_METHOD"]] as $route) {
      if ($route->match($this->url)) {
        return $route->execute();
      }
    }

    throw new Exception("No matching routes");
  }
}
