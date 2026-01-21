<?php if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../Config/database.php';

// connect
try {
    if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
        $pdo = new PDO(sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME), DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    } else {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
} catch (PDOException $e) {
    echo '<div class="alert">DB error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    require_once __DIR__ . '/layouts/footer.php';
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['skill'])) {
    $email = $_POST['email'];
    $skill = trim($_POST['skill']);
    // load existing competences
    $stmt = $pdo->prepare('SELECT competences FROM utilisateurs WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    $current = $u['competences'] ?? '';
    $list = array_filter(array_map('trim', explode(',', $current)));
    if ($skill && !in_array($skill, $list)) $list[] = $skill;
    $new = implode(', ', $list);
    $stmt = $pdo->prepare('UPDATE utilisateurs SET competences = ? WHERE email = ?');
    $stmt->execute([$new, $email]);
    $message = 'Compétence ajoutée.';
}

?>
<main class="container">
    <section class="section">
        <h1>Compétences</h1>
        <?php if ($message): ?><div class="alert"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

        <form method="get" style="margin-bottom:1rem;">
            <label>Voir profil par email: <input type="email" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>"></label>
            <button type="submit">Charger</button>
        </form>

        <?php if (!empty($_GET['email'])):
            $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ? LIMIT 1');
            $stmt->execute([$_GET['email']]);
            $u = $stmt->fetch();
            if ($u):
        ?>
            <h2><?php echo htmlspecialchars($u['prenom'] . ' ' . $u['nom']); ?> — <?php echo htmlspecialchars($u['email']); ?></h2>
            <p><strong>Compétences:</strong> <?php echo htmlspecialchars($u['competences'] ?? ''); ?></p>

            <form method="post">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($u['email']); ?>">
                <label>Ajouter compétence: <input type="text" name="skill"></label>
                <button type="submit">Ajouter</button>
            </form>
        <?php else: ?>
            <p>Aucun utilisateur trouvé.</p>
        <?php endif; endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Mes Compétences</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0a0118;
            --bg-card: #1a0d2e;
            --text-white: #ffffff;
            --text-light: #e0e0e0;
            --accent-primary: #9333ea;
            --accent-blue: #3b82f6;
            --accent-pink: #ec4899;
            --border-light: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0118 0%, #1a0d2e 100%);
            color: var(--text-white);
            line-height: 1.6;
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background: rgba(26, 13, 46, 0.8);
            backdrop-filter: blur(10px);
            border-right: 1px solid var(--border-light);
            padding: 2rem 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .sidebar-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 0 1rem;
        }

        .nav-item {
            padding: 0.75rem 1rem;
            color: var(--text-light);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(147, 51, 234, 0.2);
            color: var(--accent-primary);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .breadcrumb {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--accent-primary);
            text-decoration: none;
        }

        .competences-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .competence-card {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .competence-card:hover {
            border-color: var(--accent-primary);
            transform: translateY(-5px);
        }

        .competence-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, var(--accent-primary), var(--accent-blue));
        }

        .competence-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #9333ea, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .competence-name {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .competence-level {
            color: var(--text-light);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .level-bar {
            background: rgba(255, 255, 255, 0.1);
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .level-fill {
            height: 100%;
            background: linear-gradient(to right, var(--accent-primary), var(--accent-blue));
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .competence-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .competence-status.pending {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .competence-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-small {
            flex: 1;
            padding: 0.5rem 1rem;
            background: rgba(147, 51, 234, 0.2);
            color: var(--accent-primary);
            border: 1px solid var(--accent-primary);
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .btn-small:hover {
            background: var(--accent-primary);
            color: white;
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .stat-item {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent-primary);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: none;
            }

            .competences-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">F</div>
                <div class="sidebar-title">FindIN</div>
            </div>

            <nav class="sidebar-nav">
                <a href="/dashboard" class="nav-item">
                    <i class="fas fa-chart-line"></i> Tableau de bord
                </a>
                <a href="#" class="nav-item active">
                    <i class="fas fa-certificate"></i> Mes compétences
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-search"></i> Rechercher
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-user"></i> Mon profil
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Mes Compétences</h1>
                <div class="breadcrumb">
                    <a href="/dashboard">Dashboard</a> / Compétences
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-section">
                <div class="stat-item">
                    <div class="stat-value">12</div>
                    <div class="stat-label">Compétences</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">10</div>
                    <div class="stat-label">Validées</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">7.5</div>
                    <div class="stat-label">Note moyenne</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">83%</div>
                    <div class="stat-label">Profil complet</div>
                </div>
            </div>

            <!-- Competences Grid -->
            <div class="competences-grid">
                <!-- PHP -->
                <div class="competence-card">
                    <div class="competence-icon"><i class="fab fa-php"></i></div>
                    <div class="competence-name">PHP</div>
                    <div class="competence-level">Expert</div>
                    <span class="competence-status">✓ Validée</span>
                    <div class="level-bar">
                        <div class="level-fill" style="width: 95%;"></div>
                    </div>
                    <div style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1rem;">
                        <strong>8 ans</strong> d'expérience
                    </div>
                    <div class="competence-actions">
                        <button class="btn-small"><i class="fas fa-edit"></i> Éditer</button>
                        <button class="btn-small"><i class="fas fa-trash"></i> Supprimer</button>
                    </div>
                </div>

                <!-- JavaScript -->
                <div class="competence-card">
                    <div class="competence-icon"><i class="fab fa-js"></i></div>
                    <div class="competence-name">JavaScript</div>
                    <div class="competence-level">Avancé</div>
                    <span class="competence-status">✓ Validée</span>
                    <div class="level-bar">
                        <div class="level-fill" style="width: 85%;"></div>
                    </div>
                    <div style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1rem;">
                        <strong>6 ans</strong> d'expérience
                    </div>
                    <div class="competence-actions">
                        <button class="btn-small"><i class="fas fa-edit"></i> Éditer</button>
                        <button class="btn-small"><i class="fas fa-trash"></i> Supprimer</button>
                    </div>
                </div>

                <!-- Leadership -->
                <div class="competence-card">
                    <div class="competence-icon"><i class="fas fa-users"></i></div>
                    <div class="competence-name">Leadership</div>
                    <div class="competence-level">Intermédiaire</div>
                    <span class="competence-status pending">En attente de validation</span>
                    <div class="level-bar">
                        <div class="level-fill" style="width: 65%; background: linear-gradient(to right, #f59e0b, #f97316);"></div>
                    </div>
                    <div style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1rem;">
                        <strong>3 ans</strong> d'expérience
                    </div>
                    <div class="competence-actions">
                        <button class="btn-small"><i class="fas fa-edit"></i> Éditer</button>
                        <button class="btn-small"><i class="fas fa-trash"></i> Supprimer</button>
                    </div>
                </div>

                <!-- React -->
                <div class="competence-card">
                    <div class="competence-icon"><i class="fab fa-react"></i></div>
                    <div class="competence-name">React.js</div>
                    <div class="competence-level">Avancé</div>
                    <span class="competence-status">✓ Validée</span>
                    <div class="level-bar">
                        <div class="level-fill" style="width: 80%;"></div>
                    </div>
                    <div style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1rem;">
                        <strong>4 ans</strong> d'expérience
                    </div>
                    <div class="competence-actions">
                        <button class="btn-small"><i class="fas fa-edit"></i> Éditer</button>
                        <button class="btn-small"><i class="fas fa-trash"></i> Supprimer</button>
                    </div>
                </div>

                <!-- DevOps -->
                <div class="competence-card">
                    <div class="competence-icon"><i class="fas fa-cogs"></i></div>
                    <div class="competence-name">DevOps</div>
                    <div class="competence-level">Avancé</div>
                    <span class="competence-status">✓ Validée</span>
                    <div class="level-bar">
                        <div class="level-fill" style="width: 78%;"></div>
                    </div>
                    <div style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1rem;">
                        <strong>5 ans</strong> d'expérience
                    </div>
                    <div class="competence-actions">
                        <button class="btn-small"><i class="fas fa-edit"></i> Éditer</button>
                        <button class="btn-small"><i class="fas fa-trash"></i> Supprimer</button>
                    </div>
                </div>

                <!-- Communication -->
                <div class="competence-card">
                    <div class="competence-icon"><i class="fas fa-comment"></i></div>
                    <div class="competence-name">Communication</div>
                    <div class="competence-level">Expert</div>
                    <span class="competence-status">✓ Validée</span>
                    <div class="level-bar">
                        <div class="level-fill" style="width: 90%;"></div>
                    </div>
                    <div style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1rem;">
                        <strong>9 ans</strong> d'expérience
                    </div>
                    <div class="competence-actions">
                        <button class="btn-small"><i class="fas fa-edit"></i> Éditer</button>
                        <button class="btn-small"><i class="fas fa-trash"></i> Supprimer</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="/assets/js/main.js"></script>
</body>
</html>
