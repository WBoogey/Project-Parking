<?php

namespace App\Infrastructure\Core\Config;

use PDO;

class Database
{
  private PDO $connection;

  private static ?Database $instance = null;

  private function __construct(array $config)
  {
    $dsn = sprintf(
      "mysql:host=%s;port=%d;dbname=%s;charset=%s",
      $config["host"],
      $config["port"],
      $config["database"],
      $config["charset"],
    );

    $this->connection = new PDO($dsn, $config["username"], $config["password"]);
    $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public static function getInstance(array $config): Database
  {
    if (self::$instance === null) {
      self::$instance = new Database($config);
    }

    return self::$instance;
  }

  public function getConnection(): PDO
  {
    return $this->connection;
  }
}
