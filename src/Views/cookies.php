<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Politique de Cookies</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        :root { --bg-dark: #0a0118; --bg-card: #1a0d2e; --text-white: #ffffff; --text-light: #e0e0e0; --accent-primary: #9333ea; --accent-blue: #3b82f6; --border-light: rgba(255,255,255,0.1); }
        [data-theme="light"] { --bg-dark: #f8fafc; --bg-card: #ffffff; --text-white: #1e293b; --text-light: #475569; --border-light: rgba(0,0,0,0.1); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0a0118 0%, #1a0d2e 100%); color: var(--text-white); min-height: 100vh; }
        [data-theme="light"] body { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); }
        .orb-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; }
        .orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.3; }
        [data-theme="light"] .orb { opacity: 0.15; }
        .orb-1 { width: 350px; height: 350px; background: radial-gradient(circle, #f59e0b, transparent 70%); top: 15%; right: 10%; animation: float1 20s ease-in-out infinite; }
        .orb-2 { width: 300px; height: 300px; background: radial-gradient(circle, #9333ea, transparent 70%); bottom: 15%; left: 5%; animation: float2 18s ease-in-out infinite; }
        @keyframes float1 { 0%, 100% { transform: translate(0,0); } 50% { transform: translate(-40px, 60px); } }
        @keyframes float2 { 0%, 100% { transform: translate(0,0); } 50% { transform: translate(50px, -40px); } }
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
        .page-hero { min-height: 30vh; display: flex; align-items: center; justify-content: center; padding: 4rem 2rem; text-align: center; }
        .page-hero h1 { font-size: clamp(2rem, 5vw, 3rem); font-weight: 800; background: linear-gradient(135deg, #fff, #e0e0e0); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 1rem; }
        [data-theme="light"] .page-hero h1 { background: linear-gradient(135deg, #1e293b, #334155); -webkit-background-clip: text; }
        .page-hero p { font-size: 1.1rem; color: var(--text-light); max-width: 600px; margin: 0 auto; }
        .cookies-container { max-width: 900px; margin: 0 auto; padding: 2rem; }
        .cookie-section { background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 20px; padding: 2rem; margin-bottom: 2rem; }
        .cookie-section h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem; }
        .cookie-section h2 i { color: var(--accent-primary); }
        .cookie-section p { color: var(--text-light); line-height: 1.8; margin-bottom: 1rem; }
        .cookie-section ul { color: var(--text-light); padding-left: 1.5rem; line-height: 1.8; margin-bottom: 1rem; }
        .cookie-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .cookie-table th, .cookie-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-light); }
        .cookie-table th { background: rgba(147,51,234,0.1); color: var(--text-white); font-weight: 600; }
        .cookie-table td { color: var(--text-light); }
        .cookie-toggle { display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 12px; margin-bottom: 1rem; }
        [data-theme="light"] .cookie-toggle { background: rgba(0,0,0,0.03); }
        .cookie-toggle-info h4 { font-weight: 600; margin-bottom: 0.25rem; }
        .cookie-toggle-info p { font-size: 0.85rem; color: var(--text-light); }
        .toggle-switch { width: 50px; height: 26px; background: rgba(255,255,255,0.2); border-radius: 15px; position: relative; cursor: pointer; transition: background 0.3s; }
        .toggle-switch.active { background: var(--accent-primary); }
        .toggle-switch::after { content: ''; position: absolute; width: 20px; height: 20px; background: white; border-radius: 50%; top: 3px; left: 3px; transition: transform 0.3s; }
        .toggle-switch.active::after { transform: translateX(24px); }
        .toggle-switch.disabled { opacity: 0.6; cursor: not-allowed; }
        .btn-save { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #9333ea, #3b82f6); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; margin-top: 1rem; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(147,51,234,0.3); }
        footer { background: rgba(10,1,24,0.8); border-top: 1px solid var(--border-light); padding: 2rem; text-align: center; margin-top: 4rem; }
        [data-theme="light"] footer { background: rgba(255,255,255,0.9); }
        footer p { color: var(--text-light); }
        .last-update { text-align: center; color: var(--text-light); font-size: 0.9rem; margin-bottom: 2rem; }
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
                    <a href="/documentation">Docs</a>
                    <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon theme-icon"></i></button>
                </nav>
            </div>
        </header>
        <section class="page-hero">
            <div>
                <h1><i class="fas fa-cookie-bite"></i> Politique de Cookies</h1>
                <p>Découvrez comment nous utilisons les cookies pour améliorer votre expérience sur FindIN.</p>
            </div>
        </section>
        <div class="cookies-container">
            <p class="last-update">Dernière mise à jour : Janvier 2025</p>
            <div class="cookie-section">
                <h2><i class="fas fa-info-circle"></i> Qu'est-ce qu'un cookie ?</h2>
                <p>Un cookie est un petit fichier texte stocké sur votre appareil lorsque vous visitez un site web. Les cookies permettent au site de mémoriser vos actions et préférences (comme la connexion, la langue, la taille de police et d'autres préférences d'affichage) pendant une période déterminée.</p>
            </div>
            <div class="cookie-section">
                <h2><i class="fas fa-sliders-h"></i> Gérer vos préférences</h2>
                <div class="cookie-toggle">
                    <div class="cookie-toggle-info">
                        <h4>Cookies essentiels</h4>
                        <p>Nécessaires au fonctionnement du site. Ne peuvent pas être désactivés.</p>
                    </div>
                    <div class="toggle-switch active disabled"></div>
                </div>
                <div class="cookie-toggle">
                    <div class="cookie-toggle-info">
                        <h4>Cookies analytiques</h4>
                        <p>Nous aident à comprendre comment vous utilisez le site pour l'améliorer.</p>
                    </div>
                    <div class="toggle-switch active" onclick="this.classList.toggle('active')"></div>
                </div>
                <div class="cookie-toggle">
                    <div class="cookie-toggle-info">
                        <h4>Cookies de préférences</h4>
                        <p>Permettent de mémoriser vos choix (thème, langue, etc.).</p>
                    </div>
                    <div class="toggle-switch active" onclick="this.classList.toggle('active')"></div>
                </div>
                <div class="cookie-toggle">
                    <div class="cookie-toggle-info">
                        <h4>Cookies marketing</h4>
                        <p>Utilisés pour afficher des publicités pertinentes.</p>
                    </div>
                    <div class="toggle-switch" onclick="this.classList.toggle('active')"></div>
                </div>
                <button class="btn-save"><i class="fas fa-save"></i> Enregistrer mes préférences</button>
            </div>
            <div class="cookie-section">
                <h2><i class="fas fa-list"></i> Cookies utilisés</h2>
                <table class="cookie-table">
                    <thead>
                        <tr><th>Nom</th><th>Type</th><th>Durée</th><th>Description</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>session_id</td><td>Essentiel</td><td>Session</td><td>Identifiant de session utilisateur</td></tr>
                        <tr><td>csrf_token</td><td>Essentiel</td><td>Session</td><td>Protection contre les attaques CSRF</td></tr>
                        <tr><td>theme_pref</td><td>Préférence</td><td>1 an</td><td>Mémorise le thème clair/sombre choisi</td></tr>
                        <tr><td>lang</td><td>Préférence</td><td>1 an</td><td>Langue préférée de l'utilisateur</td></tr>
                        <tr><td>_ga</td><td>Analytique</td><td>2 ans</td><td>Google Analytics - Statistiques d'usage</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="cookie-section">
                <h2><i class="fas fa-shield-alt"></i> Vos droits</h2>
                <p>Conformément au RGPD, vous disposez des droits suivants :</p>
                <ul>
                    <li>Droit d'accès à vos données personnelles</li>
                    <li>Droit de rectification des données inexactes</li>
                    <li>Droit à l'effacement (« droit à l'oubli »)</li>
                    <li>Droit à la portabilité de vos données</li>
                    <li>Droit d'opposition au traitement</li>
                </ul>
                <p>Pour exercer ces droits, contactez-nous à : <strong>privacy@findin.fr</strong></p>
            </div>
        </div>
        <footer><p>&copy; 2025 FindIN. Tous droits réservés.</p></footer>
    </div>
    <script src="/assets/js/main.js"></script>
</body>
</html>
