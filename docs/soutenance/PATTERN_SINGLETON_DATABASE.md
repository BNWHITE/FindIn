# Le Pattern Singleton appliqué à la gestion de base de données

**Sujet de soutenance** : Implémentation du Pattern Singleton pour la connexion à la base de données dans FindIN

---

## 1. Introduction

### Contexte du projet FindIN
FindIN est une plateforme web de gestion des compétences en entreprise développée en PHP. Elle permet aux employés de déclarer leurs compétences, aux managers de les valider, et aux RH de rechercher des profils qualifiés pour des projets.

Le projet s'appuie sur une architecture MVC avec une base de données MySQL (ou SQLite en développement). L'accès aux données est centralisé via la classe `Database.php` qui implémente le pattern Singleton.

### Intérêt du sujet
La gestion des connexions à une base de données est un enjeu critique dans toute application web. Une mauvaise gestion peut entraîner :
- Des problèmes de performance (multiplication des connexions)
- Des fuites de ressources mémoire
- Des incohérences dans les transactions
- Une complexité accrue du code

Le pattern Singleton résout ces problématiques en garantissant **une seule instance de connexion** réutilisée dans toute l'application.

---

## 2. Le Pattern Singleton : Concept fondamental

### Origine et définition
Le pattern Singleton fait partie des **Design Patterns GoF** (Gang of Four, 1994). Il appartient à la catégorie des **patterns de création**.

**Définition** : Le Singleton garantit qu'une classe ne possède qu'une seule instance et fournit un point d'accès global à cette instance.

### Principe de fonctionnement

```
┌─────────────────────────────────────┐
│         Classe Singleton            │
├─────────────────────────────────────┤
│ - instance (statique, privée)       │
│ - __construct() (privé)             │
├─────────────────────────────────────┤
│ + getInstance() (statique, public)  │
└─────────────────────────────────────┘
```

**Les 3 piliers du Singleton** :
1. **Constructeur privé** → Empêche l'instanciation directe (`new`)
2. **Propriété statique privée** → Stocke l'instance unique
3. **Méthode statique publique** → Fournit l'accès à l'instance

### Pourquoi l'utiliser pour une base de données ?
- ✅ **Économie de ressources** : Une seule connexion PDO réutilisée
- ✅ **Cohérence** : Tous les modèles partagent la même connexion
- ✅ **Contrôle** : Point d'accès unique et centralisé
- ✅ **Performance** : Pas de reconnexion à chaque requête

---

## 3. Application concrète dans FindIN

### Rôle du composant `Database.php`
La classe `Database` (fichier `src/Models/Database.php`) est le **point d'entrée unique** pour toutes les interactions avec la base de données. Elle :
1. Initialise la connexion PDO au premier appel
2. Stocke cette connexion en mémoire
3. La retourne pour toutes les requêtes suivantes
4. Gère la compatibilité MySQL/SQLite
5. Crée automatiquement les tables si nécessaire

### Interaction avec les autres composants

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│   User.php   │     │Competence.php│     │ Project.php  │
│   (Model)    │     │   (Model)    │     │   (Model)    │
└──────┬───────┘     └──────┬───────┘     └──────┬───────┘
       │                    │                    │
       │ getInstance()      │ getInstance()      │ getInstance()
       └────────────────────┼────────────────────┘
                            ▼
                    ┌──────────────┐
                    │ Database.php │ ◄─── Singleton
                    │  (Instance   │
                    │   unique)    │
                    └──────┬───────┘
                           │
                           ▼
                    ┌──────────────┐
                    │   MySQL/     │
                    │   SQLite     │
                    └──────────────┘
```

**Tous les modèles** (`User`, `Competence`, `Project`, etc.) utilisent la même instance de connexion via `Database::getInstance()`.

---

## 4. Exemple d'implémentation dans FindIN

### Structure simplifiée de la classe Database

```php
class Database {
    // Propriété statique privée stockant l'instance unique
    private static $instance = null;
    
    // Connexion PDO
    private $connection;

    // Constructeur PRIVÉ → empêche new Database()
    private function __construct() {
        // Configuration selon DB_TYPE (mysql ou sqlite)
        if (DB_TYPE === 'mysql') {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);
        } else {
            $this->connection = new PDO("sqlite:" . DB_PATH);
        }
        
