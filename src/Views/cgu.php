<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CGU - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg-primary: #0a0118; --bg-secondary: #1a0d2e; --bg-card: #241538; --text-primary: #ffffff; --text-secondary: #a0a0a0; --accent-purple: #9333ea; --accent-blue: #3b82f6; --border-color: rgba(255,255,255,0.1); }
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
        .hero p { color: var(--text-secondary); }
        .content { max-width: 800px; margin: 0 auto; padding: 3rem 2rem; }
        .section { margin-bottom: 2.5rem; }
        .section h2 { font-size: 1.35rem; margin-bottom: 1rem; color: var(--accent-purple); }
        .section p, .section li { color: var(--text-secondary); margin-bottom: 0.75rem; }
        .section ul { padding-left: 1.5rem; }
        .footer { background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 3rem 2rem; text-align: center; }
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
        <h1>Conditions Générales d'Utilisation</h1>
        <p>Dernière mise à jour : 12 décembre 2024</p>
    </section>

    <div class="content">
        <section class="section">
            <h2>Article 1 - Définitions</h2>
            <p><strong>Plateforme</strong> : désigne le service FindIN accessible en ligne.<br>
            <strong>Utilisateur</strong> : toute personne physique utilisant la Plateforme.<br>
            <strong>Contenu</strong> : toute information saisie par l'Utilisateur.</p>
        </section>

        <section class="section">
            <h2>Article 2 - Objet</h2>
            <p>Les présentes CGU définissent les conditions d'accès et d'utilisation de la Plateforme FindIN. L'inscription implique l'acceptation sans réserve des présentes CGU.</p>
        </section>

        <section class="section">
            <h2>Article 3 - Inscription</h2>
            <p>L'inscription est gratuite et réservée aux personnes majeures. L'Utilisateur garantit l'exactitude des informations fournies et s'engage à les tenir à jour.</p>
        </section>

        <section class="section">
            <h2>Article 4 - Obligations de l'Utilisateur</h2>
            <ul>
                <li>Fournir des informations exactes et complètes</li>
                <li>Préserver la confidentialité de ses identifiants</li>
                <li>Ne pas porter atteinte au bon fonctionnement de la Plateforme</li>
                <li>Respecter les droits des autres utilisateurs</li>
            </ul>
        </section>

        <section class="section">
            <h2>Article 5 - Contenu utilisateur</h2>
            <p>L'Utilisateur reste propriétaire de ses Contenus. Il accorde à FindIN une licence d'utilisation non exclusive pour les besoins du service. L'Utilisateur garantit disposer des droits nécessaires sur ses Contenus.</p>
        </section>

        <section class="section">
            <h2>Article 6 - Responsabilité</h2>
            <p>FindIN ne peut être tenu responsable des dommages indirects, de l'utilisation faite par l'Utilisateur, ni de l'indisponibilité temporaire du service.</p>
        </section>

        <section class="section">
            <h2>Article 7 - Modification des CGU</h2>
            <p>FindIN se réserve le droit de modifier les présentes CGU. Les modifications entrent en vigueur dès leur publication. L'utilisation continue du service vaut acceptation.</p>
        </section>
    </div>

    <footer class="footer">
        <div class="footer-links"><a href="/about">À propos</a><a href="/contact">Contact</a><a href="/privacy">Confidentialité</a><a href="/terms">Conditions</a><a href="/mentions_legales">Mentions légales</a></div>
        <p>&copy; 2024 FindIN. Tous droits réservés.</p>
    </footer>

    <script>const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});</script>
</body>
</html>
