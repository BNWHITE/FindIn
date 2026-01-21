# 🏗️ Architecture FindIN

## 📐 Vue d'ensemble

FindIN est une application web PHP suivant le pattern **MVC (Model-View-Controller)** avec une architecture modulaire et extensible.

```
┌─────────────────────────────────────────────────────┐
│                    UTILISATEUR                       │
└───────────────────┬─────────────────────────────────┘
                    │ HTTP Request
                    ▼
┌─────────────────────────────────────────────────────┐
│                  .htaccess                           │
│          (URL Rewriting & Security)                  │
└───────────────────┬─────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────────┐
│                  index.php                           │
│              (Front Controller)                      │
│  ┌───────────────────────────────────────────────┐  │
│  │  1. Initialisation session                    │  │
│  │  2. Chargement configuration                  │  │
│  │  3. Routing des URLs                          │  │
│  │  4. Gestion des erreurs                       │  │
│  └───────────────────────────────────────────────┘  │
└───────────────────┬─────────────────────────────────┘
                    │
        ┌───────────┴───────────┐
        │                       │
        ▼                       ▼
┌──────────────┐       ┌──────────────┐
│ CONTROLLERS  │       │   MODELS     │
│              │◄─────►│              │
│ • Auth       │       │ • User       │
│ • Dashboard  │       │ • Competence │
│ • Search     │       │ • Project    │
│ • Profile    │       │ • Department │
│ • Admin      │       │ • Database   │
└──────┬───────┘       └──────┬───────┘
       │                      │
       │                      ▼
       │              ┌──────────────┐
       │              │   DATABASE   │
       │              │              │
       │              │ MySQL/MariaDB│
       │              └──────────────┘
       │
       ▼
┌──────────────┐
│    VIEWS     │
│              │
│ • Layouts    │
│ • Auth       │
│ • Dashboard  │
│ • Components │
└──────────────┘
```

---

## 📁 Structure des dossiers

```
FindIn/
├── 📂 config/              # Configuration de l'application
│   ├── database.php        # Configuration base de données
│   └── google_oauth.php    # Configuration OAuth (optionnel)
│
├── 📂 controllers/         # Contrôleurs MVC
│   ├── AuthController.php          # Authentification
│   ├── DashboardController.php     # Tableau de bord
│   ├── SearchController.php        # Recherche
│   ├── ProfileController.php       # Gestion profil
│   ├── AdminController.php         # Administration
│   ├── HomeController.php          # Page d'accueil
│   └── BaseController.php          # Contrôleur de base
│
├── 📂 models/              # Modèles de données
│   ├── Database.php        # Singleton PDO
│   ├── User.php            # Modèle utilisateur
│   ├── Competence.php      # Modèle compétence
│   ├── Project.php         # Modèle projet
│   ├── Department.php      # Modèle département
│   └── Validation.php      # Validations
│
├── 📂 views/               # Vues et templates
│   ├── layouts/            # Templates de base
│   │   ├── header.php      # En-tête
│   │   └── footer.php      # Pied de page
│   ├── auth/               # Pages authentification
│   │   ├── login.php
│   │   └── register.php
│   ├── dashboard/          # Pages tableau de bord
│   │   ├── index.php
│   │   ├── _sidebar.php
│   │   └── bilan.php
│   ├── index.php           # Page d'accueil
│   └── ...autres vues
│
├── 📂 public/ (ou assets/)  # Ressources publiques
│   ├── css/                 # Feuilles de style
│   │   ├── style.css        # Styles principaux
│   │   └── dashboard.css    # Styles dashboard
│   ├── js/                  # Scripts JavaScript
│   │   └── main.js          # Scripts principaux
│   ├── images/              # Images
│   └── uploads/             # Fichiers uploadés
│
├── 📂 database/            # Scripts base de données
│   ├── migrations/         # Migrations
│   ├── seeds/              # Données de test
│   ├── backups/            # Sauvegardes
│   └── schema.sql          # Schéma complet
│
├── 📂 src/                 # Code source avancé
│   ├── Helpers/            # Fonctions utilitaires
│   ├── Services/           # Services métier
│   └── Middleware/         # Middlewares
│
├── 📂 lib/                 # Bibliothèques
│   ├── cv_parser.php       # Parser de CV
│   └── upload_utils.php    # Utilitaires upload
│
├── 📂 tests/               # Tests automatisés
│   ├── Unit/               # Tests unitaires
│   └── Feature/            # Tests fonctionnels
│
├── 📂 docs/                # Documentation
│   ├── technical/          # Docs techniques
│   ├── guides/             # Guides utilisateur
│   └── api/                # Documentation API
│
├── .htaccess               # Configuration Apache
├── index.php               # Point d'entrée
├── composer.json           # Dépendances PHP
└── README.md               # Documentation principale
```

---

## 🎯 Pattern MVC

### **Model (Modèle)**
Responsable de la logique métier et de l'accès aux données.

```php
// models/User.php
class User {
    public function getAllUsers() {
        // Logique d'accès aux données
    }
    
    public function createUser($data) {
        // Validation et insertion
    }
}
```

### **View (Vue)**
Présentation des données à l'utilisateur.

```php
// views/dashboard/index.php
<h1>Tableau de bord</h1>
<p>Bienvenue, <?= htmlspecialchars($user_name) ?></p>
```

### **Controller (Contrôleur)**
Gère la logique de l'application et fait le lien entre Model et View.

```php
// controllers/DashboardController.php
class DashboardController {
    public function index() {
        $user = new User();
        $data = $user->getUserById($_SESSION['user_id']);
        require 'views/dashboard/index.php';
    }
}
```

