<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de Confidentialité - FindIN</title>
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
        .btn-primary { background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; padding: 0.75rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 600; }
        .theme-toggle { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; color: var(--text-primary); display: flex; align-items: center; justify-content: center; }
        .hero { padding: 4rem 2rem; text-align: center; background: linear-gradient(135deg, rgba(147,51,234,0.1), rgba(59,130,246,0.05)); }
        .hero h1 { font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--text-secondary); }
        .content { max-width: 800px; margin: 0 auto; padding: 3rem 2rem; }
        .section { margin-bottom: 3rem; }
        .section h2 { font-size: 1.5rem; margin-bottom: 1rem; color: var(--accent-purple); display: flex; align-items: center; gap: 0.75rem; }
        .section h2 i { font-size: 1.25rem; }
        .section p, .section li { color: var(--text-secondary); margin-bottom: 1rem; }
        .section ul { padding-left: 1.5rem; }
        .section li { margin-bottom: 0.5rem; }
        .highlight-box { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; margin: 1.5rem 0; }
        .highlight-box h3 { margin-bottom: 0.75rem; font-size: 1.1rem; }
        .footer { background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 3rem 2rem; text-align: center; }
        .footer-links { display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .footer-links a { color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; }
        .footer p { color: var(--text-secondary); font-size: 0.85rem; }
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
        <h1>Politique de Confidentialité</h1>
        <p>Dernière mise à jour : 12 décembre 2024</p>
    </section>

    <div class="content">
        <section class="section">
            <h2><i class="fas fa-shield-alt"></i> Privacy by Design</h2>
            <p>Chez FindIN, la protection de vos données personnelles est au cœur de notre conception. Nous appliquons les principes du Privacy by Design et du RGPD dès la conception de nos services.</p>
        </section>

        <section class="section">
            <h2><i class="fas fa-database"></i> Données collectées</h2>
            <p>Nous collectons uniquement les données nécessaires au fonctionnement du service :</p>
            <ul>
                <li><strong>Données d'identification</strong> : nom, prénom, email professionnel</li>
                <li><strong>Données professionnelles</strong> : département, compétences, CV (si fourni)</li>
                <li><strong>Données de connexion</strong> : logs de connexion, adresse IP</li>
            </ul>
        </section>

        <section class="section">
            <h2><i class="fas fa-lock"></i> Finalités du traitement</h2>
            <ul>
                <li>Gestion de votre compte utilisateur</li>
                <li>Recherche et matching de compétences</li>
                <li>Amélioration de nos services</li>
                <li>Communication sur les mises à jour du service</li>
            </ul>
        </section>

        <div class="highlight-box">
            <h3><i class="fas fa-user-shield"></i> Vos droits</h3>
            <p>Conformément au RGPD, vous disposez des droits suivants : accès, rectification, effacement, portabilité, limitation et opposition au traitement de vos données.</p>
            <p>Pour exercer vos droits : <a href="mailto:privacy@findin.io" style="color: var(--accent-purple);">privacy@findin.io</a></p>
        </div>

        <section class="section">
            <h2><i class="fas fa-clock"></i> Conservation des données</h2>
            <p>Vos données sont conservées pendant la durée de votre utilisation du service, puis supprimées dans un délai de 30 jours après la clôture de votre compte.</p>
        </section>

        <section class="section">
            <h2><i class="fas fa-share-alt"></i> Partage des données</h2>
            <p>Vos données ne sont jamais vendues à des tiers. Elles peuvent être partagées avec :</p>
            <ul>
                <li>Les autres utilisateurs de votre organisation (selon les permissions)</li>
                <li>Nos sous-traitants techniques (hébergement, sécurité)</li>
                <li>Les autorités compétentes sur demande légale</li>
            </ul>
        </section>

        <section class="section">
            <h2><i class="fas fa-server"></i> Hébergement</h2>
            <p>Vos données sont hébergées en France/UE sur des serveurs sécurisés conformes aux normes ISO 27001.</p>
        </section>
    </div>

    <footer class="footer">
        <div class="footer-links"><a href="/about">À propos</a><a href="/contact">Contact</a><a href="/terms">Conditions</a><a href="/cgu">CGU</a><a href="/mentions_legales">Mentions légales</a></div>
        <p>&copy; 2024 FindIN. Tous droits réservés.</p>
    </footer>

    <script>const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});</script>
</body>
</html>
