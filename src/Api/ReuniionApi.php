<?php
/**
 * ReuniionApi.php - API REST pour les réunions 1-to-1
 * Endpoints : POST/GET /api/reunions/*
 */

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/database.php';

class ReuniionApi {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * GET /api/reunions/list?employe_id=uuid&manager_id=uuid
     */
    public function list() {
        try {
            $employeId = $_GET['employe_id'] ?? null;
            $managerId = $_GET['manager_id'] ?? null;
            
            if ($employeId && $managerId) {
                $sql = "SELECT * FROM reunions 
                        WHERE (employe_id = :employe_id AND manager_id = :manager_id)
                           OR (employe_id = :manager_id AND manager_id = :employe_id)
                        ORDER BY date_reunion DESC";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':employe_id' => $employeId, ':manager_id' => $managerId]);
            } elseif ($employeId) {
                $sql = "SELECT * FROM reunions WHERE employe_id = :employe_id ORDER BY date_reunion DESC";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':employe_id' => $employeId]);
            } elseif ($managerId) {
                $sql = "SELECT * FROM reunions WHERE manager_id = :manager_id ORDER BY date_reunion DESC";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':manager_id' => $managerId]);
            } else {
                $sql = "SELECT * FROM reunions ORDER BY date_reunion DESC";
                $stmt = $this->db->query($sql);
            }
            
            $reunions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'data' => $reunions,
                'count' => count($reunions)
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/reunions/add
     */
    public function add() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $required = ['employe_id', 'manager_id', 'titre', 'date_reunion'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return json_encode([
                        'success' => false,
                        'error' => ucfirst(str_replace('_', ' ', $field)) . ' manquant'
                    ]);
                }
            }
            
            $sql = "INSERT INTO reunions (employe_id, manager_id, titre, description, date_reunion, duree_minutes, status)
                    VALUES (:employe_id, :manager_id, :titre, :description, :date_reunion, :duree_minutes, :status)
                    RETURNING *";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':employe_id' => $data['employe_id'],
                ':manager_id' => $data['manager_id'],
                ':titre' => htmlspecialchars($data['titre']),
                ':description' => htmlspecialchars($data['description'] ?? ''),
                ':date_reunion' => $data['date_reunion'],
                ':duree_minutes' => intval($data['duree_minutes'] ?? 30),
                ':status' => htmlspecialchars($data['status'] ?? 'planifiee')
            ]);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de la création'
                ]);
            }
            
            $reunion = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'message' => 'Réunion créée avec succès',
                'data' => $reunion
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/reunions/update
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
            
            if (isset($data['titre'])) {
                $updates[] = "titre = :titre";
                $params[':titre'] = htmlspecialchars($data['titre']);
            }
            if (isset($data['notes'])) {
                $updates[] = "notes = :notes";
                $params[':notes'] = htmlspecialchars($data['notes']);
            }
            if (isset($data['status'])) {
                $updates[] = "status = :status";
                $params[':status'] = htmlspecialchars($data['status']);
            }
            
            if (empty($updates)) {
                return json_encode([
                    'success' => false,
                    'error' => 'Aucun champ à mettre à jour'
                ]);
            }
            
            $sql = "UPDATE reunions SET " . implode(', ', $updates) . "
                    WHERE id_reunion = :id RETURNING *";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de la mise à jour'
                ]);
            }
            
            $reunion = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'message' => 'Réunion modifiée avec succès',
                'data' => $reunion
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/reunions/delete
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
            
            $sql = "DELETE FROM reunions WHERE id_reunion = :id";
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
                'message' => 'Réunion supprimée avec succès'
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
