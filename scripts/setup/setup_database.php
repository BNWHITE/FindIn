<?php
// setup_database.php - Configuration UI for database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

$config_file = __DIR__ . '/config/database.php';
$message = '';
$db_status = 'unknown';

// Check current config
if (file_exists($config_file)) {
    $content = file_get_contents($config_file);
    preg_match("/define\('DB_TYPE',\s*getenv\('DB_TYPE'\)\s*\?:\s*'(.*?)'\);/", $content, $matches);
    $current_db_type = $matches[1] ?? 'sqlite';
    
    preg_match("/define\('DB_HOST',\s*getenv\('DB_HOST'\)\s*\?:\s*'(.*?)'\);/", $content, $matches);
    $current_host = $matches[1] ?? '127.0.0.1';
    
    preg_match("/define\('DB_USER',\s*getenv\('DB_USER'\)\s*\?:\s*'(.*?)'\);/", $content, $matches);
    $current_user = $matches[1] ?? 'root';
}

// Test connection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_type = $_POST['db_type'] ?? 'mysql';
    $host = $_POST['host'] ?? '127.0.0.1';
    $port = $_POST['port'] ?? '3306';
    $name = $_POST['db_name'] ?? 'gestion_competences';
    $user = $_POST['db_user'] ?? 'root';
    $pass = $_POST['db_pass'] ?? '';
    
    // Test connection
    try {
        if ($db_type === 'mysql') {
            $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            
            // Create database if not exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Connect to the database
            $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            
            $db_status = '✅ Connected to MySQL';
            $message = "Database connection successful! Updated to MySQL ($host:$port)";
        } else {
            $db_status = '✅ SQLite ready';
            $message = "SQLite configuration ready";
        }
        
        // Update config file if test passed
        $new_config = str_replace(
            "define('DB_TYPE', getenv('DB_TYPE') ?: 'mysql');",
            "define('DB_TYPE', getenv('DB_TYPE') ?: '$db_type');",
            file_get_contents($config_file)
        );
        
        $new_config = str_replace(
            "define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');",
            "define('DB_HOST', getenv('DB_HOST') ?: '$host');",
            $new_config
        );
        
        $new_config = str_replace(
            "define('DB_PORT', getenv('DB_PORT') ?: '3306');",
            "define('DB_PORT', getenv('DB_PORT') ?: '$port');",
            $new_config
        );
        
        $new_config = str_replace(
            "define('DB_NAME', getenv('DB_NAME') ?: 'gestion_competences');",
            "define('DB_NAME', getenv('DB_NAME') ?: '$name');",
            $new_config
        );
        
        $new_config = str_replace(
            "define('DB_USER', getenv('DB_USER') ?: 'root');",
            "define('DB_USER', getenv('DB_USER') ?: '$user');",
            $new_config
        );
        
        $new_config = str_replace(
            "define('DB_PASS', getenv('DB_PASS') ?: '');",
            "define('DB_PASS', getenv('DB_PASS') ?: '$pass');",
            $new_config
        );
        
        file_put_contents($config_file, $new_config);
        $message .= " - Configuration saved!";
        
    } catch (Exception $e) {
        $message = "❌ Connection failed: " . $e->getMessage();
        $db_status = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Database Setup</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a0118 0%, #1a0d2e 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            width: 100%;
            max-width: 600px;
            background: rgba(26, 13, 46, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 3rem;
        }
        
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }
        
        .logo span {
            font-size: 1.75rem;
            font-weight: 700;
        }
        
        h1 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
        }
        
        input, select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }
        
        input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #9333ea;
            background: rgba(147, 51, 234, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        
        .message.success {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.5);
            color: #86efac;
        }
        
        .message.error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #fca5a5;
        }
        
        .btn {
            width: 100%;
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(147, 51, 234, 0.3);
        }
        
        .status {
            text-align: center;
            padding: 1rem;
            background: rgba(147, 51, 234, 0.1);
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        
        .info-box {
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 2rem;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <div class="logo-icon">F</div>
                <span>FindIN</span>
            </div>
            <h1>Database Configuration</h1>
            <p class="subtitle">Configure your database connection for FindIN</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '✅') !== false || strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="status">
            <strong>Current Status:</strong> <?php echo htmlspecialchars($current_db_type ?? 'Not configured'); ?>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="db_type"><i class="fas fa-database"></i> Database Type</label>
                <select name="db_type" id="db_type" onchange="updateFields()">
                    <option value="mysql">MySQL (XAMPP)</option>
                    <option value="sqlite">SQLite (File-based)</option>
                </select>
            </div>
            
            <div id="mysql-fields" style="display: none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="host">Host</label>
                        <input type="text" name="host" id="host" value="127.0.0.1" placeholder="127.0.0.1">
                    </div>
                    <div class="form-group">
                        <label for="port">Port</label>
                        <input type="number" name="port" id="port" value="3306" placeholder="3306">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="db_name">Database Name</label>
                    <input type="text" name="db_name" id="db_name" value="gestion_competences" placeholder="gestion_competences">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="db_user">Username</label>
                        <input type="text" name="db_user" id="db_user" value="root" placeholder="root">
                    </div>
                    <div class="form-group">
                        <label for="db_pass">Password</label>
                        <input type="password" name="db_pass" id="db_pass" placeholder="Leave empty if no password">
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-check"></i> Test & Save Configuration
            </button>
        </form>
        
        <div class="info-box">
            <p><strong>XAMPP Setup:</strong></p>
            <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
                <li>Ensure XAMPP MySQL is running</li>
                <li>Default host: 127.0.0.1</li>
                <li>Default port: 3306</li>
                <li>Default user: root</li>
                <li>Default password: (empty)</li>
                <li>PhpMyAdmin: http://localhost/phpmyadmin</li>
            </ul>
        </div>
    </div>
    
    <script>
        function updateFields() {
            const type = document.getElementById('db_type').value;
            const mysqlFields = document.getElementById('mysql-fields');
            mysqlFields.style.display = type === 'mysql' ? 'block' : 'none';
        }
        
        // Set initial state
        const dbType = document.getElementById('db_type');
        dbType.value = '<?php echo $current_db_type ?? 'mysql'; ?>';
        updateFields();
    </script>
</body>
</html>
