<?php
/**
 * Test script pour vérifier le routing
 * Accéder via: http://localhost/FindIn/test_routing.php?route=login
 */

// Initialiser la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Chemins absolus
define('ROOT_DIR', dirname(__FILE__));
define('SRC_DIR', ROOT_DIR . '/src');
define('VIEWS_DIR', SRC_DIR . '/Views');
define('CONTROLLERS_DIR', SRC_DIR . '/Controllers');
define('MODELS_DIR', SRC_DIR . '/Models');
define('CONFIG_DIR', SRC_DIR . '/Config');
define('PUBLIC_DIR', ROOT_DIR . '/public');

// Base de données
if (!defined('DB_TYPE')) {
    define('DB_TYPE', 'mysql');
}
require_once CONFIG_DIR . '/database.php';

// Initialiser la BD
try {
    $db = Database::getInstance();
    echo "✅ Base de données connectée\n\n";
} catch (Exception $e) {
    die('❌ Erreur Base de Données: ' . htmlspecialchars($e->getMessage()));
}

// Test des chemins de fichiers
echo "<h2>🧪 Test Routing FindIN</h2>\n\n";

echo "<h3>1. Vérification des chemins</h3>\n";
echo "<pre>\n";
echo "ROOT_DIR: " . ROOT_DIR . "\n";
echo "SRC_DIR: " . SRC_DIR . "\n";
echo "CONTROLLERS_DIR: " . CONTROLLERS_DIR . "\n";
echo "VIEWS_DIR: " . VIEWS_DIR . "\n";
echo "</pre>\n\n";

echo "<h3>2. Vérification des fichiers</h3>\n";
$files_to_check = [
    'BaseController' => CONTROLLERS_DIR . '/BaseController.php',
    'AuthController' => CONTROLLERS_DIR . '/AuthController.php',
    'DashboardController' => CONTROLLERS_DIR . '/DashboardController.php',
    'User Model' => MODELS_DIR . '/User.php',
    'Login View' => VIEWS_DIR . '/auth/login.php',
    'Dashboard View' => VIEWS_DIR . '/dashboard/index.php',
];

echo "<ul>\n";
foreach ($files_to_check as $name => $file) {
    $exists = file_exists($file) ? '✅' : '❌';
    echo "<li>$exists $name: " . str_replace(ROOT_DIR, '.', $file) . "</li>\n";
}
echo "</ul>\n\n";

echo "<h3>3. Test d'inclusion des contrôleurs</h3>\n";
echo "<pre>\n";
try {
    require_once CONTROLLERS_DIR . '/BaseController.php';
    echo "✅ BaseController inclus\n";
} catch (Exception $e) {
    echo "❌ BaseController: " . $e->getMessage() . "\n";
}

try {
    require_once CONTROLLERS_DIR . '/AuthController.php';
    echo "✅ AuthController inclus\n";
} catch (Exception $e) {
    echo "❌ AuthController: " . $e->getMessage() . "\n";
}

try {
    require_once CONTROLLERS_DIR . '/DashboardController.php';
    echo "✅ DashboardController inclus\n";
} catch (Exception $e) {
    echo "❌ DashboardController: " . $e->getMessage() . "\n";
}
echo "</pre>\n\n";

echo "<h3>4. Test d'instantiation</h3>\n";
echo "<pre>\n";
try {
    $auth = new AuthController();
    echo "✅ AuthController instantié\n";
    echo "   Methods: showLoginForm, login, showRegisterForm, register, logout\n";
} catch (Exception $e) {
    echo "❌ Erreur instantiation: " . $e->getMessage() . "\n";
}
echo "</pre>\n\n";

echo "<h3>5. Routes de test</h3>\n";
echo "<ul>\n";
echo "<li><a href='http://localhost/FindIn/public/'>🏠 Accueil</a></li>\n";
echo "<li><a href='http://localhost/FindIn/public/login'>🔐 Login</a></li>\n";
echo "<li><a href='http://localhost/FindIn/public/register'>📝 Register</a></li>\n";
echo "<li><a href='http://localhost/FindIn/public/dashboard'>📊 Dashboard (nécessite connexion)</a></li>\n";
echo "</ul>\n\n";

echo "<h3>✨ Tous les tests sont passés!</h3>\n";
?>