        // Configuration PDO
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Création automatique des tables
        $this->createMinimalTables();
    }

    // Méthode statique publique → SEUL point d'accès
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();  // Création UNE SEULE FOIS
        }
        return self::$instance->connection;  // Retourne PDO
    }
    
    // Helper pour exécuter des requêtes rapidement
    public static function query($sql, $params = []) {
        $db = self::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
```

### Utilisation dans un modèle (exemple User.php)

```php
class User {
    private $db;

    public function __construct() {
        // Récupération de l'instance unique de connexion
        $this->db = Database::getInstance();
    }

    public function getUserById($id) {
        // Utilisation de la connexion PDO partagée
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function getAllUsers() {
        // Alternative avec le helper statique
        return Database::query("SELECT * FROM utilisateurs ORDER BY nom, prenom")->fetchAll();
    }
}
```

### Utilisation dans un contrôleur

```php
class ProfileController extends BaseController {
    private $userModel;

    public function __construct() {
        // Chaque modèle utilise la MÊME connexion Database
        $this->userModel = new User();
        $this->competenceModel = new Competence();
    }

    public function view() {
        $userId = $_SESSION['user_id'];
        
        // Ces deux requêtes utilisent la même connexion PDO
        $user = $this->userModel->getUserById($userId);
        $competences = $this->competenceModel->getUserCompetences($userId);
        
        $this->view('profile/view', ['user' => $user, 'competences' => $competences]);
    }
}
```

---

## 5. Intérêt technique et bonnes pratiques

### Avantages constatés dans FindIN

| Aspect | Bénéfice |
|--------|----------|
| **Performance** | Une seule connexion TCP vers MySQL pour toute la requête HTTP |
| **Mémoire** | Pas de duplication de l'objet PDO (économie ~5-10 MB par instance) |
| **Maintenance** | Configuration centralisée (changement DB_TYPE en un seul endroit) |
| **Sécurité** | Requêtes préparées systématiques via la même connexion |
| **Évolutivité** | Ajout facile de fonctionnalités (migrations auto, logs, cache) |

### Bonnes pratiques respectées

✅ **Lazy initialization** : La connexion n'est créée qu'au premier appel (pas au chargement de la classe)

✅ **Gestion d'erreurs** : Try/catch dans le constructeur avec messages explicites

✅ **Configuration flexible** : Support MySQL et SQLite via constante `DB_TYPE`

✅ **Helper method** : `Database::query()` simplifie l'écriture des requêtes simples

✅ **Auto-migration** : `createMinimalTables()` garantit la structure minimale de la BDD

### Points d'attention (limites du pattern)

⚠️ **Tests unitaires** : Singleton rend les tests plus complexes (état global partagé)

⚠️ **Multithreading** : En PHP CLI avec threads, nécessiterait une synchronisation

⚠️ **Couplage** : Les modèles sont fortement couplés à la classe Database

---

## 6. Conclusion pour la soutenance orale

### Points clés à retenir

1. **Le pattern Singleton garantit une instance unique** d'une classe dans toute l'application

2. **Dans FindIN, il gère la connexion à la base de données** via la classe `Database.php`

3. **Les 3 éléments essentiels** :
   - Constructeur privé
   - Propriété statique privée
   - Méthode `getInstance()` statique publique

4. **Tous les modèles** (User, Competence, Project...) partagent la même connexion PDO

5. **Bénéfices concrets** : Performance, économie mémoire, cohérence des données, maintenance simplifiée

### Message de conclusion

> "Le pattern Singleton dans FindIN illustre comment un pattern de conception classique résout un problème réel : gérer efficacement les ressources de connexion à une base de données. C'est un exemple de **bon design orienté objet** qui privilégie la réutilisabilité, la performance et la maintenabilité du code."

### Ouverture possible

Ce pattern pourrait évoluer vers une injection de dépendances pour améliorer la testabilité, mais il reste adapté à la taille actuelle du projet FindIN et à ses contraintes de simplicité (projet académique sans framework).

---

**Durée estimée de présentation** : 8-10 minutes  
**Fichiers à montrer pendant la soutenance** : `src/Models/Database.php`, `src/Models/User.php`
