# FindIN - Documentation Technique
## Architecture et Implémentation

### 🏗️ Architecture Générale

FindIN suit une architecture **MVC vanille** (sans framework) basée sur:
- **Front Controller:** `public/index.php`
- **Routeur manuel:** Switch/case avec strpos() pour les wildcard routes
- **Base de données:** MySQL avec PDO

```
Client HTTP
    ↓
public/index.php (Front Controller)
    ↓
Router (parseRoute / handleRoute)
    ↓
Controller (BaseController extends)
    ↓
Models (PDO queries)
    ↓
Database (MySQL)
    ↑
Views (PHP templates)
```

### 📁 Structure du Projet

```
FindIn/
├── public/
│   └── index.php              # Front controller & router principal
├── src/
│   ├── Config/
│   │   └── database.php        # Configuration DB avec singleton PDO
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   ├── InvitationController.php
│   │   ├── ContactController.php
│   │   ├── ValidationController.php
│   │   ├── AuthController.php
│   │   └── ...
│   ├── Models/
│   │   ├── Invitation.php
│   │   ├── User.php
│   │   └── ...
│   ├── Views/
│   │   ├── auth/
│   │   │   └── accept-invitation.php
│   │   ├── dashboard/
│   │   │   ├── rh-invitations.php
│   │   │   └── manager-validations.php
│   │   ├── contact.php
│   │   ├── layouts/
│   │   │   ├── header.php
│   │   │   └── footer.php
│   │   └── ...
│   ├── Lib/
│   │   ├── EmailSender.php
│   │   └── ...
│   └── Api/
│       └── ...
├── database/
│   ├── schema.sql
│   └── ...
├── storage/
│   ├── logs/
│   │   └── php-errors.log
│   └── cache/
└── scripts/
```

### 🔄 Flux de Requête

#### 1. Client → Serveur
```
GET /dashboard/rh-invitations
    ↓
Server (localhost:8000)
    ↓
public/index.php loaded
```

#### 2. Index.php
```php
// 1. Session start
session_start();

// 2. DB connection
$db = Database::getInstance();

// 3. Parse route
$path = parseRoute();  // Enlève /FindIn/public, trim slashes

// 4. Handle route
handleRoute($path);
```

#### 3. Router Logic
```php
function handleRoute($path) {
    // Checks in order:
    
    // 1. Home
    if (empty($path)) return requireView('home/index');
    
    // 2. Auth routes (login, register, logout)
    if ($path === 'login') return requireController('AuthController', 'login');
    
    // 3. Public routes (invitation)
    if (strpos($path, 'invitation/') === 0) {
        $action = substr($path, strlen('invitation/'));
        return requireController('InvitationController', $action);
    }
    
    // 4. Dashboard routes (requires auth)
    if (strpos($path, 'dashboard/') === 0) {
        if (!isset($_SESSION['user_id'])) redirect('/login');
        
        $subPage = substr($path, strlen('dashboard/'));
        
        // Special dashboard routes
        if ($subPage === 'rh-invitations')
            return requireController('InvitationController', 'dashboard');
        if ($subPage === 'manager-validations')
            return requireController('ValidationController', 'pending');
        
        // Generic views
        return requireView('dashboard/' . $subPage);
    }
    
    // ... other routes
    
    // 5. 404
    return handle404();
}
```

#### 4. Controller Execution
```php
function requireController($controller, $action) {
    // 1. Load controller file
    require_once CONTROLLERS_DIR . '/' . $controller . '.php';
    
    // 2. Instantiate
    $instance = new $controller();
    
    // 3. Determine method
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $method = str_replace('show', '', $action);
    } else {
        $method = $action;
    }
    
    // 4. Call method
    $instance->$method();
    exit;
}
```

#### 5. Controller → View
```php
class InvitationController extends BaseController {
    public function dashboard() {
        $this->checkAuth();
        
        // Get data
        $invitations = $this->invitationModel->getAllInvitations();
        
        // Pass to view
        $data = ['invitations' => $invitations];
        $this->view('dashboard/rh-invitations', $data);
    }
}

// In BaseController
protected function view($path, $data = []) {
    extract($data);  // Create variables: $invitations, etc.
    require_once __DIR__ . '/../Views/' . $path . '.php';
}
```

