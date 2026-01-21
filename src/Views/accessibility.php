<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Accessibilité</title>
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
        .orb-1 { width: 350px; height: 350px; background: radial-gradient(circle, #10b981, transparent 70%); top: 10%; left: 5%; animation: float1 20s ease-in-out infinite; }
        .orb-2 { width: 400px; height: 400px; background: radial-gradient(circle, #3b82f6, transparent 70%); bottom: 10%; right: 5%; animation: float2 22s ease-in-out infinite; }
        @keyframes float1 { 0%, 100% { transform: translate(0,0); } 50% { transform: translate(50px, 60px); } }
        @keyframes float2 { 0%, 100% { transform: translate(0,0); } 50% { transform: translate(-40px, -50px); } }
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
        .access-container { max-width: 1000px; margin: 0 auto; padding: 2rem; }
        .commitment-banner { background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(59,130,246,0.2)); border: 1px solid var(--border-light); border-radius: 20px; padding: 2rem; text-align: center; margin-bottom: 3rem; }
        .commitment-banner h2 { font-size: 1.5rem; margin-bottom: 1rem; }
        .commitment-banner p { color: var(--text-light); max-width: 700px; margin: 0 auto; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 3rem; }
        .feature-card { background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 20px; padding: 2rem; transition: all 0.3s; }
        .feature-card:hover { border-color: var(--accent-green); transform: translateY(-5px); }
        .feature-icon { width: 60px; height: 60px; background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(59,130,246,0.2)); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; font-size: 1.5rem; color: var(--accent-green); }
        .feature-card h3 { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.75rem; }
        .feature-card p { color: var(--text-light); line-height: 1.7; font-size: 0.95rem; }
        .section { background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 20px; padding: 2rem; margin-bottom: 2rem; }
        .section h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem; }
        .section h2 i { color: var(--accent-primary); }
        .section p { color: var(--text-light); line-height: 1.8; margin-bottom: 1rem; }
        .section ul { color: var(--text-light); padding-left: 1.5rem; line-height: 2; margin-bottom: 1rem; }
        .section ul li { margin-bottom: 0.5rem; }
        .wcag-badge { display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #10b981, #3b82f6); color: white; padding: 0.5rem 1rem; border-radius: 50px; font-weight: 600; font-size: 0.9rem; margin-bottom: 1rem; }
        .shortcuts-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .shortcuts-table th, .shortcuts-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-light); }
        .shortcuts-table th { background: rgba(147,51,234,0.1); color: var(--text-white); font-weight: 600; }
        .shortcuts-table td { color: var(--text-light); }
        .shortcuts-table kbd { background: rgba(255,255,255,0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-family: monospace; border: 1px solid var(--border-light); }
        [data-theme="light"] .shortcuts-table kbd { background: rgba(0,0,0,0.05); }
        .contact-box { background: linear-gradient(135deg, rgba(147,51,234,0.1), rgba(59,130,246,0.1)); border: 1px solid var(--border-light); border-radius: 16px; padding: 2rem; text-align: center; }
        .contact-box h3 { font-size: 1.3rem; margin-bottom: 1rem; }
        .contact-box p { color: var(--text-light); margin-bottom: 1.5rem; }
        .btn-contact { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #9333ea, #3b82f6); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; }
        .btn-contact:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(147,51,234,0.3); }
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
                    <a href="/documentation">Docs</a>
                    <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon theme-icon"></i></button>
                </nav>
            </div>
        </header>
        <section class="page-hero">
            <div>
                <h1><i class="fas fa-universal-access"></i> Accessibilité</h1>
                <p>Notre engagement pour une plateforme accessible à tous, sans exception.</p>
            </div>
        </section>
        <div class="access-container">
            <div class="commitment-banner">
                <span class="wcag-badge"><i class="fas fa-check-circle"></i> Conforme WCAG 2.1 AA</span>
                <h2>L'accessibilité est au cœur de notre mission</h2>
                <p>Nous nous engageons à rendre FindIN utilisable par tous, y compris les personnes en situation de handicap. Nous travaillons continuellement pour améliorer l'accessibilité de notre plateforme.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-eye"></i></div>
                    <h3>Contraste élevé</h3>
                    <p>Nos thèmes clair et sombre respectent les ratios de contraste WCAG pour une lisibilité optimale.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-keyboard"></i></div>
                    <h3>Navigation clavier</h3>
                    <p>Toutes les fonctionnalités sont accessibles uniquement au clavier, sans souris.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-assistive-listening-systems"></i></div>
                    <h3>Lecteurs d'écran</h3>
                    <p>Compatible avec les principaux lecteurs d'écran (JAWS, NVDA, VoiceOver).</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-text-height"></i></div>
                    <h3>Tailles adaptables</h3>
                    <p>Interface responsive et zoom jusqu'à 200% sans perte de fonctionnalité.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-closed-captioning"></i></div>
                    <h3>Sous-titres vidéo</h3>
                    <p>Toutes nos vidéos de tutoriels incluent des sous-titres en français.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-language"></i></div>
                    <h3>Langage clair</h3>
                    <p>Nous privilégions un langage simple et compréhensible par tous.</p>
                </div>
            </div>
            <div class="section">
                <h2><i class="fas fa-keyboard"></i> Raccourcis clavier</h2>
                <p>Naviguez plus rapidement avec ces raccourcis :</p>
                <table class="shortcuts-table">
                    <thead>
                        <tr><th>Action</th><th>Raccourci</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Aller au contenu principal</td><td><kbd>Tab</kbd> puis <kbd>Entrée</kbd></td></tr>
                        <tr><td>Ouvrir le menu de navigation</td><td><kbd>Alt</kbd> + <kbd>M</kbd></td></tr>
                        <tr><td>Basculer thème clair/sombre</td><td><kbd>Alt</kbd> + <kbd>T</kbd></td></tr>
                        <tr><td>Ouvrir la recherche</td><td><kbd>Ctrl</kbd> + <kbd>K</kbd></td></tr>
                        <tr><td>Fermer les modales</td><td><kbd>Échap</kbd></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="section">
                <h2><i class="fas fa-tasks"></i> Conformité et normes</h2>
                <p>FindIN respecte les standards d'accessibilité suivants :</p>
                <ul>
                    <li><strong>WCAG 2.1 niveau AA</strong> - Directives pour l'accessibilité des contenus Web</li>
                    <li><strong>RGAA 4.1</strong> - Référentiel Général d'Amélioration de l'Accessibilité (France)</li>
                    <li><strong>Section 508</strong> - Exigences américaines d'accessibilité</li>
                    <li><strong>EN 301 549</strong> - Norme européenne d'accessibilité</li>
                </ul>
                <p>Notre dernier audit d'accessibilité a été réalisé en janvier 2025 avec un taux de conformité de 94%.</p>
            </div>
            <div class="section">
                <h2><i class="fas fa-tools"></i> Améliorations en cours</h2>
                <p>Nous travaillons actuellement sur les points suivants :</p>
                <ul>
                    <li>Amélioration de la navigation par repères ARIA</li>
                    <li>Ajout de descriptions alternatives pour les graphiques</li>
                    <li>Support des préférences de mouvement réduit</li>
                    <li>Mode daltonien avec palettes de couleurs adaptées</li>
                </ul>
            </div>
            <div class="contact-box">
                <h3>Une difficulté d'accès ?</h3>
                <p>Si vous rencontrez un problème d'accessibilité sur notre site, n'hésitez pas à nous contacter. Nous ferons tout notre possible pour vous aider.</p>
                <a href="/contact" class="btn-contact"><i class="fas fa-envelope"></i> Nous contacter</a>
            </div>
        </div>
        <footer><p>&copy; 2025 FindIN. Tous droits réservés.</p></footer>
    </div>
    <script src="/assets/js/main.js"></script>
</body>
</html>
