<?php
/**
 * Sync automatique Homebrew→XAMPP après chaque enregistrement
 * À ajouter dans AuthController::register()
 */

function syncUsersToXAMPP() {
    try {
        // Source: Homebrew (3306)
        $source = new PDO('mysql:host=127.0.0.1;port=3306;dbname=findin', 'root', '');
        
        // Dest: XAMPP (3305)
        $dest = new PDO('mysql:host=127.0.0.1;port=3305;dbname=findin', 'root', '');
        
        // Copier tous les utilisateurs
        $users = $source->query("SELECT * FROM utilisateurs ORDER BY id_utilisateur")->fetchAll(PDO::FETCH_ASSOC);
        
        // Vider et réinsérer
        $dest->exec("DELETE FROM utilisateurs");
        
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
        
        error_log("✅ Sync: " . count($users) . " users synced to XAMPP MySQL");
        return true;
    } catch (Exception $e) {
        error_log("❌ Sync error: " . $e->getMessage());
        return false;
    }
}
?>
