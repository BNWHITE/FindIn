<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrières - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg-primary: #0a0118; --bg-secondary: #1a0d2e; --bg-card: #241538; --text-primary: #ffffff; --text-secondary: #a0a0a0; --accent-purple: #9333ea; --accent-blue: #3b82f6; --accent-green: #10b981; --border-color: rgba(255,255,255,0.1); }
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
        .hero { padding: 5rem 2rem; text-align: center; background: linear-gradient(135deg, rgba(147,51,234,0.1) 0%, rgba(59,130,246,0.05) 100%); }
        .hero h1 { font-size: 3rem; font-weight: 800; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--text-secondary); font-size: 1.25rem; max-width: 600px; margin: 0 auto; }
        .content { max-width: 1000px; margin: 0 auto; padding: 4rem 2rem; }
        .section-title { font-size: 1.75rem; margin-bottom: 2rem; text-align: center; }
        .jobs-grid { display: flex; flex-direction: column; gap: 1.5rem; }
        .job-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s; }
        .job-card:hover { border-color: var(--accent-purple); transform: translateX(5px); }
        .job-info h3 { font-size: 1.25rem; margin-bottom: 0.5rem; }
        .job-meta { display: flex; gap: 1.5rem; flex-wrap: wrap; }
        .job-meta span { display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; }
        .job-meta i { color: var(--accent-purple); }
        .btn-apply { background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; padding: 0.75rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 600; transition: all 0.3s; white-space: nowrap; }
        .btn-apply:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(147, 51, 234, 0.3); }
        .perks { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 4rem; }
        .perk-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; text-align: center; }
        .perk-icon { width: 50px; height: 50px; background: linear-gradient(135deg, rgba(147,51,234,0.2), rgba(59,130,246,0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--accent-purple); font-size: 1.25rem; margin: 0 auto 1rem; }
        .perk-card h4 { margin-bottom: 0.5rem; }
        .perk-card p { color: var(--text-secondary); font-size: 0.9rem; }
        .footer { background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 3rem 2rem; text-align: center; }
        .footer-links { display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .footer-links a { color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; }
        .footer p { color: var(--text-secondary); font-size: 0.85rem; }
        @media (max-width: 768px) { .nav-links { display: none; } .hero h1 { font-size: 2rem; } .job-card { flex-direction: column; gap: 1.5rem; align-items: flex-start; } }
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
        <h1>Rejoignez l'aventure FindIN</h1>
        <p>Nous construisons l'avenir de la gestion des talents. Rejoignez une équipe passionnée et innovante.</p>
    </section>

    <div class="content">
        <h2 class="section-title">Postes ouverts</h2>
        <div class="jobs-grid">
            <div class="job-card">
                <div class="job-info">
                    <h3>Développeur Full Stack Senior</h3>
                    <div class="job-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Paris / Remote</span>
                        <span><i class="fas fa-briefcase"></i> CDI</span>
                        <span><i class="fas fa-euro-sign"></i> 55-70k</span>
                    </div>
                </div>
                <a href="/contact" class="btn-apply">Postuler</a>
            </div>
            <div class="job-card">
                <div class="job-info">
                    <h3>Product Designer UX/UI</h3>
                    <div class="job-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Paris</span>
                        <span><i class="fas fa-briefcase"></i> CDI</span>
                        <span><i class="fas fa-euro-sign"></i> 45-55k</span>
                    </div>
                </div>
                <a href="/contact" class="btn-apply">Postuler</a>
            </div>
            <div class="job-card">
                <div class="job-info">
                    <h3>Data Scientist ML</h3>
                    <div class="job-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Remote</span>
                        <span><i class="fas fa-briefcase"></i> CDI</span>
                        <span><i class="fas fa-euro-sign"></i> 50-65k</span>
                    </div>
                </div>
                <a href="/contact" class="btn-apply">Postuler</a>
            </div>
            <div class="job-card">
                <div class="job-info">
                    <h3>Customer Success Manager</h3>
                    <div class="job-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Paris</span>
                        <span><i class="fas fa-briefcase"></i> CDI</span>
                        <span><i class="fas fa-euro-sign"></i> 40-50k</span>
                    </div>
                </div>
                <a href="/contact" class="btn-apply">Postuler</a>
            </div>
        </div>

        <div class="perks">
            <div class="perk-card"><div class="perk-icon"><i class="fas fa-laptop-house"></i></div><h4>Remote-first</h4><p>Travaillez d'où vous voulez</p></div>
            <div class="perk-card"><div class="perk-icon"><i class="fas fa-heart"></i></div><h4>Mutuelle 100%</h4><p>Alan prise en charge</p></div>
            <div class="perk-card"><div class="perk-icon"><i class="fas fa-graduation-cap"></i></div><h4>Formation</h4><p>Budget formation annuel</p></div>
            <div class="perk-card"><div class="perk-icon"><i class="fas fa-umbrella-beach"></i></div><h4>RTT</h4><p>10 jours de RTT / an</p></div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-links"><a href="/about">À propos</a><a href="/contact">Contact</a><a href="/presse">Presse</a><a href="/privacy">Confidentialité</a><a href="/terms">Conditions</a><a href="/cgu">CGU</a></div>
        <p>&copy; 2024 FindIN. Tous droits réservés.</p>
    </footer>

    <script>const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});</script>
</body>
</html>
