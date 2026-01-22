<?php
/**
 * DocumentApi.php - API REST pour les documents (CV, Portfolio, etc)
 * Endpoints : POST/GET /api/documents/*
 */

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/database.php';

class DocumentApi {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * GET /api/documents/list?user_id=uuid&type=cv
     */
    public function list() {
        try {
            $userId = $_GET['user_id'] ?? null;
            $type = $_GET['type'] ?? null;
            
            if ($userId && $type) {
                $sql = "SELECT * FROM documents WHERE user_id = :user_id AND type = :type ORDER BY date_upload DESC";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':user_id' => $userId, ':type' => $type]);
            } elseif ($userId) {
                $sql = "SELECT * FROM documents WHERE user_id = :user_id ORDER BY date_upload DESC";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':user_id' => $userId]);
            } else {
                $sql = "SELECT * FROM documents ORDER BY date_upload DESC";
                $stmt = $this->db->query($sql);
            }
            
            $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'data' => $documents,
                'count' => count($documents)
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/documents/add
     */
    public function add() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $required = ['user_id', 'nom', 'url_fichier'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return json_encode([
                        'success' => false,
                        'error' => ucfirst($field) . ' manquant'
                    ]);
                }
            }
            
            $sql = "INSERT INTO documents (user_id, nom, type, url_fichier)
                    VALUES (:user_id, :nom, :type, :url_fichier)
                    RETURNING *";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':user_id' => $data['user_id'],
                ':nom' => htmlspecialchars($data['nom']),
                ':type' => htmlspecialchars($data['type'] ?? 'autre'),
                ':url_fichier' => htmlspecialchars($data['url_fichier'])
            ]);
            
            if (!$result) {
                return json_encode([
                    'success' => false,
                    'error' => 'Erreur lors du téléchargement'
                ]);
            }
            
            $document = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'message' => 'Document téléchargé avec succès',
                'data' => $document
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * POST /api/documents/delete
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
            
            // Récupérer le document pour supprimer le fichier
            $sql = "SELECT url_fichier FROM documents WHERE id_document = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $data['id']]);
            $document = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$document) {
                return json_encode([
                    'success' => false,
                    'error' => 'Document non trouvé'
                ]);
            }
            
            // Supprimer le fichier physique (optionnel)
            if (file_exists($document['url_fichier'])) {
                @unlink($document['url_fichier']);
            }
            
            // Supprimer la ligne DB
            $sql = "DELETE FROM documents WHERE id_document = :id";
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
                'message' => 'Document supprimé avec succès'
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
