# FindIN - AI Coding Instructions

## Architecture Overview

FindIN is a **PHP MVC skills management platform** (French: "gestion des compétences") built without frameworks. Request flow: `public/index.php` (front controller) → Manual routing → Controllers → Models → Views.

**Key directories:**
- `public/` - Web root with `index.php` front controller + assets
- `src/Controllers/` - MVC controllers extending `BaseController`
- `src/Models/` - Database models using PDO singleton pattern
- `src/Views/` - Pure PHP templates with `layouts/` and `dashboard/` subdirectories
- `src/Config/` - Configuration files (`database.php` with DB_TYPE constant)
- `src/Lib/` - Utilities (`cv_parser.php`, `upload_utils.php`)
- `database/` - SQL schemas, migrations, seeds

## Routing System

**Manual switch/case routing** in `public/index.php`:
```php
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

switch ($path) {
    case 'dashboard':
        require_once __DIR__ . '/../src/Controllers/DashboardController.php';
        $dashboard = new DashboardController();
        $dashboard->index();
        exit;
    // ...
}
```

**Adding new routes**: Add case in switch statement, require controller file, instantiate and call method. No framework router.

## Database Patterns

**Dual-database support**: MySQL (XAMPP) or SQLite via `DB_TYPE` constant in `src/Config/database.php`.

**Singleton pattern with static helper**:
```php
// Get PDO instance
$db = Database::getInstance();
$stmt = $db->prepare($sql);
$stmt->execute([':param' => $value]);

// Or use static query helper
$stmt = Database::query('SELECT * FROM users WHERE email = ?', [$email]);
$user = $stmt->fetch();
```

**Auto-migrations**: `Database::createMinimalTables()` runs on instantiation, creates/alters tables if missing. Uses `SHOW COLUMNS` (MySQL) or `PRAGMA table_info` (SQLite) to check existing columns before ALTER TABLE.

**Dual-table compatibility** (legacy vs modern schema):
```php
// Try modern table first, fallback to legacy
$stmt = Database::query('SELECT * FROM users WHERE email = ?', [$email]);
if (!$user = $stmt->fetch()) {
    $stmt = Database::query('SELECT * FROM utilisateurs WHERE email = ?', [$email]);
}
```

**Common table pairs**: 
- `users` / `utilisateurs` (ID: `id` / `id_utilisateur`)
- `user_competences` / `competences_utilisateurs`
- `competences` (shared, ID: `id` / `id_competence`)

**Error handling**: Models use try/catch with fallbacks to return empty arrays rather than throw exceptions.

## Controller Patterns

**BaseController inheritance** - Most controllers extend `BaseController`:
```php
class ProfileController extends BaseController {
    private $userModel;
    private $competenceModel;

    public function __construct() {
        parent::__construct();
        $this->checkAuth();  // Inherited protection
        $this->userModel = new User();  // Direct instantiation (no DI container)
    }
    
    public function index() {
        $data = ['user' => $this->userModel->getUserById($_SESSION['user_id'])];
        $this->view('profile/index', $data);
    }
}
```

**BaseController methods**:
- `view($viewPath, $data)` - Renders view with data injection via `extract()`
- `checkAuth()` - Redirects to `/login` if `$_SESSION['user_id']` not set
- `checkRole($allowedRoles)` - Verifies user role against allowed array
- `redirect($url)` - Shorthand for header Location redirect

**No autoloading**: All classes loaded via explicit `require_once` at file top.

## View Rendering

**Extract pattern for data injection**:
```php
// In controller
$this->view('dashboard/index', [
    'user' => $user,
    'competences' => $competences,
    'stats' => ['total' => 10]
]);

// In BaseController::view()
extract($data);  // Creates $user, $competences, $stats variables
require_once __DIR__ . '/../Views/dashboard/index.php';

// In view file (dashboard/index.php)
echo $user['prenom'];  // Direct access after extract()
```

**View structure**:
- `src/Views/layouts/header.php` - Shared header with CSS variables for theming
- `src/Views/dashboard/_sidebar.php` - Dashboard navigation (prefix `_` for partials)
- Views are standalone PHP files, not blade/twig templates
- Include layouts: `require_once __DIR__ . '/layouts/header.php';`

