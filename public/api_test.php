<?php
// Test direct Supabase sans routeur
require_once __DIR__ . '/../src/Config/database.php';
require_once __DIR__ . '/../src/Models/Database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $db = Database::getInstance();
    
    // Test 1: Compte utilisateurs
    $stmt = $db->query('SELECT COUNT(*) as count FROM utilisateurs');
    $users_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    // Test 2: Compte compétences
    $stmt = $db->query('SELECT COUNT(*) as count FROM competences');
    $skills_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    // Test 3: Liste utilisateurs
    $stmt = $db->query('SELECT id_utilisateur, email, prenom, nom, role FROM utilisateurs LIMIT 5');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Test 4: Liste compétences
    $stmt = $db->query('SELECT id_competence, nom, type_competence FROM competences LIMIT 10');
    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Réponse JSON
    echo json_encode([
        'success' => true,
        'message' => '✅ Connexion Supabase réussie!',
        'database' => 'Supabase PostgreSQL',
        'counts' => [
            'utilisateurs' => $users_count,
            'competences' => $skills_count
        ],
        'utilisateurs' => $users,
        'competences' => $skills
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
