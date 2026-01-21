<?php
// controllers/BaseController.php
class BaseController {
    
    protected function view($view, $data = []) {
        // Extraire les données pour les rendre accessibles dans la vue
        extract($data);
        
        // Chemin vers le fichier de vue
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            // Afficher une erreur plus conviviale
            http_response_code(404);
            echo "<!DOCTYPE html><html><head><title>Erreur</title></head><body>";
            echo "<h1>Erreur : Vue non trouvée</h1>";
            echo "<p>La vue '$view' n'existe pas.</p>";
            echo "<p><a href='/'>Retour à l'accueil</a></p>";
            echo "</body></html>";
            exit;
        }
    }
    
    protected function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
    }
    
    protected function checkRole($allowedRoles) {
        $this->checkAuth();
        
        if (!in_array($_SESSION['user_role'], $allowedRoles)) {
            die("Accès non autorisé");
        }
    }
    
    protected function redirect($url) {
        header("Location: $url");
        exit();
    }
}
?>
