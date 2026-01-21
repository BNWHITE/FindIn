<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0118;
            --bg-secondary: #1a0d2e;
            --bg-card: #241538;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --accent-purple: #9333ea;
            --accent-blue: #3b82f6;
            --accent-pink: #ec4899;
            --border-color: rgba(255,255,255,0.1);
        }
        [data-theme="light"] {
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: rgba(0,0,0,0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }
        .header {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.5rem;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: var(--accent-purple); }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(147, 51, 234, 0.3);
        }
        .theme-toggle {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero {
            padding: 6rem 2rem;
            text-align: center;
            background: linear-gradient(135deg, rgba(147,51,234,0.1) 0%, rgba(59,130,246,0.05) 100%);
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero p { color: var(--text-secondary); font-size: 1.25rem; max-width: 600px; margin: 0 auto; }
        .content { max-width: 1000px; margin: 0 auto; padding: 4rem 2rem; }
        .section { margin-bottom: 4rem; }
        .section h2 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }
        .section p { color: var(--text-secondary); margin-bottom: 1rem; }
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .team-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
        }
        .team-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-pink));
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
        }
        .team-card h3 { margin-bottom: 0.5rem; }
        .team-card p { color: var(--text-secondary); font-size: 0.9rem; }
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .value-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
        }
        .value-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgba(147,51,234,0.2), rgba(59,130,246,0.1));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-purple);
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        .value-card h3 { margin-bottom: 0.75rem; }
        .value-card p { color: var(--text-secondary); font-size: 0.95rem; }
        .footer {
            background: var(--bg-secondary);
            border-top: 1px solid var(--border-color);
            padding: 3rem 2rem;
            text-align: center;
        }
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
        }
        .footer-links a:hover { color: var(--accent-purple); }
        .footer p { color: var(--text-secondary); font-size: 0.85rem; }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hero h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="/" class="logo">
                <div class="logo-icon"><i class="fas fa-search"></i></div>
                <span>FindIN</span>
            </a>
            <nav class="nav-links">
                <a href="/">Accueil</a>
                <a href="/pricing">Tarifs</a>
                <a href="/contact">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/dashboard" class="btn-primary">Dashboard</a>
                <?php else: ?>
                    <a href="/login" class="btn-primary">Connexion</a>
                <?php endif; ?>
                <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            </nav>
        </div>
    </header>

    <section class="hero">
        <h1>À propos de FindIN</h1>
        <p>Nous révolutionnons la gestion des compétences en entreprise avec une plateforme intelligente et intuitive.</p>
    </section>

    <div class="content">
        <section class="section">
            <h2>Notre Mission</h2>
            <p>FindIN est née d'un constat simple : les entreprises sous-exploitent les compétences de leurs collaborateurs. Notre mission est de révéler les talents cachés et d'optimiser la gestion des ressources humaines.</p>
            <p>Grâce à notre plateforme, les entreprises peuvent identifier rapidement les expertises disponibles, favoriser la mobilité interne et construire des équipes performantes.</p>
        </section>

        <section class="section">
            <h2>Nos Valeurs</h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Privacy by Design</h3>
                    <p>La protection des données est au cœur de notre conception. Vos données restent les vôtres.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class="fas fa-lightbulb"></i></div>
                    <h3>Innovation</h3>
                    <p>Nous utilisons l'IA et le machine learning pour améliorer continuellement notre plateforme.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class="fas fa-users"></i></div>
                    <h3>Humain d'abord</h3>
                    <p>La technologie au service de l'humain, jamais l'inverse.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class="fas fa-globe"></i></div>
                    <h3>Accessibilité</h3>
                    <p>Une interface accessible à tous, conforme aux standards WCAG 2.1.</p>
                </div>
            </div>
        </section>

        <section class="section">
            <h2>Notre Équipe</h2>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Une équipe d'étudiants développeurs passionnés de l'ISEP.</p>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar">�‍�</div>
                    <h3>Chems Nouri</h3>
                    <p>Développeur Full Stack</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">👨‍💻</div>
                    <h3>Sacha Saoudi</h3>
                    <p>Développeur Full Stack</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">�‍💻</div>
                    <h3>Morgan Musik</h3>
                    <p>Développeur Full Stack</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">👨‍�</div>
                    <h3>Axel Musik</h3>
                    <p>Développeur Full Stack</p>
                </div>
            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="footer-links">
            <a href="/about">À propos</a>
            <a href="/contact">Contact</a>
            <a href="/carrieres">Carrières</a>
            <a href="/presse">Presse</a>
            <a href="/privacy">Confidentialité</a>
            <a href="/terms">Conditions</a>
            <a href="/cgu">CGU</a>
        </div>
        <p>&copy; 2024 FindIN. Tous droits réservés.</p>
    </footer>

    <script>
        const toggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        const icon = toggle.querySelector('i');
        const saved = localStorage.getItem('theme') || 'dark';
        html.setAttribute('data-theme', saved);
        icon.className = saved === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        toggle.addEventListener('click', () => {
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            icon.className = next === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        });
    </script>
</body>
</html>
