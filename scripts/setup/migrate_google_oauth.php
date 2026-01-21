<?php
// migrate_google_oauth.php
// Ajoute les colonnes nécessaires pour l'authentification Google

require_once __DIR__ . '/models/Database.php';

try {
    $db = Database::getInstance();
    
    echo "Migration Google OAuth...\n";
    
    // Vérifier si la colonne google_id existe
    $columns = $db->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    
    $migrations = [];
    
    if (!in_array('google_id', $columns)) {
        $migrations[] = "ALTER TABLE users ADD COLUMN google_id VARCHAR(255) NULL AFTER password";
        echo "- Ajout colonne google_id\n";
    }
    
    if (!in_array('avatar_url', $columns)) {
        $migrations[] = "ALTER TABLE users ADD COLUMN avatar_url VARCHAR(500) NULL AFTER google_id";
        echo "- Ajout colonne avatar_url\n";
    }
    
    // Rendre le mot de passe nullable pour les utilisateurs Google
    $migrations[] = "ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NULL";
    echo "- Modification colonne password (nullable)\n";
    
    foreach ($migrations as $sql) {
        try {
            $db->exec($sql);
        } catch (PDOException $e) {
            // Ignorer si la colonne existe déjà
            if (strpos($e->getMessage(), 'Duplicate column') === false) {
                echo "  Avertissement: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n✅ Migration Google OAuth terminée!\n";
    echo "\n📋 Prochaines étapes:\n";
    echo "1. Allez sur https://console.cloud.google.com/apis/credentials\n";
    echo "2. Créez un projet ou sélectionnez-en un existant\n";
    echo "3. Créez des identifiants OAuth 2.0\n";
    echo "4. Ajoutez 'http://localhost:8000/auth/google/callback' comme URI de redirection\n";
    echo "5. Copiez le Client ID et Client Secret dans config/google_oauth.php\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
