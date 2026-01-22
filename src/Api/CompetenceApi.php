<?php
/**
 * CompetenceApi.php - API REST pour la gestion des compétences
 * Endpoints : POST/GET /api/competences/*
 * Connecté à Supabase PostgreSQL
 */

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/database.php';

class CompetenceApi {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * GET /api/competences/list
     * Retourne la liste de toutes les compétences
     */
    public function list() {
        try {
            $sql = "SELECT * FROM competences ORDER BY nom ASC";
            $stmt = $this->db->query($sql);
            $competences = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'data' => $competences,
                'count' => count($competences)
            ]);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * GET /api/competences/list?id=uuid
     * Retourne une compétence spécifique
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
            
            $sql = "SELECT * FROM competences WHERE id_competence = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $competence = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$competence) {
                return json_encode([
                    'success' => false,
                    'error' => 'Compétence non trouvée'
                ]);
            }
            
            return json_encode([
                'success' => true,
                'data' => $competence
            ]);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/competences/add
     * Ajouter une nouvelle compétence
     * 
     * Body JSON:
     * {
     *   "nom": "Python",
     *   "description": "Langage Python",
     *   "type_competence": "technique"
     * }
     */
    public function add() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validation
            if (!isset($data['nom']) || empty($data['nom'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'Nom de compétence manquant'
                ]);
            }
            
            $nom = htmlspecialchars($data['nom']);
            $description = htmlspecialchars($data['description'] ?? '');
            $type = htmlspecialchars($data['type_competence'] ?? 'technique');
            
            // Vérifier si la compétence existe déjà
            $check = $this->db->prepare("SELECT id_competence FROM competences WHERE nom = :nom");
            $check->execute([':nom' => $nom]);
            
            if ($check->fetch()) {
                return json_encode([
                    'success' => false,
                    'error' => 'Cette compétence existe déjà'
                ]);
            }
            
            // Insérer la compétence
            $sql = "INSERT INTO competences (nom, description, type_competence) 
                    VALUES (:nom, :description, :type) 
                    RETURNING *";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':nom' => $nom,
                ':description' => $description,
                ':type' => $type
            ]);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de l\'insertion'
                ]);
            }
            
            $competence = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'message' => 'Compétence ajoutée avec succès',
                'data' => $competence
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/competences/update
     * Modifier une compétence existante
     * 
     * Body JSON:
     * {
     *   "id": "uuid-here",
     *   "nom": "Python 3.11",
     *   "description": "Version 3.11",
     *   "type_competence": "technique"
     * }
     */
    public function update() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validation
            if (!isset($data['id']) || empty($data['id'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'ID manquant'
                ]);
            }
            
            if (!isset($data['nom']) || empty($data['nom'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'Nom manquant'
                ]);
            }
            
            // Vérifier que la compétence existe
            $check = $this->db->prepare("SELECT id_competence FROM competences WHERE id_competence = :id");
            $check->execute([':id' => $data['id']]);
            
            if (!$check->fetch()) {
                return json_encode([
                    'success' => false,
                    'error' => 'Compétence non trouvée'
                ]);
            }
            
            // Mettre à jour
            $sql = "UPDATE competences SET 
                    nom = :nom,
                    description = :description,
                    type_competence = :type
                    WHERE id_competence = :id
                    RETURNING *";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':id' => $data['id'],
                ':nom' => htmlspecialchars($data['nom']),
                ':description' => htmlspecialchars($data['description'] ?? ''),
                ':type' => htmlspecialchars($data['type_competence'] ?? 'technique')
            ]);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de la mise à jour'
                ]);
            }
            
            $competence = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'message' => 'Compétence modifiée avec succès',
                'data' => $competence
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/competences/delete
     * Supprimer une compétence
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
            
            // Vérifier que la compétence existe
            $check = $this->db->prepare("SELECT id_competence FROM competences WHERE id_competence = :id");
            $check->execute([':id' => $data['id']]);
            
            if (!$check->fetch()) {
                return json_encode([
                    'success' => false,
                    'error' => 'Compétence non trouvée'
                ]);
            }
            
            // Supprimer (CASCADE delete les associations)
            $sql = "DELETE FROM competences WHERE id_competence = :id";
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
                'message' => 'Compétence supprimée avec succès'
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/competences/assignUser
     * Assigner une compétence à un utilisateur
     * 
     * Body JSON:
     * {
     *   "user_id": "uuid-user",
     *   "competence_id": "uuid-comp",
     *   "niveau_declare": 3
     * }
     */
    public function assignUser() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validation
            if (!isset($data['user_id']) || !isset($data['competence_id']) || !isset($data['niveau_declare'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'Données manquantes'
                ]);
            }
            
            // Vérifier que l'utilisateur et la compétence existent
            $userCheck = $this->db->prepare("SELECT id_utilisateur FROM utilisateurs WHERE id_utilisateur = :id");
            $userCheck->execute([':id' => $data['user_id']]);
            
            if (!$userCheck->fetch()) {
                return json_encode([
                    'success' => false,
                    'error' => 'Utilisateur non trouvé'
                ]);
            }
            
            $compCheck = $this->db->prepare("SELECT id_competence FROM competences WHERE id_competence = :id");
            $compCheck->execute([':id' => $data['competence_id']]);
            
            if (!$compCheck->fetch()) {
                return json_encode([
                    'success' => false,
                    'error' => 'Compétence non trouvée'
                ]);
            }
            
            // Insérer l'association
            $sql = "INSERT INTO competences_utilisateurs (user_id, id_competence, niveau_declare)
                    VALUES (:user_id, :competence_id, :niveau)
                    ON CONFLICT (user_id, id_competence) DO UPDATE 
                    SET niveau_declare = :niveau
                    RETURNING *";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':user_id' => $data['user_id'],
                ':competence_id' => $data['competence_id'],
                ':niveau' => intval($data['niveau_declare'])
            ]);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de l\'association'
                ]);
            }
            
            $association = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'message' => 'Compétence assignée avec succès',
                'data' => $association
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * GET /api/competences/user?user_id=uuid
     * Récupérer les compétences d'un utilisateur
     */
    public function getUserCompetences() {
        try {
            $userId = $_GET['user_id'] ?? null;
            
            if (!$userId) {
                return json_encode([
                    'success' => false,
                    'error' => 'user_id manquant'
                ]);
            }
            
            $sql = "SELECT c.*, cu.niveau_declare, cu.niveau_valide, cu.id_manager_validateur, cu.date_validation
                    FROM competences_utilisateurs cu
                    JOIN competences c ON cu.id_competence = c.id_competence
                    WHERE cu.user_id = :user_id
                    ORDER BY c.nom ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $competences = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'data' => $competences,
                'count' => count($competences)
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
