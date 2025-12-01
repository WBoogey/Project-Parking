<?php

namespace App\Infrastructure\Core\Config;

use PDO;
use PDOException;

class Database
{
  private PDO $connection;

  private static ?Database $instance = null;

  private function __construct()
  {
    $config = Config::getInstance();

    $host = $config->get("db.host");
    $port = $config->get("db.port");
    $dbname = $config->get("db.dbname");
    $user = $config->get("db.user");
    $password = $config->get("db.password");
    $charset = $config->get("db.charset");

    try {
      // Connexion sans base de données pour vérifier/créer
      $this->connection = new PDO(
        "mysql:host={$host};port={$port};charset={$charset}",
        $user,
        $password,
      );
      $this->connection->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION,
      );

      // Vérifier si la base existe, sinon la créer
      $result = $this->connection->query("SHOW DATABASES LIKE '{$dbname}'");
      $dbCreated = false;
      if ($result->rowCount() === 0) {
        $this->connection->exec("CREATE DATABASE `{$dbname}`");
        $dbCreated = true;
      }

      // Reconnexion avec la base de données
      $this->connection = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}",
        $user,
        $password,
      );
      $this->connection->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION,
      );

      // Auto-migration si la base vient d'être créée ou si les tables n'existent pas
      if ($dbCreated || !$this->tablesExist()) {
        $this->runMigrations();
      }
    } catch (PDOException $e) {
      throw new PDOException("Database connection failed: " . $e->getMessage());
    }
  }

  public static function getInstance(): Database
  {
    if (self::$instance === null) {
      self::$instance = new Database();
    }

    return self::$instance;
  }

  public function getConnection(): PDO
  {
    return $this->connection;
  }

  private function tablesExist(): bool
  {
    $result = $this->connection->query("SHOW TABLES LIKE 'users'");
    return $result->rowCount() > 0;
  }

  private function runMigrations(): void
  {
    $sqlFile = __DIR__ . "/../../../../database/create_tables.sql";

    if (!file_exists($sqlFile)) {
      return;
    }

    $sql = file_get_contents($sqlFile);

    // Séparer les requêtes par point-virgule
    $statements = array_filter(
      array_map("trim", explode(";", $sql)),
      fn($s) => !empty($s) && !str_starts_with($s, "--"),
    );

    foreach ($statements as $statement) {
      if (!empty($statement)) {
        $this->connection->exec($statement);
      }
    }
  }
}
