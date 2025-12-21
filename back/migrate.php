<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use App\Infrastructure\Core\Config\Database;

echo "=== Subscriptions Table Migration ===\n\n";

$pdo = Database::getInstance()->getConnection();

try {
  // Check current schema
  echo "Current schema:\n";
  $stmt = $pdo->query("DESCRIBE subscriptions");
  $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $existingColumns = [];
  foreach ($columns as $col) {
    echo "  - {$col["Field"]} ({$col["Type"]})\n";
    $existingColumns[] = $col["Field"];
  }
  echo "\n";

  // Step 1: Add rate_id column if it doesn't exist
  if (!in_array("rate_id", $existingColumns)) {
    echo "Adding rate_id column... ";
    $pdo->exec(
      "ALTER TABLE subscriptions ADD COLUMN rate_id CHAR(36) NULL AFTER end_date",
    );
    echo "OK\n";
  } else {
    echo "rate_id column already exists\n";
  }

  // Step 2: Make old rate column nullable (if it exists)
  if (in_array("rate", $existingColumns)) {
    echo "Making old rate column nullable... ";
    $pdo->exec(
      "ALTER TABLE subscriptions MODIFY COLUMN rate FLOAT NULL DEFAULT NULL",
    );
    echo "OK\n";
  }

  // Step 3: Add Stripe payment columns
  $stripeColumns = [
    "stripe_session_id" => "VARCHAR(255) NULL",
    "stripe_payment_status" =>
      "ENUM('pending', 'success', 'failed', 'refunded', 'cancelled') NULL",
    "amount" => "INT NULL",
    "currency" => "VARCHAR(10) NULL DEFAULT 'eur'",
    "paid_at" => "TIMESTAMP NULL",
    "refunded_at" => "TIMESTAMP NULL",
  ];

  foreach ($stripeColumns as $colName => $colDef) {
    if (!in_array($colName, $existingColumns)) {
      echo "Adding {$colName} column... ";
      $pdo->exec("ALTER TABLE subscriptions ADD COLUMN {$colName} {$colDef}");
      echo "OK\n";
    } else {
      echo "{$colName} column already exists\n";
    }
  }

  // Step 4: Migrate data from old 'rate' column to 'rate_id' if needed
  if (
    in_array("rate", $existingColumns) &&
    in_array("rate_id", $existingColumns)
  ) {
    // Check if there are subscriptions with rate but no rate_id
    $stmt = $pdo->query(
      "SELECT COUNT(*) FROM subscriptions WHERE rate IS NOT NULL AND rate_id IS NULL",
    );
    $count = (int) $stmt->fetchColumn();

    if ($count > 0) {
      echo "\nMigrating {$count} subscriptions from rate to rate_id...\n";

      // Get unique rates that need to be migrated
      $stmt = $pdo->query(
        "SELECT DISTINCT rate FROM subscriptions WHERE rate IS NOT NULL AND rate_id IS NULL",
      );
      $rates = $stmt->fetchAll(PDO::FETCH_COLUMN);

      foreach ($rates as $rateValue) {
        // Find or create a rate entry
        $stmt = $pdo->prepare(
          "SELECT id FROM rates WHERE price = :price LIMIT 1",
        );
        $stmt->execute([":price" => $rateValue]);
        $rateId = $stmt->fetchColumn();

        if (!$rateId) {
          // Create a new rate
          $rateId = sprintf(
            "%08x-%04x-%04x-%04x-%012x",
            mt_rand(0, 0xffffffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffffffffffff),
          );
          $stmt = $pdo->prepare(
            "INSERT INTO rates (id, type, calculation_rule, price) VALUES (:id, 'monthly_subscription', 'fixed', :price)",
          );
          $stmt->execute([":id" => $rateId, ":price" => $rateValue]);
          echo "  Created rate {$rateId} for price {$rateValue}\n";
        }

        // Update subscriptions
        $stmt = $pdo->prepare(
          "UPDATE subscriptions SET rate_id = :rate_id WHERE rate = :rate AND rate_id IS NULL",
        );
        $stmt->execute([":rate_id" => $rateId, ":rate" => $rateValue]);
        echo "  Updated subscriptions with rate {$rateValue} -> rate_id {$rateId}\n";
      }
    }
  }

  // Final schema
  echo "\n=== Final Schema ===\n";
  $stmt = $pdo->query("DESCRIBE subscriptions");
  $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($columns as $col) {
    echo "  - {$col["Field"]} ({$col["Type"]})\n";
  }

  echo "\n=== Migration Complete ===\n";
} catch (PDOException $e) {
  echo "ERROR: " . $e->getMessage() . "\n";
  exit(1);
}
