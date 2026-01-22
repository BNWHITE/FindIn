<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }

$user_id = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userEmail = $_SESSION['user_email'] ?? '';
$userRole = $_SESSION['user_role'] ?? 'employe';
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
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 1rem 0; }
        .nav-section { margin-bottom: 1.5rem; }
        .nav-section-title { padding: 0 1.5rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 0.5rem; letter-spacing: 0.05em; }
        .nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.5rem; color: var(--text-secondary); text-decoration: none; font-size: 0.95rem; transition: all 0.2s; }
        .nav-item:hover { color: var(--text-primary); background: rgba(147,51,234,0.1); }
        .nav-item.active { color: var(--accent-purple); background: rgba(147,51,234,0.15); border-left: 3px solid var(--accent-purple); padding-left: calc(1.5rem - 3px); }
        .sidebar-footer { padding: 1.5rem; border-top: 1px solid var(--border-color); }
        .user-card { display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: rgba(147,51,234,0.1); border-radius: 12px; margin-bottom: 1rem; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--accent-purple); display: flex; align-items: center; justify-content: center; font-weight: 600; }
        .user-info { flex: 1; }
        .user-name { font-weight: 600; font-size: 0.9rem; }
        .user-role { font-size: 0.75rem; color: var(--text-secondary); }
        
        /* Main Content */
        .main-content { margin-left: 280px; flex: 1; padding: 2rem; overflow-y: auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-title { font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; }
        .page-subtitle { color: var(--text-secondary); }
        .header-actions { display: flex; gap: 1rem; align-items: center; }
        .btn { padding: 0.75rem 1.5rem; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-primary { background: var(--accent-purple); color: white; }
        .btn-primary:hover { background: #7c3aed; transform: translateY(-2px); }
        .theme-toggle { background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary); width: 40px; height: 40px; border-radius: 8px; cursor: pointer; }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
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
            font-size: 1.1rem;
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
        .project-status.pending { background: var(--accent-yellow); }
        .project-info { flex: 1; }
        .project-name { font-weight: 500; }
        .project-meta { font-size: 0.8rem; color: var(--text-secondary); }
        
        /* Loading State */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid var(--border-color);
            border-radius: 50%;
            border-top-color: var(--accent-purple);
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
        }
        .empty-state i {
            font-size: 2rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
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
            <i class="fas fa-rocket" style="color: var(--accent-purple); font-size: 1.5rem;"></i>
            <span>FindIN</span>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Principal</div>
                <a href="/dashboard" class="nav-item active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="/dashboard/competences" class="nav-item"><i class="fas fa-brain"></i> Compétences</a>
                <a href="/dashboard/certifications" class="nav-item"><i class="fas fa-certificate"></i> Certifications</a>
                <a href="/dashboard/profile" class="nav-item"><i class="fas fa-user"></i> Mon Profil</a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Outils</div>
                <a href="/dashboard/documents" class="nav-item"><i class="fas fa-file-alt"></i> Documents</a>
                <a href="/dashboard/reunions" class="nav-item"><i class="fas fa-calendar"></i> Réunions</a>
                <a href="/dashboard/projets" class="nav-item"><i class="fas fa-project-diagram"></i> Projets</a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Administration</div>
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
                <div class="stat-value" id="stats-competences">-</div>
                <div class="stat-label">Compétences</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-project-diagram"></i></div>
                <div class="stat-value" id="stats-projets">-</div>
                <div class="stat-label">Projets actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-certificate"></i></div>
                <div class="stat-value" id="stats-certifications">-</div>
                <div class="stat-label">Certifications</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow"><i class="fas fa-users"></i></div>
                <div class="stat-value" id="stats-team">-</div>
                <div class="stat-label">Utilisateurs</div>
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
                    <div id="competences-list">
                        <div class="empty-state"><div class="loading"></div></div>
                    </div>
                </div>
                
                <!-- Projects Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Projets en cours</h2>
                        <a href="/dashboard/projets" class="card-link">Voir tout →</a>
                    </div>
                    <div id="projets-list">
                        <div class="empty-state"><div class="loading"></div></div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Right -->
            <div>
                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Utilisateurs</h2>
                    </div>
                    <div id="utilisateurs-list">
                        <div class="empty-state"><div class="loading"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        const API_BASE = '/api';
        const USER_ID = '<?= $user_id ?>';
        
        // Fetch Competences
        async function loadCompetences() {
            try {
                const response = await fetch(`${API_BASE}/competences/list`);
                const result = await response.json();
                
                if (result.success && result.data && result.data.length > 0) {
                    const container = document.getElementById('competences-list');
                    const colors = ['purple', 'blue', 'green', 'yellow', 'pink', 'red'];
                    
                    container.innerHTML = result.data.slice(0, 5).map((comp, idx) => `
                        <div class="competence-item">
                            <div class="competence-icon" style="background: rgba(147,51,234,0.15); color: var(--accent-purple);">
                                <i class="fas fa-code"></i>
                            </div>
                            <div class="competence-info">
                                <div class="competence-name">${comp.nom}</div>
                                <div class="competence-category">${comp.type_competence || 'Technique'}</div>
                            </div>
                            <div class="competence-level">
                                ${[1,2,3,4,5].map(i => `<div class="level-dot ${i <= 3 ? 'active' : ''}"></div>`).join('')}
                            </div>
                        </div>
                    `).join('');
                    
                    document.getElementById('stats-competences').textContent = result.count;
                } else {
                    document.getElementById('competences-list').innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Aucune compétence trouvée</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Erreur chargement compétences:', error);
                document.getElementById('competences-list').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Erreur de chargement</p>
                    </div>
                `;
            }
        }
        
        // Fetch Projets
        async function loadProjets() {
            try {
                const response = await fetch(`${API_BASE}/projets/list`);
                const result = await response.json();
                
                if (result.success && result.data && result.data.length > 0) {
                    const container = document.getElementById('projets-list');
                    container.innerHTML = result.data.slice(0, 5).map(proj => `
                        <div class="project-item">
                            <div class="project-status ${proj.statut === 'en_cours' ? 'active' : 'pending'}"></div>
                            <div class="project-info">
                                <div class="project-name">${proj.nom}</div>
                                <div class="project-meta">${proj.statut || 'En cours'}</div>
                            </div>
                        </div>
                    `).join('');
                    
                    document.getElementById('stats-projets').textContent = result.count;
                } else {
                    document.getElementById('projets-list').innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <p>Aucun projet pour le moment</p>
                        </div>
                    `;
                    document.getElementById('stats-projets').textContent = '0';
                }
            } catch (error) {
                console.error('Erreur chargement projets:', error);
                document.getElementById('projets-list').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Erreur de chargement</p>
                    </div>
                `;
            }
        }
        
        // Fetch Utilisateurs
        async function loadUtilisateurs() {
            try {
                const response = await fetch(`${API_BASE}/utilisateurs/list`);
                const result = await response.json();
                
                if (result.success && result.data && result.data.length > 0) {
                    const container = document.getElementById('utilisateurs-list');
                    container.innerHTML = result.data.slice(0, 5).map(user => `
                        <div class="competence-item">
                            <div class="competence-icon" style="background: rgba(59,130,246,0.15); color: var(--accent-blue);">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="competence-info">
                                <div class="competence-name">${user.prenom} ${user.nom}</div>
                                <div class="competence-category">${user.role || 'Utilisateur'}</div>
                            </div>
                        </div>
                    `).join('');
                    
                    document.getElementById('stats-team').textContent = result.count;
                } else {
                    document.getElementById('utilisateurs-list').innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>Aucun utilisateur</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Erreur chargement utilisateurs:', error);
                document.getElementById('utilisateurs-list').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Erreur de chargement</p>
                    </div>
                `;
            }
        }
        
        // Fetch Certifications
        async function loadCertifications() {
            try {
                const response = await fetch(`${API_BASE}/certifications/list`);
                const result = await response.json();
                
                if (result.success && result.count) {
                    document.getElementById('stats-certifications').textContent = result.count;
                } else {
                    document.getElementById('stats-certifications').textContent = '0';
                }
            } catch (error) {
                console.error('Erreur chargement certifications:', error);
                document.getElementById('stats-certifications').textContent = '0';
            }
        }
        
        // Load all data on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadCompetences();
            loadProjets();
            loadUtilisateurs();
            loadCertifications();
            
            // Theme toggle
            const themeToggle = document.getElementById('themeToggle');
            const html = document.documentElement;
            const currentTheme = localStorage.getItem('theme') || 'dark';
            html.setAttribute('data-theme', currentTheme);
            
            themeToggle.addEventListener('click', () => {
                const newTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                themeToggle.innerHTML = newTheme === 'dark' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
            });
        });
        
        // Auto-refresh toutes les 30 secondes
        setInterval(() => {
            loadCompetences();
            loadProjets();
            loadUtilisateurs();
            loadCertifications();
        }, 30000);
    </script>
</body>
</html>
