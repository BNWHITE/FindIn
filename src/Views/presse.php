<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presse - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg-primary: #0a0118; --bg-secondary: #1a0d2e; --bg-card: #241538; --text-primary: #ffffff; --text-secondary: #a0a0a0; --accent-purple: #9333ea; --accent-blue: #3b82f6; --border-color: rgba(255,255,255,0.1); }
        [data-theme="light"] { --bg-primary: #f8fafc; --bg-secondary: #ffffff; --bg-card: #ffffff; --text-primary: #1e293b; --text-secondary: #64748b; --border-color: rgba(0,0,0,0.1); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); line-height: 1.6; }
        .header { background: var(--bg-secondary); border-bottom: 1px solid var(--border-color); padding: 1rem 2rem; position: sticky; top: 0; z-index: 100; }
        .header-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: var(--text-primary); font-weight: 700; font-size: 1.5rem; }
        .logo-icon { width: 40px; height: 40px; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: var(--text-secondary); text-decoration: none; font-weight: 500; }
        .btn-primary { background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; padding: 0.75rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 600; }
        .theme-toggle { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; color: var(--text-primary); display: flex; align-items: center; justify-content: center; }
        .hero { padding: 5rem 2rem; text-align: center; }
        .hero h1 { font-size: 3rem; font-weight: 800; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--text-secondary); font-size: 1.25rem; max-width: 600px; margin: 0 auto; }
        .content { max-width: 1000px; margin: 0 auto; padding: 2rem 2rem 4rem; }
        .press-kit { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; padding: 2.5rem; margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 2rem; }
        .press-kit h2 { font-size: 1.5rem; }
        .press-kit p { color: var(--text-secondary); }
        .btn-download { background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; padding: 1rem 2rem; border-radius: 12px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s; }
        .btn-download:hover { transform: translateY(-2px); }
        .section-title { font-size: 1.5rem; margin-bottom: 2rem; }
        .press-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .press-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; transition: all 0.3s; }
        .press-card:hover { border-color: var(--accent-purple); transform: translateY(-5px); }
        .press-source { color: var(--accent-purple); font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .press-card h3 { margin-bottom: 1rem; font-size: 1.25rem; }
        .press-card p { color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.95rem; }
        .press-date { color: var(--text-secondary); font-size: 0.85rem; }
        .contact-press { background: linear-gradient(135deg, rgba(147,51,234,0.1), rgba(59,130,246,0.05)); border-radius: 20px; padding: 3rem; text-align: center; margin-top: 3rem; }
        .contact-press h3 { font-size: 1.5rem; margin-bottom: 1rem; }
        .contact-press p { color: var(--text-secondary); margin-bottom: 1.5rem; }
        .footer { background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 3rem 2rem; text-align: center; }
        .footer-links { display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .footer-links a { color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; }
        .footer p { color: var(--text-secondary); font-size: 0.85rem; }
        @media (max-width: 768px) { .nav-links { display: none; } .hero h1 { font-size: 2rem; } .press-kit { flex-direction: column; text-align: center; } }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="/" class="logo"><div class="logo-icon"><i class="fas fa-search"></i></div><span>FindIN</span></a>
            <nav class="nav-links">
                <a href="/">Accueil</a><a href="/about">À propos</a><a href="/contact">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?><a href="/dashboard" class="btn-primary">Dashboard</a><?php else: ?><a href="/login" class="btn-primary">Connexion</a><?php endif; ?>
                <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            </nav>
        </div>
    </header>

    <section class="hero">
        <h1>Espace Presse</h1>
        <p>Retrouvez nos dernières actualités, communiqués de presse et ressources médias.</p>
    </section>

    <div class="content">
        <div class="press-kit">
            <div>
                <h2>Kit Presse</h2>
                <p>Logos, captures d'écran, biographies et informations clés.</p>
            </div>
            <a href="#" class="btn-download"><i class="fas fa-download"></i> Télécharger le kit</a>
        </div>

        <h2 class="section-title">Dans les médias</h2>
        <div class="press-grid">
            <div class="press-card">
                <div class="press-source">Les Echos</div>
                <h3>FindIN lève 2M€ pour révolutionner la gestion des talents</h3>
                <p>La startup française ambitionne de devenir le leader européen de la gestion des compétences...</p>
                <div class="press-date">15 novembre 2024</div>
            </div>
            <div class="press-card">
                <div class="press-source">Maddyness</div>
                <h3>Ces startups HR Tech qui transforment les RH</h3>
                <p>Dans notre sélection des pépites de la HR Tech, FindIN se distingue par son approche innovante...</p>
                <div class="press-date">8 octobre 2024</div>
            </div>
            <div class="press-card">
                <div class="press-source">BFM Business</div>
                <h3>L'IA au service des ressources humaines</h3>
                <p>Interview de Sophie Martin, CEO de FindIN, sur l'utilisation de l'intelligence artificielle...</p>
                <div class="press-date">22 septembre 2024</div>
            </div>
        </div>

        <div class="contact-press">
            <h3>Contact Presse</h3>
            <p>Pour toute demande presse, interview ou partenariat média</p>
            <a href="mailto:presse@findin.io" class="btn-primary"><i class="fas fa-envelope"></i> presse@findin.io</a>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-links"><a href="/about">À propos</a><a href="/contact">Contact</a><a href="/carrieres">Carrières</a><a href="/privacy">Confidentialité</a><a href="/terms">Conditions</a><a href="/cgu">CGU</a></div>
        <p>&copy; 2024 FindIN. Tous droits réservés.</p>
    </footer>

    <script>const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});</script>
</body>
</html>
