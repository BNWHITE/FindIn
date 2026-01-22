<?php
require_once __DIR__ . '/src/Config/database.php';

try {
    $db = Database::getInstance();
    
    $version = $db->query("SELECT @@version as version, @@datadir as datadir")->fetch(PDO::FETCH_ASSOC);
    
    echo "📊 MYSQL UTILISÉ PAR PHP:\n";
    echo "Version: " . $version['version'] . "\n";
    echo "Data Directory: " . $version['datadir'] . "\n";
    
    $count = $db->query("SELECT COUNT(*) as total FROM utilisateurs")->fetch(PDO::FETCH_ASSOC);
    echo "Utilisateurs: " . $count['total'] . "\n";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
?>
