<?php
// start.php - Script de démarrage et vérification
session_start();
require_once 'config/database.php';

echo "<h1>🔧 Vérification et Installation FindIN</h1>";

// Vérifier PHP
echo "<h3>✅ PHP Version: " . phpversion() . "</h3>";

// Vérifier les extensions
$required_extensions = ['pdo_mysql', 'mbstring', 'session'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ Extension $ext chargée<br>";
    } else {
        echo "❌ Extension $ext manquante<br>";
    }
}

// Tester la connexion MySQL
echo "<h3>🔌 Test de connexion MySQL</h3>";
try {
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    echo "✅ Connexion MySQL réussie<br>";
    
    // Créer la base si elle n'existe pas
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Base de données créée/verifiée<br>";
    
    // Sélectionner la base
    $pdo->exec("USE " . DB_NAME);
    
    // Lire et exécuter le script SQL
    $sql_file = 'create_database_simple.sql';
    if (file_exists($sql_file)) {
        $sql = file_get_contents($sql_file);
        $pdo->exec($sql);
        echo "✅ Données de test créées<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur MySQL: " . $e->getMessage() . "<br>";
    echo "<p>Solutions possibles :</p>";
    echo "<ul>";
    echo "<li>Vérifiez que MySQL est démarré</li>";
    echo "<li>Sur macOS: <code>brew services start mysql</code></li>";
    echo "<li>Sur MAMP: Utilisez le port 8889</li>";
    echo "<li>Vérifiez les identifiants dans config/database.php</li>";
    echo "</ul>";
}

// Vérifier la structure des dossiers
echo "<h3>📁 Structure des dossiers</h3>";
$required_dirs = ['controllers', 'models', 'views', 'assets/css', 'assets/js', 'config'];
foreach ($required_dirs as $dir) {
    if (is_dir($dir)) {
        echo "✅ Dossier $dir existe<br>";
    } else {
        echo "⚠️ Dossier $dir manquant - création...<br>";
        mkdir($dir, 0777, true);
    }
}

// Vérifier les fichiers essentiels
echo "<h3>📄 Fichiers essentiels</h3>";
$required_files = [
    'index.php',
    'router.php',
    'config/database.php',
    'controllers/BaseController.php',
    'controllers/AuthController.php',
    'models/Database.php',
    'models/User.php',
    'views/auth/login.php',
    'create_database_simple.sql'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
    } else {
        echo "❌ $file manquant<br>";
    }
}

echo "<hr>";
echo "<h2>🎉 Installation terminée !</h2>";
echo "<p>Accédez à l'application : <a href='http://localhost:8000'>http://localhost:8000</a></p>";
echo "<p>Identifiants de test : admin@findin.com / (n'importe quel mot de passe)</p>";
?>