### 🗄️ Modèle de Données

#### Invitation Lifecycle

```
1. RH crée invitation
   INSERT INTO invitations (token, email, prenom, nom, role, status, expires_at)
   status = 'pending'
   expires_at = NOW() + 7 days
   
2. Employee reçoit email avec token

3. Employee clique sur lien
   GET /invitation/accept?token=xxx
   Invitation trouvée avec:
     WHERE status = 'pending' AND expires_at > NOW()
   
4. Employee remplit formulaire
   POST /invitation/accept
   - Valide password (min 8 chars)
   - Hash password
   
5. Créer account utilisateur
   INSERT INTO utilisateurs (email, prenom, nom, password, role)
   
6. Mettre à jour invitation
   UPDATE invitations SET status = 'accepted', user_id = NEW_USER_ID
   
7. Session créée, redirection /dashboard
```

#### Validation Competency Lifecycle

```
1. Employee déclare compétence (future)
   INSERT INTO demandes_validation 
     (user_id, id_competence, niveau_demande, manager_id, status)
   status = 'en_attente'
   
2. Manager reçoit notification

3. Manager voit demande sur /dashboard/manager-validations

4. Manager approuve/rejette
   UPDATE demandes_validation SET status = 'approuve'|'rejete'
   
5. Employee notifié du résultat (future)
```

### 🔐 Sécurité - Détails d'Implémentation

#### Token Generation
```php
$token = bin2hex(random_bytes(32));
// Result: 64 characters hex string
// Example: "8ff3f917c5ac6a39bb636c32f8a0876853d3ff90478b6fc905aa6274a61402ff"
```

**Sécurité:**
- `random_bytes()` utilise `/dev/urandom` (cryptographiquement sûr)
- 32 bytes = 256 bits = très difficile à brute-force
- `bin2hex()` convertit en format stockable dans VARCHAR

#### Password Hashing
```php
// Création
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Vérification
if (password_verify($input_password, $password_hash)) {
    // OK
}
```

**Sécurité:**
- PASSWORD_DEFAULT = argon2id (recommandé)
- Hashing automatique + salt
- Resistir to rainbow tables

#### Session Security
```php
// Démarrage sécurisé
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification auth
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Session variables
$_SESSION['user_id']       // INT, PK de utilisateurs
$_SESSION['user_email']    // VARCHAR
$_SESSION['user_role']     // VARCHAR (employe, manager, rh)
$_SESSION['user_name']     // VARCHAR
```

#### Input Validation

**Email:**
```php
filter_var($email, FILTER_VALIDATE_EMAIL)
```

**Password:**
```php
if (strlen($password) < 8) throw error;
if ($password !== $password_confirm) throw error;
```

**SQL Injection Prevention:**
```php
// ✅ Prepared statements (safe)
$stmt = $db->prepare("SELECT * FROM invitations WHERE token = :token");
$stmt->execute([':token' => $token]);

// ❌ String concatenation (unsafe - NOT used)
// $sql = "SELECT * FROM invitations WHERE token = '$token'";
```

### 📧 Email System

#### EmailSender Class

```php
class EmailSender {
    private static $from_email = 'contact@findin.fr';
    
    public static function send($to, $subject, $html) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . self::$from_email . "\r\n";
        
        $result = mail($to, $subject, $html, $headers);
        return $result;
    }
}
```

#### Template System

```php
// Template pour invitation
private static function getInvitationEmailTemplate($prenom, $nom, $lien) {
    return <<<HTML
    <!DOCTYPE html>
    <html>
    <body>
        <h1>Bienvenue chez FindIN</h1>
        <p>Bonjour {$prenom} {$nom},</p>
        <p>Vous avez été invité...</p>
        <a href="{$lien}">Accepter l'invitation</a>
    </body>
    </html>
    HTML;
}
```

#### Usage

```php
// Envoyer invitation
$link = "http://" . $_SERVER['HTTP_HOST'] . "/invitation/accept?token=$token";
EmailSender::sendInvitation($token, $email, $prenom, $nom, $link);

// Envoyer contact
EmailSender::sendContactEmail($name, $from_email, $subject, $message);
```

**Configuration:**
- PHP's built-in `mail()` function
- Requires working SMTP on server
- Sendmail/Postfix configuration

