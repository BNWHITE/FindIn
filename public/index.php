<?php
/**
 * FindIN - Front Controller
 * Router centralisé avec gestion d'erreurs complète
 */

// Initialiser la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuration
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/logs/php-errors.log');

// Sécurité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Content-Type: text/html; charset=utf-8');

// Chemins absolus
define('ROOT_DIR', dirname(__DIR__));
define('SRC_DIR', ROOT_DIR . '/src');
define('VIEWS_DIR', SRC_DIR . '/Views');
define('CONTROLLERS_DIR', SRC_DIR . '/Controllers');
define('MODELS_DIR', SRC_DIR . '/Models');
define('CONFIG_DIR', SRC_DIR . '/Config');
define('PUBLIC_DIR', __DIR__);

// Base de données
if (!defined('DB_TYPE')) {
    define('DB_TYPE', 'mysql');
}
require_once CONFIG_DIR . '/database.php';

// Initialiser la BD
try {
    $db = Database::getInstance();
} catch (Exception $e) {
    http_response_code(500);
    die('❌ Erreur Base de Données: ' . htmlspecialchars($e->getMessage()));
}

/**
 * Parser la route depuis REQUEST_URI
 */
function parseRoute() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($request_uri, PHP_URL_PATH);
    
    // Supprimer le préfixe /FindIn/public (pour Apache)
    $path = preg_replace('#^/FindIn/public/?#i', '', $path);
    // Ou supprimer seulement / (pour PHP dev server)
    $path = preg_replace('#^/?#', '', $path);
    
    // Supprimer les barres obliques inutiles
    $path = trim($path, '/');
    
    // Supprimer les paramètres de query
    if (strpos($path, '?') !== false) {
        $path = substr($path, 0, strpos($path, '?'));
    }
    
    return $path ?: '';
}

/**
 * Charger et exécuter une route
 */
function handleRoute($path) {
    // Normaliser le chemin (supprimer les trailing slashes)
    $path = rtrim($path, '/');
    
    // Routes publiques
    if (empty($path) || $path === 'index' || $path === 'index.php') {
        return requireView('home/index');
    }
    
    if ($path === 'login') {
        return requireController('AuthController', 'login');
    }
    
    if ($path === 'register') {
        return requireController('AuthController', 'register');
    }
    
    if ($path === 'logout') {
        session_destroy();
        header('Location: /FindIn/public/login');
        exit;
    }

    // Routes d'invitation (publiques)
    if (strpos($path, 'invitation/') === 0) {
        $action = substr($path, strlen('invitation/'));
        return requireController('InvitationController', $action);
    }
    
    // Routes de validation (protégées)
    if (strpos($path, 'validation/') === 0) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /FindIn/public/login');
            exit;
        }
        $action = substr($path, strlen('validation/'));
        return requireController('ValidationController', $action);
    }
    
    // Routes protégées
    if (in_array($path, ['dashboard', 'profile', 'competences'])) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /FindIn/public/login');
            exit;
        }
        return requireController(ucfirst($path) . 'Controller', 'index');
    }
    
    // Routes protégées du dashboard (sous-pages)
    if (strpos($path, 'dashboard/') === 0) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /FindIn/public/login');
            exit;
        }
        
        $subPage = substr($path, strlen('dashboard/'));
        
        // Routes spéciales du dashboard
        if ($subPage === 'rh-invitations') {
            return requireController('InvitationController', 'dashboard');
        }
        
        if ($subPage === 'manager-validations') {
            return requireController('ValidationController', 'pending');
        }
        
        // Charger la sous-page du dashboard (vue simple)
        return requireView('dashboard/' . $subPage);
    }
    
    // Pages publiques (infos)
    $publicPages = [
        'about', 'faq', 'features', 'pricing', 'security', 
        'roadmap', 'documentation', 'blog', 'tutorials', 'community',
        'privacy', 'terms', 'cookies', 'accessibility', 'mentions_legales',
        'cgu', 'carrieres', 'presse', 'search'
    ];
    
    // Route de contact (avec formulaire)
    if ($path === 'contact') {
        return requireController('ContactController', 'index');
    }
    
    if (in_array($path, $publicPages)) {
        return requireView($path);
    }
    
    // API
    if (strpos($path, 'api/') === 0) {
        return handleApiRoute($path);
    }
    
    // 404
    return handle404();
}

/**
 * Charger une vue simple
 */
function requireView($view) {
    $file = VIEWS_DIR . '/' . $view . '.php';
    if (!file_exists($file)) {
        return handle404();
    }
    http_response_code(200);
    require_once $file;
    exit;
}

/**
 * Charger un contrôleur
 */
