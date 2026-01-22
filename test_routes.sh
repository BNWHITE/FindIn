#!/bin/bash
# test_routes.sh - Tester les routes principales

echo "🧪 Test des routes FindIN"
echo "=========================="
echo ""

# Fonction pour tester une route
test_route() {
    local route=$1
    local method=${2:-GET}
    local description=$3
    
    echo "Testing: $description"
    echo "Route: $route"
    php -r "
    \$_SERVER['REQUEST_URI'] = '/FindIn/public$route';
    \$_SERVER['REQUEST_METHOD'] = '$method';
    \$_SERVER['HTTP_HOST'] = 'localhost';
    
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    define('ROOT_DIR', dirname(dirname(__FILE__)));
    define('SRC_DIR', ROOT_DIR . '/src');
    define('VIEWS_DIR', SRC_DIR . '/Views');
    define('CONTROLLERS_DIR', SRC_DIR . '/Controllers');
    define('MODELS_DIR', SRC_DIR . '/Models');
    define('CONFIG_DIR', SRC_DIR . '/Config');
    define('PUBLIC_DIR', ROOT_DIR . '/public');
    
    if (!defined('DB_TYPE')) {
        define('DB_TYPE', 'mysql');
    }
    require_once CONFIG_DIR . '/database.php';
    
    function parseRoute() {
        \$request_uri = \$_SERVER['REQUEST_URI'] ?? '/';
        \$path = parse_url(\$request_uri, PHP_URL_PATH);
        \$path = preg_replace('#^/FindIn/public/?#i', '', \$path);
        return trim(\$path, '/');
    }
    
    \$path = parseRoute();
    
    if (empty(\$path) || \$path === 'index' || \$path === 'index.php') {
        echo \"✅ Route '' matched\";
    } elseif (\$path === 'login' && \$_SERVER['REQUEST_METHOD'] === 'GET') {
        echo \"✅ Route 'login' matched (GET)\";
    } elseif (\$path === 'register' && \$_SERVER['REQUEST_METHOD'] === 'GET') {
        echo \"✅ Route 'register' matched (GET)\";
    } elseif (\$path === 'logout') {
        echo \"✅ Route 'logout' matched\";
    } else {
        echo \"❌ Route '\$path' not matched\";
    }
    " 2>&1 | grep -E "(✅|❌)"
    
    echo ""
}

# Tester les routes
test_route "" "GET" "Accueil"
test_route "/login" "GET" "Page de login"
test_route "/register" "GET" "Page d'inscription"
test_route "/logout" "GET" "Déconnexion"
test_route "/404" "GET" "Page non trouvée (devrait être 404)"

echo ""
echo "✨ Tests terminés!"