### 🔧 Patterns Utilisés

#### 1. Singleton Pattern (Database)
```php
class Database {
    private static $instance = null;
    private $pdo;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->pdo = new PDO(...);
    }
}

// Usage
$db = Database::getInstance();
```

**Avantages:**
- Une seule connexion DB
- Global accessible
- Lazy initialization

#### 2. MVC Pattern
```
Model  → Data access, business logic
View   → HTML templates, display
Ctrl   → Request handling, logic coordination
```

**Avantages:**
- Séparation des concerns
- Réutilisable
- Testable

#### 3. Front Controller Pattern
```
Client → public/index.php (1 entry point)
      → Router
      → Appropriate Controller
      → View
```

**Avantages:**
- Centralized routing
- Common middleware
- Easier URL rewriting

#### 4. Template Extraction Pattern
```php
// In controller
$this->view('page', ['name' => $name, 'email' => $email]);

// In BaseController::view()
extract($data);  // Creates $name, $email variables
require_once $view_file;

// In template
echo $name;  // Works!
```

**Avantages:**
- Simple variable passing
- Clean template code
- No template engine needed

### 📊 API Endpoints

#### Invitation API
```
POST /invitation/create
  Body: {email, prenom, nom, manager_id, departement, role}
  Response: {success, message, invitation_link}

POST /invitation/delete
  Body: {id}
  Response: {success, message}
```

#### Validation API
```
POST /validation/approve
  Body: {validation_id, commentaire}
  Response: {success, message}

POST /validation/reject
  Body: {validation_id, reason}
  Response: {success, message}
```

#### Contact API
```
POST /contact
  Body: {name, email, subject, message}
  Response: HTML form (redirect on success)
```

### 🔍 Error Handling

#### Database Errors
```php
try {
    $stmt->execute([...]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    return false;
}
```

#### HTTP Errors
```php
if (!$found) {
    http_response_code(404);
    return handle404();
}
```

#### Validation Errors
```php
if (strlen($password) < 8) {
    $error = 'Le mot de passe...';
    $this->view('page', ['error' => $error]);
}
```

### 🧪 Testing Checklist

- [ ] RH can create invitation
- [ ] Email sent to employee
- [ ] Token expires after 7 days
- [ ] Employee can accept with password
- [ ] Account created with correct role
- [ ] Manager can approve competency
- [ ] Manager can reject competency
- [ ] Contact form sends email
- [ ] SQL injection prevention works
- [ ] Session hijacking prevention works

### 📈 Performance Considerations

#### Database
- Add indexes on: token (UNIQUE), email, manager_id
- Use connection pooling (future)
- Implement query caching (future)

#### Caching
- Cache manager list
- Cache competencies
- Use Redis (future)

#### Frontend
- Minify CSS/JS
- Lazy load images
- Use CDN for assets (future)

### 🚀 Deployment

#### Prerequisites
- PHP 8.0+
- MySQL 5.7+
- Apache with rewrite enabled

#### Steps
1. Copy files to /var/www/findin
2. Set up virtual host
3. Configure MySQL database
4. Update src/Config/database.php
5. Run database migrations
6. Set permissions on storage/ directory
7. Restart Apache
8. Test all routes

#### Server Setup
```bash
# Create database
mysql -u root -e "CREATE DATABASE findin CHARACTER SET utf8mb4;"

# Run migrations
mysql -u root findin < database/schema.sql

# Set permissions
chmod 755 /var/www/findin/storage/logs
chmod 755 /var/www/findin/public/uploads

# Update config
sed -i 's/localhost:8000/findin.local/g' src/Config/database.php
```

### 📝 Code Standards

**Style Guide:**
- PHP PSR-2 compatible
- Camel case for variables
- CONSTANT_CASE for constants
- Descriptive names
- Comments for complex logic

**Security Rules:**
- Always use prepared statements
- Always validate input
- Always check authentication
- Always sanitize output with htmlspecialchars()
- Always log errors

**File Organization:**
- Models in src/Models/
- Controllers in src/Controllers/
- Views in src/Views/
- Utilities in src/Lib/

---

**Version:** 1.0 Beta  
**Last Updated:** 22 Janvier 2026  
**Status:** Complete Architecture 📚
