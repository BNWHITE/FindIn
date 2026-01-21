<?php
// models/Validation.php
require_once __DIR__ . '/Database.php';

class Validation {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getPendingValidations($managerId) {
        $sql = "SELECT cu.*, u.prenom, u.nom, u.email, c.nom as competence_nom
                FROM competences_utilisateurs cu
                JOIN utilisateurs u ON cu.id_utilisateur = u.id_utilisateur
                JOIN competences c ON cu.id_competence = c.id_competence
                WHERE cu.niveau_valide IS NULL 
                AND cu.niveau_declare IS NOT NULL
                AND EXISTS (
                    SELECT 1 FROM roles_utilisateurs 
                    WHERE id_utilisateur = :manager_id 
                    AND role = 'manager'
                )
                ORDER BY cu.date_validation DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':manager_id' => $managerId]);
        return $stmt->fetchAll();
    }

    public function validateCompetence($compUserId, $niveauValide, $managerId) {
        $sql = "UPDATE competences_utilisateurs SET 
                niveau_valide = :niveau_valide,
                id_manager_validateur = :manager_id,
                date_validation = NOW()
                WHERE id_comp_utilisateur = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':niveau_valide' => $niveauValide,
            ':manager_id' => $managerId,
            ':id' => $compUserId
        ]);
    }
}
?>