---

## 🔄 Flux de traitement d'une requête

```
1. Utilisateur demande : http://findin.local/dashboard
                         ↓
2. .htaccess redirige vers index.php
                         ↓
3. index.php analyse l'URL : /dashboard
                         ↓
4. Routing : switch($path) { case 'dashboard': ... }
                         ↓
5. Vérification session : if(!isset($_SESSION['user_id']))
                         ↓
6. Instanciation contrôleur : new DashboardController()
                         ↓
7. Appel méthode : $dashboard->index()
                         ↓
8. Contrôleur appelle Model : User->getUserById()
                         ↓
9. Model interroge la DB : SELECT * FROM users...
                         ↓
10. Model retourne les données au Contrôleur
                         ↓
11. Contrôleur prépare les données pour la Vue
                         ↓
12. Inclusion de la Vue : require 'views/dashboard/index.php'
                         ↓
13. Vue génère le HTML avec les données
                         ↓
14. Réponse HTTP envoyée au client
```

---

## 🔐 Système d'authentification

### Sessions PHP
```php
// Démarrage session
session_start();

// Connexion
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];

// Vérification
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers login
}

// Déconnexion
session_destroy();
```

### Hashage des mots de passe
```php
// Création
$hash = password_hash($password, PASSWORD_DEFAULT);

// Vérification
if (password_verify($input, $hash)) {
    // Connexion réussie
}
```

---

## 💾 Couche d'accès aux données

### Singleton Database
```php
class Database {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
            self::$instance = new PDO($dsn, DB_USER, DB_PASS);
        }
        return self::$instance;
    }
    
    public static function query($sql, $params = []) {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
```

### Utilisation
```php
// Requête simple
$users = Database::query('SELECT * FROM users')->fetchAll();

// Requête préparée
$user = Database::query(
    'SELECT * FROM users WHERE email = ?',
    [$email]
)->fetch();
```

---

## 🎨 Système de templates

### Layout principal
```php
// views/layouts/header.php
<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'FindIN' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
```

### Vue avec layout
```php
// views/dashboard/index.php
<?php
$title = 'Dashboard';
include 'views/layouts/header.php';
?>

<div class="content">
    <!-- Contenu de la page -->
</div>

<?php include 'views/layouts/footer.php'; ?>
```

---

## 🛡️ Sécurité

### Protection XSS
```php
// Toujours échapper les données utilisateur
<?= htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8') ?>
```

### Protection CSRF
```php
// Génération token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Vérification
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token invalide');
}
```

### Protection SQL Injection
```php
// ✅ CORRECT : Requêtes préparées
$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);

// ❌ INCORRECT : Concaténation
$sql = "SELECT * FROM users WHERE id = " . $user_id;
```

---

## 📊 Schéma de la base de données

Voir [DATABASE.md](DATABASE.md) pour le schéma détaillé.

Tables principales :
- `users` - Utilisateurs
- `competences` - Compétences
- `competences_utilisateurs` - Liaison utilisateurs ↔ compétences
- `projets` - Projets
- `departements` - Départements
- `categories_competences` - Catégories

---

## 🚀 Extensibilité

### Ajout d'un nouveau module

**1. Créer le modèle** :
```php
// models/NewModule.php
class NewModule {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
}
```

**2. Créer le contrôleur** :
```php
// controllers/NewModuleController.php
class NewModuleController {
    public function index() {
        // Logique
    }
}
```

**3. Créer la vue** :
```php
// views/newmodule/index.php
```

**4. Ajouter la route** :
```php
// index.php
case 'newmodule':
    require_once 'controllers/NewModuleController.php';
    $ctrl = new NewModuleController();
    $ctrl->index();
    exit;
```

---

## 📱 Responsive Design

L'application utilise CSS moderne avec :
- **Flexbox** pour les layouts
- **Grid CSS** pour les grilles
- **Media queries** pour le responsive
- **Mobile-first** approach

Breakpoints :
- Mobile : < 768px
- Tablet : 768px - 1024px
- Desktop : > 1024px

---

## 🔧 Technologies utilisées

| Technologie | Version | Utilisation |
|------------|---------|-------------|
| PHP | 8.0+ | Backend |
| MySQL/MariaDB | 5.7+ / 10.4+ | Base de données |
| JavaScript (Vanilla) | ES6+ | Interactivité |
| CSS3 | - | Styles |
| Apache | 2.4+ | Serveur web |
| Font Awesome | 6.4.0 | Icônes |
| Inter Font | - | Typographie |

---

## 🎯 Bonnes pratiques appliquées

✅ **Séparation des responsabilités** (MVC)  
✅ **Requêtes préparées** (Protection SQL Injection)  
✅ **Validation des entrées** utilisateur  
✅ **Échappement des sorties** (Protection XSS)  
✅ **Sessions sécurisées**  
✅ **Mots de passe hashés** (bcrypt)  
✅ **Code commenté et documenté**  
✅ **Structure modulaire**

---

## 📈 Performance

### Optimisations appliquées
- ✅ Singleton pour la connexion DB
- ✅ Requêtes préparées (mise en cache)
- ✅ Compression gzip (.htaccess)
- ✅ Cache navigateur pour assets
- ✅ Lazy loading des images

### Recommandations futures
- [ ] Système de cache (Redis/Memcached)
- [ ] CDN pour les assets
- [ ] Optimisation des requêtes SQL
- [ ] Minification CSS/JS
- [ ] Service Workers (PWA)
