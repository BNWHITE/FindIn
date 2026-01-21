# 🎬 Script de Démonstration - Soutenance FindIN

**Durée estimée** : 15-20 minutes  
**Date** : Janvier 2026  
**Projet** : FindIN - Plateforme de Gestion des Compétences

---

## 📋 Checklist Pré-Démonstration

### Environnement
- [ ] XAMPP démarré (Apache + MySQL)
- [ ] Base `gestion_competences` accessible
- [ ] Table `user_competences` créée ✅
- [ ] Navigateur ouvert sur http://localhost:8000 ou http://findin.local

### Comptes de test prêts
| Email | Mot de passe | Rôle | Usage |
|-------|--------------|------|-------|
| `admin@findin.fr` | `admin123` | Admin | Gestion complète |
| `test@findin.fr` | `test123` | Employé | Parcours utilisateur |

### Vérification rapide
```bash
# Tester la connexion MySQL
mysql -u root -e "USE gestion_competences; SHOW TABLES;"

# Démarrer le serveur PHP (si pas d'Apache)
cd /path/to/FindIn
php -S localhost:8000 -t public
```

---

## 🎯 Partie 1 : Présentation de l'Application (3 min)

### 1.1 Page d'Accueil
**Action** : Ouvrir http://localhost:8000

**Points à mentionner** :
- "FindIN est une plateforme de gestion des compétences en entreprise"
- "L'objectif : identifier et valoriser les talents cachés des collaborateurs"
- "Architecture MVC sans framework, développée from scratch en PHP"

### 1.2 Interface de Connexion
**Action** : Cliquer sur "Connexion"

**Points techniques** :
- "Formulaire sécurisé avec protection CSRF"
- "Mots de passe hashés avec bcrypt (jamais stockés en clair)"
- "Sessions PHP sécurisées"

---

## 🔐 Partie 2 : Démonstration Utilisateur (5 min)

### 2.1 Connexion Employé
**Action** : Se connecter avec `test@findin.fr` / `test123`

**Expliquer** :
```php
// Code de vérification (AuthController.php)
$user = $this->userModel->login($email, $password);
// Utilise password_verify() pour comparer les hashs
```

### 2.2 Dashboard Personnel
**Action** : Montrer le tableau de bord

**Points à montrer** :
- Menu latéral avec les 7 sections (Accueil, Projets, Réunions, Documents, etc.)
- Affichage des compétences personnelles
- Statistiques

**Expliquer le pattern MVC** :
```
Requête → index.php (Front Controller) → DashboardController → View
```

### 2.3 Gestion des Compétences
**Action** : Ajouter une nouvelle compétence

**Scénario** :
1. Aller dans "Profil" ou "Compétences"
2. Cliquer "Ajouter une compétence"
3. Sélectionner "Python" niveau 3
4. Valider

**Code associé** (Competence.php) :
```php
public function addUserCompetence($userId, $competenceId, $niveau) {
    $sql = "INSERT INTO user_competences (user_id, competence_id, niveau_declare) 
            VALUES (:user_id, :competence_id, :niveau)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':user_id' => $userId,
        ':competence_id' => $competenceId,
        ':niveau' => $niveau
    ]);
}
```

### 2.4 Recherche de Collaborateurs
**Action** : Utiliser la recherche

**Scénario** :
1. Aller dans "Recherche"
2. Chercher "PHP" niveau minimum 3
3. Montrer les résultats filtrés

---

## ⚙️ Partie 3 : Points Techniques Clés (7 min)

### 3.1 Pattern Singleton (Database.php)
**Action** : Ouvrir le fichier `src/Models/Database.php` dans l'IDE

**Script oral** :
> "Le pattern Singleton garantit une seule instance de connexion à la base de données. 
> Cela évite d'ouvrir plusieurs connexions coûteuses et maintient la cohérence."

**Code à montrer** :
```php
class Database {
    private static ?PDO $instance = null;
    
    private function __construct() {} // Constructeur privé
    
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::$instance = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER, DB_PASS
            );
        }
        return self::$instance;
    }
}
```

**Points à souligner** :
- Constructeur privé → empêche `new Database()`
- Variable statique → partagée entre toutes les instances
- Lazy loading → connexion créée uniquement au premier appel

### 3.2 Architecture MVC
**Action** : Montrer le flux d'une requête

**Schéma à dessiner/expliquer** :
```
[Navigateur] 
    ↓ GET /dashboard
[public/index.php] - Front Controller
    ↓ switch($path)
[DashboardController]
    ↓ $this->view('dashboard/index', $data)
[Views/dashboard/index.php]
    ↓ HTML rendu
[Navigateur]
```

