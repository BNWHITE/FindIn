<?php
/**
 * ValidationController.php - Gestion des validations de compétences par les managers
 */
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/User.php';

class ValidationController extends BaseController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Tableau de bord des validations en attente pour le manager
     */
    public function pending() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $this->checkAuth();
        
        // Vérifier que l'utilisateur est manager
        if ($_SESSION['user_role'] !== 'manager') {
            header('Location: /dashboard');
            exit;
        }
        
        $manager_id = $_SESSION['user_id'];
        
        // Récupérer les demandes de validation en attente pour ce manager
        $sql = "SELECT 
                    dv.id,
                    dv.user_id,
                    dv.id_competence,
                    dv.niveau_demande,
                    dv.date_demande,
                    u.prenom,
                    u.nom,
                    u.email,
                    c.nom as competence_nom,
                    c.description as competence_desc
                FROM demandes_validation dv
                LEFT JOIN utilisateurs u ON dv.user_id = u.id_utilisateur
                LEFT JOIN competences c ON dv.id_competence = c.id_competence
                WHERE dv.manager_id = :manager_id AND dv.statut = 'en_attente'
                ORDER BY dv.date_demande DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':manager_id' => $manager_id]);
        $validations = $stmt->fetchAll();
        
        // Récupérer les validations approuvées récemment
        $sql_approved = "SELECT 
                            dv.id,
                            dv.user_id,
                            dv.date_validation,
                            u.prenom,
                            u.nom,
                            c.nom as competence_nom
                        FROM demandes_validation dv
                        LEFT JOIN utilisateurs u ON dv.user_id = u.id_utilisateur
                        LEFT JOIN competences c ON dv.id_competence = c.id_competence
                        WHERE dv.manager_id = :manager_id AND dv.statut IN ('approuve', 'rejete')
                        ORDER BY dv.date_validation DESC
                        LIMIT 10";
        
        $stmt = $this->db->prepare($sql_approved);
        $stmt->execute([':manager_id' => $manager_id]);
        $recent = $stmt->fetchAll();
        
        $data = [
            'validations' => $validations,
            'recent' => $recent,
            'page_title' => 'Validations en attente'
        ];
        
        $this->view('dashboard/manager-validations', $data);
    }
    
    /**
     * Approuver une demande de validation
     */
    public function approve() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Méthode non autorisée'], 405);
            return;
        }
        
        // Récupérer les données JSON
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $validation_id = $data['validation_id'] ?? '';
        $commentaire = trim($data['commentaire'] ?? '');
        
        if (empty($validation_id)) {
            $this->jsonResponse(['success' => false, 'error' => 'ID de validation requis'], 400);
            return;
        }
        
        // Vérifier que la validation existe et appartient au manager
        $sql = "SELECT * FROM demandes_validation WHERE id = :id AND manager_id = :manager_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $validation_id, ':manager_id' => $_SESSION['user_id']]);
        $validation = $stmt->fetch();
        
        if (!$validation) {
            $this->jsonResponse(['success' => false, 'error' => 'Validation non trouvée'], 404);
            return;
        }
        
        // Mettre à jour la validation
        $sql = "UPDATE demandes_validation 
                SET statut = 'approuve', 
                    commentaire = :commentaire,
                    date_validation = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':id' => $validation_id,
            ':commentaire' => $commentaire
        ]);
        
        if ($result) {
            $this->jsonResponse(['success' => true, 'message' => 'Validation approuvée']);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la mise à jour'], 500);
        }
    }
    
    /**
     * Rejeter une demande de validation
     */
    public function reject() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Méthode non autorisée'], 405);
            return;
        }
        
        // Récupérer les données JSON
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $validation_id = $data['validation_id'] ?? '';
        $reason = trim($data['reason'] ?? 'Non justifié');
        
        if (empty($validation_id)) {
            $this->jsonResponse(['success' => false, 'error' => 'ID de validation requis'], 400);
            return;
        }
        
        // Vérifier que la validation existe et appartient au manager
        $sql = "SELECT * FROM demandes_validation WHERE id = :id AND manager_id = :manager_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $validation_id, ':manager_id' => $_SESSION['user_id']]);
        $validation = $stmt->fetch();
        
        if (!$validation) {
            $this->jsonResponse(['success' => false, 'error' => 'Validation non trouvée'], 404);
            return;
        }
        
        // Mettre à jour la validation
        $sql = "UPDATE demandes_validation 
                SET statut = 'rejete', 
                    commentaire = :reason,
                    date_validation = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':id' => $validation_id,
            ':reason' => $reason
        ]);
        
        if ($result) {
            $this->jsonResponse(['success' => true, 'message' => 'Validation rejetée']);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la mise à jour'], 500);
        }
    }
}
?>
