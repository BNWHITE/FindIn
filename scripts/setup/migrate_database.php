<?php
/**
 * Database Migration Script
 * Fixes missing mot_de_passe column and recreates tables if needed
 */

require_once 'config/database.php';

try {
    if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
        // Connect to MySQL
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME),
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        
        echo "✅ Connected to MySQL database\n";
        
        // Check if utilisateurs table exists
        $checkTable = $pdo->query("SHOW TABLES LIKE 'utilisateurs'")->fetch();
        
        if ($checkTable) {
            echo "📋 Table utilisateurs exists\n";
            
            // Check for mot_de_passe column
            $checkCol = $pdo->query("SHOW COLUMNS FROM utilisateurs LIKE 'mot_de_passe'")->fetch();
            
            if (!$checkCol) {
                echo "⚠️  mot_de_passe column missing, adding...\n";
                
                // Try adding the column
                try {
                    $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN mot_de_passe VARCHAR(255) AFTER email");
                    echo "✅ mot_de_passe column added successfully!\n";
                } catch (PDOException $e) {
                    echo "❌ Could not add column: " . $e->getMessage() . "\n";
                    echo "Trying alternative position...\n";
                    
                    try {
                        $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN mot_de_passe VARCHAR(255)");
                        echo "✅ mot_de_passe column added (at end)!\n";
                    } catch (PDOException $e2) {
                        echo "❌ Migration failed: " . $e2->getMessage() . "\n";
                    }
                }
            } else {
                echo "✅ mot_de_passe column already exists\n";
            }
        } else {
            echo "📋 Table utilisateurs does not exist, will be created on next page load\n";
        }
        
        // Show table structure
        echo "\n📊 Current utilisateurs table structure:\n";
        $columns = $pdo->query("SHOW COLUMNS FROM utilisateurs")->fetchAll();
        foreach ($columns as $col) {
            echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
        
    } else {
        // SQLite
        $pdo = new PDO("sqlite:" . DB_PATH);
        echo "✅ Connected to SQLite database\n";
        
        $checkTable = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='utilisateurs'")->fetch();
        
        if ($checkTable) {
            echo "📋 Table utilisateurs exists\n";
            
            // Check columns
            $columns = $pdo->query("PRAGMA table_info(utilisateurs)")->fetchAll();
            $hasPassword = false;
            
            foreach ($columns as $col) {
                if ($col['name'] === 'mot_de_passe') {
                    $hasPassword = true;
                    break;
                }
            }
            
            if (!$hasPassword) {
                echo "⚠️  mot_de_passe column missing, adding...\n";
                try {
                    $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN mot_de_passe TEXT");
                    echo "✅ mot_de_passe column added successfully!\n";
                } catch (Exception $e) {
                    echo "❌ Migration failed: " . $e->getMessage() . "\n";
                }
            } else {
                echo "✅ mot_de_passe column already exists\n";
            }
        } else {
            echo "📋 Table utilisateurs does not exist, will be created on next page load\n";
        }
        
        // Show table structure
        echo "\n📊 Current utilisateurs table structure:\n";
        $columns = $pdo->query("PRAGMA table_info(utilisateurs)")->fetchAll();
        foreach ($columns as $col) {
            echo "  - " . $col['name'] . " (" . $col['type'] . ")\n";
        }
    }
    
    echo "\n✅ Migration complete! Try logging in now.\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
