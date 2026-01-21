<?php
// start_sqlite.php
echo "<h1>🚀 Installation Express FindIN avec SQLite</h1>";

// 1. Créer la base de données SQLite
$dbFile = 'database.sqlite';
if (!file_exists($dbFile)) {
    file_put_contents($dbFile, '');
    echo "✅ Base de données SQLite créée<br>";
} else {
    echo "✅ Base de données SQLite existe déjà<br>";
}

// 2. Vérifier PDO SQLite
if (!extension_loaded('pdo_sqlite')) {
    echo "❌ Extension pdo_sqlite manquante<br>";
    echo "Sur macOS: <code>brew install php-sqlite3</code><br>";
} else {
    echo "✅ Extension pdo_sqlite chargée<br>";
}

// 3. Tester la connexion
try {
    $pdo = new PDO("sqlite:$dbFile");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion SQLite réussie<br>";
    
    // Créer les tables
    $sql = "
    -- Table utilisateurs
    CREATE TABLE IF NOT EXISTS utilisateurs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        prenom TEXT,
        nom TEXT,
        departement TEXT,
        role TEXT DEFAULT 'employe',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    
    -- Table competences
    CREATE TABLE IF NOT EXISTS competences (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT UNIQUE NOT NULL,
        categorie TEXT,
        type_competence TEXT DEFAULT 'savoir_faire'
    );
    
    -- Table competences_utilisateurs
    CREATE TABLE IF NOT EXISTS competences_utilisateurs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        utilisateur_id INTEGER NOT NULL,
        competence_id INTEGER NOT NULL,
        niveau_declare INTEGER CHECK(niveau_declare BETWEEN 1 AND 5),
        niveau_valide INTEGER CHECK(niveau_valide BETWEEN 1 AND 5),
        UNIQUE(utilisateur_id, competence_id)
    );
    ";
    
    $pdo->exec($sql);
    echo "✅ Tables créées<br>";
    
    // Vérifier si l'admin existe
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM utilisateurs WHERE email = 'admin@findin.com'");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        // Insérer l'admin
        $pdo->exec("INSERT INTO utilisateurs (email, prenom, nom, departement, role) VALUES 
                   ('admin@findin.com', 'Admin', 'FindIN', 'Direction', 'admin'),
                   ('dev@findin.com', 'Développeur', 'Test', 'IT', 'employe'),
                   ('manager@findin.com', 'Manager', 'Test', 'IT', 'manager')");
        
        // Insérer des compétences
        $pdo->exec("INSERT INTO competences (nom, categorie) VALUES 
                   ('PHP', 'Langages'),
                   ('JavaScript', 'Langages'),
                   ('Python', 'Langages'),
                   ('Communication', 'Soft Skills')");
        
        echo "✅ Données de test insérées<br>";
    } else {
        echo "✅ Données existent déjà<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>🎉 Installation terminée !</h2>";
echo "<p><a href='http://localhost:8000' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>🚀 ACCÉDER À FINDIN</a></p>";
echo "<p><strong>Identifiants:</strong> admin@findin.com / (mot de passe quelconque)</p>";
?>
