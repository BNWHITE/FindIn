<?php
// config/database.php
// Support MySQL (XAMPP), SQLite, ou Supabase (PostgreSQL)
// Examples:
//  - SQLite (default): DB_TYPE = 'sqlite' -> uses file database.sqlite in project root
//  - MySQL (XAMPP): DB_TYPE = 'mysql' -> configure host, name, user, pass below
//  - Supabase: DB_TYPE = 'supabase' -> configure Supabase credentials below

// Switch here for database type:
// Options: 'mysql', 'sqlite', 'supabase'
if (!defined('DB_TYPE')) {
    define('DB_TYPE', getenv('DB_TYPE') ?: 'mysql');
}

// MySQL / XAMPP connection defaults (overridable via environment variables)
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3305');  // XAMPP MySQL port
define('DB_NAME', getenv('DB_NAME') ?: 'findin');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// SQLite settings (used when DB_TYPE == 'sqlite')
define('DB_PATH', __DIR__ . '/../../storage/database/database.sqlite');

// =============================================================================
// SUPABASE Configuration (PostgreSQL)
// =============================================================================
// Credentials Supabase - Project: ugdkdrdgxtfwsehzpmvm
// Transaction Pooler (IPv4 compatible) - port 6543
// =============================================================================
define('SUPABASE_HOST', getenv('SUPABASE_HOST') ?: 'aws-1-eu-west-1.pooler.supabase.com');
define('SUPABASE_PORT', getenv('SUPABASE_PORT') ?: '6543');
define('SUPABASE_DB', getenv('SUPABASE_DB') ?: 'postgres');
define('SUPABASE_USER', getenv('SUPABASE_USER') ?: 'postgres.ugdkdrdgxtfwsehzpmvm');
define('SUPABASE_PASS', getenv('SUPABASE_PASS') ?: 'DvDrd3rVeU6qgOdd');

// Rôles utilisateurs
define('ROLE_EMPLOYEE', 'employe');
define('ROLE_MANAGER', 'manager');
define('ROLE_RH', 'rh');
define('ROLE_ADMIN', 'admin');

// Configuration
define('APP_NAME', 'FindIN');
// If using XAMPP with Apache on port 80, APP_URL can be http://localhost
define('APP_URL', getenv('APP_URL') ?: 'http://findin.local');
define('DEBUG_MODE', true);

