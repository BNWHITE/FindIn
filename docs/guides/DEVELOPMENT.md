# 💻 Guide de développement FindIN

## 🎯 Configuration de l'environnement de développement

### Prérequis installés
- ✅ PHP 8.0+
- ✅ MySQL/MariaDB
- ✅ XAMPP ou LAMP/MAMP
- ✅ Git
- ✅ VS Code (recommandé) ou PHPStorm

### Extensions VS Code recommandées
```json
{
  "recommendations": [
    "bmewburn.vscode-intelephense-client",  // IntelliSense PHP
    "xdebug.php-debug",                      // Débogage
    "onecentlin.laravel-blade",              // Syntaxe
    "mikestead.dotenv",                      // .env support
    "esbenp.prettier-vscode"                 // Formatage
  ]
}
```

---

## 📁 Workflow de développement

### 1. Cloner le projet
```bash
cd /Applications/XAMPP/htdocs
git clone https://github.com/votre-repo/FindIn.git findin
cd findin
```

### 2. Créer une branche de fonctionnalité
```bash
git checkout -b feature/nom-de-la-fonctionnalite
```

### 3. Développer la fonctionnalité
Suivre le pattern MVC (voir ci-dessous)

### 4. Tester localement
```bash
# Lancer le serveur
http://findin.local

# Tester la fonctionnalité
```

### 5. Commit et Push
```bash
git add .
git commit -m "feat: description de la fonctionnalité"
git push origin feature/nom-de-la-fonctionnalite
```

### 6. Créer une Pull Request
Sur GitHub, créer une PR vers la branche `main`

---

## 🏗️ Créer une nouvelle fonctionnalité

### Exemple : Module "Formations"

#### Étape 1 : Créer la table
```sql
-- database/migrations/create_formations_table.sql
CREATE TABLE formations (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    duree INT COMMENT 'Durée en heures',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

Exécuter :
```bash
mysql -u root -p gestion_competences < database/migrations/create_formations_table.sql
```

#### Étape 2 : Créer le modèle
```php
<?php
// models/Formation.php

require_once 'models/Database.php';

class Formation {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupérer toutes les formations
     * @return array
     */
    public function getAll() {
        $sql = "SELECT * FROM formations ORDER BY titre";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupérer une formation par ID
     * @param string $id
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT * FROM formations WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Créer une nouvelle formation
     * @param array $data
     * @return bool
     */
    public function create($data) {
        $sql = "INSERT INTO formations (id, titre, description, duree) 
                VALUES (UUID(), ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['titre'],
            $data['description'],
            $data['duree']
        ]);
    }
    
    /**
     * Mettre à jour une formation
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE formations 
                SET titre = ?, description = ?, duree = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['titre'],
            $data['description'],
            $data['duree'],
            $id
        ]);
    }
    
    /**
     * Supprimer une formation
     * @param string $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM formations WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
```

#### Étape 3 : Créer le contrôleur
```php
<?php
// controllers/FormationController.php

require_once 'models/Formation.php';

class FormationController {
    private $formationModel;
    
    public function __construct() {
        $this->formationModel = new Formation();
    }
    
    /**
     * Page liste des formations
     */
    public function index() {
        // Vérifier l'authentification
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Récupérer les données
        $formations = $this->formationModel->getAll();
        
        // Charger la vue
        require_once 'views/formations/index.php';
    }
    
    /**
     * Afficher une formation
     */
    public function show($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $formation = $this->formationModel->getById($id);
        
        if (!$formation) {
            http_response_code(404);
            require_once 'views/errors/404.php';
            return;
        }
        
        require_once 'views/formations/show.php';
    }
    
    /**
     * Créer une formation
     */
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // GET : Afficher le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require_once 'views/formations/create.php';
            return;
        }
        
        // POST : Traiter le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation
            $errors = [];
            
            if (empty($_POST['titre'])) {
                $errors[] = 'Le titre est requis';
            }
            
            if (empty($_POST['duree']) || !is_numeric($_POST['duree'])) {
                $errors[] = 'La durée doit être un nombre';
            }
            
            // Si erreurs, réafficher le formulaire
            if (!empty($errors)) {
                require_once 'views/formations/create.php';
                return;
            }
            
            // Créer la formation
            $data = [
                'titre' => trim($_POST['titre']),
                'description' => trim($_POST['description'] ?? ''),
                'duree' => (int)$_POST['duree']
            ];
            
            if ($this->formationModel->create($data)) {
                $_SESSION['success'] = 'Formation créée avec succès';
                header('Location: /formations');
            } else {
                $_SESSION['error'] = 'Erreur lors de la création';
                require_once 'views/formations/create.php';
            }
        }
    }
}
```

#### Étape 4 : Créer les vues
```php
<?php
// views/formations/index.php
$title = 'Formations';
include 'views/layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Formations disponibles</h1>
        <a href="/formations/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle formation
        </a>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <div class="formations-grid">
        <?php foreach ($formations as $formation): ?>
            <div class="formation-card">
                <h3><?= htmlspecialchars($formation['titre']) ?></h3>
                <p><?= htmlspecialchars($formation['description']) ?></p>
                <div class="formation-meta">
                    <span><i class="fas fa-clock"></i> <?= $formation['duree'] ?>h</span>
                </div>
                <a href="/formations/<?= $formation['id'] ?>" class="btn btn-sm">
                    Voir détails
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
```

```php
<?php
// views/formations/create.php
$title = 'Nouvelle formation';
include 'views/layouts/header.php';
?>

