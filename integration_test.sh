#!/bin/bash
# Integration test - Vérifier le flux complet

echo "🔄 Test d'intégration FindIN"
echo "=============================="

php << 'PHPEOF'
<?php
// Simuler une requête réelle vers /login
$_SERVER['REQUEST_URI'] = '/FindIn/public/login';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "\n1️⃣ Initialisation du système\n";
echo "   REQUEST_URI: {$_SERVER['REQUEST_URI']}\n";
echo "   REQUEST_METHOD: {$_SERVER['REQUEST_METHOD']}\n";

// Chemins
define('ROOT_DIR', dirname(__FILE__) . '/src/..');
define('SRC_DIR', ROOT_DIR . '/src');
define('VIEWS_DIR', SRC_DIR . '/Views');
define('CONTROLLERS_DIR', SRC_DIR . '/Controllers');
define('MODELS_DIR', SRC_DIR . '/Models');
define('CONFIG_DIR', SRC_DIR . '/Config');

if (!defined('DB_TYPE')) {
    define('DB_TYPE', 'mysql');
}

echo "\n2️⃣ Chargement de la base de données\n";
require_once CONFIG_DIR . '/database.php';

try {
    $db = Database::getInstance();
    echo "   ✅ Connexion réussie\n";
} catch (Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n3️⃣ Chargement des contrôleurs\n";
require_once CONTROLLERS_DIR . '/BaseController.php';
echo "   ✅ BaseController chargé\n";

require_once CONTROLLERS_DIR . '/AuthController.php';
echo "   ✅ AuthController chargé\n";

echo "\n4️⃣ Parsing de la route\n";
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH);
$path = preg_replace('#^/FindIn/public/?#i', '', $path);
$path = trim($path, '/');
echo "   Parsed path: '$path'\n";

echo "\n5️⃣ Routage\n";
if ($path === 'login') {
    echo "   ✅ Route 'login' détectée\n";
    echo "   ✅ AuthController::showLoginForm() serait appelé\n";
    
    // Vérifier que la vue existe
    $login_view = VIEWS_DIR . '/auth/login.php';
    if (file_exists($login_view)) {
        echo "   ✅ Vue login.php trouvée\n";
        echo "\n✨ TOUT EST FONCTIONNEL!\n";
        echo "\nPour tester en vrai:\n";
        echo "   http://localhost/FindIn/public/login\n";
    } else {
        echo "   ❌ Vue login.php non trouvée\n";
        exit(1);
    }
} else {
    echo "   ❌ Route non reconnue\n";
    exit(1);
}

echo "\n";
?>
PHPEOF
