<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
$currentPage = 'projets';
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userRole = $_SESSION['user_role'] ?? 'collaborateur';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets - FindIN</title>
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="/assets/css/dashboard.css" rel="stylesheet">
    <style>
        .projects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; }
        .project-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; transition: all 0.3s; }
        .project-card:hover { transform: translateY(-5px); border-color: var(--accent-purple); }
        .project-header { padding: 1.5rem; border-bottom: 1px solid var(--border-color); }
        .project-status { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; margin-bottom: 0.75rem; }
        .status-active { background: rgba(16, 185, 129, 0.15); color: var(--accent-green); }
        .status-planning { background: rgba(59, 130, 246, 0.15); color: var(--accent-blue); }
        .status-onhold { background: rgba(245, 158, 11, 0.15); color: var(--accent-yellow); }
        .status-completed { background: rgba(147, 51, 234, 0.15); color: var(--accent-purple); }
        .project-title { font-size: 1.15rem; font-weight: 600; margin-bottom: 0.35rem; }
        .project-desc { font-size: 0.85rem; color: var(--text-secondary); }
        .project-body { padding: 1.5rem; }
        .project-meta { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem; }
        .meta-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-secondary); }
        .meta-item i { color: var(--accent-purple); width: 16px; }
        .project-team { margin-bottom: 1rem; }
        .project-team-label { font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.5rem; }
        .project-progress { margin-bottom: 1rem; }
        .project-progress-header { display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.5rem; }
        .project-actions { display: flex; gap: 0.5rem; }
        .project-actions .btn { flex: 1; justify-content: center; }
        .kanban-view { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-top: 2rem; }
        @media (max-width: 1200px) { .kanban-view { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .kanban-view { grid-template-columns: 1fr; } }
        .kanban-column { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem; }
        .kanban-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color); }
        .kanban-title { font-weight: 600; display: flex; align-items: center; gap: 0.5rem; }
        .kanban-count { background: var(--bg-primary); padding: 0.2rem 0.5rem; border-radius: 10px; font-size: 0.75rem; }
        .kanban-task { background: var(--bg-primary); border-radius: 10px; padding: 1rem; margin-bottom: 0.75rem; cursor: pointer; transition: all 0.2s; }
        .kanban-task:hover { transform: translateX(3px); border-left: 3px solid var(--accent-purple); }
        .kanban-task h4 { font-size: 0.9rem; margin-bottom: 0.5rem; }
        .kanban-task p { font-size: 0.8rem; color: var(--text-secondary); }
        .kanban-task-meta { display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem; font-size: 0.75rem; color: var(--text-secondary); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/_sidebar.php'; ?>
    <main class="main-content">
        <div class="page-header">
            <div><h1 class="page-title"><i class="fas fa-project-diagram"></i> Projets</h1><p class="page-subtitle">Gérez et suivez vos projets d'équipe</p></div>
            <div class="header-actions">
                <button class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau projet</button>
                <button class="mobile-menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon"></i></button>
            </div>
        </div>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-folder-open"></i></div><div class="stat-value">8</div><div class="stat-label">Projets actifs</div></div>
            <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-tasks"></i></div><div class="stat-value">45</div><div class="stat-label">Tâches totales</div></div>
            <div class="stat-card"><div class="stat-icon green"><i class="fas fa-check-circle"></i></div><div class="stat-value">32</div><div class="stat-label">Tâches terminées</div></div>
            <div class="stat-card"><div class="stat-icon yellow"><i class="fas fa-users"></i></div><div class="stat-value">12</div><div class="stat-label">Membres impliqués</div></div>
        </div>
        <div class="filters" style="margin-bottom: 1.5rem;">
            <button class="filter-btn active">Tous</button>
            <button class="filter-btn">En cours</button>
            <button class="filter-btn">Planification</button>
            <button class="filter-btn">Terminés</button>
        </div>
        <div class="projects-grid">
            <div class="project-card">
                <div class="project-header"><span class="project-status status-active"><i class="fas fa-circle"></i> En cours</span><h3 class="project-title">Refonte API REST</h3><p class="project-desc">Migration vers une architecture microservices</p></div>
                <div class="project-body">
                    <div class="project-meta"><div class="meta-item"><i class="fas fa-calendar"></i> 15 Jan 2025</div><div class="meta-item"><i class="fas fa-clock"></i> 45 jours restants</div></div>
                    <div class="project-team"><div class="project-team-label">Équipe (5)</div><div class="avatar-group"><div class="avatar avatar-sm">JD</div><div class="avatar avatar-sm">ML</div><div class="avatar avatar-sm">TD</div><div class="avatar avatar-sm">+2</div></div></div>
                    <div class="project-progress"><div class="project-progress-header"><span>Progression</span><span>68%</span></div><div class="progress-bar"><div class="progress-fill" style="width: 68%;"></div></div></div>
                    <div class="project-actions"><button class="btn btn-outline btn-sm"><i class="fas fa-eye"></i> Voir</button><button class="btn btn-outline btn-sm"><i class="fas fa-edit"></i></button></div>
                </div>
            </div>
            <div class="project-card">
                <div class="project-header"><span class="project-status status-planning"><i class="fas fa-circle"></i> Planification</span><h3 class="project-title">Dashboard Analytics</h3><p class="project-desc">Nouveau tableau de bord avec visualisations</p></div>
                <div class="project-body">
                    <div class="project-meta"><div class="meta-item"><i class="fas fa-calendar"></i> 1 Fév 2025</div><div class="meta-item"><i class="fas fa-clock"></i> 60 jours restants</div></div>
                    <div class="project-team"><div class="project-team-label">Équipe (3)</div><div class="avatar-group"><div class="avatar avatar-sm">SB</div><div class="avatar avatar-sm">PL</div><div class="avatar avatar-sm">AR</div></div></div>
                    <div class="project-progress"><div class="project-progress-header"><span>Progression</span><span>15%</span></div><div class="progress-bar"><div class="progress-fill" style="width: 15%;"></div></div></div>
                    <div class="project-actions"><button class="btn btn-outline btn-sm"><i class="fas fa-eye"></i> Voir</button><button class="btn btn-outline btn-sm"><i class="fas fa-edit"></i></button></div>
                </div>
            </div>
            <div class="project-card">
                <div class="project-header"><span class="project-status status-completed"><i class="fas fa-check"></i> Terminé</span><h3 class="project-title">App Mobile v2</h3><p class="project-desc">Nouvelle version de l'application mobile</p></div>
                <div class="project-body">
                    <div class="project-meta"><div class="meta-item"><i class="fas fa-calendar"></i> 30 Nov 2024</div><div class="meta-item"><i class="fas fa-check"></i> Livré</div></div>
                    <div class="project-team"><div class="project-team-label">Équipe (4)</div><div class="avatar-group"><div class="avatar avatar-sm">ML</div><div class="avatar avatar-sm">TD</div><div class="avatar avatar-sm">JD</div><div class="avatar avatar-sm">+1</div></div></div>
                    <div class="project-progress"><div class="project-progress-header"><span>Progression</span><span>100%</span></div><div class="progress-bar"><div class="progress-fill" style="width: 100%;"></div></div></div>
                    <div class="project-actions"><button class="btn btn-outline btn-sm"><i class="fas fa-eye"></i> Voir</button><button class="btn btn-outline btn-sm"><i class="fas fa-archive"></i></button></div>
                </div>
            </div>
        </div>
        <h3 style="margin: 2rem 0 1rem; display: flex; align-items: center; gap: 0.5rem;"><i class="fas fa-columns" style="color: var(--accent-purple);"></i> Vue Kanban - Refonte API</h3>
        <div class="kanban-view">
            <div class="kanban-column"><div class="kanban-header"><span class="kanban-title">À faire</span><span class="kanban-count">3</span></div><div class="kanban-task"><h4>Documentation API</h4><p>Rédiger la doc OpenAPI</p><div class="kanban-task-meta"><span><i class="fas fa-user"></i> JD</span><span>2j</span></div></div><div class="kanban-task"><h4>Tests unitaires</h4><p>Couverture 80%</p><div class="kanban-task-meta"><span><i class="fas fa-user"></i> ML</span><span>3j</span></div></div></div>
            <div class="kanban-column"><div class="kanban-header"><span class="kanban-title">En cours</span><span class="kanban-count">2</span></div><div class="kanban-task"><h4>Endpoint Users</h4><p>CRUD utilisateurs</p><div class="kanban-task-meta"><span><i class="fas fa-user"></i> TD</span><span>1j</span></div></div></div>
            <div class="kanban-column"><div class="kanban-header"><span class="kanban-title">Review</span><span class="kanban-count">1</span></div><div class="kanban-task"><h4>Auth JWT</h4><p>Implémentation OAuth2</p><div class="kanban-task-meta"><span><i class="fas fa-user"></i> SB</span><span>0j</span></div></div></div>
            <div class="kanban-column"><div class="kanban-header"><span class="kanban-title">Terminé</span><span class="kanban-count">5</span></div><div class="kanban-task"><h4>Setup Docker</h4><p>Conteneurisation</p><div class="kanban-task-meta"><span><i class="fas fa-check"></i></span><span>Fait</span></div></div></div>
        </div>
    </main>
    <script>
        function toggleTheme(){const h=document.documentElement,n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);document.querySelector('.theme-toggle i').className=n==='dark'?'fas fa-moon':'fas fa-sun';}
        const t=localStorage.getItem('theme')||'dark';document.documentElement.setAttribute('data-theme',t);document.querySelector('.theme-toggle i').className=t==='dark'?'fas fa-moon':'fas fa-sun';
        function toggleSidebar(){document.querySelector('.sidebar').classList.toggle('open');}
        document.querySelectorAll('.filter-btn').forEach(btn => btn.addEventListener('click', () => { document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active')); btn.classList.add('active'); }));
    </script>
</body>
</html>
