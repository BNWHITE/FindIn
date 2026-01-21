<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Révélez les talents cachés</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        :root {
            --bg-dark: #0a0118;
            --bg-card: #1a0d2e;
            --text-white: #ffffff;
            --text-light: #e0e0e0;
            --accent-primary: #9333ea;
            --accent-blue: #3b82f6;
            --accent-pink: #ec4899;
            --border-light: rgba(255, 255, 255, 0.1);
        }

        /* Light theme overrides */
        [data-theme="light"] {
            --bg-dark: #f8fafc;
            --bg-card: #ffffff;
            --text-white: #1e293b;
            --text-light: #475569;
            --border-light: rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0118 0%, #1a0d2e 100%);
            color: var(--text-white);
            line-height: 1.6;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
        }

        [data-theme="light"] body,
        html[data-theme="light"] body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--text-white);
        }

        /* Animated Gradient Orbs Background */
        .orb-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
        }

        [data-theme="light"] .orb {
            opacity: 0.15;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, #d946ef 0%, #9333ea 50%, transparent 70%);
            top: -100px;
            right: -100px;
            animation: float1 20s ease-in-out infinite;
        }

        .orb-2 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, #3b82f6 0%, #2563eb 50%, transparent 70%);
            bottom: 100px;
            left: 5%;
            animation: float2 18s ease-in-out infinite;
            animation-delay: 5s;
        }

        .orb-3 {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, #ec4899 0%, #db2777 50%, transparent 70%);
            top: 50%;
            right: 10%;
            animation: float3 22s ease-in-out infinite;
            animation-delay: 10s;
        }

        @keyframes float1 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(100px, -100px); }
        }

        @keyframes float2 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-50px, 100px); }
        }

        @keyframes float3 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(50px, 100px); }
        }

        /* Content Wrapper */
        .content-wrapper {
            position: relative;
            z-index: 1;
        }

        /* Header */
        header {
            background: rgba(10, 1, 24, 0.7);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-light);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        [data-theme="light"] header {
            background: rgba(255, 255, 255, 0.9);
        }

        .header-container {
            max-width: 1400px;
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
            color: var(--text-white);
            font-weight: 700;
            font-size: 1.5rem;
            transition: opacity 0.3s ease;
        }

        .logo:hover {
            opacity: 0.8;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }

        nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        nav a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            font-size: 0.95rem;
        }

        nav a:hover {
            color: var(--accent-primary);
        }

        .theme-toggle {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-light);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-light);
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            background: rgba(147, 51, 234, 0.2);
            border-color: var(--accent-primary);
            color: var(--accent-primary);
        }

        /* Hero Section */
        .hero {
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 2rem;
            text-align: center;
            position: relative;
        }

        .hero-content {
            max-width: 800px;
            z-index: 2;
        }

        .hero-subtitle {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--accent-primary);
            margin-bottom: 1rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 8vw, 4.5rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        [data-theme="light"] .hero h1 {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            font-weight: 300;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            color: white;
            box-shadow: 0 10px 30px rgba(147, 51, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(147, 51, 234, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-white);
            border: 2px solid var(--border-light);
        }

        .btn-secondary:hover {
            border-color: var(--accent-primary);
            background: rgba(147, 51, 234, 0.1);
        }

        /* Features Section */
        .features {
            max-width: 1400px;
            margin: 4rem auto;
            padding: 4rem 2rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .section-header p {
            font-size: 1.1rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 1px solid var(--border-light);
            border-radius: 24px;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        [data-theme="light"] .feature-card {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.05) 0%, rgba(59, 130, 246, 0.03) 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(147, 51, 234, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover {
            border-color: var(--accent-primary);
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(147, 51, 234, 0.2);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.3) 0%, rgba(59, 130, 246, 0.2) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.5rem;
            color: var(--accent-primary);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--text-light);
            font-weight: 300;
            line-height: 1.8;
        }

        /* Stats Section */
        .stats {
            max-width: 1400px;
            margin: 4rem auto;
            padding: 4rem 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-card {
            padding: 2rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-light);
            font-weight: 500;
        }

        /* CTA Section */
        .cta {
            max-width: 1000px;
            margin: 4rem auto;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 1px solid var(--border-light);
            border-radius: 24px;
            text-align: center;
        }

        [data-theme="light"] .cta {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.05) 0%, rgba(59, 130, 246, 0.03) 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .cta h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .cta p {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 2rem;
        }

        /* Footer */
        footer {
            background: rgba(10, 1, 24, 0.7);
            backdrop-filter: blur(10px);
            border-top: 1px solid var(--border-light);
            padding: 3rem 2rem;
            margin-top: 4rem;
        }

        [data-theme="light"] footer {
            background: rgba(255, 255, 255, 0.9);
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-column h3 {
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .footer-column p {
            color: var(--text-light);
        }

        .footer-column a {
            display: block;
            color: var(--text-light);
            text-decoration: none;
            margin-bottom: 0.75rem;
            transition: color 0.3s ease;
        }

        .footer-column a:hover {
            color: var(--accent-primary);
        }

        .footer-bottom {
            border-top: 1px solid var(--border-light);
            padding-top: 2rem;
            text-align: center;
            color: var(--text-light);
        }

        /* Responsive */
        @media (max-width: 768px) {
            nav {
                display: none;
            }

            .hero {
                min-height: 70vh;
                padding: 2rem 1rem;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .features {
                padding: 2rem 1rem;
                margin: 2rem auto;
            }

            .stats {
                padding: 2rem 1rem;
                margin: 2rem auto;
            }

            .cta {
                padding: 2rem 1rem;
                margin: 2rem auto;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="orb-container">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="content-wrapper">
        <!-- Header -->
        <header>
            <div class="header-container">
                <a href="/" class="logo">
                    <div class="logo-icon">F</div>
                    <span>FindIN</span>
                </a>
                <nav>
                    <a href="#features">Fonctionnalités</a>
                    <a href="#stats">Résultats</a>
                    <a href="/login">Connexion</a>
                </nav>
                <button class="theme-toggle" id="themeToggle">
                    <i class="fas fa-moon theme-icon"></i>
                </button>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <div class="hero-subtitle">Révélez les talents cachés</div>
                <h1>Découvrez le potentiel de votre organisation</h1>
                <p>FindIN vous aide à identifier, valider et développer les compétences cachées au sein de vos équipes grâce à une plateforme intelligente et intuitive.</p>
                <div class="hero-buttons">
                    <a href="/register" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Commencer Maintenant
                    </a>
                    <a href="#features" class="btn btn-secondary">
                        <i class="fas fa-play-circle"></i> En Savoir Plus
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features" id="features">
            <div class="section-header">
                <h2>Nos Fonctionnalités Principales</h2>
                <p>Une suite complète d'outils pour gérer et valoriser les compétences de votre équipe</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Recherche Intelligente</h3>
                    <p>Trouvez rapidement les compétences spécifiques au sein de votre organisation avec notre moteur de recherche avancé.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3>Validation des Compétences</h3>
                    <p>Validez et certifiez les compétences de vos collaborateurs pour une meilleure crédibilité et reconnaissance.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Analytics Puissants</h3>
                    <p>Obtenez des insights détaillés sur les compétences de votre équipe avec des tableaux de bord interactifs.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Gestion des Talents</h3>
                    <p>Identifiez les talents cachés et créez des plans de développement personnalisés pour chaque collaborateur.</p>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats" id="stats">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Utilisateurs Actifs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Entreprises Partenaires</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">5000+</div>
                    <div class="stat-label">Compétences Validées</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">99%</div>
                    <div class="stat-label">Satisfaction Client</div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta">
            <h2>Prêt à Révéler les Talents ?</h2>
            <p>Rejoignez les entreprises qui transforment leur gestion des compétences avec FindIN</p>
            <div class="hero-buttons">
                <a href="/register" class="btn btn-primary">
                    <i class="fas fa-rocket"></i> Démarrer Gratuitement
                </a>
                <a href="#" class="btn btn-secondary">
                    <i class="fas fa-envelope"></i> Nous Contacter
                </a>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="footer-container">
                <div class="footer-grid">
                    <div class="footer-column">
                        <h3>FindIN</h3>
                        <p>Révélez les talents cachés de votre organisation.</p>
                    </div>
                    <div class="footer-column">
                        <h3>Produit</h3>
                        <a href="/features">Fonctionnalités</a>
                        <a href="/pricing">Tarification</a>
                        <a href="/security">Sécurité</a>
                        <a href="/roadmap">Roadmap</a>
                    </div>
                    <div class="footer-column">
                        <h3>Ressources</h3>
                        <a href="/documentation">Documentation</a>
                        <a href="/blog">Blog</a>
                        <a href="/tutorials">Tutoriels</a>
                        <a href="/community">Communauté</a>
                    </div>
                    <div class="footer-column">
                        <h3>Légal</h3>
                        <a href="/privacy">Confidentialité</a>
                        <a href="/terms">Conditions</a>
                        <a href="/cookies">Cookies</a>
                        <a href="/accessibility">Accessibilité</a>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2025 FindIN. Tous droits réservés.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="/assets/js/main.js"></script>
    <script>
        // Fallback inline script pour le toggle thème
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const html = document.documentElement;
            
            // Appliquer le thème sauvegardé
            const savedTheme = localStorage.getItem('findin-theme') || 'dark';
            html.setAttribute('data-theme', savedTheme);
            updateIcon(savedTheme);
            
            if (themeToggle) {
                themeToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentTheme = html.getAttribute('data-theme') || 'dark';
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    
                    html.setAttribute('data-theme', newTheme);
                    localStorage.setItem('findin-theme', newTheme);
                    updateIcon(newTheme);
                    
                    // Animation
                    this.style.transform = 'scale(1.1) rotate(180deg)';
                    setTimeout(() => { this.style.transform = ''; }, 300);
                });
            }
            
            function updateIcon(theme) {
                const icon = document.querySelector('#themeToggle .theme-icon');
                if (icon) {
                    icon.className = 'theme-icon fas fa-' + (theme === 'dark' ? 'sun' : 'moon');
                }
            }
            
            // Smooth scroll pour les liens d'ancrage
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href && href !== '#') {
                        const target = document.querySelector(href);
                        if (target) {
                            e.preventDefault();
                            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