// Fonction de débogage
function debug($data) {
    if (DEBUG_MODE) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

// Vérifier les extensions PDO disponibles
if (DB_TYPE === 'sqlite') {
    if (!extension_loaded('pdo_sqlite')) {
        die("L'extension PDO SQLite n'est pas chargée. Activez-la dans php.ini ou installez php-sqlite3.");
    }
} elseif (DB_TYPE === 'mysql') {
    if (!extension_loaded('pdo_mysql')) {
        die("L'extension PDO MySQL n'est pas chargée. Activez-la dans php.ini (extension=pdo_mysql)");
    }
} elseif (DB_TYPE === 'supabase') {
    if (!extension_loaded('pdo_pgsql')) {
        die("L'extension PDO PostgreSQL n'est pas chargée. Activez-la dans php.ini (extension=pdo_pgsql)");
    }
} else {
    die("DB_TYPE inconnu. Utilisez 'sqlite', 'mysql' ou 'supabase'.");
}

// ============================================================================
// CONFIGURATION BASE DE DONNEES - XAMPP LOCAL
// ============================================================================

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $this->connect();
        $this->createTables();
    }

    private function connect() {
        try {
            $host = getenv('DB_HOST') ?: 'localhost';
            $port = getenv('DB_PORT') ?: '3306';
            $db = getenv('DB_NAME') ?: 'findin';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: '';
            $charset = 'utf8mb4';

            // DSN avec options de connexion optimisées
            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
            
            // Options PDO pour une meilleure performance et stabilité
            $options = [
                // Mode erreur
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                
                // Fetch par défaut
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                
                // Éviter l'émulation des requêtes préparées (plus sûr et rapide)
                PDO::ATTR_EMULATE_PREPARES => false,
                
                // Timeout de connexion
                PDO::ATTR_TIMEOUT => 10,
                
                // Persistent connections (optionnel, utile pour pools)
                // PDO::ATTR_PERSISTENT => true,  // Décommenter pour connection pooling
            ];
            
            $this->pdo = new PDO($dsn, $user, $pass, $options);
            
            // Définir les variables de session MySQL
            $this->pdo->exec("SET SESSION sql_mode='STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
            $this->pdo->exec("SET NAMES utf8mb4");
            $this->pdo->exec("SET CHARACTER SET utf8mb4");
            $this->pdo->exec("SET COLLATION_CONNECTION='utf8mb4_unicode_ci'");

            error_log("✅ MySQL connecté: {$db} @ {$host}:{$port}");
        } catch (PDOException $e) {
            error_log("❌ Erreur MySQL: " . $e->getMessage());
            die("Erreur de connexion à la base de données. Vérifiez votre configuration.");
        }
    }

    private function createTables() {
        try {
            // Table utilisateurs
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS utilisateurs (
                id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                prenom VARCHAR(100),
                nom VARCHAR(100),
                mot_de_passe VARCHAR(255) NOT NULL,
                role VARCHAR(50) DEFAULT 'employe',
                photo VARCHAR(255),
                cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_role (role)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Table compétences
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS competences (
                id_competence INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(255) UNIQUE NOT NULL,
                description TEXT,
                type_competence VARCHAR(50) DEFAULT 'technique',
                cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_nom (nom)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Table compétences utilisateurs
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS competences_utilisateurs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                id_competence INT NOT NULL,
                niveau_declare INT DEFAULT 1,
                niveau_valide INT,
                date_validation TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
                FOREIGN KEY (id_competence) REFERENCES competences(id_competence) ON DELETE CASCADE,
                UNIQUE KEY unique_user_comp (user_id, id_competence),
                INDEX idx_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Table projets
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS projets (
                id_projet INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(255) NOT NULL,
                description TEXT,
                responsable_id INT,
                statut VARCHAR(50) DEFAULT 'en_cours',
                date_debut DATE,
                date_fin DATE,
                cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (responsable_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
                INDEX idx_statut (statut)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Table certifications
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS certifications (
                id_certification INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                nom VARCHAR(255) NOT NULL,
                organisme VARCHAR(255),
                date_obtention DATE,
                date_expiration DATE,
                cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
                INDEX idx_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Table documents
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS documents (
                id_document INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                nom VARCHAR(255) NOT NULL,
                type VARCHAR(50),
                url_fichier VARCHAR(255) NOT NULL,
                date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
                INDEX idx_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Table réunions
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS reunions (
                id_reunion INT AUTO_INCREMENT PRIMARY KEY,
                employe_id INT NOT NULL,
                manager_id INT NOT NULL,
                titre VARCHAR(255) NOT NULL,
                description TEXT,
                date_reunion TIMESTAMP NOT NULL,
                duree_minutes INT,
                notes TEXT,
                status VARCHAR(50) DEFAULT 'planifiee',
                cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (employe_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
                FOREIGN KEY (manager_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
                INDEX idx_date (date_reunion)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Table messages
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS messages (
                id_message INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(255),
                email VARCHAR(255),
                sujet VARCHAR(255),
                message TEXT,
                is_read BOOLEAN DEFAULT FALSE,
                cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Table tests
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS tests (
                id_test INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                competence_id INT,
                titre VARCHAR(255) NOT NULL,
                description TEXT,
                score_obtenu INT,
                score_maximum INT,
                date_test TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status VARCHAR(50) DEFAULT 'en_cours',
                FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
                FOREIGN KEY (competence_id) REFERENCES competences(id_competence) ON DELETE SET NULL,
                INDEX idx_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            error_log("✅ Tables créées/vérifiées");

            // Insérer les données de test si vides
            $this->seedData();
        } catch (Exception $e) {
            error_log("⚠️ Erreur création tables: " . $e->getMessage());
        }
    }

    private function seedData() {
        try {
            // Vérifier si l'admin existe
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM utilisateurs WHERE email = ?");
            $stmt->execute(['admin@findin.fr']);
            $result = $stmt->fetch();

            if ($result['count'] == 0) {
                // Insérer admin
                $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
                $this->pdo->prepare("INSERT INTO utilisateurs (email, prenom, nom, mot_de_passe, role) 
                    VALUES (?, ?, ?, ?, ?)")
                    ->execute(['admin@findin.fr', 'Admin', 'FindIN', $password_hash, 'admin']);

                // Insérer utilisateur test
                $this->pdo->prepare("INSERT INTO utilisateurs (email, prenom, nom, mot_de_passe, role) 
                    VALUES (?, ?, ?, ?, ?)")
                    ->execute(['test@findin.fr', 'Test', 'User', $password_hash, 'employe']);

                // Insérer compétences
                $competences = [
                    ['PHP', 'Programmation PHP', 'technique'],
                    ['JavaScript', 'Programmation JavaScript', 'technique'],
                    ['Python', 'Programmation Python', 'technique'],
                    ['Communication', 'Compétences de communication', 'soft_skill'],
                    ['Leadership', 'Leadership et gestion d\'équipe', 'soft_skill'],
                ];

                foreach ($competences as $comp) {
                    $this->pdo->prepare("INSERT IGNORE INTO competences (nom, description, type_competence) VALUES (?, ?, ?)")
                        ->execute($comp);
                }

                error_log("✅ Données de test insérées");
            }
        } catch (Exception $e) {
            error_log("⚠️ Erreur insertion données: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }

    public static function query($sql, $params = []) {
        $db = self::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
?>
