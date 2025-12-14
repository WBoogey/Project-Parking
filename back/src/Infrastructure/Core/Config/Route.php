<?php

namespace App\Infrastructure\Core\Config;

use App\Infrastructure\Middleware\MiddlewareHandler;
use Exception;

/**
 * Classe responsable de la gestion des routes individuelles, incluant la correspondance d'URL et l'exécution des actions.
 */
class Route
{
  private string $path;
  private $action;
  private array $params = [];
  private ?MiddlewareHandler $middlewareHandler = null;

  /**
   * constructeur
   * @param callable|array $action
   */
  public function __construct(string $path, callable|array $action)
  {
    $this->path = trim($path, "/");
    $this->action = $action;
  }

  public function setMiddlewareHandler(MiddlewareHandler $handler): void
  {
    $this->middlewareHandler = $handler;
  }

  public function match(string $url): bool
  {
    $path_replace = preg_replace("#:([\w]+)#", "([^/]+)", $this->path);
    $regex = preg_match("#^{$path_replace}$#i", $url, $matches);

    if ($regex) {
      array_shift($matches);
      preg_match_all("#:([\w]+)#", $this->path, $paramNames);
      $this->params = array_combine($paramNames[1], $matches) ?: [];
      return true;
    }

    return false;
  }

  public function getParams(): array
  {
    return $this->params;
  }

  public function execute(): mixed
  {
    if (is_array($this->action)) {
      [$controllerOrInstance, $method] = $this->action;

      // Si c'est une instance de controller
      if (is_object($controllerOrInstance)) {
        $controller = $controllerOrInstance;
      } else {
        // Si c'est un nom de classe
        if (!class_exists($controllerOrInstance)) {
          throw new Exception("Controller {$controllerOrInstance} not found");
        }
        $controller = new $controllerOrInstance();
      }

      if (!method_exists($controller, $method)) {
        $className = get_class($controller);
        throw new Exception("Method {$method} not found in {$className}");
      }

      // Vérifier les middlewares si un handler est défini
      if ($this->middlewareHandler !== null) {
        $result = $this->middlewareHandler->handle($controller, $method);

        if (!$result["success"]) {
          http_response_code($result["status"] ?? 401);
          return json_encode([
            "type" => "https://httpstatuses.com/" . ($result["status"] ?? 401),
            "title" => $result["status"] === 403 ? "Forbidden" : "Unauthorized",
            "detail" => $result["error"],
            "status" => $result["status"] ?? 401,
          ]);
        }
      }

      return $controller->$method(...array_values($this->params));
    }

    if (is_callable($this->action)) {
      return call_user_func_array($this->action, $this->params);
    }

    throw new Exception("Invalid action type");
  }
}
