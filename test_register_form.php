<?php
/**
 * test_register_form.php - Simuler l'envoi du formulaire de register
 */

// Simuler une requête POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'email' => 'simulate_test_' . time() . '@findin.fr',
    'prenom' => 'Simulated',
    'nom' => 'User',
    'password' => 'SimTest123!'
];

session_start();

echo "🔍 SIMULATION DE SOUMISSION DE FORMULAIRE\n";
echo str_repeat("=", 50) . "\n\n";

echo "📋 Données POST:\n";
echo "  Email: {$_POST['email']}\n";
echo "  Prenom: {$_POST['prenom']}\n";
echo "  Nom: {$_POST['nom']}\n";
echo "  Password: [MASKED]\n\n";

// Charger l'auth controller
require_once __DIR__ . '/src/Config/database.php';
require_once __DIR__ . '/src/Controllers/BaseController.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Controllers/AuthController.php';

try {
    echo "🚀 Appel AuthController::register()...\n\n";
    $authController = new AuthController();
    $authController->register();
    
    echo "\n✅ Aucune exception levée\n";
    
    if (isset($_SESSION['success'])) {
        echo "✅ SUCCESS SESSION: {$_SESSION['success']}\n";
    }
    
    if (isset($_SESSION['error'])) {
        echo "❌ ERROR SESSION: {$_SESSION['error']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ EXCEPTION LEVÉE:\n";
    echo "  Message: {$e->getMessage()}\n";
    echo "  Code: {$e->getCode()}\n";
    echo "  File: {$e->getFile()}\n";
    echo "  Line: {$e->getLine()}\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Vérifier si l'utilisateur s'est créé
require_once __DIR__ . '/src/Models/User.php';
$userModel = new User();
$created = $userModel->getUserByEmail($_POST['email']);

if ($created) {
    echo "✅ UTILISATEUR CRÉÉ AVEC SUCCÈS:\n";
    echo "  ID: {$created['id_utilisateur']}\n";
    echo "  Email: {$created['email']}\n";
} else {
    echo "❌ L'UTILISATEUR N'A PAS ÉTÉ CRÉÉ!\n";
}

?>
