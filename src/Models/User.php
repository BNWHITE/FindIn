<?php
/**
 * User.php - Modèle utilisateur
 * Compatible MySQL/SQLite/PostgreSQL (Supabase)
 */
require_once __DIR__ . '/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function login($email) {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function getAllUsers() {
        $sql = "SELECT * FROM utilisateurs ORDER BY nom, prenom";
        return $this->db->query($sql)->fetchAll();
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM utilisateurs WHERE id_utilisateur = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateProfile($id, $data) {
        $sql = "UPDATE utilisateurs SET 
                prenom = :prenom, 
                nom = :nom, 
                id_departement = :departement 
                WHERE id_utilisateur = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':prenom' => $data['prenom'],
            ':nom' => $data['nom'],
            ':departement' => $data['departement'] ?? null,
            ':id' => $id
        ]);
    }

    // ========== FONCTIONS RH ==========
    
    public function createUser($data) {
        $id = $data['id'] ?? uniqid('user-');
        $sql = "INSERT INTO utilisateurs (id_utilisateur, email, prenom, nom, mot_de_passe, role, manager_id) 
                VALUES (:id, :email, :prenom, :nom, :password, :role, :manager_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':email' => $data['email'],
            ':prenom' => $data['prenom'],
            ':nom' => $data['nom'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role' => $data['role'] ?? 'employe',
            ':manager_id' => $data['manager_id'] ?? null
        ]);
    }

    public function updateUser($id, $data) {
        $sql = "UPDATE utilisateurs SET 
                prenom = :prenom, 
                nom = :nom, 
                email = :email,
                role = :role,
                manager_id = :manager_id
                WHERE id_utilisateur = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':prenom' => $data['prenom'],
            ':nom' => $data['nom'],
            ':email' => $data['email'],
            ':role' => $data['role'],
            ':manager_id' => $data['manager_id'] ?? null,
            ':id' => $id
        ]);
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM utilisateurs WHERE id_utilisateur = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function setUserRole($userId, $role) {
        $sql = "UPDATE utilisateurs SET role = :role WHERE id_utilisateur = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $userId, ':role' => $role]);
    }

    public function setUserManager($userId, $managerId) {
        $sql = "UPDATE utilisateurs SET manager_id = :manager_id WHERE id_utilisateur = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $userId, ':manager_id' => $managerId]);
    }

    public function getManagers() {
        $sql = "SELECT * FROM utilisateurs WHERE role = 'manager' ORDER BY nom, prenom";
        return $this->db->query($sql)->fetchAll();
    }

    public function getUsersByRole($role) {
        $sql = "SELECT * FROM utilisateurs WHERE role = :role ORDER BY nom, prenom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll();
    }

    // ========== FONCTIONS MANAGER ==========
    
    public function getTeamMembers($managerId) {
        $sql = "SELECT * FROM utilisateurs WHERE manager_id = :manager_id ORDER BY nom, prenom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':manager_id' => $managerId]);
        return $stmt->fetchAll();
    }

    public function getPendingValidations($managerId) {
        $sql = "SELECT dv.*, u.prenom, u.nom, u.email, c.nom as competence_nom 
                FROM demandes_validation dv
                JOIN utilisateurs u ON dv.user_id = u.id_utilisateur
                JOIN competences c ON dv.competence_id = c.id_competence
                WHERE dv.statut = 'en_attente' 
                AND u.manager_id = :manager_id
                ORDER BY dv.date_demande DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':manager_id' => $managerId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function validateCompetence($validationId, $managerId, $statut, $commentaire = null) {
        $sql = "UPDATE demandes_validation SET 
                statut = :statut, 
                manager_id = :manager_id,
                commentaire = :commentaire,
                date_validation = CURRENT_TIMESTAMP
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':statut' => $statut,
            ':manager_id' => $managerId,
            ':commentaire' => $commentaire,
            ':id' => $validationId
        ]);

        // Si approuvé, mettre à jour user_competences
        if ($result && $statut === 'approuve') {
            $stmt_demande = $this->db->prepare("SELECT * FROM demandes_validation WHERE id = :id");
            $stmt_demande->execute([':id' => $validationId]);
            $demande = $stmt_demande->fetch();
            if ($demande) {
                $sql2 = "UPDATE competences_utilisateurs SET niveau_valide = niveau_declare, id_manager_validateur = :manager_id, date_validation = CURRENT_TIMESTAMP 
                         WHERE user_id = :user_id AND id_competence = :comp_id";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->execute([
                    ':manager_id' => $managerId,
                    ':user_id' => $demande['user_id'],
                    ':comp_id' => $demande['competence_id']
                ]);
            }
        }
        return $result;
    }

    public function getTeamCompetences($managerId) {
        $sql = "SELECT u.id_utilisateur, u.prenom, u.nom, c.nom as competence, cu.niveau_declare, cu.niveau_valide
                FROM utilisateurs u
                JOIN competences_utilisateurs cu ON u.id_utilisateur = cu.user_id
                JOIN competences c ON cu.id_competence = c.id_competence
                WHERE u.manager_id = :manager_id
                ORDER BY u.nom, c.nom";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':manager_id' => $managerId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    // ========== FONCTIONS EMPLOYE ==========
    
    public function requestValidation($userId, $competenceId, $niveau) {
        $sql = "INSERT INTO demandes_validation (user_id, competence_id, niveau_demande) 
                VALUES (:user_id, :competence_id, :niveau)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':competence_id' => $competenceId,
            ':niveau' => $niveau
        ]);
    }

    public function getMyValidationRequests($userId) {
        $sql = "SELECT dv.*, c.nom as competence_nom 
                FROM demandes_validation dv
                JOIN competences c ON dv.competence_id = c.id_competence
                WHERE dv.user_id = :user_id
                ORDER BY dv.date_demande DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    // ========== STATS ADMIN ==========
    
    public function getStats() {
        $stats = [];
        try {
            $stats['total_users'] = $this->db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
            $stats['employes'] = $this->db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'employe'")->fetchColumn();
            $stats['managers'] = $this->db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'manager'")->fetchColumn();
            $stats['rh'] = $this->db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'rh'")->fetchColumn();
            $stats['pending_validations'] = 0;
        } catch (Exception $e) {
            $stats = ['total_users' => 0, 'employes' => 0, 'managers' => 0, 'rh' => 0, 'pending_validations' => 0];
        }
        return $stats;
    }

    public function getUsersByDepartment($department) {
        $sql = "SELECT * FROM utilisateurs WHERE id_departement = :department ORDER BY nom, prenom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':department' => $department]);
        return $stmt->fetchAll();
    }

    public function getDepartments() {
        $sql = "SELECT * FROM departements ORDER BY nom";
        try {
            return $this->db->query($sql)->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function updatePassword($userId, $newPassword) {
        $sql = "UPDATE utilisateurs SET mot_de_passe = :password WHERE id_utilisateur = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':password' => password_hash($newPassword, PASSWORD_DEFAULT), ':id' => $userId]);
    }

    public function updatePhoto($userId, $photoPath) {
        $sql = "UPDATE utilisateurs SET photo = :photo WHERE id_utilisateur = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':photo' => $photoPath, ':id' => $userId]);
    }
}
?>
