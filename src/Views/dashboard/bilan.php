<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
$currentPage = 'bilan';
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userRole = $_SESSION['user_role'] ?? 'collaborateur';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilan annuel - FindIN</title>
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="/assets/css/dashboard.css" rel="stylesheet">
    <style>
        .bilan-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
        @media (max-width: 1024px) { .bilan-grid { grid-template-columns: 1fr; } }
        .bilan-section { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; }
        .bilan-section h3 { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; font-size: 1.1rem; }
        .bilan-section h3 i { color: var(--accent-purple); }
        .objective-item { background: var(--bg-primary); border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; }
        .objective-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
        .objective-title { font-weight: 600; }
        .objective-status { font-size: 0.75rem; padding: 0.25rem 0.6rem; border-radius: 20px; }
        .status-completed { background: rgba(16, 185, 129, 0.15); color: var(--accent-green); }
        .status-progress { background: rgba(59, 130, 246, 0.15); color: var(--accent-blue); }
        .status-pending { background: rgba(245, 158, 11, 0.15); color: var(--accent-yellow); }
        .objective-progress { margin-top: 0.5rem; }
        .objective-progress-text { display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.35rem; }
        .rating { display: flex; gap: 0.25rem; }
        .rating i { color: var(--accent-yellow); }
        .rating i.empty { color: var(--border-color); }
        .skill-evolution { display: flex; align-items: center; gap: 1rem; padding: 0.75rem; background: var(--bg-primary); border-radius: 10px; margin-bottom: 0.5rem; }
        .skill-name { flex: 1; font-weight: 500; }
        .skill-change { display: flex; align-items: center; gap: 0.25rem; font-size: 0.85rem; }
        .skill-change.positive { color: var(--accent-green); }
        .skill-change.negative { color: var(--accent-red); }
        .feedback-card { background: var(--bg-primary); border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; border-left: 3px solid var(--accent-purple); }
        .feedback-author { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; }
        .feedback-author h4 { font-size: 0.9rem; }
        .feedback-author span { font-size: 0.75rem; color: var(--text-secondary); }
        .feedback-text { font-size: 0.9rem; color: var(--text-secondary); font-style: italic; }
        .chart-placeholder { height: 200px; background: var(--bg-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--text-secondary); }
        .summary-card { background: linear-gradient(135deg, rgba(147, 51, 234, 0.15), rgba(59, 130, 246, 0.15)); border: 1px solid rgba(147, 51, 234, 0.3); border-radius: 16px; padding: 1.5rem; text-align: center; }
        .summary-score { font-size: 4rem; font-weight: 800; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .summary-label { color: var(--text-secondary); margin-bottom: 1rem; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/_sidebar.php'; ?>
    <main class="main-content">
        <div class="page-header">
            <div><h1 class="page-title"><i class="fas fa-chart-line"></i> Bilan annuel 2024</h1><p class="page-subtitle">Synthèse de votre progression et objectifs</p></div>
            <div class="header-actions">
                <button class="btn btn-primary"><i class="fas fa-download"></i> Exporter PDF</button>
                <button class="mobile-menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon"></i></button>
            </div>
        </div>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-bullseye"></i></div><div class="stat-value">8/10</div><div class="stat-label">Objectifs atteints</div></div>
            <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-graduation-cap"></i></div><div class="stat-value">+5</div><div class="stat-label">Nouvelles compétences</div></div>
            <div class="stat-card"><div class="stat-icon green"><i class="fas fa-certificate"></i></div><div class="stat-value">3</div><div class="stat-label">Certifications</div></div>
            <div class="stat-card"><div class="stat-icon yellow"><i class="fas fa-star"></i></div><div class="stat-value">4.2/5</div><div class="stat-label">Évaluation globale</div></div>
        </div>
        <div class="bilan-grid">
            <div>
                <div class="bilan-section">
                    <h3><i class="fas fa-bullseye"></i> Objectifs 2024</h3>
                    <div class="objective-item"><div class="objective-header"><span class="objective-title">Maîtriser React.js</span><span class="objective-status status-completed"><i class="fas fa-check"></i> Atteint</span></div><div class="objective-progress"><div class="objective-progress-text"><span>Progression</span><span>100%</span></div><div class="progress-bar"><div class="progress-fill" style="width: 100%;"></div></div></div></div>
                    <div class="objective-item"><div class="objective-header"><span class="objective-title">Certification AWS</span><span class="objective-status status-completed"><i class="fas fa-check"></i> Atteint</span></div><div class="objective-progress"><div class="objective-progress-text"><span>Progression</span><span>100%</span></div><div class="progress-bar"><div class="progress-fill" style="width: 100%;"></div></div></div></div>
                    <div class="objective-item"><div class="objective-header"><span class="objective-title">Leadership d'équipe</span><span class="objective-status status-progress"><i class="fas fa-spinner"></i> En cours</span></div><div class="objective-progress"><div class="objective-progress-text"><span>Progression</span><span>75%</span></div><div class="progress-bar"><div class="progress-fill" style="width: 75%;"></div></div></div></div>
                </div>
                <div class="bilan-section">
                    <h3><i class="fas fa-chart-bar"></i> Évolution des compétences</h3>
                    <div class="skill-evolution"><span class="skill-name">React.js</span><div class="rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star empty"></i></div><span class="skill-change positive"><i class="fas fa-arrow-up"></i> +2</span></div>
                    <div class="skill-evolution"><span class="skill-name">Node.js</span><div class="rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star empty"></i></div><span class="skill-change positive"><i class="fas fa-arrow-up"></i> +1</span></div>
                    <div class="skill-evolution"><span class="skill-name">AWS</span><div class="rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star empty"></i><i class="fas fa-star empty"></i></div><span class="skill-change positive"><i class="fas fa-arrow-up"></i> +3</span></div>
                    <div class="skill-evolution"><span class="skill-name">Docker</span><div class="rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star empty"></i><i class="fas fa-star empty"></i></div><span class="skill-change positive"><i class="fas fa-arrow-up"></i> +1</span></div>
                </div>
            </div>
            <div>
                <div class="summary-card"><div class="summary-score">A</div><div class="summary-label">Évaluation globale</div><button class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Voir détails</button></div>
                <div class="bilan-section" style="margin-top: 1.5rem;">
                    <h3><i class="fas fa-comments"></i> Feedbacks reçus</h3>
                    <div class="feedback-card"><div class="feedback-author"><div class="avatar avatar-sm">ML</div><div><h4>Manager</h4><span>Nov 2024</span></div></div><div class="feedback-text">"Excellent travail sur le projet React. Leadership naturel et bonne communication avec l'équipe."</div></div>
                    <div class="feedback-card"><div class="feedback-author"><div class="avatar avatar-sm">TD</div><div><h4>Pair</h4><span>Oct 2024</span></div></div><div class="feedback-text">"Toujours disponible pour aider. Explications claires et patience appréciée."</div></div>
                </div>
            </div>
        </div>
    </main>
    <script>
        function toggleTheme(){const h=document.documentElement,n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);document.querySelector('.theme-toggle i').className=n==='dark'?'fas fa-moon':'fas fa-sun';}
        const t=localStorage.getItem('theme')||'dark';document.documentElement.setAttribute('data-theme',t);document.querySelector('.theme-toggle i').className=t==='dark'?'fas fa-moon':'fas fa-sun';
        function toggleSidebar(){document.querySelector('.sidebar').classList.toggle('open');}
    </script>
</body>
</html>