**Code du routeur** (index.php) :
```php
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

switch ($path) {
    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        exit;
    // ...
}
```

### 3.3 Requêtes Préparées (Sécurité SQL)
**Action** : Montrer un exemple de requête sécurisée

**Script oral** :
> "Toutes nos requêtes utilisent des prepared statements PDO. 
> Cela protège contre les injections SQL car les paramètres sont échappés automatiquement."

**Exemple de code** :
```php
// ❌ DANGEREUX (ne jamais faire)
$sql = "SELECT * FROM users WHERE email = '$email'";

// ✅ SÉCURISÉ (notre approche)
$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $db->prepare($sql);
$stmt->execute([':email' => $email]);
```

### 3.4 Système Dual-Table
**Action** : Montrer la gestion des deux schémas

**Script oral** :
> "Le projet maintient deux schémas : legacy (français) et moderne (anglais).
> Cela permet la rétrocompatibilité pendant la migration progressive."

**Tables concernées** :
| Legacy (Français) | Moderne (Anglais) |
|-------------------|-------------------|
| `utilisateurs` | `users` |
| `competences_utilisateurs` | `user_competences` |

---

## 👨‍💼 Partie 4 : Rôle Administrateur (3 min)

### 4.1 Reconnexion Admin
**Action** : Se déconnecter et reconnecter avec `admin@findin.fr` / `admin123`

### 4.2 Gestion des Utilisateurs
**Action** : Montrer le panneau d'administration

**Points à montrer** :
- Liste de tous les utilisateurs
- Modification des rôles (Employé → Manager)
- Gestion des compétences globales

### 4.3 Statistiques
**Action** : Montrer les statistiques globales

**Requête SQL associée** :
```sql
SELECT 
    c.nom AS competence,
    COUNT(DISTINCT uc.user_id) AS nb_utilisateurs
FROM competences c
LEFT JOIN user_competences uc ON c.id = uc.competence_id
GROUP BY c.id
ORDER BY nb_utilisateurs DESC;
```

---

## ❓ Partie 5 : Questions Anticipées

### Q1 : "Pourquoi ne pas utiliser un framework comme Laravel ?"
**Réponse** :
> "L'objectif pédagogique était de comprendre les mécanismes fondamentaux du MVC.
> En codant from scratch, on maîtrise chaque composant : routing, ORM, templating.
> En production, un framework offrirait plus de fonctionnalités out-of-the-box."

### Q2 : "Comment gérez-vous les sessions ?"
**Réponse** :
```php
// Initialisation sécurisée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Stockage après authentification
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_role'] = $user['role'];

// Vérification sur pages protégées
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
```

### Q3 : "Expliquez le Singleton en 30 secondes"
**Réponse** :
> "Le Singleton est un design pattern qui garantit qu'une classe n'a qu'une seule instance.
> On l'utilise ici pour la connexion base de données : une seule connexion PDO partagée
> par toute l'application, ce qui économise les ressources et maintient la cohérence."

### Q4 : "Comment testeriez-vous l'application ?"
**Réponse** :
> "Tests unitaires pour les Models (PHPUnit), tests fonctionnels pour les Controllers,
> et tests d'intégration pour vérifier les flux complets utilisateur."

### Q5 : "Quelles améliorations envisagez-vous ?"
**Réponse** :
- API REST pour une application mobile
- Cache Redis pour les performances
- Tests automatisés CI/CD
- Migration vers un framework (Symfony/Laravel) pour production

---

## 📊 Résumé des Points Techniques

| Concept | Implémentation |
|---------|----------------|
| **Design Pattern** | Singleton (Database.php) |
| **Architecture** | MVC (Model-View-Controller) |
| **Sécurité SQL** | Requêtes préparées PDO |
| **Authentification** | bcrypt + sessions |
| **Routing** | Front Controller (switch/case) |
| **Base de données** | MySQL via XAMPP |

---

## ⏱️ Timing Suggéré

| Section | Durée |
|---------|-------|
| Présentation application | 3 min |
| Démo utilisateur | 5 min |
| Points techniques | 7 min |
| Rôle admin | 3 min |
| Questions | 5-10 min |
| **Total** | **23-28 min** |

---

## 🎯 Conseils pour le Jour J

1. **Tester tout la veille** - Connexion DB, comptes de test, navigation
2. **Avoir un backup** - Screenshots si problème technique
3. **Connaître son code** - Pouvoir expliquer n'importe quelle ligne
4. **Rester calme** - Si bug, expliquer le comportement attendu
5. **Anticiper les questions** - Préparer des réponses courtes et claires

---

**Bonne chance pour la soutenance ! 🍀**