function requireController($controller, $action) {
    $file = CONTROLLERS_DIR . '/' . $controller . '.php';
    
    if (!file_exists($file)) {
        return handle404();
    }
    
    try {
        require_once $file;
        
        if (!class_exists($controller)) {
            throw new Exception("Classe {$controller} non trouvée");
        }
        
        $instance = new $controller();
        
        // Déterminer la méthode à appeler
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Pour les POST, appeler la méthode sans "show"
            $method = str_replace('show', '', $action);
        } else {
            // Pour les GET, utiliser showXxxForm ou index
            if ($action === 'login' || $action === 'register') {
                $method = 'show' . ucfirst($action) . 'Form';
            } else {
                $method = $action;
            }
        }
        
        if (!method_exists($instance, $method)) {
            $method = $action;
        }
        
        if (!method_exists($instance, $method)) {
            throw new Exception("Méthode {$method} non trouvée dans {$controller}");
        }
        
        $instance->$method();
        exit;
        
    } catch (Exception $e) {
        error_log("Erreur contrôleur {$controller}: " . $e->getMessage());
        return handle404();
    }
}

/**
 * Gérer les routes API
 */
function handleApiRoute($path) {
    header('Content-Type: application/json; charset=utf-8');
    
    $parts = explode('/', trim($path, '/'));
    array_shift(); // Enlever 'api'
    
    $resource = $parts[0] ?? null;
    $action = $parts[1] ?? null;
    
    if (!$resource || !$action) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Route API invalide']);
        exit;
    }
    
    $method = $_SERVER['REQUEST_METHOD'];
    $allowed_methods = ['GET' => ['list', 'get'], 'POST' => ['add', 'update', 'delete']];
    
    if (!isset($allowed_methods[$method]) || !in_array($action, $allowed_methods[$method])) {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
        exit;
    }
    
    $api_class = ucfirst($resource) . 'Api';
    $api_file = SRC_DIR . '/Api/' . $api_class . '.php';
    
    if (!file_exists($api_file)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'API non trouvée']);
        exit;
    }
    
    require_once $api_file;
    
    if (!class_exists($api_class)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Classe API non trouvée']);
        exit;
    }
    
    $api = new $api_class();
    
    if (!method_exists($api, $action)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Action non trouvée']);
        exit;
    }
    
    $params = [];
    if ($method === 'GET') {
        $params = $_GET;
    } elseif ($method === 'POST') {
        $params = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    }
    
    try {
        $result = $api->$action($params);
        echo is_string($result) ? $result : json_encode($result);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

/**
 * Page 404
 */
function handle404() {
    http_response_code(404);
    require_once VIEWS_DIR . '/404.php';
    exit;
}

// ========================================
// EXÉCUTION PRINCIPALE
// ========================================
$path = parseRoute();
error_log("🔍 Route: '{$path}' | Méthode: {$_SERVER['REQUEST_METHOD']}");
handleRoute($path);
?>
    case 'dashboard/':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /FindIn/public/login');
            exit;
        }
        require_once CONTROLLERS_DIR . '/DashboardController.php';
        $dashboard = new DashboardController();
        $dashboard->index();
        exit;

    case 'profile':
    case 'profile/':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /FindIn/public/login');
            exit;
        }
        require_once CONTROLLERS_DIR . '/ProfileController.php';
        $profile = new ProfileController();
        $profile->index();
        exit;

    case 'competences':
    case 'competences/':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /FindIn/public/login');
            exit;
        }
        require_once CONTROLLERS_DIR . '/CompetenceController.php';
        $comp = new CompetenceController();
        $comp->index();
        exit;

    // ========== PAGES D'INFO ==========
    case 'about':
    case 'contact':
    case 'faq':
        $page = $path;
        require_once VIEWS_DIR . '/' . $page . '.php';
        exit;

    // ========== API ==========
    case strpos($path, 'api/') === 0:
        header('Content-Type: application/json; charset=utf-8');
        handleApiRoute($path);
        exit;

    // ========== 404 ==========
    default:
        http_response_code(404);
        require_once VIEWS_DIR . '/404.php';
        exit;
}

/**
 * Gestion centralisée des routes API
 */
function handleApiRoute($path) {
    $parts = explode('/', trim($path, '/'));
    array_shift(); // Enlever 'api'
    
    $resource = $parts[0] ?? null;
    $action = $parts[1] ?? null;
    
    if (!$resource || !$action) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Route API invalide']);
        return;
    }

    // Contrôler la méthode HTTP
    $method = $_SERVER['REQUEST_METHOD'];
    $allowed_methods = ['GET' => ['list', 'get'], 'POST' => ['add', 'update', 'delete']];
    
    if (!isset($allowed_methods[$method]) || !in_array($action, $allowed_methods[$method])) {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
        return;
    }

    // Charger l'API correspondante
    $api_class = ucfirst($resource) . 'Api';
    $api_file = SRC_DIR . '/Api/' . $api_class . '.php';

    if (!file_exists($api_file)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'API non trouvée']);
        return;
    }

    require_once $api_file;
    $api = new $api_class();

    // Appeler la méthode
    if (!method_exists($api, $action)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Action non trouvée']);
        return;
    }

    // Préparer les paramètres
    $params = [];
    if ($method === 'GET') {
        $params = $_GET;
    } elseif ($method === 'POST') {
        $params = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    }

    try {
        $result = $api->$action($params);
        echo is_string($result) ? $result : json_encode($result);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
