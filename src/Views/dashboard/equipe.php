<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
$user = ['name' => $_SESSION['user_name'] ?? 'Utilisateur', 'email' => $_SESSION['user_email'] ?? '', 'role' => $_SESSION['user_role'] ?? 'employe'];
$currentPage = 'equipe';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Équipe - FindIN</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="/assets/css/dashboard.css" rel="stylesheet">
    <style>
        .team-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .view-toggle {
            display: flex;
            background: var(--bg-tertiary);
            border-radius: 8px;
            padding: 4px;
        }
        .view-btn {
            padding: 8px 16px;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .view-btn.active {
            background: var(--accent-primary);
            color: white;
        }
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .member-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid var(--border-color);
        }
        .member-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .member-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            font-weight: 600;
            position: relative;
        }
        .member-status {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 3px solid var(--bg-secondary);
        }
        .status-online { background: #10b981; }
        .status-away { background: #f59e0b; }
        .status-busy { background: #ef4444; }
        .status-offline { background: #6b7280; }
        .member-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        .member-role {
            color: var(--accent-primary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .member-department {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        .member-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .skill-tag {
            padding: 4px 10px;
            background: var(--bg-tertiary);
            border-radius: 20px;
            font-size: 0.75rem;
            color: var(--text-secondary);
        }
        .member-stats {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
        .member-stat {
            text-align: center;
        }
        .member-stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .member-stat-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }
        .member-actions {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .action-btn:hover {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
        }
        .team-list {
            display: none;
        }
        .team-list.active {
            display: block;
        }
        .team-grid.active {
            display: grid;
        }
        .list-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 100px;
            padding: 1rem 1.5rem;
            background: var(--bg-tertiary);
            border-radius: 12px 12px 0 0;
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        .list-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 100px;
            padding: 1rem 1.5rem;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            align-items: center;
            transition: background 0.2s;
        }
        .list-row:hover {
            background: var(--bg-tertiary);
        }
        .list-row:last-child {
            border-radius: 0 0 12px 12px;
        }
        .list-member {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .list-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        .org-chart {
            display: none;
            padding: 2rem;
        }
        .org-chart.active {
            display: block;
        }
        .org-level {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            position: relative;
        }
        .org-card {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            min-width: 150px;
            border: 2px solid var(--border-color);
        }
        .org-card.manager {
            border-color: var(--accent-primary);
        }
        .department-section {
            margin-bottom: 2rem;
        }
        .department-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent-primary);
            display: inline-block;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <main class="main-content">
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>

        <div class="page-header">
            <div>
                <h1 class="page-title">Mon Équipe</h1>
                <p class="page-subtitle">Découvrez les talents de votre organisation</p>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <button class="btn btn-secondary" onclick="toggleTheme()">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Inviter
                </button>
            </div>
        </div>

        <!-- Filtres et vue -->
        <div class="team-header">
            <div class="filters">
                <div class="search-box" style="min-width: 250px;">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher un collaborateur...">
                </div>
                <select class="filter-select" style="padding: 10px 15px; border-radius: 8px; background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    <option>Tous les départements</option>
                    <option>Développement</option>
                    <option>Design</option>
                    <option>Marketing</option>
                    <option>RH</option>
                </select>
            </div>
            <div class="view-toggle">
                <button class="view-btn active" onclick="showView('grid')">
                    <i class="fas fa-th-large"></i>
                </button>
                <button class="view-btn" onclick="showView('list')">
                    <i class="fas fa-list"></i>
                </button>
                <button class="view-btn" onclick="showView('org')">
                    <i class="fas fa-sitemap"></i>
                </button>
            </div>
        </div>

        <!-- Stats équipe -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">24</div>
                    <div class="stat-label">Membres</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">18</div>
                    <div class="stat-label">En ligne</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">5</div>
                    <div class="stat-label">Départements</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">156</div>
                    <div class="stat-label">Compétences uniques</div>
                </div>
            </div>
        </div>

        <!-- Vue Grille -->
        <div class="team-grid active" id="gridView">
            <div class="member-card">
                <div class="member-avatar">
                    SL
                    <span class="member-status status-online"></span>
                </div>
                <div class="member-name">Sophie Laurent</div>
                <div class="member-role">Lead Developer</div>
                <div class="member-department"><i class="fas fa-code"></i> Développement</div>
                <div class="member-skills">
                    <span class="skill-tag">React</span>
                    <span class="skill-tag">Node.js</span>
                    <span class="skill-tag">TypeScript</span>
                </div>
                <div class="member-stats">
                    <div class="member-stat">
                        <div class="member-stat-value">12</div>
                        <div class="member-stat-label">Projets</div>
                    </div>
                    <div class="member-stat">
                        <div class="member-stat-value">94%</div>
                        <div class="member-stat-label">Match</div>
                    </div>
                </div>
                <div class="member-actions">
                    <button class="action-btn" title="Message"><i class="fas fa-comment"></i></button>
                    <button class="action-btn" title="Profil"><i class="fas fa-user"></i></button>
                    <button class="action-btn" title="Email"><i class="fas fa-envelope"></i></button>
                </div>
            </div>

            <div class="member-card">
                <div class="member-avatar">
                    MD
                    <span class="member-status status-online"></span>
                </div>
                <div class="member-name">Marc Dubois</div>
                <div class="member-role">UX Designer</div>
                <div class="member-department"><i class="fas fa-paint-brush"></i> Design</div>
                <div class="member-skills">
                    <span class="skill-tag">Figma</span>
                    <span class="skill-tag">UI/UX</span>
                    <span class="skill-tag">Prototyping</span>
                </div>
                <div class="member-stats">
                    <div class="member-stat">
                        <div class="member-stat-value">8</div>
                        <div class="member-stat-label">Projets</div>
                    </div>
                    <div class="member-stat">
                        <div class="member-stat-value">87%</div>
                        <div class="member-stat-label">Match</div>
                    </div>
                </div>
                <div class="member-actions">
                    <button class="action-btn" title="Message"><i class="fas fa-comment"></i></button>
                    <button class="action-btn" title="Profil"><i class="fas fa-user"></i></button>
                    <button class="action-btn" title="Email"><i class="fas fa-envelope"></i></button>
                </div>
            </div>

            <div class="member-card">
                <div class="member-avatar">
                    JP
                    <span class="member-status status-away"></span>
                </div>
                <div class="member-name">Julie Petit</div>
                <div class="member-role">Product Manager</div>
                <div class="member-department"><i class="fas fa-briefcase"></i> Product</div>
                <div class="member-skills">
                    <span class="skill-tag">Agile</span>
                    <span class="skill-tag">Scrum</span>
                    <span class="skill-tag">Roadmap</span>
                </div>
                <div class="member-stats">
                    <div class="member-stat">
                        <div class="member-stat-value">15</div>
                        <div class="member-stat-label">Projets</div>
                    </div>
                    <div class="member-stat">
                        <div class="member-stat-value">92%</div>
                        <div class="member-stat-label">Match</div>
                    </div>
                </div>
                <div class="member-actions">
                    <button class="action-btn" title="Message"><i class="fas fa-comment"></i></button>
                    <button class="action-btn" title="Profil"><i class="fas fa-user"></i></button>
                    <button class="action-btn" title="Email"><i class="fas fa-envelope"></i></button>
                </div>
            </div>

            <div class="member-card">
                <div class="member-avatar">
                    TR
                    <span class="member-status status-busy"></span>
                </div>
                <div class="member-name">Thomas Richard</div>
                <div class="member-role">DevOps Engineer</div>
                <div class="member-department"><i class="fas fa-server"></i> Infrastructure</div>
                <div class="member-skills">
                    <span class="skill-tag">Docker</span>
                    <span class="skill-tag">Kubernetes</span>
                    <span class="skill-tag">AWS</span>
                </div>
                <div class="member-stats">
                    <div class="member-stat">
                        <div class="member-stat-value">10</div>
                        <div class="member-stat-label">Projets</div>
                    </div>
                    <div class="member-stat">
                        <div class="member-stat-value">89%</div>
                        <div class="member-stat-label">Match</div>
                    </div>
                </div>
                <div class="member-actions">
                    <button class="action-btn" title="Message"><i class="fas fa-comment"></i></button>
                    <button class="action-btn" title="Profil"><i class="fas fa-user"></i></button>
                    <button class="action-btn" title="Email"><i class="fas fa-envelope"></i></button>
                </div>
            </div>

            <div class="member-card">
                <div class="member-avatar">
                    EM
                    <span class="member-status status-online"></span>
                </div>
                <div class="member-name">Emma Martin</div>
                <div class="member-role">Data Scientist</div>
                <div class="member-department"><i class="fas fa-chart-bar"></i> Data</div>
                <div class="member-skills">
                    <span class="skill-tag">Python</span>
                    <span class="skill-tag">ML</span>
                    <span class="skill-tag">TensorFlow</span>
                </div>
                <div class="member-stats">
                    <div class="member-stat">
                        <div class="member-stat-value">6</div>
                        <div class="member-stat-label">Projets</div>
                    </div>
                    <div class="member-stat">
                        <div class="member-stat-value">95%</div>
                        <div class="member-stat-label">Match</div>
                    </div>
                </div>
                <div class="member-actions">
                    <button class="action-btn" title="Message"><i class="fas fa-comment"></i></button>
                    <button class="action-btn" title="Profil"><i class="fas fa-user"></i></button>
                    <button class="action-btn" title="Email"><i class="fas fa-envelope"></i></button>
                </div>
            </div>

            <div class="member-card">
                <div class="member-avatar">
                    LB
                    <span class="member-status status-offline"></span>
                </div>
                <div class="member-name">Lucas Bernard</div>
                <div class="member-role">Mobile Developer</div>
                <div class="member-department"><i class="fas fa-mobile-alt"></i> Mobile</div>
                <div class="member-skills">
                    <span class="skill-tag">React Native</span>
                    <span class="skill-tag">Swift</span>
                    <span class="skill-tag">Kotlin</span>
                </div>
                <div class="member-stats">
                    <div class="member-stat">
                        <div class="member-stat-value">7</div>
                        <div class="member-stat-label">Projets</div>
                    </div>
                    <div class="member-stat">
                        <div class="member-stat-value">88%</div>
                        <div class="member-stat-label">Match</div>
                    </div>
                </div>
                <div class="member-actions">
                    <button class="action-btn" title="Message"><i class="fas fa-comment"></i></button>
                    <button class="action-btn" title="Profil"><i class="fas fa-user"></i></button>
                    <button class="action-btn" title="Email"><i class="fas fa-envelope"></i></button>
                </div>
            </div>
        </div>

        <!-- Vue Liste -->
        <div class="team-list" id="listView">
            <div class="list-header">
                <div>Membre</div>
                <div>Rôle</div>
                <div>Département</div>
                <div>Statut</div>
                <div>Actions</div>
            </div>
            <div class="list-row">
                <div class="list-member">
                    <div class="list-avatar">SL</div>
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">Sophie Laurent</div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">sophie.laurent@company.com</div>
                    </div>
                </div>
                <div style="color: var(--accent-primary);">Lead Developer</div>
                <div style="color: var(--text-secondary);">Développement</div>
                <div><span class="badge badge-success">En ligne</span></div>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="action-btn"><i class="fas fa-comment"></i></button>
                    <button class="action-btn"><i class="fas fa-user"></i></button>
                </div>
            </div>
            <div class="list-row">
                <div class="list-member">
                    <div class="list-avatar">MD</div>
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">Marc Dubois</div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">marc.dubois@company.com</div>
                    </div>
                </div>
                <div style="color: var(--accent-primary);">UX Designer</div>
                <div style="color: var(--text-secondary);">Design</div>
                <div><span class="badge badge-success">En ligne</span></div>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="action-btn"><i class="fas fa-comment"></i></button>
                    <button class="action-btn"><i class="fas fa-user"></i></button>
                </div>
            </div>
            <div class="list-row">
                <div class="list-member">
                    <div class="list-avatar">JP</div>
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">Julie Petit</div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">julie.petit@company.com</div>
                    </div>
                </div>
                <div style="color: var(--accent-primary);">Product Manager</div>
                <div style="color: var(--text-secondary);">Product</div>
                <div><span class="badge badge-warning">Absent</span></div>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="action-btn"><i class="fas fa-comment"></i></button>
                    <button class="action-btn"><i class="fas fa-user"></i></button>
                </div>
            </div>
        </div>

        <!-- Vue Organigramme -->
        <div class="org-chart" id="orgView">
            <div class="department-section">
                <h3 class="department-title"><i class="fas fa-crown"></i> Direction</h3>
                <div class="org-level">
                    <div class="org-card manager">
                        <div class="list-avatar" style="margin: 0 auto 0.5rem; width: 50px; height: 50px; font-size: 1.25rem;">PD</div>
                        <div style="font-weight: 600; color: var(--text-primary);">Pierre Dupont</div>
                        <div style="font-size: 0.875rem; color: var(--accent-primary);">CEO</div>
                    </div>
                </div>
            </div>
            <div class="department-section">
                <h3 class="department-title"><i class="fas fa-code"></i> Développement</h3>
                <div class="org-level">
                    <div class="org-card manager">
                        <div class="list-avatar" style="margin: 0 auto 0.5rem;">SL</div>
                        <div style="font-weight: 600; color: var(--text-primary);">Sophie Laurent</div>
                        <div style="font-size: 0.875rem; color: var(--accent-primary);">Lead Developer</div>
                    </div>
                    <div class="org-card">
                        <div class="list-avatar" style="margin: 0 auto 0.5rem;">TR</div>
                        <div style="font-weight: 600; color: var(--text-primary);">Thomas Richard</div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">DevOps</div>
                    </div>
                    <div class="org-card">
                        <div class="list-avatar" style="margin: 0 auto 0.5rem;">LB</div>
                        <div style="font-weight: 600; color: var(--text-primary);">Lucas Bernard</div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">Mobile Dev</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
        
        function toggleMobileMenu() {
            document.querySelector('.sidebar').classList.toggle('open');
        }
        
        function showView(view) {
            document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
            event.target.closest('.view-btn').classList.add('active');
            
            document.getElementById('gridView').classList.remove('active');
            document.getElementById('listView').classList.remove('active');
            document.getElementById('orgView').classList.remove('active');
            
            if (view === 'grid') {
                document.getElementById('gridView').classList.add('active');
            } else if (view === 'list') {
                document.getElementById('listView').classList.add('active');
            } else if (view === 'org') {
                document.getElementById('orgView').classList.add('active');
            }
        }

        // Theme persistence
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</body>
</html>
