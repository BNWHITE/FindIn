<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Produit</title>
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
            opacity: 0.3;
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

        @keyframes float1 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(100px, -100px); }
        }

        @keyframes float2 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-50px, 100px); }
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
        }

        header {
            background: rgba(10, 1, 24, 0.7);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-light);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
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

        .page-hero {
            min-height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 2rem;
            text-align: center;
            border-bottom: 1px solid var(--border-light);
        }

        .page-hero h1 {
            font-size: clamp(2rem, 6vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-hero p {
            font-size: 1.1rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
        }

        .page-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }

        .section {
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .section-title i {
            color: var(--accent-primary);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .feature-card {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            border-color: var(--accent-primary);
            transform: translateY(-5px);
        }

        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--text-light);
            line-height: 1.8;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .pricing-card {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .pricing-card.featured {
            border-color: var(--accent-primary);
            transform: scale(1.05);
        }

        .pricing-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .price {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--accent-primary);
            margin-bottom: 0.5rem;
        }

        .price-period {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .features-list {
            list-style: none;
            margin-bottom: 2rem;
        }

        .features-list li {
            padding: 0.75rem 0;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .features-list i {
            color: var(--accent-primary);
        }

        .btn {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(147, 51, 234, 0.3);
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

        footer {
            background: rgba(10, 1, 24, 0.7);
            backdrop-filter: blur(10px);
            border-top: 1px solid var(--border-light);
            padding: 3rem 2rem;
            margin-top: 4rem;
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

        @media (max-width: 768px) {
            nav {
                display: none;
            }

            .page-hero {
                min-height: 40vh;
                padding: 2rem 1rem;
            }

            .page-hero h1 {
                font-size: 1.75rem;
            }

            .page-content {
                padding: 2rem 1rem;
            }

            .pricing-card.featured {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="orb-container">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    <div class="content-wrapper">
        <header>
            <div class="header-container">
                <a href="/" class="logo">
                    <div class="logo-icon">F</div>
                    <span>FindIN</span>
                </a>
                <nav>
                    <a href="/">Accueil</a>
                    <a href="/product">Produit</a>
                    <a href="/features">Fonctionnalités</a>
                    <a href="/pricing">Tarification</a>
                    <a href="/security">Sécurité</a>
                    <a href="/login">Connexion</a>
                </nav>
                <button class="theme-toggle" id="themeToggle">
                    <i class="fas fa-moon theme-icon"></i>
                </button>
            </div>
        </header>

        <section class="page-hero">
            <div>
                <h1>Notre Produit</h1>
                <p>Une plateforme complète pour révéler, valider et développer les talents cachés de votre organisation</p>
            </div>
        </section>

        <div class="page-content">
            <section class="section">
                <h2 class="section-title">
                    <i class="fas fa-cube"></i>
                    Ce que nous offrons
                </h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <h3><i class="fas fa-search"></i> Moteur de Recherche Intelligent</h3>
                        <p>Trouvez les talents et les compétences spécifiques en utilisant le langage naturel. Notre technologie IA comprend vos besoins.</p>
                    </div>
                    <div class="feature-card">
                        <h3><i class="fas fa-certificate"></i> Validation Blockchain</h3>
                        <p>Validez et certifiez les compétences avec traçabilité complète. Chaque validation est enregistrée de manière sécurisée.</p>
                    </div>
                    <div class="feature-card">
                        <h3><i class="fas fa-chart-line"></i> Analytics Avancés</h3>
                        <p>Obtenez des insights détaillés sur les compétences de votre organisation avec des tableaux de bord interactifs.</p>
                    </div>
                </div>
            </section>

            <section class="section">
                <h2 class="section-title">
                    <i class="fas fa-users"></i>
                    Pour qui ?
                </h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <h3>Entreprises</h3>
                        <p>Optimisez votre capital humain et découvrez les talents cachés au sein de vos équipes.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Ressources Humaines</h3>
                        <p>Simplifiez la gestion des compétences et améliorez vos processus de recrutement interne.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Collaborateurs</h3>
                        <p>Mettez en valeur vos compétences et découvrez de nouvelles opportunités internes.</p>
                    </div>
                </div>
            </section>
        </div>

        <footer>
            <div class="footer-container">
                <div class="footer-grid">
                    <div class="footer-column">
                        <h3>Produit</h3>
                        <a href="/product">Vue d'ensemble</a>
                        <a href="/features">Fonctionnalités</a>
                        <a href="/pricing">Tarification</a>
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
                        <h3>Entreprise</h3>
                        <a href="/security">Sécurité</a>
                        <a href="/privacy">Confidentialité</a>
                        <a href="/terms">Conditions</a>
                        <a href="/accessibility">Accessibilité</a>
                    </div>
                    <div class="footer-column">
                        <h3>FindIN</h3>
                        <p style="color: var(--text-light);">Révélez les talents cachés de votre organisation.</p>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2024 FindIN. Tous droits réservés.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="/assets/js/main.js"></script>
</body>
</html>
