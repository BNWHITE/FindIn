<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarifs - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0118; --bg-secondary: #1a0d2e; --bg-card: #241538;
            --text-primary: #ffffff; --text-secondary: #a0a0a0;
            --accent-purple: #9333ea; --accent-blue: #3b82f6; --accent-green: #10b981;
            --border-color: rgba(255,255,255,0.1);
        }
        [data-theme="light"] {
            --bg-primary: #f8fafc; --bg-secondary: #ffffff; --bg-card: #ffffff;
            --text-primary: #1e293b; --text-secondary: #64748b;
            --border-color: rgba(0,0,0,0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); line-height: 1.6; }
        .header { background: var(--bg-secondary); border-bottom: 1px solid var(--border-color); padding: 1rem 2rem; position: sticky; top: 0; z-index: 100; }
        .header-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: var(--text-primary); font-weight: 700; font-size: 1.5rem; }
        .logo-icon { width: 40px; height: 40px; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: var(--text-secondary); text-decoration: none; font-weight: 500; }
        .nav-links a:hover { color: var(--accent-purple); }
        .btn-primary { background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; padding: 0.75rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 600; }
        .theme-toggle { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; color: var(--text-primary); display: flex; align-items: center; justify-content: center; }
        .hero { padding: 5rem 2rem; text-align: center; }
        .hero h1 { font-size: 3rem; font-weight: 800; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--text-secondary); font-size: 1.25rem; max-width: 600px; margin: 0 auto 2rem; }
        .toggle-billing { display: flex; justify-content: center; gap: 1rem; align-items: center; margin-bottom: 3rem; }
        .toggle-billing span { color: var(--text-secondary); }
        .toggle-billing .active { color: var(--text-primary); font-weight: 600; }
        .pricing-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; max-width: 1100px; margin: 0 auto; padding: 0 2rem; }
        .pricing-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; padding: 2.5rem; position: relative; transition: all 0.3s; }
        .pricing-card:hover { transform: translateY(-5px); border-color: var(--accent-purple); }
        .pricing-card.popular { border-color: var(--accent-purple); }
        .popular-badge { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; padding: 0.5rem 1.5rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .plan-name { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; }
        .plan-desc { color: var(--text-secondary); margin-bottom: 1.5rem; }
        .plan-price { font-size: 3rem; font-weight: 800; margin-bottom: 0.5rem; }
        .plan-price span { font-size: 1rem; font-weight: 400; color: var(--text-secondary); }
        .plan-features { list-style: none; margin: 2rem 0; }
        .plan-features li { padding: 0.75rem 0; display: flex; align-items: center; gap: 0.75rem; color: var(--text-secondary); border-bottom: 1px solid var(--border-color); }
        .plan-features li:last-child { border-bottom: none; }
        .plan-features i { color: var(--accent-green); }
        .plan-features .disabled { opacity: 0.5; }
        .plan-features .disabled i { color: var(--text-secondary); }
        .btn-plan { display: block; width: 100%; padding: 1rem; text-align: center; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.3s; }
        .btn-plan.primary { background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; }
        .btn-plan.secondary { background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); }
        .btn-plan:hover { transform: translateY(-2px); }
        .footer { background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 3rem 2rem; text-align: center; margin-top: 5rem; }
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
                <a href="/">Accueil</a>
                <a href="/about">À propos</a>
                <a href="/contact">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?><a href="/dashboard" class="btn-primary">Dashboard</a><?php else: ?><a href="/login" class="btn-primary">Connexion</a><?php endif; ?>
                <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            </nav>
        </div>
    </header>

    <section class="hero">
        <h1>Des tarifs simples et transparents</h1>
        <p>Choisissez le plan adapté à la taille de votre entreprise. Pas de frais cachés.</p>
        <div class="toggle-billing">
            <span class="active">Mensuel</span>
            <span>|</span>
            <span>Annuel <small style="color: var(--accent-green);">-20%</small></span>
        </div>
    </section>

    <div class="pricing-grid">
        <div class="pricing-card">
            <h3 class="plan-name">Starter</h3>
            <p class="plan-desc">Pour les petites équipes</p>
            <div class="plan-price">Gratuit</div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> Jusqu'à 10 utilisateurs</li>
                <li><i class="fas fa-check"></i> Gestion des compétences de base</li>
                <li><i class="fas fa-check"></i> Recherche simple</li>
                <li class="disabled"><i class="fas fa-times"></i> Analytics avancés</li>
                <li class="disabled"><i class="fas fa-times"></i> Support prioritaire</li>
            </ul>
            <a href="/register" class="btn-plan secondary">Commencer gratuitement</a>
        </div>

        <div class="pricing-card popular">
            <div class="popular-badge">Le plus populaire</div>
            <h3 class="plan-name">Pro</h3>
            <p class="plan-desc">Pour les entreprises en croissance</p>
            <div class="plan-price">29,99€ <span>/ mois</span></div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> Jusqu'à 100 utilisateurs</li>
                <li><i class="fas fa-check"></i> Toutes les fonctionnalités</li>
                <li><i class="fas fa-check"></i> Recherche avancée & IA</li>
                <li><i class="fas fa-check"></i> Analytics & rapports</li>
                <li><i class="fas fa-check"></i> Support par email</li>
            </ul>
            <a href="/register" class="btn-plan primary">Essai gratuit 14 jours</a>
        </div>

        <div class="pricing-card">
            <h3 class="plan-name">Enterprise</h3>
            <p class="plan-desc">Pour les grandes organisations</p>
            <div class="plan-price">Sur mesure</div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> Utilisateurs illimités</li>
                <li><i class="fas fa-check"></i> SSO & intégrations</li>
                <li><i class="fas fa-check"></i> API complète</li>
                <li><i class="fas fa-check"></i> SLA garanti</li>
                <li><i class="fas fa-check"></i> Account manager dédié</li>
            </ul>
            <a href="/contact" class="btn-plan secondary">Nous contacter</a>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-links">
            <a href="/about">À propos</a><a href="/contact">Contact</a><a href="/privacy">Confidentialité</a><a href="/terms">Conditions</a><a href="/cgu">CGU</a>
        </div>
        <p>&copy; 2025 FindIN. Tous droits réservés.</p>
    </footer>

    <script>
        const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';
        h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';
        t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});
    </script>
</body>
</html>
