<?php
// check.php
echo "Vérification de l'environnement PHP...<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Extensions chargées: " . implode(", ", get_loaded_extensions()) . "<br>";

// Vérifier la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=gestion_competences', 'root', '');
    echo "✅ Connexion MySQL OK<br>";
    
    // Vérifier les tables
    $tables = ['utilisateurs', 'competences', 'departements', 'entreprises'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' existe<br>";
        } else {
            echo "❌ Table '$table' manquante<br>";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur MySQL: " . $e->getMessage() . "<br>";
}

// Vérifier les fichiers
$requiredFiles = [
    'index.php',
    'router.php',
    'config/database.php',
    'controllers/BaseController.php',
    'controllers/AuthController.php',
    'models/Database.php',
    'models/User.php',
    'views/auth/login.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✅ Fichier '$file' existe<br>";
    } else {
        echo "❌ Fichier '$file' manquant<br>";
    }
}
?>
