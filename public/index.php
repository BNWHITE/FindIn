<?php
session_start();

// Autoloading manuel des classes (puisque vous n'utilisez pas Composer)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

// Inclure la classe Database manuellement si nécessaire
require_once __DIR__ . '/../src/Models/Database.php';

// Analyse de l'URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Routage professionnel
switch ($uri) {
    case '/':
    case '/home':
        $controller = new App\Controllers\HomeController();
        $controller->index();
        break;

    case '/admin/competences':
        $controller = new App\Controllers\AdminController();
        $controller->competences();
        break;

    case '/login':
        $controller = new App\Controllers\AuthController();
        $controller->login();
        break;

    default:
        http_response_code(404);
        echo "404 - Page non trouvée";
        break;
}