## Sessions & Authentication

**Session initialization pattern**:
```php
if (session_status() === PHP_SESSION_NONE) session_start();
```

**Route protection** (in `index.php` or controller):
```php
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
```

**Session variables used**:
- `$_SESSION['user_id']` - Primary key (int or CHAR(36) UUID)
- `$_SESSION['user_email']` - User email
- `$_SESSION['user_role']` - Role: `employe`, `manager`, `rh`, `admin`
- `$_SESSION['user_name']` - Display name

**Password handling**: Always `password_hash($password, PASSWORD_DEFAULT)` and `password_verify($input, $hash)`.

## Model Patterns

**Direct PDO usage with prepared statements**:
```php
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();  // PDO instance
    }
    
    public function getUserById($id) {
        $sql = "SELECT * FROM utilisateurs WHERE id_utilisateur = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();  // Single row
    }
    
    public function getAllUsers() {
        $sql = "SELECT * FROM utilisateurs ORDER BY nom, prenom";
        return $this->db->query($sql)->fetchAll();  // Multiple rows
    }
}
```

**Error handling pattern** - Models catch exceptions and return safe defaults:
```php
public function getUserCompetences($userId) {
    try {
        $stmt = Database::query('SELECT * FROM user_competences WHERE user_id = ?', [$userId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Erreur getUserCompetences: " . $e->getMessage());
        return [];  // Empty array instead of exception
    }
}
```

**CRUD conventions**:
- `getAllX()` - Fetch all records
- `getXById($id)` - Fetch single by primary key
- `createX($data)` - INSERT new record
- `updateX($id, $data)` - UPDATE existing
- `deleteX($id)` - DELETE record

## View Templates

- Views are plain PHP files in `src/Views/`
- Use `extract($data)` to expose variables
- Include layouts: `require_once __DIR__ . '/layouts/header.php';`
- Dark theme by default: `data-theme="dark"` on `<html>`
- CSS variables in `:root` for theming (see `layouts/header.php`)

## Utilities & Libraries

**CV Parser** (`src/Lib/cv_parser.php`):
```php
$result = parse_cv_file('/path/to/cv.pdf');
// Returns: ['text' => '...', 'emails' => [], 'phones' => [], 'skills' => []]
```
Supports: Plain text, DOCX (via ZipArchive), PDF (via pdftotext or smalot/pdfparser).

## Development Setup

```bash
# MySQL setup (XAMPP)
php scripts/setup/setup_mysql.php

# Configure Apache virtual host
bash scripts/update_apache.sh

# Test credentials
# admin@findin.fr / admin123
# test@findin.fr / test123
```

**Access**: `http://findin.local/` (or configure your Apache virtual host)

## Common Workflows

**Adding a new route**:
1. Open `public/index.php`
2. Add case in switch statement: `case 'my-route':`
3. Require controller: `require_once __DIR__ . '/../src/Controllers/MyController.php';`
4. Instantiate and call: `$controller = new MyController(); $controller->index(); exit;`

**Creating a new Model**:
1. Create file in `src/Models/MyModel.php`
2. Constructor gets DB instance: `$this->db = Database::getInstance();`
3. Use prepared statements for all queries
4. Wrap queries in try/catch, return empty arrays on error

**Creating a new Controller**:
1. Extend `BaseController` if auth needed
2. Require models at top: `require_once __DIR__ . '/../Models/User.php';`
3. Instantiate models in constructor
4. Use `$this->view('path/to/view', $data)` to render

**Creating a new View**:
1. Create PHP file in `src/Views/`
2. Start with session check: `if (session_status() === PHP_SESSION_NONE) session_start();`
3. Include layout: `require_once __DIR__ . '/layouts/header.php';`
4. Access data variables directly (after extract in controller)

## Routing

Routes defined in `public/index.php` switch statement:
```php
switch ($path) {
    case 'login':
        $auth = new AuthController();
        $auth->login();
        exit;
    // ...
}
```

Add new routes by adding cases and instantiating appropriate controllers.

## Language Note

This is a French codebase. Database columns, comments, and UI text are in French:
- `utilisateur` = user, `competence` = skill, `niveau` = level
- `prenom` = first name, `nom` = last name
- `employe` = employee, `rh` = HR
