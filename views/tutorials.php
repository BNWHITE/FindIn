<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Tutoriels</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        :root { --bg-dark: #0a0118; --bg-card: #1a0d2e; --text-white: #ffffff; --text-light: #e0e0e0; --accent-primary: #9333ea; --accent-blue: #3b82f6; --accent-green: #10b981; --border-light: rgba(255,255,255,0.1); }
        [data-theme="light"] { --bg-dark: #f8fafc; --bg-card: #ffffff; --text-white: #1e293b; --text-light: #475569; --border-light: rgba(0,0,0,0.1); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0a0118 0%, #1a0d2e 100%); color: var(--text-white); min-height: 100vh; }
        [data-theme="light"] body { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); }
        .orb-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; }
        .orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.3; }
        [data-theme="light"] .orb { opacity: 0.15; }
        .orb-1 { width: 350px; height: 350px; background: radial-gradient(circle, #10b981, transparent 70%); top: 10%; right: -50px; animation: float1 22s ease-in-out infinite; }
        .orb-2 { width: 400px; height: 400px; background: radial-gradient(circle, #9333ea, transparent 70%); bottom: -100px; left: 10%; animation: float2 18s ease-in-out infinite; }
        @keyframes float1 { 0%, 100% { transform: translate(0,0); } 50% { transform: translate(-60px, 80px); } }
        @keyframes float2 { 0%, 100% { transform: translate(0,0); } 50% { transform: translate(40px, -60px); } }
        .content-wrapper { position: relative; z-index: 1; }
        header { background: rgba(10,1,24,0.8); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border-light); padding: 1rem 2rem; position: sticky; top: 0; z-index: 100; }
        [data-theme="light"] header { background: rgba(255,255,255,0.9); }
        .header-container { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: var(--text-white); font-weight: 700; font-size: 1.5rem; }
        .logo-icon { width: 40px; height: 40px; background: linear-gradient(135deg, #9333ea, #3b82f6); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; }
        nav { display: flex; gap: 2rem; align-items: center; }
        nav a { color: var(--text-light); text-decoration: none; font-weight: 500; transition: color 0.3s; }
        nav a:hover { color: var(--accent-primary); }
        .theme-toggle { background: rgba(255,255,255,0.1); border: 1px solid var(--border-light); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-light); transition: all 0.3s; }
        .theme-toggle:hover { background: rgba(147,51,234,0.2); color: var(--accent-primary); }
        .page-hero { min-height: 35vh; display: flex; align-items: center; justify-content: center; padding: 4rem 2rem; text-align: center; }
        .page-hero h1 { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 800; background: linear-gradient(135deg, #fff, #e0e0e0); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 1rem; }
        [data-theme="light"] .page-hero h1 { background: linear-gradient(135deg, #1e293b, #334155); -webkit-background-clip: text; }
        .page-hero p { font-size: 1.2rem; color: var(--text-light); max-width: 600px; margin: 0 auto; }
        .tutorials-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .level-tabs { display: flex; justify-content: center; gap: 1rem; margin-bottom: 3rem; flex-wrap: wrap; }
        .level-tab { padding: 0.75rem 1.5rem; background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 30px; color: var(--text-light); cursor: pointer; transition: all 0.3s; font-weight: 500; }
        .level-tab:hover, .level-tab.active { background: var(--accent-primary); color: white; border-color: var(--accent-primary); }
        .tutorials-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; }
        .tutorial-card { background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 20px; overflow: hidden; transition: all 0.3s; }
        .tutorial-card:hover { border-color: var(--accent-primary); transform: translateY(-8px); box-shadow: 0 20px 40px rgba(147,51,234,0.2); }
        .tutorial-header { background: linear-gradient(135deg, rgba(147,51,234,0.2), rgba(16,185,129,0.2)); padding: 2rem; position: relative; }
        .tutorial-header i { font-size: 3rem; color: var(--accent-primary); }
        .tutorial-level { position: absolute; top: 1rem; right: 1rem; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .level-beginner { background: #10b981; color: white; }
        .level-intermediate { background: #f59e0b; color: white; }
        .level-advanced { background: #ef4444; color: white; }
        .tutorial-body { padding: 1.5rem; }
        .tutorial-body h3 { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.75rem; }
        .tutorial-body p { color: var(--text-light); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1rem; }
        .tutorial-meta { display: flex; gap: 1.5rem; margin-bottom: 1rem; font-size: 0.85rem; color: var(--text-light); }
        .tutorial-meta span { display: flex; align-items: center; gap: 0.4rem; }
        .tutorial-progress { background: rgba(255,255,255,0.1); border-radius: 10px; height: 6px; overflow: hidden; margin-bottom: 1rem; }
        .tutorial-progress-bar { height: 100%; background: linear-gradient(90deg, var(--accent-primary), var(--accent-green)); border-radius: 10px; }
        .btn-start { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #9333ea, #3b82f6); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; }
        .btn-start:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(147,51,234,0.3); }
        footer { background: rgba(10,1,24,0.8); border-top: 1px solid var(--border-light); padding: 2rem; text-align: center; margin-top: 4rem; }
        [data-theme="light"] footer { background: rgba(255,255,255,0.9); }
        footer p { color: var(--text-light); }
    </style>
</head>
<body>
    <div class="orb-container"><div class="orb orb-1"></div><div class="orb orb-2"></div></div>
    <div class="content-wrapper">
        <header>
            <div class="header-container">
                <a href="/" class="logo"><div class="logo-icon">F</div><span>FindIN</span></a>
                <nav>
                    <a href="/">Accueil</a>
                    <a href="/features">Fonctionnalités</a>
                    <a href="/tutorials">Tutoriels</a>
                    <a href="/documentation">Docs</a>
                    <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon theme-icon"></i></button>
                </nav>
            </div>
        </header>
        <section class="page-hero">
            <div>
                <h1><i class="fas fa-graduation-cap"></i> Tutoriels</h1>
                <p>Apprenez à maîtriser FindIN avec nos guides pas à pas, du débutant à l'expert.</p>
            </div>
        </section>
        <div class="tutorials-container">
            <div class="level-tabs">
                <button class="level-tab active">Tous</button>
                <button class="level-tab">🌱 Débutant</button>
                <button class="level-tab">🚀 Intermédiaire</button>
                <button class="level-tab">⚡ Avancé</button>
            </div>
            <div class="tutorials-grid">
                <div class="tutorial-card" data-level="beginner">
                    <div class="tutorial-header">
                        <i class="fas fa-play-circle"></i>
                        <span class="tutorial-level level-beginner">Débutant</span>
                    </div>
                    <div class="tutorial-body">
                        <h3>Premiers pas avec FindIN</h3>
                        <p>Découvrez l'interface et créez votre premier profil de compétences en quelques minutes.</p>
                        <div class="tutorial-meta">
                            <span><i class="fas fa-clock"></i> 15 min</span>
                            <span><i class="fas fa-video"></i> 5 vidéos</span>
                        </div>
                        <div class="tutorial-progress"><div class="tutorial-progress-bar" style="width: 0%"></div></div>
                        <a href="#" class="btn-start"><i class="fas fa-play"></i> Commencer</a>
                    </div>
                </div>
                <div class="tutorial-card" data-level="beginner">
                    <div class="tutorial-header">
                        <i class="fas fa-user-plus"></i>
                        <span class="tutorial-level level-beginner">Débutant</span>
                    </div>
                    <div class="tutorial-body">
                        <h3>Gérer les utilisateurs</h3>
                        <p>Apprenez à ajouter, modifier et organiser les utilisateurs de votre organisation.</p>
                        <div class="tutorial-meta">
                            <span><i class="fas fa-clock"></i> 20 min</span>
                            <span><i class="fas fa-video"></i> 6 vidéos</span>
                        </div>
                        <div class="tutorial-progress"><div class="tutorial-progress-bar" style="width: 30%"></div></div>
                        <a href="#" class="btn-start"><i class="fas fa-play"></i> Continuer</a>
                    </div>
                </div>
                <div class="tutorial-card" data-level="intermediate">
                    <div class="tutorial-header">
                        <i class="fas fa-search-plus"></i>
                        <span class="tutorial-level level-intermediate">Intermédiaire</span>
                    </div>
                    <div class="tutorial-body">
                        <h3>Recherche avancée</h3>
                        <p>Maîtrisez les filtres et opérateurs pour trouver exactement les profils dont vous avez besoin.</p>
                        <div class="tutorial-meta">
                            <span><i class="fas fa-clock"></i> 25 min</span>
                            <span><i class="fas fa-video"></i> 8 vidéos</span>
                        </div>
                        <div class="tutorial-progress"><div class="tutorial-progress-bar" style="width: 0%"></div></div>
                        <a href="#" class="btn-start"><i class="fas fa-play"></i> Commencer</a>
                    </div>
                </div>
                <div class="tutorial-card" data-level="intermediate">
                    <div class="tutorial-header">
                        <i class="fas fa-chart-bar"></i>
                        <span class="tutorial-level level-intermediate">Intermédiaire</span>
                    </div>
                    <div class="tutorial-body">
                        <h3>Tableaux de bord et rapports</h3>
                        <p>Créez des visualisations personnalisées et exportez des rapports détaillés.</p>
                        <div class="tutorial-meta">
                            <span><i class="fas fa-clock"></i> 30 min</span>
                            <span><i class="fas fa-video"></i> 10 vidéos</span>
                        </div>
                        <div class="tutorial-progress"><div class="tutorial-progress-bar" style="width: 60%"></div></div>
                        <a href="#" class="btn-start"><i class="fas fa-play"></i> Continuer</a>
                    </div>
                </div>
                <div class="tutorial-card" data-level="advanced">
                    <div class="tutorial-header">
                        <i class="fas fa-code"></i>
                        <span class="tutorial-level level-advanced">Avancé</span>
                    </div>
                    <div class="tutorial-body">
                        <h3>Intégration API</h3>
                        <p>Connectez FindIN à vos outils existants via notre API RESTful complète.</p>
                        <div class="tutorial-meta">
                            <span><i class="fas fa-clock"></i> 45 min</span>
                            <span><i class="fas fa-video"></i> 12 vidéos</span>
                        </div>
                        <div class="tutorial-progress"><div class="tutorial-progress-bar" style="width: 0%"></div></div>
                        <a href="#" class="btn-start"><i class="fas fa-play"></i> Commencer</a>
                    </div>
                </div>
                <div class="tutorial-card" data-level="advanced">
                    <div class="tutorial-header">
                        <i class="fas fa-cogs"></i>
                        <span class="tutorial-level level-advanced">Avancé</span>
                    </div>
                    <div class="tutorial-body">
                        <h3>Automatisation et workflows</h3>
                        <p>Automatisez les tâches répétitives et créez des workflows personnalisés.</p>
                        <div class="tutorial-meta">
                            <span><i class="fas fa-clock"></i> 40 min</span>
                            <span><i class="fas fa-video"></i> 9 vidéos</span>
                        </div>
                        <div class="tutorial-progress"><div class="tutorial-progress-bar" style="width: 0%"></div></div>
                        <a href="#" class="btn-start"><i class="fas fa-play"></i> Commencer</a>
                    </div>
                </div>
            </div>
        </div>
        <footer><p>&copy; 2025 FindIN. Tous droits réservés.</p></footer>
    </div>
    <script src="/assets/js/main.js"></script>
        <footer><p>© 2025 FindIN. Tous droits réservés.</p></footer>
</div>

<script src="/assets/js/main.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.level-tab');
    const cards = document.querySelectorAll('.tutorial-card');

    function applyFilter(filter) {
        cards.forEach(card => {
            if (filter === 'all' || card.dataset.level === filter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const text = tab.textContent.toLowerCase();

            let filter = 'all';
            if (text.includes('tous')) filter = 'all';
            else if (text.includes('débutant')) filter = 'beginner';
            else if (text.includes('intermédiaire')) filter = 'intermediate';
            else if (text.includes('avancé')) filter = 'advanced';

            applyFilter(filter);
        });
    });

    applyFilter('all');
});
</script>
</body>
</html>