<div class="container">
    <h1>Créer une formation</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="/formations/create" class="form">
        <div class="form-group">
            <label for="titre">Titre *</label>
            <input 
                type="text" 
                id="titre" 
                name="titre" 
                value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                required
            >
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea 
                id="description" 
                name="description" 
                rows="5"
            ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="duree">Durée (heures) *</label>
            <input 
                type="number" 
                id="duree" 
                name="duree" 
                value="<?= htmlspecialchars($_POST['duree'] ?? '') ?>"
                min="1"
                required
            >
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="/formations" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php include 'views/layouts/footer.php'; ?>
```

#### Étape 5 : Ajouter les routes
```php
// index.php - Ajouter dans le switch

case 'formations':
    require_once 'controllers/FormationController.php';
    $ctrl = new FormationController();
    $ctrl->index();
    exit;

case (preg_match('/^formations\/([a-f0-9-]+)$/', $path, $matches) ? true : false):
    require_once 'controllers/FormationController.php';
    $ctrl = new FormationController();
    $ctrl->show($matches[1]);
    exit;

case 'formations/create':
    require_once 'controllers/FormationController.php';
    $ctrl = new FormationController();
    $ctrl->create();
    exit;
```

#### Étape 6 : Ajouter les styles
```css
/* assets/css/formations.css */
.formations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.formation-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.formation-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.formation-card h3 {
    margin-bottom: 0.5rem;
    color: #2563eb;
}

.formation-meta {
    display: flex;
    gap: 1rem;
    margin: 1rem 0;
    color: #64748b;
    font-size: 0.875rem;
}
```

---

## 🧪 Tests

### Tests manuels
```bash
# 1. Tester la liste
http://findin.local/formations

# 2. Tester la création
http://findin.local/formations/create

# 3. Tester l'affichage
http://findin.local/formations/UUID
```

### Tests de validation
```php
// Tester avec des données invalides
- Titre vide
- Durée négative
- Durée non numérique
```

---

## 🐛 Débogage

### Activer les erreurs PHP
```php
// config/database.php
define('DEBUG_MODE', true);

// index.php
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
```

### Logs personnalisés
```php
function debug_log($message, $data = null) {
    $log = date('Y-m-d H:i:s') . ' - ' . $message;
    if ($data) {
        $log .= "\n" . print_r($data, true);
    }
    error_log($log . "\n", 3, __DIR__ . '/debug.log');
}

// Utilisation
debug_log('User login attempt', ['email' => $email]);
```

### Inspecteur de requêtes SQL
```php
// models/Database.php - Ajouter cette méthode
public static function debug($sql, $params = []) {
    if (DEBUG_MODE) {
        echo "<pre>";
        echo "SQL: " . $sql . "\n";
        echo "Params: " . print_r($params, true);
        echo "</pre>";
    }
}
```

---

## 📝 Conventions de code

### Nommage
```php
// Classes : PascalCase
class FormationController {}

// Méthodes : camelCase
public function getAllFormations() {}

// Variables : snake_case
$user_id = $_SESSION['user_id'];

// Constantes : UPPER_SNAKE_CASE
define('MAX_UPLOAD_SIZE', 5242880);
```

### Commentaires
```php
/**
 * Description de la méthode
 * 
 * @param string $id ID de l'utilisateur
 * @param array $data Données à mettre à jour
 * @return bool True si succès
 */
public function updateUser($id, $data) {
    // Code...
}
```

### Structure des fichiers
```php
<?php
// Toujours commencer par <?php (sans espace)

// 1. Requires
require_once 'models/Database.php';

// 2. Classe
class MonControler {
    // 3. Propriétés
    private $model;
    
    // 4. Constructeur
    public function __construct() {}
    
    // 5. Méthodes publiques
    public function index() {}
    
    // 6. Méthodes privées
    private function helper() {}
}

// Pas de ?> à la fin
```

---

## 🔄 Git Workflow

### Branches
```bash
main            # Production
develop         # Développement
feature/*       # Nouvelles fonctionnalités
bugfix/*        # Corrections de bugs
hotfix/*        # Corrections urgentes
```

### Commits
```bash
# Format : type: description

# Types :
feat:     # Nouvelle fonctionnalité
fix:      # Correction de bug
docs:     # Documentation
style:    # Formatage
refactor: # Refactoring
test:     # Tests
chore:    # Maintenance

# Exemples :
git commit -m "feat: ajout module formations"
git commit -m "fix: correction validation email"
git commit -m "docs: mise à jour guide développement"
```

---

## 📦 Déploiement

### Checklist pré-déploiement
- [ ] Tests passés
- [ ] Debug mode désactivé
- [ ] Credentials production configurés
- [ ] Migrations exécutées
- [ ] Assets minifiés
- [ ] Sauvegarde DB effectuée

### Commandes de déploiement
```bash
# 1. Pull dernière version
git pull origin main

# 2. Exécuter migrations
mysql -u root -p gestion_competences < database/migrations/latest.sql

# 3. Vider les caches
rm -rf cache/*

# 4. Redémarrer Apache
sudo service apache2 restart
```

---

## 🎓 Ressources utiles

- [PHP Manual](https://www.php.net/manual/fr/)
- [PDO Documentation](https://www.php.net/manual/fr/book.pdo.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [MDN Web Docs](https://developer.mozilla.org/)

---

## 💡 Astuces

### Raccourcis VS Code
- `Ctrl/Cmd + P` : Rechercher fichier
- `Ctrl/Cmd + Shift + F` : Rechercher dans tous les fichiers
- `F12` : Aller à la définition
- `Alt + Shift + F` : Formater le code

### Outils de développement
```bash
# Vérifier syntaxe PHP
php -l fichier.php

# Lancer serveur PHP intégré
php -S localhost:8000

# Connexion MySQL
mysql -u root -p
```

Bonne chance dans votre développement ! 🚀
