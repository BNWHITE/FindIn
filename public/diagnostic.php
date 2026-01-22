<?php
/**
 * Diagnostic FindIN - Vérification complète du système
 * Accéder via: http://localhost/FindIn/public/
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/storage/logs/php-errors.log');

define('ROOT_DIR', dirname(__FILE__));
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

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic FindIN</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { color: #667eea; margin-bottom: 30px; }
        h2 { color: #764ba2; margin-top: 30px; margin-bottom: 15px; font-size: 20px; }
        .status { padding: 15px; border-radius: 8px; margin-bottom: 10px; }
        .status.ok { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .status.error { background: #f8d7da; color: #721c24; border-left: 4px solid #f5c6cb; }
        .status.warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New'; }
        .button-group { margin-top: 40px; display: flex; gap: 15px; flex-wrap: wrap; }
        a { background: #667eea; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-block; }
        a:hover { background: #764ba2; }
        a.secondary { background: #6c757d; }
        a.secondary:hover { background: #5a6268; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Diagnostic FindIN</h1>

        <h2>✅ Système</h2>
        <?php
        $db_ok = false;
        try {
            $db = Database::getInstance();
            $db_ok = true;
            echo '<div class="status ok">✅ Base de données MySQL connectée</div>';
        } catch (Exception $e) {
            echo '<div class="status error">❌ Erreur DB: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }

        echo '<div class="status ok">✅ PHP ' . phpversion() . '</div>';
        echo '<div class="status ok">✅ Session démarrée</div>';
        ?>

        <h2>📁 Fichiers clés</h2>
        <?php
        $files = [
            'index.php' => PUBLIC_DIR . '/index.php',
            'AuthController.php' => CONTROLLERS_DIR . '/AuthController.php',
            'BaseController.php' => CONTROLLERS_DIR . '/BaseController.php',
            'User.php (Model)' => MODELS_DIR . '/User.php',
            'Database.php (Config)' => CONFIG_DIR . '/database.php',
            'login.php (View)' => VIEWS_DIR . '/auth/login.php',
        ];

        foreach ($files as $name => $path) {
            if (file_exists($path)) {
                echo '<div class="status ok">✅ ' . htmlspecialchars($name) . '</div>';
            } else {
                echo '<div class="status error">❌ ' . htmlspecialchars($name) . ' - Non trouvé</div>';
            }
        }
        ?>

        <h2>🔐 Routes de test</h2>
        <div class="button-group">
            <a href="/FindIn/public/">🏠 Accueil</a>
            <a href="/FindIn/public/login">🔐 Login</a>
            <a href="/FindIn/public/register">📝 Inscription</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/FindIn/public/dashboard">📊 Dashboard</a>
                <a href="/FindIn/public/logout" class="secondary">🚪 Logout</a>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <h2>👤 Utilisateur connecté</h2>
            <div class="status ok">
                ✅ Connecté en tant que: <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? '?'); ?></strong>
                (<?php echo htmlspecialchars($_SESSION['user_email'] ?? '?'); ?>)
            </div>
        <?php endif; ?>

        <h2>📋 Informations</h2>
        <p style="margin-top: 15px; color: #666; line-height: 1.6;">
            <strong>✨ FindIN</strong> est un système de gestion des compétences.<br>
            <strong>Framework:</strong> PHP MVC vanilla (pas de framework)<br>
            <strong>BD:</strong> MySQL avec gestion automatique des tables<br>
            <strong>Routing:</strong> Centralisé dans <code>public/index.php</code>
        </p>
    </div>
</body>
</html>
