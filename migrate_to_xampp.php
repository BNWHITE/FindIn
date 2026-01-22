<?php
/**
 * Migration directe: Homebrew MySQL → XAMPP MySQL
 */

echo "🔄 MIGRATION DIRECTE\n";
echo str_repeat("=", 50) . "\n\n";

// Source: Homebrew MySQL (port 3306)
$source = new PDO('mysql:host=127.0.0.1;port=3306;dbname=findin', 'root', '');
$source->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Destination: XAMPP MySQL (port 3305)
$dest = new PDO('mysql:host=127.0.0.1;port=3305;dbname=findin', 'root', '');
$dest->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // 1. Copier utilisateurs
    echo "📤 Copie des utilisateurs...\n";
    $users = $source->query("SELECT * FROM utilisateurs")->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "❌ Aucun utilisateur trouvé en source!\n";
        exit(1);
    }
    
    // Vider la table XAMPP
    $dest->exec("DELETE FROM competences_utilisateurs");
    $dest->exec("DELETE FROM utilisateurs");
    
    // Insérer les utilisateurs
    foreach ($users as $user) {
        $sql = "INSERT INTO utilisateurs (id_utilisateur, email, prenom, nom, mot_de_passe, role, manager_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $dest->prepare($sql);
        $stmt->execute([
            $user['id_utilisateur'],
            $user['email'],
            $user['prenom'],
            $user['nom'],
            $user['mot_de_passe'],
            $user['role'],
            $user['manager_id'] ?? null
        ]);
    }
    
    echo "✅ " . count($users) . " utilisateurs copiés!\n\n";
    
    // 2. Copier compétences déclarées
    echo "📤 Copie des compétences déclarées...\n";
    $competences = $source->query("SELECT * FROM competences_utilisateurs")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($competences as $comp) {
        $sql = "INSERT INTO competences_utilisateurs (id, user_id, id_competence, niveau_declare, niveau_valide, id_manager_validateur, statut, commentaire, date_declaration, date_validation) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $dest->prepare($sql);
        $stmt->execute([
            $comp['id'],
            $comp['user_id'],
            $comp['id_competence'],
            $comp['niveau_declare'],
            $comp['niveau_valide'] ?? null,
            $comp['id_manager_validateur'] ?? null,
            $comp['statut'],
            $comp['commentaire'] ?? null,
            $comp['date_declaration'],
            $comp['date_validation'] ?? null
        ]);
    }
    
    echo "✅ " . count($competences) . " compétences copiées!\n\n";
    
    // 3. Vérifier
    echo "📊 VÉRIFICATION:\n";
    $count_users = $dest->query("SELECT COUNT(*) as total FROM utilisateurs")->fetch(PDO::FETCH_ASSOC);
    $count_comps = $dest->query("SELECT COUNT(*) as total FROM competences_utilisateurs")->fetch(PDO::FETCH_ASSOC);
    
    echo "✅ XAMPP MySQL - Utilisateurs: " . $count_users['total'] . "\n";
    echo "✅ XAMPP MySQL - Compétences: " . $count_comps['total'] . "\n";
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "🎉 MIGRATION RÉUSSIE!\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
