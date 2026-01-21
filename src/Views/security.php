<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sécurité - FindIN</title>
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
        .trust-badges { display: flex; justify-content: center; gap: 3rem; flex-wrap: wrap; margin-bottom: 4rem; }
        .trust-badge { text-align: center; }
        .trust-badge-icon { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(59,130,246,0.2)); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
        .trust-badge-icon i { font-size: 2rem; color: var(--accent-green); }
        .trust-badge h3 { font-size: 1rem; margin-bottom: 0.25rem; }
        .trust-badge p { font-size: 0.85rem; color: var(--text-secondary); }
        .security-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }
        .security-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; }
        .security-card-icon { width: 50px; height: 50px; border-radius: 12px; background: rgba(147,51,234,0.15); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
        .security-card-icon i { font-size: 1.5rem; color: var(--accent-purple); }
        .security-card h3 { font-size: 1.15rem; margin-bottom: 0.75rem; }
        .security-card p { color: var(--text-secondary); font-size: 0.95rem; }
        .section-title { font-size: 1.75rem; margin-bottom: 1.5rem; text-align: center; }
        .compliance-list { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; }
        .compliance-item { display: flex; gap: 1rem; align-items: flex-start; padding: 1rem 0; border-bottom: 1px solid var(--border-color); }
        .compliance-item:last-child { border-bottom: none; }
        .compliance-icon { width: 40px; height: 40px; border-radius: 10px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .compliance-icon i { color: var(--accent-green); }
        .compliance-text h4 { font-size: 1rem; margin-bottom: 0.25rem; }
        .compliance-text p { color: var(--text-secondary); font-size: 0.9rem; }
        .cta-section { background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); border-radius: 20px; padding: 3rem; text-align: center; margin-top: 3rem; }
        .cta-section h2 { font-size: 1.5rem; margin-bottom: 1rem; color: white; }
        .cta-section p { color: rgba(255,255,255,0.8); margin-bottom: 1.5rem; }
        .cta-btn { display: inline-block; background: white; color: var(--accent-purple); padding: 0.875rem 2rem; border-radius: 10px; text-decoration: none; font-weight: 600; }
        .footer { background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 3rem 2rem; text-align: center; margin-top: 3rem; }
        .footer-links { display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .footer-links a { color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; }
        @media (max-width: 768px) { .nav-links { display: none; } .hero h1 { font-size: 2rem; } .trust-badges { gap: 1.5rem; } }
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
        <h1>Sécurité & Confidentialité</h1>
        <p>La protection de vos données est au cœur de notre mission. Découvrez nos engagements.</p>
    </section>

    <div class="content">
        <div class="trust-badges">
            <div class="trust-badge">
                <div class="trust-badge-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>RGPD</h3>
                <p>Conforme</p>
            </div>
            <div class="trust-badge">
                <div class="trust-badge-icon"><i class="fas fa-lock"></i></div>
                <h3>SSL/TLS</h3>
                <p>Chiffrement</p>
            </div>
            <div class="trust-badge">
                <div class="trust-badge-icon"><i class="fas fa-server"></i></div>
                <h3>Hébergement</h3>
                <p>France/UE</p>
            </div>
            <div class="trust-badge">
                <div class="trust-badge-icon"><i class="fas fa-user-shield"></i></div>
                <h3>Privacy by Design</h3>
                <p>Natif</p>
            </div>
        </div>

        <h2 class="section-title">Nos mesures de sécurité</h2>

        <div class="security-grid">
            <div class="security-card">
                <div class="security-card-icon"><i class="fas fa-key"></i></div>
                <h3>Authentification sécurisée</h3>
                <p>Mots de passe hashés avec bcrypt, protection contre le brute-force et possibilité d'activer la 2FA.</p>
            </div>
            <div class="security-card">
                <div class="security-card-icon"><i class="fas fa-database"></i></div>
                <h3>Chiffrement des données</h3>
                <p>Chiffrement en transit (TLS 1.3) et au repos pour les données sensibles.</p>
            </div>
            <div class="security-card">
                <div class="security-card-icon"><i class="fas fa-user-lock"></i></div>
                <h3>Contrôle d'accès</h3>
                <p>Gestion fine des rôles et permissions, principe du moindre privilège.</p>
            </div>
            <div class="security-card">
                <div class="security-card-icon"><i class="fas fa-history"></i></div>
                <h3>Audit & Logs</h3>
                <p>Traçabilité complète des actions, logs sécurisés et monitoring continu.</p>
            </div>
            <div class="security-card">
                <div class="security-card-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                <h3>Sauvegardes</h3>
                <p>Backups quotidiens chiffrés avec rétention de 30 jours et tests de restauration.</p>
            </div>
            <div class="security-card">
                <div class="security-card-icon"><i class="fas fa-bug"></i></div>
                <h3>Tests de sécurité</h3>
                <p>Audits réguliers, tests de pénétration et programme de bug bounty.</p>
            </div>
        </div>

        <h2 class="section-title">Conformité & Certifications</h2>

        <div class="compliance-list">
            <div class="compliance-item">
                <div class="compliance-icon"><i class="fas fa-check"></i></div>
                <div class="compliance-text">
                    <h4>RGPD (Règlement Général sur la Protection des Données)</h4>
                    <p>Conformité totale avec le règlement européen sur la protection des données personnelles.</p>
                </div>
            </div>
            <div class="compliance-item">
                <div class="compliance-icon"><i class="fas fa-check"></i></div>
                <div class="compliance-text">
                    <h4>Privacy by Design</h4>
                    <p>La protection des données est intégrée dès la conception de chaque fonctionnalité.</p>
                </div>
            </div>
            <div class="compliance-item">
                <div class="compliance-icon"><i class="fas fa-check"></i></div>
                <div class="compliance-text">
                    <h4>Hébergement souverain</h4>
                    <p>Données hébergées exclusivement sur des serveurs situés en France et dans l'Union Européenne.</p>
                </div>
            </div>
            <div class="compliance-item">
                <div class="compliance-icon"><i class="fas fa-check"></i></div>
                <div class="compliance-text">
                    <h4>DPO dédié</h4>
                    <p>Un Délégué à la Protection des Données veille au respect de vos droits.</p>
                </div>
            </div>
        </div>

        <div class="cta-section">
            <h2>Une question sur la sécurité ?</h2>
            <p>Notre équipe sécurité est disponible pour répondre à vos interrogations.</p>
            <a href="/contact" class="cta-btn">Nous contacter</a>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-links"><a href="/about">À propos</a><a href="/contact">Contact</a><a href="/privacy">Confidentialité</a><a href="/terms">Conditions</a><a href="/mentions_legales">Mentions légales</a></div>
        <p>&copy; 2024 FindIN. Tous droits réservés.</p>
    </footer>

    <script>const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});</script>
</body>
</html>
