<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roadmap - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg-primary: #0a0118; --bg-secondary: #1a0d2e; --bg-card: #241538; --text-primary: #ffffff; --text-secondary: #a0a0a0; --accent-purple: #9333ea; --accent-blue: #3b82f6; --accent-green: #10b981; --accent-yellow: #f59e0b; --border-color: rgba(255,255,255,0.1); }
        [data-theme="light"] { --bg-primary: #f8fafc; --bg-secondary: #ffffff; --bg-card: #ffffff; --text-primary: #1e293b; --text-secondary: #64748b; --border-color: rgba(0,0,0,0.1); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); line-height: 1.8; }
        .header { background: var(--bg-secondary); border-bottom: 1px solid var(--border-color); padding: 1rem 2rem; position: sticky; top: 0; z-index: 100; }
        .header-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: var(--text-primary); font-weight: 700; font-size: 1.5rem; }
        .logo-icon { width: 40px; height: 40px; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: var(--text-secondary); text-decoration: none; font-weight: 500; }
        .theme-toggle { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; color: var(--text-primary); display: flex; align-items: center; justify-content: center; }
        .hero { padding: 4rem 2rem; text-align: center; background: linear-gradient(135deg, rgba(147,51,234,0.1), rgba(59,130,246,0.05)); }
        .hero h1 { font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--text-secondary); max-width: 600px; margin: 0 auto; }
        .content { max-width: 900px; margin: 0 auto; padding: 3rem 2rem; }
        .timeline { position: relative; padding-left: 2rem; }
        .timeline::before { content: ''; position: absolute; left: 6px; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, var(--accent-purple), var(--accent-blue)); }
        .timeline-item { position: relative; margin-bottom: 2.5rem; padding-left: 2rem; }
        .timeline-dot { position: absolute; left: -2rem; top: 0.5rem; width: 14px; height: 14px; border-radius: 50%; border: 2px solid var(--accent-purple); background: var(--bg-primary); }
        .timeline-item.completed .timeline-dot { background: var(--accent-green); border-color: var(--accent-green); }
        .timeline-item.in-progress .timeline-dot { background: var(--accent-yellow); border-color: var(--accent-yellow); }
        .timeline-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; }
        .timeline-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem; }
        .timeline-title { font-size: 1.15rem; font-weight: 600; }
        .timeline-date { font-size: 0.85rem; color: var(--text-secondary); background: var(--bg-secondary); padding: 0.25rem 0.75rem; border-radius: 20px; }
        .timeline-desc { color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 1rem; }
        .timeline-tags { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .tag { font-size: 0.8rem; padding: 0.25rem 0.6rem; border-radius: 6px; background: rgba(147,51,234,0.15); color: var(--accent-purple); }
        .tag.green { background: rgba(16,185,129,0.15); color: var(--accent-green); }
        .tag.blue { background: rgba(59,130,246,0.15); color: var(--accent-blue); }
        .status-badge { font-size: 0.75rem; padding: 0.25rem 0.6rem; border-radius: 20px; font-weight: 500; }
        .status-badge.completed { background: rgba(16,185,129,0.15); color: var(--accent-green); }
        .status-badge.in-progress { background: rgba(245,158,11,0.15); color: var(--accent-yellow); }
        .status-badge.planned { background: rgba(147,51,234,0.15); color: var(--accent-purple); }
        .footer { background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 3rem 2rem; text-align: center; margin-top: 3rem; }
        .footer-links { display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .footer-links a { color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; }
        @media (max-width: 768px) { .nav-links { display: none; } .hero h1 { font-size: 2rem; } }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="/" class="logo"><div class="logo-icon"><i class="fas fa-search"></i></div><span>FindIN</span></a>
            <nav class="nav-links">
                <a href="/">Accueil</a><a href="/about">À propos</a><a href="/contact">Contact</a>
                <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            </nav>
        </div>
    </header>

    <section class="hero">
        <h1>Roadmap Produit</h1>
        <p>Découvrez les fonctionnalités passées et à venir de FindIN</p>
    </section>

    <div class="content">
        <div class="timeline">
            <div class="timeline-item completed">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                    <div class="timeline-header">
                        <span class="timeline-title">🚀 Lancement MVP</span>
                        <span class="timeline-date">Q4 2024</span>
                    </div>
                    <p class="timeline-desc">Lancement de la première version avec les fonctionnalités essentielles : création de profil, recherche de compétences et gestion des équipes.</p>
                    <div class="timeline-tags"><span class="tag green">Lancé</span><span class="tag">Core</span></div>
                </div>
            </div>

            <div class="timeline-item completed">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                    <div class="timeline-header">
                        <span class="timeline-title">📄 Import de CV</span>
                        <span class="timeline-date">Q4 2024</span>
                    </div>
                    <p class="timeline-desc">Extraction automatique des compétences depuis les CV uploadés pour enrichir les profils.</p>
                    <div class="timeline-tags"><span class="tag green">Lancé</span><span class="tag blue">IA</span></div>
                </div>
            </div>

            <div class="timeline-item in-progress">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                    <div class="timeline-header">
                        <span class="timeline-title">�� Recherche avancée</span>
                        <span class="timeline-date">Q1 2025</span>
                    </div>
                    <p class="timeline-desc">Filtres multi-critères, recherche par combinaison de compétences et suggestions intelligentes.</p>
                    <div class="timeline-tags"><span class="status-badge in-progress">En cours</span><span class="tag">Search</span></div>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                    <div class="timeline-header">
                        <span class="timeline-title">📊 Analytics avancés</span>
                        <span class="timeline-date">Q2 2025</span>
                    </div>
                    <p class="timeline-desc">Tableaux de bord avec insights sur les compétences de l'entreprise, gaps analysis et tendances.</p>
                    <div class="timeline-tags"><span class="status-badge planned">Planifié</span><span class="tag">Analytics</span></div>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                    <div class="timeline-header">
                        <span class="timeline-title">🤖 IA de recommandation</span>
                        <span class="timeline-date">Q3 2025</span>
                    </div>
                    <p class="timeline-desc">Suggestions automatiques de formation, matching équipe/projet optimisé par IA.</p>
                    <div class="timeline-tags"><span class="status-badge planned">Planifié</span><span class="tag blue">IA</span></div>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                    <div class="timeline-header">
                        <span class="timeline-title">🔗 Intégrations</span>
                        <span class="timeline-date">Q4 2025</span>
                    </div>
                    <p class="timeline-desc">Connexion avec Slack, Teams, SIRH et outils de gestion de projet.</p>
                    <div class="timeline-tags"><span class="status-badge planned">Planifié</span><span class="tag">Intégration</span></div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-links"><a href="/about">À propos</a><a href="/contact">Contact</a><a href="/privacy">Confidentialité</a><a href="/terms">Conditions</a><a href="/mentions_legales">Mentions légales</a></div>
        <p>&copy; 2024 FindIN. Tous droits réservés.</p>
    </footer>

    <script>const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});</script>
</body>
</html>
