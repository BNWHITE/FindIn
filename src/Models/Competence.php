<?php
// models/Competence.php - VERSION SQLITE CORRIGÉE
require_once __DIR__ . '/Database.php';

class Competence {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllCompetences() {
        $sql = "SELECT c.*, cc.nom as categorie 
                FROM competences c
                LEFT JOIN categories_competences cc ON c.id_categorie = cc.id
                ORDER BY cc.nom, c.nom";
        
        try {
            return $this->db->query($sql)->fetchAll();
        } catch (Exception $e) {
            // En cas d'erreur, retourner une liste simple
            $sql = "SELECT * FROM competences ORDER BY nom";
            return $this->db->query($sql)->fetchAll();
        }
    }

    public function getUserCompetences($userId) {
        // Utiliser competences_utilisateurs uniquement
        $sql = "SELECT cu.*, c.nom as competence_nom, c.type_competence
                FROM competences_utilisateurs cu
                JOIN competences c ON cu.id_competence = c.id_competence
                WHERE cu.user_id = :user_id
                ORDER BY c.nom";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getUserCompetences: " . $e->getMessage());
            return [];
        }
    }

    public function addUserCompetence($userId, $competenceId, $niveauDeclare) {
        $sql = "INSERT INTO competences_utilisateurs 
                (user_id, id_competence, niveau_declare) 
                VALUES (:user_id, :competence_id, :niveau)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':competence_id' => $competenceId,
            ':niveau' => $niveauDeclare
        ]);
    }

    public function updateCompetenceValidation($compUserId, $niveauValide, $managerId) {
        $sql = "UPDATE competences_utilisateurs SET 
                niveau_valide = :niveau_valide,
                id_manager_validateur = :manager_id,
                date_validation = datetime('now')
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':niveau_valide' => $niveauValide,
            ':manager_id' => $managerId,
            ':id' => $compUserId
        ]);
    }

    public function searchByCompetence($searchTerm, $filters = []) {
        $sql = "SELECT DISTINCT u.id, u.prenom, u.nom, u.email, 
                u.departement,
                c.nom as competence_nom,
                cu.niveau_valide as niveau
                FROM utilisateurs u
                JOIN competences_utilisateurs cu ON u.id = cu.id_utilisateur
                JOIN competences c ON cu.id_competence = c.id
                WHERE c.nom LIKE :search 
                OR u.prenom LIKE :search 
                OR u.nom LIKE :search";
        
        // Ajouter des filtres
        if (!empty($filters['departement'])) {
            $sql .= " AND u.departement = :departement";
        }
        
        if (!empty($filters['niveau_min'])) {
            $sql .= " AND cu.niveau_valide >= :niveau_min";
        }
        
        $stmt = $this->db->prepare($sql);
        
        $params = [':search' => '%' . $searchTerm . '%'];
        
        if (!empty($filters['departement'])) {
            $params[':departement'] = $filters['departement'];
        }
        
        if (!empty($filters['niveau_min'])) {
            $params[':niveau_min'] = $filters['niveau_min'];
        }
        
        try {
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            // Version simplifiée en cas d'erreur
            error_log("Erreur recherche: " . $e->getMessage());
            return [];
        }
    }

    public function getCompetenceById($id) {
        $sql = "SELECT * FROM competences WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getAllCategories() {
        $sql = "SELECT * FROM categories_competences ORDER BY nom";
        try {
            return $this->db->query($sql)->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    // ========== CRUD COMPETENCES ==========
    
    public function createCompetence($data) {
        $sql = "INSERT INTO competences (nom, description, type_competence) VALUES (:nom, :description, :type)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => $data['nom'],
            ':description' => $data['description'] ?? '',
            ':type' => $data['type'] ?? 'technique'
        ]);
    }

    public function updateCompetence($id, $data) {
        $sql = "UPDATE competences SET nom = :nom, description = :description, type_competence = :type WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => $data['nom'],
            ':description' => $data['description'] ?? '',
            ':type' => $data['type'] ?? 'technique',
            ':id' => $id
        ]);
    }

    public function deleteCompetence($id) {
        // D'abord supprimer les liaisons
        $this->db->prepare("DELETE FROM competences_utilisateurs WHERE id_competence = :id")->execute([':id' => $id]);
        $this->db->prepare("DELETE FROM demandes_validation WHERE competence_id = :id")->execute([':id' => $id]);
        // Puis la compétence
        $sql = "DELETE FROM competences WHERE id_competence = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ========== COMPETENCES UTILISATEUR ==========
    
    public function addCompetenceToUser($userId, $competenceId, $niveau = 1) {
        // Vérifier si déjà existant
        $check = $this->db->prepare("SELECT * FROM competences_utilisateurs WHERE user_id = :uid AND id_competence = :cid");
        $check->execute([':uid' => $userId, ':cid' => $competenceId]);
        if ($check->fetch()) {
            // Mettre à jour
            $sql = "UPDATE competences_utilisateurs SET niveau_declare = :niveau WHERE user_id = :uid AND id_competence = :cid";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':niveau' => $niveau, ':uid' => $userId, ':cid' => $competenceId]);
        }
        
        $sql = "INSERT INTO competences_utilisateurs (user_id, id_competence, niveau_declare) VALUES (:uid, :cid, :niveau)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':uid' => $userId, ':cid' => $competenceId, ':niveau' => $niveau]);
    }

    public function removeCompetenceFromUser($userId, $competenceId) {
        $sql = "DELETE FROM competences_utilisateurs WHERE user_id = :uid AND id_competence = :cid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':uid' => $userId, ':cid' => $competenceId]);
    }

    public function updateUserCompetenceLevel($userId, $competenceId, $niveau) {
        $sql = "UPDATE competences_utilisateurs SET niveau_declare = :niveau WHERE user_id = :uid AND id_competence = :cid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':niveau' => $niveau, ':uid' => $userId, ':cid' => $competenceId]);
    }

    public function getUserCompetencesWithDetails($userId) {
        $sql = "SELECT cu.*, c.nom, c.description, c.type_competence 
                FROM competences_utilisateurs cu 
                JOIN competences c ON cu.id_competence = c.id_competence 
                WHERE cu.user_id = :uid 
                ORDER BY c.nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function requestValidation($userId, $competenceId, $niveau) {
        $sql = "INSERT INTO demandes_validation (user_id, competence_id, niveau_demande) VALUES (:uid, :cid, :niveau)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':uid' => $userId, ':cid' => $competenceId, ':niveau' => $niveau]);
    }

    public function getCompetenceStats() {
        $stats = [];
        $stats['total'] = $this->db->query("SELECT COUNT(*) FROM competences")->fetchColumn();
        $stats['techniques'] = $this->db->query("SELECT COUNT(*) FROM competences WHERE type_competence = 'technique'")->fetchColumn();
        $stats['soft'] = $this->db->query("SELECT COUNT(*) FROM competences WHERE type_competence = 'soft'")->fetchColumn();
        $stats['utilisations'] = $this->db->query("SELECT COUNT(*) FROM competences_utilisateurs")->fetchColumn();
        return $stats;
    }
}
?>
