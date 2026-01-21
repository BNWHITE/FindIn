<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Communauté</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        :root { --bg-dark: #0a0118; --bg-card: #1a0d2e; --text-white: #ffffff; --text-light: #e0e0e0; --accent-primary: #9333ea; --accent-blue: #3b82f6; --accent-pink: #ec4899; --border-light: rgba(255,255,255,0.1); }
        [data-theme="light"] { --bg-dark: #f8fafc; --bg-card: #ffffff; --text-white: #1e293b; --text-light: #475569; --border-light: rgba(0,0,0,0.1); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0a0118 0%, #1a0d2e 100%); color: var(--text-white); min-height: 100vh; }
        [data-theme="light"] body { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); }
        .orb-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; }
        .orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.3; }
        [data-theme="light"] .orb { opacity: 0.15; }
        .orb-1 { width: 400px; height: 400px; background: radial-gradient(circle, #ec4899, transparent 70%); top: -50px; right: 20%; animation: float1 20s ease-in-out infinite; }
        .orb-2 { width: 350px; height: 350px; background: radial-gradient(circle, #3b82f6, transparent 70%); bottom: 10%; left: -50px; animation: float2 22s ease-in-out infinite; }
        @keyframes float1 { 0%, 100% { transform: translate(0,0); } 50% { transform: translate(-50px, 80px); } }
        @keyframes float2 { 0%, 100% { transform: translate(0,0); } 50% { transform: translate(60px, -40px); } }
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
        .community-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .stats-banner { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 4rem; }
        .stat-item { background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 16px; padding: 2rem; text-align: center; transition: all 0.3s; }
        .stat-item:hover { border-color: var(--accent-primary); transform: translateY(-5px); }
        .stat-number { font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, #9333ea, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .stat-label { color: var(--text-light); margin-top: 0.5rem; }
        .section-title { font-size: 1.8rem; font-weight: 700; margin-bottom: 2rem; text-align: center; }
        .channels-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 4rem; }
        .channel-card { background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 20px; padding: 2rem; text-align: center; transition: all 0.3s; }
        .channel-card:hover { border-color: var(--accent-primary); transform: translateY(-8px); box-shadow: 0 20px 40px rgba(147,51,234,0.2); }
        .channel-icon { width: 80px; height: 80px; background: linear-gradient(135deg, rgba(147,51,234,0.2), rgba(59,130,246,0.2)); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem; color: var(--accent-primary); }
        .channel-card h3 { font-size: 1.3rem; font-weight: 700; margin-bottom: 0.75rem; }
        .channel-card p { color: var(--text-light); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.5rem; }
        .btn-join { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #9333ea, #3b82f6); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; }
        .btn-join:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(147,51,234,0.3); }
        .contributors { margin-bottom: 4rem; }
        .contributors-grid { display: flex; justify-content: center; flex-wrap: wrap; gap: 1.5rem; }
        .contributor { text-align: center; transition: transform 0.3s; }
        .contributor:hover { transform: translateY(-5px); }
        .contributor-avatar { width: 80px; height: 80px; background: linear-gradient(135deg, #9333ea, #ec4899); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; color: white; margin-bottom: 0.75rem; border: 3px solid var(--border-light); }
        .contributor-name { font-weight: 600; font-size: 0.9rem; }
        .contributor-role { color: var(--text-light); font-size: 0.8rem; }
        .cta-section { background: linear-gradient(135deg, rgba(147,51,234,0.1), rgba(59,130,246,0.1)); border: 1px solid var(--border-light); border-radius: 24px; padding: 3rem; text-align: center; }
        .cta-section h2 { font-size: 1.8rem; font-weight: 700; margin-bottom: 1rem; }
        .cta-section p { color: var(--text-light); max-width: 500px; margin: 0 auto 2rem; }
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
                    <a href="/community">Communauté</a>
                    <a href="/documentation">Docs</a>
                    <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon theme-icon"></i></button>
                </nav>
            </div>
        </header>
        <section class="page-hero">
            <div>
                <h1><i class="fas fa-users"></i> Communauté FindIN</h1>
                <p>Rejoignez une communauté dynamique de professionnels RH, managers et développeurs passionnés.</p>
            </div>
        </section>
        <div class="community-container">
            <div class="stats-banner">
                <div class="stat-item"><div class="stat-number">5,000+</div><div class="stat-label">Membres actifs</div></div>
                <div class="stat-item"><div class="stat-number">150+</div><div class="stat-label">Entreprises</div></div>
                <div class="stat-item"><div class="stat-number">1,200+</div><div class="stat-label">Questions résolues</div></div>
                <div class="stat-item"><div class="stat-number">50+</div><div class="stat-label">Contributeurs</div></div>
            </div>
            <h2 class="section-title">Rejoignez-nous</h2>
            <div class="channels-grid">
                <div class="channel-card">
                    <div class="channel-icon"><i class="fab fa-discord"></i></div>
                    <h3>Discord</h3>
                    <p>Discutez en temps réel avec la communauté, posez vos questions et partagez vos retours d'expérience.</p>
                    <a href="#" class="btn-join"><i class="fab fa-discord"></i> Rejoindre</a>
                </div>
                <div class="channel-card">
                    <div class="channel-icon"><i class="fab fa-github"></i></div>
                    <h3>GitHub</h3>
                    <p>Contribuez au code, signalez des bugs ou proposez de nouvelles fonctionnalités.</p>
                    <a href="#" class="btn-join"><i class="fab fa-github"></i> Contribuer</a>
                </div>
                <div class="channel-card">
                    <div class="channel-icon"><i class="fab fa-linkedin"></i></div>
                    <h3>LinkedIn</h3>
                    <p>Suivez nos actualités, webinaires et connectez-vous avec d'autres professionnels.</p>
                    <a href="#" class="btn-join"><i class="fab fa-linkedin"></i> Suivre</a>
                </div>
                <div class="channel-card">
                    <div class="channel-icon"><i class="fas fa-comments"></i></div>
                    <h3>Forum</h3>
                    <p>Explorez les discussions, trouvez des solutions et partagez vos connaissances.</p>
                    <a href="#" class="btn-join"><i class="fas fa-comments"></i> Participer</a>
                </div>
            </div>
            <div class="contributors">
                <h2 class="section-title">Top Contributeurs - Devs</h2>
                <div class="contributors-grid">
                    <div class="contributor"><div class="contributor-avatar">QD</div><div class="contributor-name">Quentin D'aboville</div><div class="contributor-role">Team Devs</div></div>
                    <div class="contributor"><div class="contributor-avatar">YH</div><div class="contributor-name">Yasmina Harissi</div><div class="contributor-role">Team Devs</div></div>
                    <div class="contributor"><div class="contributor-avatar">AL</div><div class="contributor-name">Allan Lahcene</div><div class="contributor-role">Team Devs</div></div>
                    <div class="contributor"><div class="contributor-avatar">EM</div><div class="contributor-name">Evan Massé</div><div class="contributor-role">Team Devs</div></div>
                    <div class="contributor"><div class="contributor-avatar">SY</div><div class="contributor-name">Seydina Sy</div><div class="contributor-role">Team Devs</div></div>
                </div>
            </div>
            <div class="cta-section">
                <h2>Prêt à contribuer ?</h2>
                <p>Que vous soyez développeur, designer, rédacteur ou simplement passionné, il y a une place pour vous dans notre communauté.</p>
                <a href="/register" class="btn-join"><i class="fas fa-rocket"></i> Commencer maintenant</a>
            </div>
        </div>
        <footer><p>&copy; 2025 FindIN. Tous droits réservés.</p></footer>
    </div>
    <script src="/assets/js/main.js"></script>
</body>
</html>
