<?php
/**
 * database.php - Configuration de la base de données
 * 
 * Ce fichier est un TEMPLATE. Copiez-le en database.php et configurez vos credentials.
 * Le fichier database.php (avec les vrais credentials) est dans .gitignore
 */

// =============================================================================
// Type de base de données
// Options: 'mysql', 'sqlite', 'supabase'
// =============================================================================
define('DB_TYPE', getenv('DB_TYPE') ?: 'supabase');

// =============================================================================
// MySQL (XAMPP - développement local)
// =============================================================================
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'gestion_competences');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// =============================================================================
// SQLite (développement local sans serveur)
// =============================================================================
define('DB_PATH', __DIR__ . '/../../storage/database/database.sqlite');

// =============================================================================
// SUPABASE (PostgreSQL - Production)
// 
// Trouvez ces valeurs dans Supabase Dashboard:
// Project Settings > Database > Connection string > URI
// =============================================================================
define('SUPABASE_HOST', getenv('SUPABASE_HOST') ?: 'REMPLACEZ_PAR_VOTRE_HOST');
define('SUPABASE_PORT', getenv('SUPABASE_PORT') ?: '6543');
define('SUPABASE_DB', getenv('SUPABASE_DB') ?: 'postgres');
define('SUPABASE_USER', getenv('SUPABASE_USER') ?: 'postgres.VOTRE_PROJECT_REF');
define('SUPABASE_PASS', getenv('SUPABASE_PASS') ?: 'VOTRE_MOT_DE_PASSE');

// =============================================================================
// Rôles utilisateurs
// =============================================================================
define('ROLE_EMPLOYEE', 'employe');
define('ROLE_MANAGER', 'manager');
define('ROLE_RH', 'rh');
define('ROLE_ADMIN', 'admin');

// =============================================================================
// Application
// =============================================================================
define('APP_NAME', 'FindIN');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8000');
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true' || getenv('DEBUG_MODE') === '1' ? true : false);

// =============================================================================
// Fonction de débogage
// =============================================================================
function debug($data) {
    if (DEBUG_MODE) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

// =============================================================================
// Vérification des extensions PDO
// =============================================================================
if (DB_TYPE === 'sqlite') {
    if (!extension_loaded('pdo_sqlite')) {
        die("L'extension PDO SQLite n'est pas chargée.");
    }
} elseif (DB_TYPE === 'mysql') {
    if (!extension_loaded('pdo_mysql')) {
        die("L'extension PDO MySQL n'est pas chargée.");
    }
} elseif (DB_TYPE === 'supabase') {
    if (!extension_loaded('pdo_pgsql')) {
        die("L'extension PDO PostgreSQL n'est pas chargée. Activez-la dans php.ini (extension=pdo_pgsql)");
    }
} else {
    die("DB_TYPE inconnu. Utilisez 'sqlite', 'mysql' ou 'supabase'.");
}
