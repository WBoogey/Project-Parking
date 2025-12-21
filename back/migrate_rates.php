<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use App\Infrastructure\Core\Config\Database;

echo "=== Rates Table Migration: Add parking_id ===\n\n";

$pdo = Database::getInstance()->getConnection();

try {
    // Check current schema
    echo "Current rates schema:\n";
    $stmt = $pdo->query("DESCRIBE rates");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $existingColumns = [];
    foreach ($columns as $col) {
        echo "  - {$col["Field"]} ({$col["Type"]})\n";
        $existingColumns[] = $col["Field"];
    }
    echo "\n";

    // Step 1: Add parking_id column if it doesn't exist
    if (!in_array("parking_id", $existingColumns)) {
        echo "Adding parking_id column... ";
        $pdo->exec(
            "ALTER TABLE rates ADD COLUMN parking_id CHAR(36) NULL AFTER id"
        );
        echo "OK\n";
    } else {
        echo "parking_id column already exists\n";
    }

    // Step 2: Check if foreign key exists
    $stmt = $pdo->query("
        SELECT CONSTRAINT_NAME
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'rates'
        AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        AND CONSTRAINT_NAME = 'fk_rates_parking_id'
    ");
    $fkExists = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fkExists) {
        echo "Adding foreign key constraint... ";
        try {
            $pdo->exec(
                "ALTER TABLE rates ADD CONSTRAINT fk_rates_parking_id
                 FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE"
            );
            echo "OK\n";
        } catch (PDOException $e) {
            echo "SKIPPED (may have orphan data): " . $e->getMessage() . "\n";
        }
    } else {
        echo "Foreign key constraint already exists\n";
    }

    // Step 3: Check if index exists
    $stmt = $pdo->query("SHOW INDEX FROM rates WHERE Key_name = 'idx_rates_parking_id'");
    $indexExists = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$indexExists) {
        echo "Creating index on parking_id... ";
        try {
            $pdo->exec("CREATE INDEX idx_rates_parking_id ON rates(parking_id)");
            echo "OK\n";
        } catch (PDOException $e) {
            echo "SKIPPED: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Index already exists\n";
    }

    // Step 4: Check for orphan rates (rates without parking_id)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM rates WHERE parking_id IS NULL");
    $orphanCount = (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    if ($orphanCount > 0) {
        echo "\nWARNING: Found {$orphanCount} rate(s) without parking_id.\n";
        echo "These rates need to be assigned to a parking or deleted.\n";

        // List orphan rates
        $stmt = $pdo->query("SELECT id, type, price FROM rates WHERE parking_id IS NULL");
        $orphans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\nOrphan rates:\n";
        foreach ($orphans as $orphan) {
            echo "  - ID: {$orphan['id']}, Type: {$orphan['type']}, Price: {$orphan['price']}\n";
        }

        // Check if there's at least one parking to assign to
        $stmt = $pdo->query("SELECT id, location FROM parkings LIMIT 1");
        $parking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($parking) {
            echo "\nFound parking: {$parking['location']} (ID: {$parking['id']})\n";
            echo "Assigning orphan rates to this parking... ";
            $updateStmt = $pdo->prepare("UPDATE rates SET parking_id = :parking_id WHERE parking_id IS NULL");
            $updateStmt->execute([":parking_id" => $parking['id']]);
            echo "OK ({$orphanCount} rates updated)\n";
        } else {
            echo "\nNo parking found. Please create a parking first, then re-run this migration.\n";
        }
    }

    // Final schema
    echo "\n=== Final Schema ===\n";
    $stmt = $pdo->query("DESCRIBE rates");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "  - {$col["Field"]} ({$col["Type"]})\n";
    }

    // Show rates count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM rates");
    $count = (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "\nTotal rates: {$count}\n";

    // Show rates with their parkings
    if ($count > 0) {
        echo "\nRates with parking info:\n";
        $stmt = $pdo->query("
            SELECT r.id, r.type, r.price, r.parking_id, p.location
            FROM rates r
            LEFT JOIN parkings p ON r.parking_id = p.id
        ");
        $rates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rates as $rate) {
            $location = $rate['location'] ?? 'NO PARKING';
            echo "  - {$rate['type']}: {$rate['price']} EUR @ {$location}\n";
        }
    }

    echo "\n=== Migration Complete ===\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
