<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }

// Récupérer les infos utilisateur
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Database.php';
$db = Database::getInstance();

$user_id = $_SESSION['user_id'];
$user = null;
$competences = [];
$projets = [];
$documents = [];
$stats = ['competences' => 0, 'projets' => 0, 'certifications' => 0, 'documents' => 0];

try {
    // Infos utilisateur - essayer les deux tables
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $stmt = $db->prepare("SELECT *, id_utilisateur as id FROM utilisateurs WHERE id_utilisateur = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Compétences depuis user_competences
    $stmt = $db->prepare("SELECT c.*, uc.niveau_declare as level FROM user_competences uc JOIN competences c ON uc.competence_id = c.id WHERE uc.user_id = ? ORDER BY uc.niveau_declare DESC LIMIT 6");
    $stmt->execute([$user_id]);
    $competences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['competences'] = count($competences);
    
    // Projets
    $stmt = $db->prepare("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['projets'] = count($projets);
    
} catch (Exception $e) {
    // Tables peuvent ne pas exister
}

$userName = $user['name'] ?? $user['first_name'] ?? $_SESSION['user_name'] ?? 'Utilisateur';
$userEmail = $user['email'] ?? $_SESSION['user_email'] ?? '';
$userRole = $user['role'] ?? 'collaborateur';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0118;
            --bg-secondary: #1a0d2e;
            --bg-card: #241538;
            --bg-hover: #2d1b47;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --accent-purple: #9333ea;
            --accent-blue: #3b82f6;
            --accent-green: #10b981;
            --accent-yellow: #f59e0b;
            --accent-red: #ef4444;
            --accent-pink: #ec4899;
            --border-color: rgba(255,255,255,0.1);
        }
        [data-theme="light"] {
            --bg-primary: #f1f5f9;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --bg-hover: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: rgba(0,0,0,0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); min-height: 100vh; display: flex; }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .sidebar-header img { height: 32px; }
        .sidebar-header span { font-weight: 700; font-size: 1.25rem; }
        
        .sidebar-nav { flex: 1; padding: 1rem 0; overflow-y: auto; }
        .nav-section { padding: 0.5rem 1rem; margin-top: 0.5rem; }
        .nav-section-title { font-size: 0.7rem; text-transform: uppercase; color: var(--text-secondary); letter-spacing: 0.05em; margin-bottom: 0.5rem; }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .nav-item:hover { background: var(--bg-hover); color: var(--text-primary); }
        .nav-item.active { background: rgba(147,51,234,0.1); color: var(--accent-purple); border-left-color: var(--accent-purple); }
        .nav-item i { width: 20px; text-align: center; }
        
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        .user-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--bg-card);
            border-radius: 12px;
            margin-bottom: 0.75rem;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name { font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 0.75rem; color: var(--text-secondary); }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-title { font-size: 1.75rem; font-weight: 700; }
        .page-subtitle { color: var(--text-secondary); margin-top: 0.25rem; }
        
        .header-actions { display: flex; gap: 0.75rem; align-items: center; }
        .btn {
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            border: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .btn-primary { background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(147,51,234,0.3); }
        .btn-secondary { background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border-color); }
        
        .theme-toggle {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .stat-card:hover { transform: translateY(-3px); border-color: var(--accent-purple); }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        .stat-icon.purple { background: rgba(147,51,234,0.15); color: var(--accent-purple); }
        .stat-icon.blue { background: rgba(59,130,246,0.15); color: var(--accent-blue); }
        .stat-icon.green { background: rgba(16,185,129,0.15); color: var(--accent-green); }
        .stat-icon.yellow { background: rgba(245,158,11,0.15); color: var(--accent-yellow); }
        .stat-value { font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; }
        .stat-label { color: var(--text-secondary); font-size: 0.9rem; }
        .stat-change { font-size: 0.8rem; margin-top: 0.5rem; }
        .stat-change.positive { color: var(--accent-green); }
        .stat-change.negative { color: var(--accent-red); }
        
        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }
        
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .card-title { font-size: 1.1rem; font-weight: 600; }
        .card-link { color: var(--accent-purple); text-decoration: none; font-size: 0.85rem; }
        
        /* Competences List */
        .competence-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .competence-item:last-child { border-bottom: none; }
        .competence-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .competence-info { flex: 1; }
        .competence-name { font-weight: 500; margin-bottom: 0.25rem; }
        .competence-category { font-size: 0.8rem; color: var(--text-secondary); }
        .competence-level {
            display: flex;
            gap: 3px;
        }
        .level-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--border-color);
        }
        .level-dot.active { background: var(--accent-purple); }
        
        /* Projects List */
        .project-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-hover);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            transition: all 0.2s;
        }
        .project-item:hover { transform: translateX(5px); }
        .project-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        .project-status.active { background: var(--accent-green); }
        .project-status.planned { background: var(--accent-yellow); }
        .project-status.completed { background: var(--accent-blue); }
        .project-info { flex: 1; }
        .project-name { font-weight: 500; }
        .project-meta { font-size: 0.8rem; color: var(--text-secondary); }
        
        /* Shortcuts */
        .shortcuts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        .shortcut-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: var(--bg-hover);
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.2s;
        }
        .shortcut-item:hover { background: rgba(147,51,234,0.1); }
        .shortcut-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }
        .shortcut-label { font-size: 0.85rem; font-weight: 500; }
        
        /* Activity Feed */
        .activity-item {
            display: flex;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-purple);
            margin-top: 6px;
        }
        .activity-content { flex: 1; }
        .activity-text { font-size: 0.9rem; }
        .activity-time { font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem; }
        
        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .content-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="/assets/images/logo.png" alt="FindIN">
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Principal</div>
                <a href="/dashboard" class="nav-item active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="/dashboard/competences" class="nav-item"><i class="fas fa-brain"></i> Compétences</a>
                <a href="/dashboard/certifications" class="nav-item"><i class="fas fa-certificate"></i> Certifications</a>
                <a href="/dashboard/mon-espace" class="nav-item"><i class="fas fa-user"></i> Mon Espace</a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Outils</div>
                <a href="/dashboard/cvs" class="nav-item"><i class="fas fa-file-alt"></i> CVs</a>
                <a href="/dashboard/reunions" class="nav-item"><i class="fas fa-calendar"></i> Réunions</a>
                <a href="/dashboard/tests" class="nav-item"><i class="fas fa-clipboard-check"></i> Tests de compétences</a>
                <a href="/dashboard/bilan" class="nav-item"><i class="fas fa-chart-line"></i> Bilan annuel</a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Administration</div>
                <a href="/dashboard/projets" class="nav-item"><i class="fas fa-project-diagram"></i> Projets</a>
                <a href="/dashboard/equipe" class="nav-item"><i class="fas fa-users"></i> Équipe</a>
                <a href="/dashboard/parametres" class="nav-item"><i class="fas fa-cog"></i> Paramètres</a>
            </div>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                <div class="user-info">
                    <div class="user-name"><?= htmlspecialchars($userName) ?></div>
                    <div class="user-role"><?= ucfirst($userRole) ?></div>
                </div>
            </div>
            <a href="/logout" class="nav-item" style="padding: 0.5rem 0;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Bonjour, <?= htmlspecialchars(explode(' ', $userName)[0]) ?> 👋</h1>
                <p class="page-subtitle">Voici un aperçu de votre espace de travail</p>
            </div>
            <div class="header-actions">
                <a href="/dashboard/competences" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter compétence</a>
                <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-brain"></i></div>
                <div class="stat-value"><?= $stats['competences'] ?></div>
                <div class="stat-label">Compétences déclarées</div>
                <div class="stat-change positive"><i class="fas fa-arrow-up"></i> +2 ce mois</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-project-diagram"></i></div>
                <div class="stat-value"><?= $stats['projets'] ?></div>
                <div class="stat-label">Projets actifs</div>
                <div class="stat-change positive"><i class="fas fa-arrow-up"></i> +1 ce mois</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-certificate"></i></div>
                <div class="stat-value">3</div>
                <div class="stat-label">Certifications</div>
                <div class="stat-change positive"><i class="fas fa-check"></i> À jour</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow"><i class="fas fa-clipboard-check"></i></div>
                <div class="stat-value">85%</div>
                <div class="stat-label">Profil complété</div>
                <div class="stat-change"><i class="fas fa-info-circle"></i> 3 champs manquants</div>
            </div>
        </div>
        
        <!-- Content Grid -->
        <div class="content-grid">
            <div>
                <!-- Competences Card -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-header">
                        <h2 class="card-title">Mes Compétences</h2>
                        <a href="/dashboard/competences" class="card-link">Voir tout →</a>
                    </div>
                    <?php if (empty($competences)): ?>
                        <div class="competence-item">
                            <div class="competence-icon" style="background: rgba(147,51,234,0.15); color: var(--accent-purple);"><i class="fas fa-code"></i></div>
                            <div class="competence-info">
                                <div class="competence-name">PHP / Laravel</div>
                                <div class="competence-category">Développement Backend</div>
                            </div>
                            <div class="competence-level">
                                <div class="level-dot active"></div>
                                <div class="level-dot active"></div>
                                <div class="level-dot active"></div>
                                <div class="level-dot active"></div>
                                <div class="level-dot"></div>
                            </div>
                        </div>
                        <div class="competence-item">
                            <div class="competence-icon" style="background: rgba(59,130,246,0.15); color: var(--accent-blue);"><i class="fas fa-js"></i></div>
                            <div class="competence-info">
                                <div class="competence-name">JavaScript / React</div>
                                <div class="competence-category">Développement Frontend</div>
                            </div>
                            <div class="competence-level">
                                <div class="level-dot active"></div>
                                <div class="level-dot active"></div>
                                <div class="level-dot active"></div>
                                <div class="level-dot"></div>
                                <div class="level-dot"></div>
                            </div>
                        </div>
                        <div class="competence-item">
                            <div class="competence-icon" style="background: rgba(16,185,129,0.15); color: var(--accent-green);"><i class="fas fa-database"></i></div>
                            <div class="competence-info">
                                <div class="competence-name">MySQL / PostgreSQL</div>
                                <div class="competence-category">Base de données</div>
                            </div>
                            <div class="competence-level">
                                <div class="level-dot active"></div>
                                <div class="level-dot active"></div>
                                <div class="level-dot active"></div>
                                <div class="level-dot active"></div>
                                <div class="level-dot active"></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($competences as $comp): ?>
                        <div class="competence-item">
                            <div class="competence-icon" style="background: rgba(147,51,234,0.15); color: var(--accent-purple);"><i class="fas fa-star"></i></div>
                            <div class="competence-info">
                                <div class="competence-name"><?= htmlspecialchars($comp['name']) ?></div>
                                <div class="competence-category"><?= htmlspecialchars($comp['category'] ?? 'Compétence') ?></div>
                            </div>
                            <div class="competence-level">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="level-dot <?= $i <= ($comp['level'] ?? 3) ? 'active' : '' ?>"></div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Projets Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Projets Récents</h2>
                        <a href="/dashboard/projets" class="card-link">Voir tout →</a>
                    </div>
                    <div class="project-item">
                        <div class="project-status active"></div>
                        <div class="project-info">
                            <div class="project-name">FindIN MVP</div>
                            <div class="project-meta">En cours · Démarré le 01/10/2025</div>
                        </div>
                    </div>
                    <div class="project-item">
                        <div class="project-status active"></div>
                        <div class="project-info">
                            <div class="project-name">Refonte Dashboard</div>
                            <div class="project-meta">En cours · Démarré le 01/12/2025</div>
                        </div>
                    </div>
                    <div class="project-item">
                        <div class="project-status completed"></div>
                        <div class="project-info">
                            <div class="project-name">Intégration Base de Données</div>
                            <div class="project-meta">Terminé · 15/11/2025</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <!-- Shortcuts Card -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-header">
                        <h2 class="card-title">Raccourcis</h2>
                    </div>
                    <div class="shortcuts-grid">
                        <a href="/dashboard/cvs" class="shortcut-item">
                            <div class="shortcut-icon" style="background: rgba(147,51,234,0.15); color: var(--accent-purple);"><i class="fas fa-file-upload"></i></div>
                            <span class="shortcut-label">Importer CV</span>
                        </a>
                        <a href="/dashboard/tests" class="shortcut-item">
                            <div class="shortcut-icon" style="background: rgba(59,130,246,0.15); color: var(--accent-blue);"><i class="fas fa-tasks"></i></div>
                            <span class="shortcut-label">Passer un test</span>
                        </a>
                        <a href="/dashboard/reunions" class="shortcut-item">
                            <div class="shortcut-icon" style="background: rgba(16,185,129,0.15); color: var(--accent-green);"><i class="fas fa-video"></i></div>
                            <span class="shortcut-label">Planifier réunion</span>
                        </a>
                        <a href="/dashboard/bilan" class="shortcut-item">
                            <div class="shortcut-icon" style="background: rgba(245,158,11,0.15); color: var(--accent-yellow);"><i class="fas fa-chart-bar"></i></div>
                            <span class="shortcut-label">Mon bilan</span>
                        </a>
                    </div>
                </div>
                
                <!-- Activity Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Activité Récente</h2>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div class="activity-content">
                            <div class="activity-text">Nouvelle compétence ajoutée : <strong>PHP</strong></div>
                            <div class="activity-time">Il y a 2 heures</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background: var(--accent-green);"></div>
                        <div class="activity-content">
                            <div class="activity-text">Certification validée : <strong>SQL Avancé</strong></div>
                            <div class="activity-time">Hier</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background: var(--accent-blue);"></div>
                        <div class="activity-content">
                            <div class="activity-text">Rejoint le projet <strong>FindIN MVP</strong></div>
                            <div class="activity-time">Il y a 3 jours</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background: var(--accent-yellow);"></div>
                        <div class="activity-content">
                            <div class="activity-text">CV mis à jour</div>
                            <div class="activity-time">Il y a 1 semaine</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        // Theme toggle
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        const icon = themeToggle.querySelector('i');
        const savedTheme = localStorage.getItem('theme') || 'dark';
        html.setAttribute('data-theme', savedTheme);
        icon.className = savedTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        
        themeToggle.addEventListener('click', () => {
            const newTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            icon.className = newTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        });
    </script>
</body>
</html>
