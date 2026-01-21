<?php
// models/Project.php
require_once __DIR__ . '/Database.php';

class Project {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getUserProjects($userId) {
        $sql = "SELECT p.*, mp.role_dans_projet
                FROM projets p
                JOIN membres_projets mp ON p.id_projet = mp.id_projet
                WHERE mp.id_utilisateur = :user_id
                ORDER BY p.date_debut DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getAllProjects() {
        $sql = "SELECT p.*, u.prenom, u.nom as responsable_nom
                FROM projets p
                LEFT JOIN utilisateurs u ON p.id_responsable = u.id_utilisateur
                ORDER BY p.date_debut DESC";
        
        return $this->db->query($sql)->fetchAll();
    }
}
?>
