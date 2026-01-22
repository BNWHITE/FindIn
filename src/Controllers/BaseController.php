<?php
/**
 * BaseController.php - Contrôleur de base
 */
class BaseController {
    
    protected function view($path, $data = []) {
        extract($data);
        $file = __DIR__ . '/../Views/' . $path . '.php';
        if (!file_exists($file)) {
            http_response_code(500);
            die("❌ Vue non trouvée: {$file}");
        }
        require_once $file;
    }

    protected function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /FindIn/public/login');
            exit;
        }
    }

    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    protected function jsonResponse($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>
