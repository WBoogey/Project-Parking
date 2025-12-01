<?php

namespace App\Infrastructure\Core\Config;

use Dotenv\Dotenv;

class Config
{
  private static ?Config $instance = null;
  private array $config;

  private function __construct()
  {
    $dotenv = Dotenv::createImmutable(__DIR__ . "/../../../../");
    $dotenv->safeLoad();

    $this->config = [
      "db" => [
        "host" => $_ENV["DB_HOST"] ?? "localhost",
        "port" => (int) ($_ENV["DB_PORT"] ?? 3306),
        "dbname" => $_ENV["DB_NAME"] ?? "parking",
        "user" => $_ENV["DB_USER"] ?? "root",
        "password" => $_ENV["DB_PASSWORD"] ?? "",
        "charset" => $_ENV["DB_CHARSET"] ?? "utf8mb4",
      ],
      "jwt" => [
        "secret_key" => $_ENV["JWT_SECRET"] ?? "your-secret-key",
        "expiration" => (int) ($_ENV["JWT_EXPIRATION"] ?? 3600),
      ],
      "app" => [
        "url" => $_ENV["APP_URL"] ?? "http://localhost:8000",
        "env" => $_ENV["APP_ENV"] ?? "development",
      ],
    ];
  }

  public static function getInstance(): self
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function get(string $key, mixed $default = null): mixed
  {
    $keys = explode(".", $key);
    $value = $this->config;

    foreach ($keys as $k) {
      if (!isset($value[$k])) {
        return $default;
      }
      $value = $value[$k];
    }

    return $value;
  }

  public function all(): array
  {
    return $this->config;
  }
}
