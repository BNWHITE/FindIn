<?php
/**
 * Test Login Script
 * Creates a test user and verifies login functionality
 */

require_once 'config/database.php';

try {
    if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME),
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } else {
        $pdo = new PDO("sqlite:" . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    echo "🔐 Login Test\n";
    echo "=============\n\n";
    
    // Test 1: Check if admin user exists
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute(['admin@findin.com']);
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "✅ Admin user found:\n";
        echo "   Email: " . $admin['email'] . "\n";
        echo "   Name: " . ($admin['prenom'] ?? 'N/A') . " " . ($admin['nom'] ?? 'N/A') . "\n";
        echo "   Has password: " . (!empty($admin['mot_de_passe']) ? 'YES' : 'NO (can set)') . "\n";
    } else {
        echo "❌ Admin user not found\n";
        
        // Create admin user
        echo "\n📝 Creating admin user...\n";
        $id = defined('DB_TYPE') && DB_TYPE === 'mysql' ? 'UUID()' : null;
        
        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            $pdo->exec("INSERT INTO utilisateurs (id_utilisateur, email, prenom, nom, role) VALUES (UUID(), 'admin@findin.com', 'Admin', 'FindIN', 'admin')");
        } else {
            $pdo->exec("INSERT INTO utilisateurs (email, prenom, nom, role) VALUES ('admin@findin.com', 'Admin', 'FindIN', 'admin')");
        }
        
        echo "✅ Admin user created\n";
    }
    
    // Test 2: Set a password for testing
    echo "\n📝 Setting test password...\n";
    $testPassword = 'test123456';
    $hash = password_hash($testPassword, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE email = ?");
    $stmt->execute([$hash, 'admin@findin.com']);
    
    echo "✅ Test password set\n";
    echo "   Email: admin@findin.com\n";
    echo "   Password: " . $testPassword . "\n";
    
    // Test 3: Verify password hash
    echo "\n🔍 Verifying password...\n";
    $stmt = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE email = ?");
    $stmt->execute(['admin@findin.com']);
    $user = $stmt->fetch();
    
    if ($user && password_verify($testPassword, $user['mot_de_passe'])) {
        echo "✅ Password verification works!\n";
    } else {
        echo "❌ Password verification failed\n";
    }
    
    echo "\n🚀 Ready to test login at http://localhost:8000/login\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
