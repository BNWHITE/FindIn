<?php
/**
 * ProjetApi.php - API REST pour la gestion des projets
 * Endpoints : POST/GET /api/projets/*
 */

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/database.php';

class ProjetApi {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * GET /api/projets/list?statut=en_cours
     */
    public function list() {
        try {
            $statut = $_GET['statut'] ?? null;
            
            if ($statut) {
                $sql = "SELECT * FROM projets WHERE statut = :statut ORDER BY nom ASC";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':statut' => $statut]);
            } else {
                $sql = "SELECT * FROM projets ORDER BY nom ASC";
                $stmt = $this->db->query($sql);
            }
            
            $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'data' => $projets,
                'count' => count($projets)
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/projets/add
     */
    public function add() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['nom']) || empty($data['nom'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'Nom du projet manquant'
                ]);
            }
            
            $sql = "INSERT INTO projets (nom, description, responsable_id, statut, date_debut, date_fin)
                    VALUES (:nom, :description, :responsable_id, :statut, :date_debut, :date_fin)
                    RETURNING *";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':nom' => htmlspecialchars($data['nom']),
                ':description' => htmlspecialchars($data['description'] ?? ''),
                ':responsable_id' => $data['responsable_id'] ?? null,
                ':statut' => htmlspecialchars($data['statut'] ?? 'en_cours'),
                ':date_debut' => $data['date_debut'] ?? null,
                ':date_fin' => $data['date_fin'] ?? null
            ]);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de la création'
                ]);
            }
            
            $projet = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'message' => 'Projet créé avec succès',
                'data' => $projet
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/projets/update
     */
    public function update() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'ID manquant'
                ]);
            }
            
            $updates = [];
            $params = [':id' => $data['id']];
            
            if (isset($data['nom'])) {
                $updates[] = "nom = :nom";
                $params[':nom'] = htmlspecialchars($data['nom']);
            }
            if (isset($data['description'])) {
                $updates[] = "description = :description";
                $params[':description'] = htmlspecialchars($data['description']);
            }
            if (isset($data['statut'])) {
                $updates[] = "statut = :statut";
                $params[':statut'] = htmlspecialchars($data['statut']);
            }
            if (isset($data['date_fin'])) {
                $updates[] = "date_fin = :date_fin";
                $params[':date_fin'] = $data['date_fin'];
            }
            
            if (empty($updates)) {
                return json_encode([
                    'success' => false,
                    'error' => 'Aucun champ à mettre à jour'
                ]);
            }
            
            $sql = "UPDATE projets SET " . implode(', ', $updates) . "
                    WHERE id_projet = :id RETURNING *";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de la mise à jour'
                ]);
            }
            
            $projet = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'message' => 'Projet modifié avec succès',
                'data' => $projet
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/projets/delete
     */
    public function delete() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'ID manquant'
                ]);
            }
            
            $sql = "DELETE FROM projets WHERE id_projet = :id";
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
                'message' => 'Projet supprimé avec succès'
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * GET /api/projets/members?projet_id=uuid
     */
    public function getMembers() {
        try {
            $projetId = $_GET['projet_id'] ?? null;
            
            if (!$projetId) {
                return json_encode([
                    'success' => false,
                    'error' => 'projet_id manquant'
                ]);
            }
            
            $sql = "SELECT u.id_utilisateur, u.prenom, u.nom, u.email, pu.role_projet
                    FROM projets_utilisateurs pu
                    JOIN utilisateurs u ON pu.user_id = u.id_utilisateur
                    WHERE pu.projet_id = :projet_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':projet_id' => $projetId]);
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'data' => $members,
                'count' => count($members)
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/projets/addMember
     */
    public function addMember() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['projet_id']) || !isset($data['user_id'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'Données manquantes'
                ]);
            }
            
            $sql = "INSERT INTO projets_utilisateurs (projet_id, user_id, role_projet)
                    VALUES (:projet_id, :user_id, :role_projet)
                    ON CONFLICT (projet_id, user_id) DO UPDATE 
                    SET role_projet = :role_projet
                    RETURNING *";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':projet_id' => $data['projet_id'],
                ':user_id' => $data['user_id'],
                ':role_projet' => htmlspecialchars($data['role_projet'] ?? 'membre')
            ]);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de l\'ajout'
                ]);
            }
            
            return json_encode([
                'success' => true,
                'message' => 'Membre ajouté au projet'
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
