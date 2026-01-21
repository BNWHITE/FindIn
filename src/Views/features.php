<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fonctionnalités - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg-primary: #0a0118; --bg-secondary: #1a0d2e; --bg-card: #241538; --text-primary: #ffffff; --text-secondary: #a0a0a0; --accent-purple: #9333ea; --accent-blue: #3b82f6; --accent-green: #10b981; --accent-pink: #ec4899; --border-color: rgba(255,255,255,0.1); }
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
        .hero { padding: 5rem 2rem; text-align: center; background: linear-gradient(135deg, rgba(147,51,234,0.1), rgba(59,130,246,0.05)); }
        .hero h1 { font-size: 3rem; font-weight: 800; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--text-secondary); max-width: 700px; margin: 0 auto 2rem; font-size: 1.2rem; }
        .hero-btn { display: inline-block; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; padding: 1rem 2.5rem; border-radius: 12px; text-decoration: none; font-weight: 600; transition: all 0.3s; }
        .hero-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(147,51,234,0.3); }
        .content { max-width: 1200px; margin: 0 auto; padding: 4rem 2rem; }
        .section-title { font-size: 2rem; text-align: center; margin-bottom: 3rem; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; margin-bottom: 5rem; }
        .feature-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; padding: 2.5rem; transition: all 0.3s; }
        .feature-card:hover { transform: translateY(-5px); border-color: var(--accent-purple); }
        .feature-icon { width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-bottom: 1.5rem; }
        .feature-icon.purple { background: rgba(147,51,234,0.15); color: var(--accent-purple); }
        .feature-icon.blue { background: rgba(59,130,246,0.15); color: var(--accent-blue); }
        .feature-icon.green { background: rgba(16,185,129,0.15); color: var(--accent-green); }
        .feature-icon.pink { background: rgba(236,72,153,0.15); color: var(--accent-pink); }
        .feature-card h3 { font-size: 1.3rem; margin-bottom: 0.75rem; }
        .feature-card p { color: var(--text-secondary); }
        .feature-list { list-style: none; margin-top: 1rem; }
        .feature-list li { padding: 0.5rem 0; color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem; }
        .feature-list li i { color: var(--accent-green); font-size: 0.85rem; }
        .showcase { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; margin-bottom: 5rem; }
        .showcase.reverse { direction: rtl; }
        .showcase.reverse > * { direction: ltr; }
        .showcase-content h2 { font-size: 1.75rem; margin-bottom: 1rem; }
        .showcase-content p { color: var(--text-secondary); margin-bottom: 1.5rem; }
        .showcase-visual { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; padding: 2rem; min-height: 300px; display: flex; align-items: center; justify-content: center; }
        .showcase-visual i { font-size: 6rem; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .cta-section { background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); border-radius: 24px; padding: 4rem; text-align: center; }
        .cta-section h2 { font-size: 2rem; margin-bottom: 1rem; color: white; }
        .cta-section p { color: rgba(255,255,255,0.8); margin-bottom: 2rem; max-width: 500px; margin-left: auto; margin-right: auto; }
        .cta-btn { display: inline-block; background: white; color: var(--accent-purple); padding: 1rem 2.5rem; border-radius: 12px; text-decoration: none; font-weight: 600; }
        .footer { background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 3rem 2rem; text-align: center; margin-top: 3rem; }
        .footer-links { display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .footer-links a { color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; }
        @media (max-width: 768px) { .nav-links { display: none; } .hero h1 { font-size: 2rem; } .showcase { grid-template-columns: 1fr; } .showcase.reverse { direction: ltr; } }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="/" class="logo"><div class="logo-icon"><i class="fas fa-search"></i></div><span>FindIN</span></a>
            <nav class="nav-links">
                <a href="/">Accueil</a><a href="/about">À propos</a><a href="/pricing">Tarifs</a><a href="/contact">Contact</a>
                <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            </nav>
        </div>
    </header>

    <section class="hero">
        <h1>Des fonctionnalités puissantes<br>pour votre entreprise</h1>
        <p>FindIN vous offre tous les outils nécessaires pour cartographier les compétences, trouver les bons talents et optimiser la collaboration.</p>
        <a href="/register" class="hero-btn">Commencer gratuitement</a>
    </section>

    <div class="content">
        <h2 class="section-title">Toutes nos fonctionnalités</h2>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon purple"><i class="fas fa-search"></i></div>
                <h3>Recherche intelligente</h3>
                <p>Trouvez instantanément les collaborateurs avec les compétences recherchées.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Recherche multi-critères</li>
                    <li><i class="fas fa-check"></i> Filtres avancés</li>
                    <li><i class="fas fa-check"></i> Suggestions IA</li>
                </ul>
            </div>
            <div class="feature-card">
                <div class="feature-icon blue"><i class="fas fa-user-circle"></i></div>
                <h3>Profils enrichis</h3>
                <p>Des profils complets pour valoriser chaque collaborateur.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Compétences détaillées</li>
                    <li><i class="fas fa-check"></i> Historique projets</li>
                    <li><i class="fas fa-check"></i> Certifications</li>
                </ul>
            </div>
            <div class="feature-card">
                <div class="feature-icon green"><i class="fas fa-file-alt"></i></div>
                <h3>Import CV automatique</h3>
                <p>Extraction automatique des compétences depuis vos CV.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Parsing intelligent</li>
                    <li><i class="fas fa-check"></i> Formats PDF, Word</li>
                    <li><i class="fas fa-check"></i> Mise à jour facile</li>
                </ul>
            </div>
            <div class="feature-card">
                <div class="feature-icon pink"><i class="fas fa-project-diagram"></i></div>
                <h3>Gestion de projets</h3>
                <p>Constituez les meilleures équipes pour vos projets.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Matching équipe/projet</li>
                    <li><i class="fas fa-check"></i> Suivi disponibilités</li>
                    <li><i class="fas fa-check"></i> Historique missions</li>
                </ul>
            </div>
            <div class="feature-card">
                <div class="feature-icon purple"><i class="fas fa-chart-bar"></i></div>
                <h3>Analytics & Reporting</h3>
                <p>Visualisez et analysez les compétences de votre organisation.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Tableaux de bord</li>
                    <li><i class="fas fa-check"></i> Exports personnalisés</li>
                    <li><i class="fas fa-check"></i> Analyse des gaps</li>
                </ul>
            </div>
            <div class="feature-card">
                <div class="feature-icon blue"><i class="fas fa-shield-alt"></i></div>
                <h3>Sécurité & RGPD</h3>
                <p>Protection maximale de vos données sensibles.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Privacy by Design</li>
                    <li><i class="fas fa-check"></i> Chiffrement</li>
                    <li><i class="fas fa-check"></i> Hébergement UE</li>
                </ul>
            </div>
        </div>

        <div class="showcase">
            <div class="showcase-content">
                <h2>Recherche ultra-rapide</h2>
                <p>Notre moteur de recherche indexe toutes les compétences et permet de trouver le bon profil en quelques secondes. Combinez plusieurs critères pour des résultats précis.</p>
                <a href="/register" class="hero-btn">Essayer maintenant</a>
            </div>
            <div class="showcase-visual"><i class="fas fa-bolt"></i></div>
        </div>

        <div class="showcase reverse">
            <div class="showcase-content">
                <h2>Collaboration simplifiée</h2>
                <p>Connectez les bonnes personnes au bon moment. FindIN facilite la constitution d'équipes projet en identifiant rapidement les expertises disponibles.</p>
                <a href="/register" class="hero-btn">Découvrir</a>
            </div>
            <div class="showcase-visual"><i class="fas fa-users"></i></div>
        </div>

        <div class="cta-section">
            <h2>Prêt à transformer votre gestion des talents ?</h2>
            <p>Rejoignez les entreprises qui font confiance à FindIN pour valoriser leurs compétences internes.</p>
            <a href="/register" class="cta-btn">Démarrer gratuitement</a>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-links"><a href="/about">À propos</a><a href="/contact">Contact</a><a href="/privacy">Confidentialité</a><a href="/terms">Conditions</a><a href="/mentions_legales">Mentions légales</a></div>
        <p>&copy; 2024 FindIN. Tous droits réservés.</p>
    </footer>

    <script>const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});</script>
</body>
</html>
