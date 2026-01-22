<?php
/**
 * AuthController.php - Gestion de l'authentification
 */
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/User.php';

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLoginForm() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/FindIn/public/dashboard');
        }
        $this->view('auth/login');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLoginForm();
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email et mot de passe requis';
            $this->showLoginForm();
            return;
        }

        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
            $_SESSION['user_role'] = $user['role'];

            error_log("✅ Login: {$email}");
            $this->redirect('/FindIn/public/dashboard');
        } else {
            error_log("❌ Login échoué: {$email}");
            $_SESSION['error'] = 'Identifiants incorrects';
            $this->showLoginForm();
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/FindIn/public/login');
    }

    public function showRegisterForm() {
        $this->view('auth/register');
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showRegisterForm();
            return;
        }

        $email = $_POST['email'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $nom = $_POST['nom'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($prenom) || empty($nom) || empty($password)) {
            $_SESSION['error'] = 'Tous les champs sont requis';
            $this->showRegisterForm();
            return;
        }

        // Vérifier si l'email existe déjà
        $existingUser = $this->userModel->getUserByEmail($email);
        if ($existingUser) {
            $_SESSION['error'] = 'Cet email est déjà utilisé';
            $this->showRegisterForm();
            return;
        }

        // Créer le nouvel utilisateur avec les données
        $result = $this->userModel->createUser([
            'email' => $email,
            'prenom' => $prenom,
            'nom' => $nom,
            'password' => $password,
            'role' => 'employe'
        ]);
        
        if ($result) {
            error_log("✅ Registration: {$email}");
            
            // Synchroniser avec XAMPP MySQL pour phpMyAdmin
            require_once __DIR__ . '/../Lib/sync_databases.php';
            syncUsersToXAMPP();
            
            $_SESSION['success'] = 'Inscription réussie! Connectez-vous maintenant.';
            $this->redirect('/FindIn/public/login');
        } else {
            error_log("❌ Registration échouée: {$email}");
            $_SESSION['error'] = 'Erreur lors de l\'inscription. Veuillez réessayer.';
            $this->showRegisterForm();
        }
    }
}
?>
