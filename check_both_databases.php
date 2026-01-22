<?php
echo "🔍 DIAGNOSTIC UTILISATEURS\n";
echo str_repeat("=", 50) . "\n\n";

// XAMPP MySQL (3305)
try {
    $xampp = new PDO('mysql:host=127.0.0.1;port=3305;dbname=findin', 'root', '');
    $count = $xampp->query("SELECT COUNT(*) as total FROM utilisateurs")->fetch(PDO::FETCH_ASSOC);
    echo "✅ XAMPP MySQL (3305): " . $count['total'] . " utilisateurs\n";
    
    // Afficher les 3 derniers
    $users = $xampp->query("SELECT email FROM utilisateurs ORDER BY id_utilisateur DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $u) {
        echo "   - " . $u['email'] . "\n";
    }
} catch (Exception $e) {
    echo "❌ XAMPP MySQL (3305): " . $e->getMessage() . "\n";
}

echo "\n";

// Homebrew MySQL (3306)
try {
    $homebrew = new PDO('mysql:host=127.0.0.1;port=3306;dbname=findin', 'root', '');
    $count = $homebrew->query("SELECT COUNT(*) as total FROM utilisateurs")->fetch(PDO::FETCH_ASSOC);
    echo "✅ Homebrew MySQL (3306): " . $count['total'] . " utilisateurs\n";
    
    // Afficher les 3 derniers
    $users = $homebrew->query("SELECT email FROM utilisateurs ORDER BY id_utilisateur DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $u) {
        echo "   - " . $u['email'] . "\n";
    }
} catch (Exception $e) {
    echo "❌ Homebrew MySQL (3306): " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
?>
