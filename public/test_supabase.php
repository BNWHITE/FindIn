<?php
// Test connexion Supabase
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../src/Config/database.php';
require_once __DIR__ . '/../src/Models/Database.php';

try {
    $db = Database::getInstance();
    
    // Récupère le nombre d'utilisateurs
    $stmt = $db->query('SELECT COUNT(*) as count FROM utilisateurs');
    $users = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_count = $users['count'] ?? 0;
    
    // Récupère le nombre de compétences
    $stmt = $db->query('SELECT COUNT(*) as count FROM competences');
    $skills = $stmt->fetch(PDO::FETCH_ASSOC);
    $skill_count = $skills['count'] ?? 0;
    
    // Récupère les utilisateurs
    $stmt = $db->query('SELECT * FROM utilisateurs LIMIT 5');
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Test Supabase</title><style>
    body { font-family: Arial; margin: 20px; background: #f5f5f5; }
    .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
    h1 { color: #333; }
    table { width: 100%; border-collapse: collapse; background: white; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background: #007bff; color: white; }
    </style></head><body>";
    
    echo "<h1>✅ Test Connexion Supabase</h1>";
    echo "<div class='success'><strong>✅ Connexion réussie!</strong></div>";
    
    echo "<div class='info'>";
    echo "<strong>Base de données:</strong> Supabase PostgreSQL<br>";
    echo "<strong>Utilisateurs:</strong> $user_count<br>";
    echo "<strong>Compétences:</strong> $skill_count<br>";
    echo "</div>";
    
    if (!empty($utilisateurs)) {
        echo "<h2>Utilisateurs:</h2>";
        echo "<table>";
        echo "<tr><th>Email</th><th>Prénom</th><th>Nom</th><th>Rôle</th></tr>";
        foreach ($utilisateurs as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['email'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($user['prenom'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($user['nom'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($user['role'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "</body></html>";
} catch (Exception $e) {
    echo "❌ Erreur: " . htmlspecialchars($e->getMessage());
}
?>
