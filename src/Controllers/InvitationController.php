<?php
/**
 * InvitationController.php - Gestion des invitations d'employés
 */
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Invitation.php';
require_once __DIR__ . '/../Lib/EmailSender.php';

class InvitationController extends BaseController {
    private $invitationModel;

    public function __construct() {
        $this->invitationModel = new Invitation();
    }

    /**
     * Page d'acceptation d'invitation (accessible publiquement)
     */
    public function accept() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Si déjà connecté, rediriger vers dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /FindIn/public/dashboard');
            exit;
        }

        $token = $_GET['token'] ?? '';
        $message = '';
        $error = '';
        $invitation = null;

        if ($token) {
            $invitation = $this->invitationModel->getInvitationByToken($token);
            if (!$invitation) {
                $error = 'Invitation invalide ou expirée';
            }
        }

        // Traiter le formulaire POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token) {
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if (empty($password) || empty($password_confirm)) {
                $error = 'Tous les champs sont requis';
            } elseif ($password !== $password_confirm) {
                $error = 'Les mots de passe ne correspondent pas';
            } elseif (strlen($password) < 8) {
                $error = 'Le mot de passe doit contenir au moins 8 caractères';
            } else {
                $result = $this->invitationModel->acceptInvitation($token, $password);
                if ($result['success']) {
                    $_SESSION['success'] = 'Compte créé avec succès! Connectez-vous maintenant.';
                    $_SESSION['user_id'] = $result['user_id'];
                    $_SESSION['user_email'] = $result['user']['email'];
                    $_SESSION['user_name'] = $result['user']['prenom'] . ' ' . $result['user']['nom'];
                    $_SESSION['user_role'] = $result['user']['role'];
                    
                    error_log("✅ Invitation acceptée: {$result['user']['email']}");
                    header('Location: /FindIn/public/dashboard');
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
        }

        // Afficher la vue d'acceptation
        $data = [
            'token' => $token,
            'invitation' => $invitation,
            'message' => $message,
            'error' => $error
        ];

        $this->view('auth/accept-invitation', $data);
    }

    /**
     * Dashboard RH - Lister et créer les invitations
     */
    public function dashboard() {
        $this->checkAuth();
        
        // Vérifier que l'utilisateur est RH ou Admin
        if (!in_array($_SESSION['user_role'], ['rh', 'admin'])) {
            header('Location: /FindIn/public/dashboard');
            exit;
        }

        $invitations = $this->invitationModel->getAllInvitations();
        
        // Charger les managers pour le formulaire
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id_utilisateur, prenom, nom, role FROM utilisateurs WHERE role IN ('manager', 'rh') ORDER BY prenom, nom");
        $stmt->execute();
        $managers = $stmt->fetchAll();

        $data = [
            'invitations' => $invitations,
            'managers' => $managers,
            'page_title' => 'Gestion des Invitations'
        ];

        $this->view('dashboard/rh-invitations', $data);
    }

    /**
     * Créer une nouvelle invitation
     */
    public function create() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $this->checkAuth();
        
        if (!in_array($_SESSION['user_role'], ['rh', 'admin'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Non autorisé'], 403);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Méthode non autorisée'], 405);
            return;
        }

        // Récupérer les données (JSON ou POST)
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $email = trim($data['email'] ?? '');
        $prenom = trim($data['prenom'] ?? '');
        $nom = trim($data['nom'] ?? '');
        $manager_id = $data['manager_id'] ?? null;
        $departement = trim($data['departement'] ?? '');
        $role = $data['role'] ?? 'employe';

        // Validation
        if (empty($email) || empty($prenom) || empty($nom)) {
            $this->jsonResponse(['success' => false, 'error' => 'Champs requis manquants'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['success' => false, 'error' => 'Email invalide'], 400);
            return;
        }

        // Vérifier que l'email n'existe pas déjà
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $this->jsonResponse(['success' => false, 'error' => 'Cet email est déjà utilisé'], 409);
            return;
        }

        $token = $this->invitationModel->createInvitation($email, $prenom, $nom, $manager_id, $departement, $role);
        
        if ($token) {
            $invitation_link = "http://" . $_SERVER['HTTP_HOST'] . "/invitation/accept?token={$token}";
            
            // Envoyer l'invitation par email
            EmailSender::sendInvitation($token, $email, $prenom, $nom, $invitation_link);
            error_log("✅ Invitation créée et email envoyé pour: {$email}");
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Invitation créée et email envoyé avec succès',
                'invitation_link' => $invitation_link
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la création de l\'invitation'], 500);
        }
    }

    /**
     * Supprimer une invitation
     */
    public function delete() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $this->checkAuth();
        
        if (!in_array($_SESSION['user_role'], ['rh', 'admin'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Non autorisé'], 403);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Méthode non autorisée'], 405);
            return;
        }

        // Récupérer les données (JSON ou POST)
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = $data['id'] ?? '';
        
        if (empty($id)) {
            $this->jsonResponse(['success' => false, 'error' => 'ID requis'], 400);
            return;
        }

        if ($this->invitationModel->deleteInvitation($id)) {
            $this->jsonResponse(['success' => true, 'message' => 'Invitation supprimée']);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la suppression'], 500);
        }
    }
}
?>
