<?php
// setup_mysql.php
// Initialisation de la base de données MySQL pour FindIN

require_once __DIR__ . '/config/database.php';

echo "🔄 Initialisation de la base de données MySQL FindIN\n\n";

try {
    // Connexion MySQL
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion MySQL établie\n";
    
    // Supprimer et recréer la base de données
    $pdo->exec("DROP DATABASE IF EXISTS `" . DB_NAME . "`");
    $pdo->exec("CREATE DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");
    
    echo "✅ Base de données '" . DB_NAME . "' créée\n";
    
    // Créer la table users
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NULL,
            prenom VARCHAR(100),
            nom VARCHAR(100),
            google_id VARCHAR(255) NULL,
            avatar_url VARCHAR(500) NULL,
            departement VARCHAR(100),
            poste VARCHAR(100),
            role ENUM('employe', 'manager', 'rh', 'admin') DEFAULT 'employe',
            bio TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_google_id (google_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'users' créée\n";
    
    // Créer la table departments
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS departments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) UNIQUE NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✅ Table 'departments' créée\n";
    
    // Créer la table competences
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS competences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            description TEXT,
            categorie VARCHAR(100),
            type_competence ENUM('savoir_faire', 'savoir_etre', 'technique') DEFAULT 'savoir_faire',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✅ Table 'competences' créée\n";
    
    // Créer la table user_competences
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_competences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            competence_id INT NOT NULL,
            niveau_declare TINYINT UNSIGNED DEFAULT 1,
            niveau_valide TINYINT UNSIGNED DEFAULT NULL,
            validated_by INT DEFAULT NULL,
            validated_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            CONSTRAINT fk_competence FOREIGN KEY (competence_id) REFERENCES competences(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_competence (user_id, competence_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✅ Table 'user_competences' créée\n";
    
    // Créer la table projects
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(200) NOT NULL,
            description TEXT,
            status ENUM('planifie', 'en_cours', 'termine', 'annule') DEFAULT 'planifie',
            date_debut DATE,
            date_fin DATE,
            manager_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✅ Table 'projects' créée\n";
    
    // Insérer des données de test
    echo "\n📦 Insertion des données de test...\n";
    
    // Départements
    $pdo->exec("
        INSERT IGNORE INTO departments (nom, description) VALUES 
        ('Direction', 'Direction générale'),
        ('Ressources Humaines', 'Gestion des ressources humaines'),
        ('Développement', 'Équipe de développement logiciel'),
        ('Marketing', 'Marketing et communication'),
        ('Commercial', 'Équipe commerciale'),
        ('Support', 'Support technique et client')
    ");
    echo "✅ Départements insérés\n";
    
    // Compétences
    $pdo->exec("
        INSERT IGNORE INTO competences (nom, description, categorie, type_competence) VALUES 
        ('PHP', 'Développement backend PHP', 'Langages', 'technique'),
        ('JavaScript', 'Développement frontend JS', 'Langages', 'technique'),
        ('Python', 'Développement Python', 'Langages', 'technique'),
        ('MySQL', 'Base de données MySQL', 'Bases de données', 'technique'),
        ('React', 'Framework React.js', 'Frameworks', 'technique'),
        ('Node.js', 'Runtime JavaScript côté serveur', 'Frameworks', 'technique'),
        ('Docker', 'Conteneurisation Docker', 'DevOps', 'technique'),
        ('Git', 'Gestion de versions Git', 'Outils', 'technique'),
        ('Agile/Scrum', 'Méthodologie Agile', 'Méthodologies', 'savoir_faire'),
        ('Communication', 'Communication efficace', 'Soft Skills', 'savoir_etre'),
        ('Leadership', 'Leadership et management', 'Soft Skills', 'savoir_etre'),
        ('Anglais', 'Anglais professionnel', 'Langues', 'savoir_faire')
    ");
    echo "✅ Compétences insérées\n";
    
    // Utilisateur admin de test
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO users (email, password, prenom, nom, role, departement, poste) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute(['admin@findin.fr', $adminPassword, 'Admin', 'FindIN', 'admin', 'Direction', 'Administrateur']);
    echo "✅ Utilisateur admin créé (admin@findin.fr / admin123)\n";
    
    // Utilisateur test
    $testPassword = password_hash('test123', PASSWORD_DEFAULT);
    $stmt->execute(['test@findin.fr', $testPassword, 'Test', 'User', 'employe', 'Développement', 'Développeur']);
    echo "✅ Utilisateur test créé (test@findin.fr / test123)\n";
    
    echo "\n🎉 Base de données initialisée avec succès !\n";
    echo "\n📋 Comptes de test:\n";
    echo "   - Admin: admin@findin.fr / admin123\n";
    echo "   - User:  test@findin.fr / test123\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "\n💡 Vérifiez vos identifiants MySQL dans config/database.php\n";
        echo "   - DB_HOST: " . DB_HOST . "\n";
        echo "   - DB_USER: " . DB_USER . "\n";
        echo "   - DB_NAME: " . DB_NAME . "\n";
    }
    
    if (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "\n💡 MySQL n'est pas démarré. Lancez XAMPP ou MySQL.\n";
    }
    
    exit(1);
}
