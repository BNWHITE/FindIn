<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
$currentPage = 'tests';
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userRole = $_SESSION['user_role'] ?? 'collaborateur';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tests de compétences - FindIN</title>
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="/assets/css/dashboard.css" rel="stylesheet">
    <style>
        .tests-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem; }
        .test-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; transition: all 0.3s; }
        .test-card:hover { transform: translateY(-5px); border-color: var(--accent-purple); }
        .test-header { padding: 1.5rem; background: linear-gradient(135deg, rgba(147, 51, 234, 0.1), rgba(59, 130, 246, 0.1)); }
        .test-category { font-size: 0.75rem; color: var(--accent-purple); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
        .test-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.25rem; }
        .test-desc { font-size: 0.85rem; color: var(--text-secondary); }
        .test-body { padding: 1.5rem; }
        .test-meta { display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 0.85rem; color: var(--text-secondary); }
        .test-difficulty { display: flex; gap: 0.25rem; }
        .test-difficulty span { width: 8px; height: 8px; border-radius: 50%; background: var(--border-color); }
        .test-difficulty.easy span:nth-child(1) { background: var(--accent-green); }
        .test-difficulty.medium span:nth-child(1), .test-difficulty.medium span:nth-child(2) { background: var(--accent-yellow); }
        .test-difficulty.hard span { background: var(--accent-red); }
        .test-progress { margin-bottom: 1rem; }
        .test-progress-label { display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.5rem; }
        .test-actions { display: flex; gap: 0.5rem; }
        .test-actions .btn { flex: 1; justify-content: center; }
        .test-completed { background: rgba(16, 185, 129, 0.1); border-color: var(--accent-green); }
        .test-completed .test-header { background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05)); }
        .score-badge { display: inline-flex; align-items: center; gap: 0.35rem; background: var(--accent-green); color: white; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .leaderboard { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; margin-top: 2rem; }
        .leaderboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .leaderboard-item { display: flex; align-items: center; gap: 1rem; padding: 0.75rem; border-radius: 10px; margin-bottom: 0.5rem; }
        .leaderboard-item:hover { background: var(--bg-hover); }
        .leaderboard-rank { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; }
        .rank-1 { background: linear-gradient(135deg, #ffd700, #ffb800); color: #000; }
        .rank-2 { background: linear-gradient(135deg, #c0c0c0, #a0a0a0); color: #000; }
        .rank-3 { background: linear-gradient(135deg, #cd7f32, #b87333); color: white; }
        .leaderboard-user { flex: 1; }
        .leaderboard-user h4 { font-size: 0.95rem; margin-bottom: 0.1rem; }
        .leaderboard-user span { font-size: 0.8rem; color: var(--text-secondary); }
        .leaderboard-score { font-weight: 700; color: var(--accent-purple); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/_sidebar.php'; ?>
    <main class="main-content">
        <div class="page-header">
            <div><h1 class="page-title"><i class="fas fa-clipboard-check"></i> Tests de compétences</h1><p class="page-subtitle">Évaluez et certifiez vos compétences techniques</p></div>
            <div class="header-actions">
                <button class="btn btn-primary"><i class="fas fa-play"></i> Nouveau test</button>
                <button class="mobile-menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon"></i></button>
            </div>
        </div>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-tasks"></i></div><div class="stat-value">12</div><div class="stat-label">Tests complétés</div></div>
            <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-trophy"></i></div><div class="stat-value">85%</div><div class="stat-label">Score moyen</div></div>
            <div class="stat-card"><div class="stat-icon green"><i class="fas fa-medal"></i></div><div class="stat-value">5</div><div class="stat-label">Badges obtenus</div></div>
            <div class="stat-card"><div class="stat-icon yellow"><i class="fas fa-clock"></i></div><div class="stat-value">3</div><div class="stat-label">Tests en attente</div></div>
        </div>
        <div class="filters" style="margin-bottom: 1.5rem;">
            <button class="filter-btn active">Tous</button>
            <button class="filter-btn">Programmation</button>
            <button class="filter-btn">DevOps</button>
            <button class="filter-btn">Base de données</button>
            <button class="filter-btn">Soft Skills</button>
        </div>
        <div class="tests-grid">
            <div class="test-card test-completed">
                <div class="test-header"><div class="test-category">Programmation</div><div class="test-title">PHP Avancé</div><div class="test-desc">POO, Design Patterns, Sécurité</div></div>
                <div class="test-body">
                    <div class="test-meta"><span><i class="fas fa-clock"></i> 45 min</span><span><i class="fas fa-question-circle"></i> 30 questions</span><div class="test-difficulty medium"><span></span><span></span><span></span></div></div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="score-badge"><i class="fas fa-check"></i> 92% réussi</span>
                        <button class="btn btn-outline btn-sm"><i class="fas fa-redo"></i> Refaire</button>
                    </div>
                </div>
            </div>
            <div class="test-card">
                <div class="test-header"><div class="test-category">Programmation</div><div class="test-title">JavaScript ES6+</div><div class="test-desc">Async/Await, Modules, Classes</div></div>
                <div class="test-body">
                    <div class="test-meta"><span><i class="fas fa-clock"></i> 30 min</span><span><i class="fas fa-question-circle"></i> 25 questions</span><div class="test-difficulty easy"><span></span><span></span><span></span></div></div>
                    <div class="test-progress"><div class="test-progress-label"><span>Progression</span><span>60%</span></div><div class="progress-bar"><div class="progress-fill" style="width: 60%;"></div></div></div>
                    <div class="test-actions"><button class="btn btn-primary btn-sm"><i class="fas fa-play"></i> Continuer</button></div>
                </div>
            </div>
            <div class="test-card">
                <div class="test-header"><div class="test-category">DevOps</div><div class="test-title">Docker & Kubernetes</div><div class="test-desc">Conteneurs, Orchestration, CI/CD</div></div>
                <div class="test-body">
                    <div class="test-meta"><span><i class="fas fa-clock"></i> 60 min</span><span><i class="fas fa-question-circle"></i> 40 questions</span><div class="test-difficulty hard"><span></span><span></span><span></span></div></div>
                    <div class="test-actions"><button class="btn btn-primary btn-sm"><i class="fas fa-play"></i> Commencer</button><button class="btn btn-outline btn-sm"><i class="fas fa-info-circle"></i></button></div>
                </div>
            </div>
            <div class="test-card">
                <div class="test-header"><div class="test-category">Base de données</div><div class="test-title">SQL Avancé</div><div class="test-desc">Jointures, Index, Optimisation</div></div>
                <div class="test-body">
                    <div class="test-meta"><span><i class="fas fa-clock"></i> 40 min</span><span><i class="fas fa-question-circle"></i> 35 questions</span><div class="test-difficulty medium"><span></span><span></span><span></span></div></div>
                    <div class="test-actions"><button class="btn btn-primary btn-sm"><i class="fas fa-play"></i> Commencer</button><button class="btn btn-outline btn-sm"><i class="fas fa-info-circle"></i></button></div>
                </div>
            </div>
        </div>
        <div class="leaderboard">
            <div class="leaderboard-header"><h3><i class="fas fa-trophy"></i> Classement équipe</h3><span class="badge badge-purple">Ce mois</span></div>
            <div class="leaderboard-item"><div class="leaderboard-rank rank-1">1</div><div class="avatar">ML</div><div class="leaderboard-user"><h4>Marie Lambert</h4><span>15 tests - 94% moyen</span></div><div class="leaderboard-score">1420 pts</div></div>
            <div class="leaderboard-item"><div class="leaderboard-rank rank-2">2</div><div class="avatar">TD</div><div class="leaderboard-user"><h4>Thomas Durand</h4><span>12 tests - 89% moyen</span></div><div class="leaderboard-score">1180 pts</div></div>
            <div class="leaderboard-item"><div class="leaderboard-rank rank-3">3</div><div class="avatar">SB</div><div class="leaderboard-user"><h4>Sophie Bernard</h4><span>10 tests - 91% moyen</span></div><div class="leaderboard-score">1050 pts</div></div>
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
