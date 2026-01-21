<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg-primary: #0a0118; --bg-secondary: #1a0d2e; --bg-card: #241538; --text-primary: #ffffff; --text-secondary: #a0a0a0; --accent-purple: #9333ea; --accent-blue: #3b82f6; --accent-green: #10b981; --border-color: rgba(255,255,255,0.1); }
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
        .content { max-width: 1000px; margin: 0 auto; padding: 3rem 2rem; }
        .featured { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; overflow: hidden; margin-bottom: 3rem; display: grid; grid-template-columns: 1fr 1fr; }
        .featured-img { background: linear-gradient(135deg, rgba(147,51,234,0.3), rgba(59,130,246,0.3)); min-height: 300px; display: flex; align-items: center; justify-content: center; }
        .featured-img i { font-size: 5rem; color: white; }
        .featured-content { padding: 2.5rem; display: flex; flex-direction: column; justify-content: center; }
        .featured-badge { display: inline-block; background: var(--accent-purple); color: white; font-size: 0.75rem; padding: 0.3rem 0.75rem; border-radius: 20px; margin-bottom: 1rem; width: fit-content; }
        .featured-title { font-size: 1.75rem; margin-bottom: 1rem; line-height: 1.3; }
        .featured-excerpt { color: var(--text-secondary); margin-bottom: 1.5rem; }
        .featured-meta { display: flex; gap: 1.5rem; color: var(--text-secondary); font-size: 0.85rem; }
        .blog-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
        .blog-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; transition: all 0.3s; text-decoration: none; }
        .blog-card:hover { transform: translateY(-5px); border-color: var(--accent-purple); }
        .blog-card-img { height: 160px; background: linear-gradient(135deg, rgba(147,51,234,0.2), rgba(59,130,246,0.2)); display: flex; align-items: center; justify-content: center; }
        .blog-card-img i { font-size: 3rem; color: var(--accent-purple); }
        .blog-card-content { padding: 1.5rem; }
        .blog-card-tag { font-size: 0.75rem; color: var(--accent-purple); margin-bottom: 0.5rem; }
        .blog-card-title { font-size: 1.1rem; margin-bottom: 0.75rem; color: var(--text-primary); }
        .blog-card-excerpt { color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem; }
        .blog-card-meta { color: var(--text-secondary); font-size: 0.8rem; }
        .footer { background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 3rem 2rem; text-align: center; margin-top: 3rem; }
        .footer-links { display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .footer-links a { color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; }
        @media (max-width: 768px) { .nav-links { display: none; } .hero h1 { font-size: 2rem; } .featured { grid-template-columns: 1fr; } }
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
        <h1>Blog FindIN</h1>
        <p>Conseils, actualités et bonnes pratiques sur la gestion des compétences et le talent management</p>
    </section>

    <div class="content">
        <article class="featured">
            <div class="featured-img"><i class="fas fa-brain"></i></div>
            <div class="featured-content">
                <span class="featured-badge">À la une</span>
                <h2 class="featured-title">L'IA au service de la gestion des compétences en entreprise</h2>
                <p class="featured-excerpt">Découvrez comment l'intelligence artificielle révolutionne la cartographie des talents et permet aux entreprises de mieux identifier les expertises internes.</p>
                <div class="featured-meta"><span><i class="far fa-calendar"></i> 10 Déc 2024</span><span><i class="far fa-clock"></i> 8 min</span></div>
            </div>
        </article>

        <div class="blog-grid">
            <a href="#" class="blog-card">
                <div class="blog-card-img"><i class="fas fa-users-cog"></i></div>
                <div class="blog-card-content">
                    <div class="blog-card-tag">RH & Management</div>
                    <h3 class="blog-card-title">5 stratégies pour développer les compétences de vos équipes</h3>
                    <p class="blog-card-excerpt">Formation, mentorat, projets transverses... Les meilleures approches pour faire monter en compétences.</p>
                    <div class="blog-card-meta"><i class="far fa-calendar"></i> 5 Déc 2024 · 5 min</div>
                </div>
            </a>
            <a href="#" class="blog-card">
                <div class="blog-card-img"><i class="fas fa-chart-pie"></i></div>
                <div class="blog-card-content">
                    <div class="blog-card-tag">Analytics</div>
                    <h3 class="blog-card-title">Mesurer le ROI de la gestion des talents</h3>
                    <p class="blog-card-excerpt">Les indicateurs clés pour évaluer l'impact de votre stratégie de talent management.</p>
                    <div class="blog-card-meta"><i class="far fa-calendar"></i> 28 Nov 2024 · 6 min</div>
                </div>
            </a>
            <a href="#" class="blog-card">
                <div class="blog-card-img"><i class="fas fa-shield-alt"></i></div>
                <div class="blog-card-content">
                    <div class="blog-card-tag">RGPD</div>
                    <h3 class="blog-card-title">Privacy by Design : protéger les données RH</h3>
                    <p class="blog-card-excerpt">Comment concilier exploitation des données talents et respect de la vie privée.</p>
                    <div class="blog-card-meta"><i class="far fa-calendar"></i> 20 Nov 2024 · 4 min</div>
                </div>
            </a>
            <a href="#" class="blog-card">
                <div class="blog-card-img"><i class="fas fa-rocket"></i></div>
                <div class="blog-card-content">
                    <div class="blog-card-tag">Produit</div>
                    <h3 class="blog-card-title">Nouveautés FindIN : Import CV et matching IA</h3>
                    <p class="blog-card-excerpt">Découvrez les dernières fonctionnalités pour optimiser votre recherche de talents.</p>
                    <div class="blog-card-meta"><i class="far fa-calendar"></i> 15 Nov 2024 · 3 min</div>
                </div>
            </a>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-links"><a href="/about">À propos</a><a href="/contact">Contact</a><a href="/privacy">Confidentialité</a><a href="/terms">Conditions</a><a href="/mentions_legales">Mentions légales</a></div>
        <p>&copy; 2024 FindIN. Tous droits réservés.</p>
    </footer>

    <script>const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});</script>
</body>
</html>
