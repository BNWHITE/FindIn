<?php
// controllers/AuthController.php - SANS REDIRECTION FORCÉE
class AuthController {
    
    public function login() {
        // Si déjà connecté, montrer directement le dashboard
        if (isset($_SESSION['user_id'])) {
            $this->showDashboard();
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            require_once __DIR__ . '/../Config/database.php';
            require_once __DIR__ . '/../Models/Database.php';
            
            // Utiliser uniquement la table utilisateurs
            $stmt = Database::query('SELECT * FROM utilisateurs WHERE email = ?', [$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Récupérer le mot de passe
                $stored = $user['mot_de_passe'] ?? null;
                if (empty($stored)) {
                    // Accepter connexion et set mot de passe
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    Database::query('UPDATE utilisateurs SET mot_de_passe = ? WHERE email = ?', [$hash, $email]);
                } else {
                    if (!password_verify($password, $stored)) {
                        // Mauvais mot de passe -> afficher login
                        $data['error'] = 'Email ou mot de passe invalide.';
                        require_once __DIR__ . '/../Views/auth/login.php';
                        return;
                    }
                }

                // Set session
                $_SESSION['user_id'] = $user['id'] ?? $user['id_utilisateur'] ?? time();
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: $user['email'];
                $_SESSION['user_role'] = $user['role'] ?? 'employe';
                $_SESSION['departement'] = $user['departement'] ?? ($user['id_departement'] ?? null);

                $this->showDashboard();
                return;
            } else {
                // Aucun utilisateur -> créer dans la table utilisateurs
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $uuid = $this->generateUUID();
                Database::query('INSERT INTO utilisateurs (id, email, mot_de_passe, prenom, nom, role) VALUES (?, ?, ?, ?, ?, ?)', [$uuid, $email, $hash, '', '', 'employe']);
                $_SESSION['user_id'] = $uuid;

                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $email;
                $_SESSION['user_role'] = 'employe';

                $this->showDashboard();
                return;
            }
        }
        
        // Afficher la page de login
        require_once __DIR__ . '/../Views/auth/login.php';
    }
    
    public function logout() {
        // Détruire la session
        session_destroy();
        
        // Afficher directement la page de login
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function register() {
        // Si déjà connecté, afficher dashboard
        if (isset($_SESSION['user_id'])) {
            $this->showDashboard();
            return;
        }

        // POST -> création
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prenom = trim($_POST['prenom'] ?? '');
            $nom = trim($_POST['nom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            $departement = $_POST['id_departement'] ?? null;

            if (empty($prenom) || empty($nom) || empty($email) || empty($password)) {
                $data['error'] = 'Veuillez remplir tous les champs requis.';
                require_once __DIR__ . '/../Views/auth/register.php';
                return;
            }

            if ($password !== $confirm) {
                $data['error'] = 'Les mots de passe ne correspondent pas.';
                require_once __DIR__ . '/../Views/auth/register.php';
                return;
            }

            // Vérifier existence dans la table utilisateurs
            require_once __DIR__ . '/../Config/database.php';
            require_once __DIR__ . '/../Models/Database.php';
            $stmt = Database::query('SELECT * FROM utilisateurs WHERE email = ?', [$email]);
            $user = $stmt->fetch();

            if ($user) {
                $data['error'] = 'Un compte existe déjà avec cet e-mail.';
                require_once __DIR__ . '/../Views/auth/register.php';
                return;
            }

            // Hash mot de passe
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Insérer dans la table utilisateurs
            $uuid = $this->generateUUID();
            Database::query('INSERT INTO utilisateurs (id, email, mot_de_passe, prenom, nom, id_departement, role) VALUES (?, ?, ?, ?, ?, ?, ?)', [$uuid, $email, $hash, $prenom, $nom, $departement, 'employe']);
            $_SESSION['user_id'] = $uuid;

            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $prenom . ' ' . $nom;
            $_SESSION['user_role'] = 'employe';

            $this->showDashboard();
            return;
        }

        // Sinon afficher le formulaire
        require_once __DIR__ . '/../Views/auth/register.php';
    }

    private function generateUUID() {
        // Générateur simple v4
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    
    private function showDashboard() {
        // Afficher directement le dashboard
        require_once __DIR__ . '/../Controllers/DashboardController.php';
        $dashboard = new DashboardController();
        $dashboard->index();
    }
}
?>
