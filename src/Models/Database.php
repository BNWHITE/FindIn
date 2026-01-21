<?php
// models/Database.php - Support MySQL, SQLite, Supabase (PostgreSQL)
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $dbType = defined('DB_TYPE') ? DB_TYPE : 'sqlite';
            
            if ($dbType === 'supabase') {
                // Supabase PostgreSQL
                $dsn = sprintf(
                    'pgsql:host=%s;port=%s;dbname=%s;sslmode=require',
                    SUPABASE_HOST,
                    SUPABASE_PORT,
                    SUPABASE_DB
                );
                $this->connection = new PDO($dsn, SUPABASE_USER, SUPABASE_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                // Tables already exist in Supabase, no need to create
                
            } elseif ($dbType === 'mysql') {
                $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
                $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                $this->createMinimalTables();
                
            } else {
                // Par défaut SQLite
                $dbPath = defined('DB_PATH') ? DB_PATH : (__DIR__ . '/../../storage/database/database.sqlite');

                // Créer le fichier si il n'existe pas
                if (!file_exists($dbPath)) {
                    $dir = dirname($dbPath);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    touch($dbPath);
                }

                // Connexion SQLite
                $this->connection = new PDO("sqlite:$dbPath");
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->createMinimalTables();
            }

        } catch (PDOException $e) {
            // Message d'erreur simple
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                die("Erreur DB: " . $e->getMessage());
            } else {
                die("Erreur de connexion à la base de données.");
            }
        }
    }

    private function createMinimalTables() {
        $dbType = defined('DB_TYPE') ? DB_TYPE : 'sqlite';
        
        // Skip for Supabase - tables are managed via SQL Editor
        if ($dbType === 'supabase') {
            return;
        }
        
        // Table utilisateurs (minimale)
        if ($dbType === 'mysql') {
            $sql = "CREATE TABLE IF NOT EXISTS utilisateurs (
                id_utilisateur CHAR(36) NOT NULL PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                prenom VARCHAR(100),
                nom VARCHAR(100),
                mot_de_passe VARCHAR(255),
                id_departement CHAR(36) DEFAULT NULL,
                role VARCHAR(50) DEFAULT 'employe',
                cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->connection->exec($sql);
            
            // Migrate: Add mot_de_passe column if it doesn't exist (MySQL)
            try {
                $checkCol = $this->connection->query("SHOW COLUMNS FROM utilisateurs LIKE 'mot_de_passe'")->fetch();
                if (!$checkCol) {
                    // Try different positions
                    try {
                        $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN mot_de_passe VARCHAR(255) AFTER email");
                    } catch (Exception $e1) {
                        try {
                            $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN mot_de_passe VARCHAR(255)");
                        } catch (Exception $e2) {
                            // Already exists, ignore
                        }
                    }
                }
            } catch (Exception $e) {
                // Column might already exist, continue
            }
            // Migrate: Add role column if missing (MySQL)
            try {
                $checkRole = $this->connection->query("SHOW COLUMNS FROM utilisateurs LIKE 'role'")->fetch();
                if (!$checkRole) {
                    $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN role VARCHAR(50) DEFAULT 'employe'");
                }
            } catch (Exception $e) {
                // ignore
            }

            // Migrate: Add id_departement column if missing (MySQL)
            try {
                $checkDept = $this->connection->query("SHOW COLUMNS FROM utilisateurs LIKE 'id_departement'")->fetch();
                if (!$checkDept) {
                    $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN id_departement CHAR(36) DEFAULT NULL");
                }
            } catch (Exception $e) {
                // ignore
            }
            // Migrate: Add photo column if missing (MySQL)
            try {
                $checkPhoto = $this->connection->query("SHOW COLUMNS FROM utilisateurs LIKE 'photo'")->fetch();
                if (!$checkPhoto) {
                    $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN photo VARCHAR(255) DEFAULT NULL");
                }
            } catch (Exception $e) { }
            // Migrate: Add competences column if missing (MySQL)
            try {
                $checkComp = $this->connection->query("SHOW COLUMNS FROM utilisateurs LIKE 'competences'")->fetch();
                if (!$checkComp) {
                    $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN competences TEXT DEFAULT NULL");
                }
            } catch (Exception $e) { }
            // Migrate: Add last_cv column if missing (MySQL)
            try {
                $checkLastCv = $this->connection->query("SHOW COLUMNS FROM utilisateurs LIKE 'last_cv'")->fetch();
                if (!$checkLastCv) {
                    $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN last_cv VARCHAR(255) DEFAULT NULL");
                }
            } catch (Exception $e) { }
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS utilisateurs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT UNIQUE NOT NULL,
                prenom TEXT,
                nom TEXT,
                mot_de_passe TEXT,
                departement TEXT,
                role TEXT DEFAULT 'employe'
            );";
            $this->connection->exec($sql);
            
            // Migrate: Add mot_de_passe column if it doesn't exist (SQLite)
            try {
                $checkCol = $this->connection->query("PRAGMA table_info(utilisateurs)")->fetchAll();
                $hasPassword = false;
                foreach ($checkCol as $col) {
                    if ($col['name'] === 'mot_de_passe') {
                        $hasPassword = true;
                        break;
                    }
                }
                if (!$hasPassword) {
                    $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN mot_de_passe TEXT");
                }
            } catch (Exception $e) {
                // Column might already exist, continue
            }
            // Migrate: Add role column if missing (SQLite)
            try {
                $cols = $this->connection->query("PRAGMA table_info(utilisateurs)")->fetchAll();
                $hasRole = false;
                foreach ($cols as $c) {
                    if ($c['name'] === 'role') { $hasRole = true; break; }
                }
                if (!$hasRole) {
                    $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN role TEXT DEFAULT 'employe'");
                }
            } catch (Exception $e) {
                // ignore
            }

            // Migrate: Add departement column if missing (SQLite)
            try {
                $cols = $this->connection->query("PRAGMA table_info(utilisateurs)")->fetchAll();
                $hasDept = false;
                foreach ($cols as $c) {
                    if ($c['name'] === 'departement' || $c['name'] === 'id_departement') { $hasDept = true; break; }
                }
                if (!$hasDept) {
                    $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN departement TEXT");
                }
            } catch (Exception $e) {
                // ignore
            }
            // Migrate: Add photo column if missing (SQLite)
            try {
                $cols = $this->connection->query("PRAGMA table_info(utilisateurs)")->fetchAll();
                $hasPhoto = false;
                foreach ($cols as $c) { if ($c['name'] === 'photo') { $hasPhoto = true; break; } }
                if (!$hasPhoto) { $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN photo TEXT DEFAULT NULL"); }
            } catch (Exception $e) { }
            // Migrate: Add competences column if missing (SQLite)
            try {
                $cols = $this->connection->query("PRAGMA table_info(utilisateurs)")->fetchAll();
                $hasComp = false;
                foreach ($cols as $c) { if ($c['name'] === 'competences') { $hasComp = true; break; } }
                if (!$hasComp) { $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN competences TEXT DEFAULT NULL"); }
            } catch (Exception $e) { }
            // Migrate: Add last_cv column if missing (SQLite)
            try {
                $cols = $this->connection->query("PRAGMA table_info(utilisateurs)")->fetchAll();
                $hasLastCv = false;
                foreach ($cols as $c) { if ($c['name'] === 'last_cv') { $hasLastCv = true; break; } }
                if (!$hasLastCv) { $this->connection->exec("ALTER TABLE utilisateurs ADD COLUMN last_cv TEXT DEFAULT NULL"); }
            } catch (Exception $e) { }
        }

        // Table competences (minimale)
        $sql = (defined('DB_TYPE') && DB_TYPE === 'mysql') ?
            "CREATE TABLE IF NOT EXISTS competences (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(255) UNIQUE NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
            :
            "CREATE TABLE IF NOT EXISTS competences (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nom TEXT UNIQUE NOT NULL
            );";
        try {
            $this->connection->exec($sql);
        } catch (Exception $e) {
            // Table might already exist
        }

        // Table competences_utilisateurs (minimale) - sans clés étrangères pour éviter erreurs
        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            $sql = "CREATE TABLE IF NOT EXISTS competences_utilisateurs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                utilisateur_id INT,
                competence_id INT,
                niveau TINYINT DEFAULT 1,
                INDEX idx_user (utilisateur_id),
                INDEX idx_comp (competence_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS competences_utilisateurs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                utilisateur_id INTEGER,
                competence_id INTEGER,
                niveau INTEGER DEFAULT 1
            );";
        }
        try {
            $this->connection->exec($sql);
        } catch (Exception $e) {
            // Table might already exist
        }

        // Table messages/contact (minimale)
        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            $sql = "CREATE TABLE IF NOT EXISTS messages (
                id_message CHAR(36) NOT NULL PRIMARY KEY,
                nom VARCHAR(255),
                email VARCHAR(255),
                message TEXT,
                cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nom TEXT,
                email TEXT,
                message TEXT,
                cree_le DATETIME DEFAULT CURRENT_TIMESTAMP
            );";
        }
        try {
            $this->connection->exec($sql);
        } catch (Exception $e) {
            // ignore
        }

        // Migrate: add is_read column if missing (MySQL/SQLite)
        try {
            if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
                $check = $this->connection->query("SHOW COLUMNS FROM messages LIKE 'is_read'")->fetch();
                if (!$check) {
                    $this->connection->exec("ALTER TABLE messages ADD COLUMN is_read TINYINT DEFAULT 0");
                }
            } else {
                $cols = $this->connection->query("PRAGMA table_info(messages)")->fetchAll();
                $hasIsRead = false;
                foreach ($cols as $c) {
                    if ($c['name'] === 'is_read') { $hasIsRead = true; break; }
                }
                if (!$hasIsRead) {
                    $this->connection->exec("ALTER TABLE messages ADD COLUMN is_read INTEGER DEFAULT 0");
                }
            }
        } catch (Exception $e) {
            // ignore migration errors
        }

        // Insérer l'admin si nécessaire
        $stmt = $this->connection->query("SELECT COUNT(*) as count FROM utilisateurs");
        $row = $stmt->fetch();
        $count = isset($row['count']) ? $row['count'] : (isset($row[0]) ? $row[0] : 0);

        if ($count == 0) {
            // Insérer un utilisateur admin de test
            if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
                $this->connection->exec("INSERT INTO utilisateurs (id_utilisateur, email, prenom, nom, role) VALUES 
                    (UUID(), 'admin@findin.com', 'Admin', 'FindIN', 'admin')");
            } else {
                $this->connection->exec("INSERT INTO utilisateurs (email, prenom, nom, role) VALUES 
                    ('admin@findin.com', 'Admin', 'FindIN', 'admin')");
            }

            // Insert quelques compétences
            if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
                $this->connection->exec("INSERT INTO competences (id_competence, nom) VALUES 
                    (UUID(), 'PHP'), (UUID(), 'JavaScript'), (UUID(), 'Python'), (UUID(), 'Communication')");
            } else {
                $this->connection->exec("INSERT INTO competences (nom) VALUES 
                    ('PHP'), ('JavaScript'), ('Python'), ('Communication')");
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }

    public static function query($sql, $params = []) {
        $db = self::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
?>
