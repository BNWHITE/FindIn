<?php
// models/Department.php
require_once __DIR__ . '/Database.php';

class Department {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllDepartements() {
        $sql = "SELECT * FROM departements ORDER BY nom";
        return $this->db->query($sql)->fetchAll();
    }

    public function getDepartementById($id) {
        $sql = "SELECT * FROM departements WHERE id_departement = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getDepartementUsers($departementId) {
        $sql = "SELECT * FROM utilisateurs WHERE id_departement = :departement_id ORDER BY nom, prenom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':departement_id' => $departementId]);
        return $stmt->fetchAll();
    }
}
?>
