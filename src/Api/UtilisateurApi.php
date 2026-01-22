<?php
/**
 * UtilisateurApi.php - API REST pour la gestion des utilisateurs
 * Endpoints : POST/GET /api/utilisateurs/*
 * Connecté à Supabase PostgreSQL
 */

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/database.php';

class UtilisateurApi {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * GET /api/utilisateurs/list?role=manager
     * Lister les utilisateurs (optionnellement filtrés par rôle)
     */
    public function list() {
        try {
            $role = $_GET['role'] ?? null;
            
            if ($role) {
                $sql = "SELECT id_utilisateur, email, prenom, nom, role, manager_id, photo, cree_le 
                        FROM utilisateurs WHERE role = :role ORDER BY nom, prenom";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':role' => $role]);
            } else {
                $sql = "SELECT id_utilisateur, email, prenom, nom, role, manager_id, photo, cree_le 
                        FROM utilisateurs ORDER BY nom, prenom";
                $stmt = $this->db->query($sql);
            }
            
            $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'data' => $utilisateurs,
                'count' => count($utilisateurs)
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * GET /api/utilisateurs/get?id=uuid
     * Récupérer un utilisateur spécifique
     */
    public function get() {
        try {
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                return json_encode([
                    'success' => false,
                    'error' => 'ID manquant'
                ]);
            }
            
            $sql = "SELECT id_utilisateur, email, prenom, nom, role, manager_id, photo, id_departement, cree_le
                    FROM utilisateurs WHERE id_utilisateur = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$utilisateur) {
                return json_encode([
                    'success' => false,
                    'error' => 'Utilisateur non trouvé'
                ]);
            }
            
            return json_encode([
                'success' => true,
                'data' => $utilisateur
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/utilisateurs/add
     * Ajouter un nouvel utilisateur
     * 
     * Body JSON:
     * {
     *   "email": "user@findin.fr",
     *   "prenom": "Jean",
     *   "nom": "Dupont",
     *   "mot_de_passe": "SecurePassword123",
     *   "role": "employe",
     *   "manager_id": "uuid-optionnel"
     * }
     */
    public function add() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validation
            $required = ['email', 'prenom', 'nom', 'mot_de_passe'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return json_encode([
                        'success' => false,
                        'error' => ucfirst($field) . ' manquant'
                    ]);
                }
            }
            
            // Vérifier que l'email n'existe pas
            $check = $this->db->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = :email");
            $check->execute([':email' => $data['email']]);
            
            if ($check->fetch()) {
                return json_encode([
                    'success' => false,
                    'error' => 'Cet email est déjà utilisé'
                ]);
            }
            
            // Hash du mot de passe
            $hashedPassword = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
            
            // Insérer l'utilisateur
            $sql = "INSERT INTO utilisateurs (email, prenom, nom, mot_de_passe, role, manager_id)
                    VALUES (:email, :prenom, :nom, :password, :role, :manager_id)
                    RETURNING id_utilisateur, email, prenom, nom, role, manager_id, cree_le";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':email' => htmlspecialchars($data['email']),
                ':prenom' => htmlspecialchars($data['prenom']),
                ':nom' => htmlspecialchars($data['nom']),
                ':password' => $hashedPassword,
                ':role' => htmlspecialchars($data['role'] ?? 'employe'),
                ':manager_id' => $data['manager_id'] ?? null
            ]);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de la création'
                ]);
            }
            
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'data' => $utilisateur
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/utilisateurs/update
     * Modifier un utilisateur
     * 
     * Body JSON:
     * {
     *   "id": "uuid-here",
     *   "prenom": "Jean",
     *   "nom": "Dupont",
     *   "role": "manager",
     *   "manager_id": "uuid"
     * }
     */
    public function update() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id']) || empty($data['id'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'ID manquant'
                ]);
            }
            
            // Vérifier que l'utilisateur existe
            $check = $this->db->prepare("SELECT id_utilisateur FROM utilisateurs WHERE id_utilisateur = :id");
            $check->execute([':id' => $data['id']]);
            
            if (!$check->fetch()) {
                return json_encode([
                    'success' => false,
                    'error' => 'Utilisateur non trouvé'
                ]);
            }
            
            // Construire la requête UPDATE
            $updates = [];
            $params = [':id' => $data['id']];
            
            if (isset($data['prenom'])) {
                $updates[] = "prenom = :prenom";
                $params[':prenom'] = htmlspecialchars($data['prenom']);
            }
            if (isset($data['nom'])) {
                $updates[] = "nom = :nom";
                $params[':nom'] = htmlspecialchars($data['nom']);
            }
            if (isset($data['role'])) {
                $updates[] = "role = :role";
                $params[':role'] = htmlspecialchars($data['role']);
            }
            if (isset($data['manager_id'])) {
                $updates[] = "manager_id = :manager_id";
                $params[':manager_id'] = $data['manager_id'];
            }
            if (isset($data['id_departement'])) {
                $updates[] = "id_departement = :id_departement";
                $params[':id_departement'] = $data['id_departement'];
            }
            
            if (empty($updates)) {
                return json_encode([
                    'success' => false,
                    'error' => 'Aucun champ à mettre à jour'
                ]);
            }
            
            $sql = "UPDATE utilisateurs SET " . implode(', ', $updates) . "
                    WHERE id_utilisateur = :id
                    RETURNING id_utilisateur, email, prenom, nom, role, manager_id, cree_le";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de la mise à jour'
                ]);
            }
            
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'message' => 'Utilisateur modifié avec succès',
                'data' => $utilisateur
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/utilisateurs/delete
     * Supprimer un utilisateur
     * 
     * Body JSON:
     * {
     *   "id": "uuid-here"
     * }
     */
    public function delete() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id']) || empty($data['id'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'ID manquant'
                ]);
            }
            
            // Vérifier que l'utilisateur existe
            $check = $this->db->prepare("SELECT id_utilisateur FROM utilisateurs WHERE id_utilisateur = :id");
            $check->execute([':id' => $data['id']]);
            
            if (!$check->fetch()) {
                return json_encode([
                    'success' => false,
                    'error' => 'Utilisateur non trouvé'
                ]);
            }
            
            // Supprimer l'utilisateur (CASCADE delete les associations)
            $sql = "DELETE FROM utilisateurs WHERE id_utilisateur = :id";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([':id' => $data['id']]);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de la suppression'
                ]);
            }
            
            return json_encode([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * GET /api/utilisateurs/team?manager_id=uuid
     * Récupérer l'équipe d'un manager
     */
    public function getTeam() {
        try {
            $managerId = $_GET['manager_id'] ?? null;
            
            if (!$managerId) {
                return json_encode([
                    'success' => false,
                    'error' => 'manager_id manquant'
                ]);
            }
            
            $sql = "SELECT id_utilisateur, email, prenom, nom, role, photo, cree_le
                    FROM utilisateurs 
                    WHERE manager_id = :manager_id
                    ORDER BY nom, prenom";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':manager_id' => $managerId]);
            $team = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'data' => $team,
                'count' => count($team)
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/utilisateurs/changePassword
     * Changer le mot de passe d'un utilisateur
     * 
     * Body JSON:
     * {
     *   "id": "uuid",
     *   "old_password": "OldPass123",
     *   "new_password": "NewPass123"
     * }
     */
    public function changePassword() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id']) || !isset($data['old_password']) || !isset($data['new_password'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'Données manquantes'
                ]);
            }
            
            // Récupérer l'utilisateur
            $sql = "SELECT mot_de_passe FROM utilisateurs WHERE id_utilisateur = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $data['id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return json_encode([
                    'success' => false,
                    'error' => 'Utilisateur non trouvé'
                ]);
            }
            
            // Vérifier l'ancien mot de passe
            if (!password_verify($data['old_password'], $user['mot_de_passe'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'Ancien mot de passe incorrect'
                ]);
            }
            
            // Mettre à jour le mot de passe
            $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);
            $sql = "UPDATE utilisateurs SET mot_de_passe = :password WHERE id_utilisateur = :id";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':password' => $hashedPassword,
                ':id' => $data['id']
            ]);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de la mise à jour'
                ]);
            }
            
            return json_encode([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès'
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
