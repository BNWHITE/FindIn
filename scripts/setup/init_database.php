<?php
// init_database.php
echo "<h1>🔄 Initialisation de la base de données FindIN</h1>";

try {
    // Créer ou ouvrir la base SQLite
    $dbFile = 'database.sqlite';
    $pdo = new PDO("sqlite:$dbFile");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion SQLite établie<br>";
    
    // Supprimer les anciennes tables si elles existent
    $tables = ['utilisateurs', 'competences', 'competences_utilisateurs', 'departements', 'categories_competences'];
    foreach ($tables as $table) {
        try {
            $pdo->exec("DROP TABLE IF EXISTS $table");
        } catch (Exception $e) {
            // Ignorer les erreurs
        }
    }
    
    // Créer les tables avec la structure SIMPLIFIÉE pour le MVP
    $sql = "
    -- Table utilisateurs
    CREATE TABLE utilisateurs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        prenom TEXT,
        nom TEXT,
        departement TEXT,
        role TEXT DEFAULT 'employe',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    
    -- Table categories_competences (simplifiée)
    CREATE TABLE categories_competences (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT UNIQUE NOT NULL
    );
    
    -- Table competences (simplifiée)
    CREATE TABLE competences (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT UNIQUE NOT NULL,
        description TEXT,
        id_categorie INTEGER,
        type_competence TEXT DEFAULT 'savoir_faire',
        FOREIGN KEY (id_categorie) REFERENCES categories_competences(id)
    );
    
    -- Table competences_utilisateurs (simplifiée)
    CREATE TABLE competences_utilisateurs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_utilisateur INTEGER NOT NULL,
        id_competence INTEGER NOT NULL,
        niveau_declare INTEGER CHECK(niveau_declare BETWEEN 1 AND 5),
        niveau_valide INTEGER CHECK(niveau_valide BETWEEN 1 AND 5),
        id_manager_validateur INTEGER,
        date_validation DATETIME,
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
        FOREIGN KEY (id_competence) REFERENCES competences(id) ON DELETE CASCADE,
        UNIQUE(id_utilisateur, id_competence)
    );
    
    -- Table departements (simplifiée)
    CREATE TABLE departements (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT UNIQUE NOT NULL
    );
    ";
    
    $pdo->exec($sql);
    echo "✅ Tables créées avec succès<br>";
    
    // Insérer les données de test
    $pdo->exec("
        -- Catégories
        INSERT INTO categories_competences (nom) VALUES 
        ('Langages de programmation'),
        ('Frameworks'),
        ('Bases de données'),
        ('Outils de développement'),
        ('Gestion de projet'),
        ('Design'),
        ('Soft skills'),
        ('Systèmes et réseaux'),
        ('Méthodologies'),
        ('Langues');
        
        -- Départements
        INSERT INTO departements (nom) VALUES 
        ('Direction'),
        ('Ressources Humaines'),
        ('Développement'),
        ('Marketing'),
        ('Commercial'),
        ('Support Technique');
        
        -- Compétences
        INSERT INTO competences (nom, id_categorie, type_competence) VALUES
        ('PHP', (SELECT id FROM categories_competences WHERE nom = 'Langages de programmation'), 'savoir_faire'),
        ('JavaScript', (SELECT id FROM categories_competences WHERE nom = 'Langages de programmation'), 'savoir_faire'),
        ('Python', (SELECT id FROM categories_competences WHERE nom = 'Langages de programmation'), 'savoir_faire'),
        ('Laravel', (SELECT id FROM categories_competences WHERE nom = 'Frameworks'), 'savoir_faire'),
        ('React', (SELECT id FROM categories_competences WHERE nom = 'Frameworks'), 'savoir_faire'),
        ('MySQL', (SELECT id FROM categories_competences WHERE nom = 'Bases de données'), 'savoir_faire'),
        ('Git', (SELECT id FROM categories_competences WHERE nom = 'Outils de développement'), 'savoir_faire'),
        ('Agile/Scrum', (SELECT id FROM categories_competences WHERE nom = 'Gestion de projet'), 'savoir_faire'),
        ('Communication', (SELECT id FROM categories_competences WHERE nom = 'Soft skills'), 'savoir_etre'),
        ('Leadership', (SELECT id FROM categories_competences WHERE nom = 'Soft skills'), 'savoir_etre'),
        ('Anglais', (SELECT id FROM categories_competences WHERE nom = 'Langues'), 'savoir_etre');
        
        -- Utilisateurs
        INSERT INTO utilisateurs (email, prenom, nom, departement, role) VALUES
        ('admin@findin.com', 'Admin', 'FindIN', 'Direction', 'admin'),
        ('manager@findin.com', 'Manager', 'Test', 'Développement', 'manager'),
        ('rh@findin.com', 'Ressources', 'Humaines', 'Ressources Humaines', 'rh'),
        ('dev1@findin.com', 'Lucas', 'Développeur', 'Développement', 'employe'),
        ('dev2@findin.com', 'Emma', 'Codeuse', 'Développement', 'employe'),
        ('commercial@findin.com', 'Pierre', 'Commercial', 'Commercial', 'employe');
        
        -- Compétences utilisateurs
        INSERT INTO competences_utilisateurs (id_utilisateur, id_competence, niveau_declare, niveau_valide) VALUES
        ((SELECT id FROM utilisateurs WHERE email = 'dev1@findin.com'), 
         (SELECT id FROM competences WHERE nom = 'PHP'), 4, 4),
        ((SELECT id FROM utilisateurs WHERE email = 'dev1@findin.com'), 
         (SELECT id FROM competences WHERE nom = 'Laravel'), 5, 5),
        ((SELECT id FROM utilisateurs WHERE email = 'dev1@findin.com'), 
         (SELECT id FROM competences WHERE nom = 'MySQL'), 3, 3),
        ((SELECT id FROM utilisateurs WHERE email = 'dev2@findin.com'), 
         (SELECT id FROM competences WHERE nom = 'JavaScript'), 5, 5),
        ((SELECT id FROM utilisateurs WHERE email = 'dev2@findin.com'), 
         (SELECT id FROM competences WHERE nom = 'React'), 4, 4),
        ((SELECT id FROM utilisateurs WHERE email = 'dev2@findin.com'), 
         (SELECT id FROM competences WHERE nom = 'Python'), 3, NULL),
        ((SELECT id FROM utilisateurs WHERE email = 'admin@findin.com'), 
         (SELECT id FROM competences WHERE nom = 'Leadership'), 5, 5),
        ((SELECT id FROM utilisateurs WHERE email = 'admin@findin.com'), 
         (SELECT id FROM competences WHERE nom = 'Anglais'), 4, 4);
    ");
    
    echo "✅ Données de test insérées<br>";
    
    // Vérifier
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll();
    echo "<h3>📊 Tables créées :</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) as count FROM " . $table['name'])->fetch()['count'];
        echo "<li>{$table['name']} ($count enregistrements)</li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    echo "<h2>🎉 Base de données initialisée avec succès !</h2>";
    echo "<p><a href='/' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>🚀 Aller à FindIN</a></p>";
    echo "<p><strong>Identifiants de test :</strong></p>";
    echo "<ul>";
    echo "<li>👑 Admin: admin@findin.com (n'importe quel mot de passe)</li>";
    echo "<li>👨‍💼 Manager: manager@findin.com</li>";
    echo "<li>👩‍💼 RH: rh@findin.com</li>";
    echo "<li>👨‍💻 Développeur: dev1@findin.com</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "<br>";
}
?>
