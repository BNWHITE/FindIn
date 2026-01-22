<?php
/**
 * Invitation.php - Modèle pour gérer les invitations d'employés
 */
class Invitation {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Créer une invitation pour un nouvel employé
     */
    public function createInvitation($email, $prenom, $nom, $manager_id = null, $departement = null, $role = 'employe', $days_valid = 7) {
        try {
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime("+{$days_valid} days"));
            
            $sql = "INSERT INTO invitations (token, email, prenom, nom, manager_id, departement, role, expires_at) 
                    VALUES (:token, :email, :prenom, :nom, :manager_id, :departement, :role, :expires_at)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':token' => $token,
                ':email' => $email,
                ':prenom' => $prenom,
                ':nom' => $nom,
                ':manager_id' => $manager_id,
                ':departement' => $departement,
                ':role' => $role,
                ':expires_at' => $expires_at
            ]);
            
            return $result ? $token : false;
        } catch (Exception $e) {
            error_log("Erreur createInvitation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier une invitation et récupérer ses détails
     */
    public function getInvitationByToken($token) {
        try {
            $sql = "SELECT * FROM invitations WHERE token = :token AND status = 'pending' AND (expires_at IS NULL OR expires_at > NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':token' => $token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getInvitationByToken: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Accepter une invitation et créer le compte utilisateur
     */
    public function acceptInvitation($token, $password) {
        try {
            $invitation = $this->getInvitationByToken($token);
            
            if (!$invitation) {
                return ['success' => false, 'message' => 'Invitation invalide ou expirée'];
            }

            // Vérifier que l'email n'existe pas déjà
            $stmt = $this->db->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = :email");
            $stmt->execute([':email' => $invitation['email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
            }

            // Créer l'utilisateur
            $sql = "INSERT INTO utilisateurs (email, prenom, nom, mot_de_passe, role, manager_id, departement, date_creation) 
                    VALUES (:email, :prenom, :nom, :password, :role, :manager_id, :departement, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $user_created = $stmt->execute([
                ':email' => $invitation['email'],
                ':prenom' => $invitation['prenom'],
                ':nom' => $invitation['nom'],
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':role' => $invitation['role'],
                ':manager_id' => $invitation['manager_id'],
                ':departement' => $invitation['departement']
            ]);

            if (!$user_created) {
                return ['success' => false, 'message' => 'Erreur lors de la création du compte'];
            }

            $user_id = $this->db->lastInsertId();

            // Mettre à jour l'invitation comme acceptée
            $update_sql = "UPDATE invitations SET status = 'accepted', user_id = :user_id WHERE id = :id";
            $update_stmt = $this->db->prepare($update_sql);
            $update_stmt->execute([':user_id' => $user_id, ':id' => $invitation['id']]);

            return [
                'success' => true,
                'message' => 'Compte créé avec succès',
                'user_id' => $user_id,
                'user' => [
                    'id' => $user_id,
                    'email' => $invitation['email'],
                    'prenom' => $invitation['prenom'],
                    'nom' => $invitation['nom'],
                    'role' => $invitation['role']
                ]
            ];
        } catch (Exception $e) {
            error_log("Erreur acceptInvitation: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur'];
        }
    }

    /**
     * Récupérer toutes les invitations (pour le dashboard RH)
     */
    public function getAllInvitations($filter_status = null) {
        try {
            $sql = "SELECT i.*, u.prenom as manager_prenom, u.nom as manager_nom 
                    FROM invitations i
                    LEFT JOIN utilisateurs u ON i.manager_id = u.id_utilisateur";
            
            if ($filter_status) {
                $sql .= " WHERE i.status = :status";
            }
            
            $sql .= " ORDER BY i.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            
            if ($filter_status) {
                $stmt->execute([':status' => $filter_status]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getAllInvitations: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Supprimer une invitation
     */
    public function deleteInvitation($id) {
        try {
            $sql = "DELETE FROM invitations WHERE id = :id AND status = 'pending'";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) {
            error_log("Erreur deleteInvitation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Renvoyer une invitation (regénérer le token)
     */
    public function resendInvitation($id, $days_valid = 7) {
        try {
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime("+{$days_valid} days"));
            
            $sql = "UPDATE invitations SET token = :token, expires_at = :expires_at, status = 'pending' WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':token' => $token,
                ':expires_at' => $expires_at,
                ':id' => $id
            ]) ? $token : false;
        } catch (Exception $e) {
            error_log("Erreur resendInvitation: " . $e->getMessage());
            return false;
        }
    }
}
?>
