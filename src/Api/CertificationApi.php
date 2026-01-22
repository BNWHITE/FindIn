<?php
/**
 * CertificationApi.php - API REST pour les certifications
 * Endpoints : POST/GET /api/certifications/*
 */

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/database.php';

class CertificationApi {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * GET /api/certifications/list?user_id=uuid
     */
    public function list() {
        try {
            $userId = $_GET['user_id'] ?? null;
            
            if ($userId) {
                $sql = "SELECT * FROM certifications WHERE user_id = :user_id ORDER BY date_obtention DESC";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':user_id' => $userId]);
            } else {
                $sql = "SELECT * FROM certifications ORDER BY date_obtention DESC";
                $stmt = $this->db->query($sql);
            }
            
            $certifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'data' => $certifications,
                'count' => count($certifications)
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/certifications/add
     */
    public function add() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $required = ['user_id', 'nom'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return json_encode([
                        'success' => false,
                        'error' => ucfirst($field) . ' manquant'
                    ]);
                }
            }
            
            $sql = "INSERT INTO certifications (user_id, nom, organisme, date_obtention, date_expiration, url_verification)
                    VALUES (:user_id, :nom, :organisme, :date_obtention, :date_expiration, :url_verification)
                    RETURNING *";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':user_id' => $data['user_id'],
                ':nom' => htmlspecialchars($data['nom']),
                ':organisme' => htmlspecialchars($data['organisme'] ?? ''),
                ':date_obtention' => $data['date_obtention'] ?? null,
                ':date_expiration' => $data['date_expiration'] ?? null,
                ':url_verification' => htmlspecialchars($data['url_verification'] ?? '')
            ]);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de l\'ajout'
                ]);
            }
            
            $certification = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'message' => 'Certification ajoutée avec succès',
                'data' => $certification
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/certifications/update
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
            if (isset($data['organisme'])) {
                $updates[] = "organisme = :organisme";
                $params[':organisme'] = htmlspecialchars($data['organisme']);
            }
            if (isset($data['date_expiration'])) {
                $updates[] = "date_expiration = :date_expiration";
                $params[':date_expiration'] = $data['date_expiration'];
            }
            if (isset($data['url_verification'])) {
                $updates[] = "url_verification = :url_verification";
                $params[':url_verification'] = htmlspecialchars($data['url_verification']);
            }
            
            if (empty($updates)) {
                return json_encode([
                    'success' => false,
                    'error' => 'Aucun champ à mettre à jour'
                ]);
            }
            
            $sql = "UPDATE certifications SET " . implode(', ', $updates) . "
                    WHERE id_certification = :id RETURNING *";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de la mise à jour'
                ]);
            }
            
            $certification = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'message' => 'Certification modifiée avec succès',
                'data' => $certification
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/certifications/delete
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
            
            $sql = "DELETE FROM certifications WHERE id_certification = :id";
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
                'message' => 'Certification supprimée avec succès'
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
