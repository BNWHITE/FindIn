<?php
// Page d'accueil FindIN
if (session_status() === PHP_SESSION_NONE) session_start();

// Charger la BD si possible
$userCount = "?";
try {
    if (function_exists('Database::getInstance')) {
        $db = Database::getInstance();
        $result = $db->query("SELECT COUNT(*) as total FROM utilisateurs")->fetch();
        $userCount = $result['total'] ?? "?";
    }
} catch (Exception $e) {
    // Silencieusement échouer
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Gestion des Compétences</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        [data-theme="light"] {
            --bg-dark: #f8fafc;
            --bg-card: #ffffff;
            --text-white: #1e293b;
            --text-light: #475569;
            --border-light: rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-dark);
            color: var(--text-white);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            transition: background 0.3s ease;
        }

        .container {
            max-width: 1000px;
            width: 100%;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 80px;
            padding: 0 20px;
        }

        .logo {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-auth {
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-login {
            background: transparent;
            color: var(--accent-primary);
            border: 2px solid var(--accent-primary);
        }

        .btn-login:hover {
            background: var(--accent-primary);
            color: white;
        }

        .btn-register {
            background: var(--accent-primary);
            color: white;
        }

        .btn-register:hover {
            background: var(--accent-pink);
        }

        .hero {
            text-align: center;
            margin-bottom: 100px;
        }

        .hero h1 {
            font-size: 64px;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 20px;
            color: var(--text-light);
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-cta {
            padding: 16px 48px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-pink));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(147, 51, 234, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: var(--accent-blue);
            border: 2px solid var(--accent-blue);
        }

        .btn-secondary:hover {
            background: var(--accent-blue);
            color: white;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 100px;
        }

        .feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s;
        }

        .feature-card:hover {
            border-color: var(--accent-primary);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(147, 51, 234, 0.2);
        }

        .feature-icon {
            font-size: 48px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feature-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: var(--text-white);
        }

        .feature-card p {
            color: var(--text-light);
            line-height: 1.6;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 100px;
            text-align: center;
        }

        .stat {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 30px;
        }

        .stat-number {
            font-size: 40px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 14px;
        }

        footer {
            text-align: center;
            margin-top: 100px;
            padding-top: 40px;
            border-top: 1px solid var(--border-light);
            color: var(--text-light);
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 40px;
            }

            .hero p {
                font-size: 18px;
            }

            header {
                flex-direction: column;
                gap: 20px;
                margin-bottom: 40px;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .btn-cta {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">🎯 FindIN</div>
            <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="auth-buttons">
                <a href="/login" class="btn-auth btn-login">Se Connecter</a>
                <a href="/register" class="btn-auth btn-register">S'Inscrire</a>
            </div>
            <?php else: ?>
            <div class="auth-buttons">
                <span style="padding: 10px 24px;">Bienvenue, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?></span>
                <a href="/dashboard" class="btn-auth btn-register">Tableau de Bord</a>
                <a href="/logout" class="btn-auth btn-login">Déconnexion</a>
            </div>
            <?php endif; ?>
        </header>

        <section class="hero">
            <h1>Gestion des Compétences Simplifiée</h1>
            <p>Découvrez une plateforme moderne pour suivre, développer et valoriser les compétences de votre équipe.</p>
            
            <div class="cta-buttons">
                <a href="/login" class="btn-cta btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Se Connecter
                </a>
                <a href="/register" class="btn-cta btn-secondary">
                    <i class="fas fa-user-plus"></i> Créer un Compte
                </a>
            </div>
        </section>

        <section class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3>Suivi des Compétences</h3>
                <p>Visualisez l'évolution des compétences de votre équipe en temps réel.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-book"></i>
                </div>
                <h3>Formations</h3>
                <p>Accédez à des ressources de formation pour améliorer vos compétences.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Collaboration</h3>
                <p>Partagez vos connaissances et collaborez avec votre équipe.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3>Certifications</h3>
                <p>Obtenez des certifications pour valider vos compétences.</p>
            </div>
        </section>

        <section class="stats">
            <div class="stat">
                <div class="stat-number">100+</div>
                <div class="stat-label">Compétences Référencées</div>
            </div>
            <div class="stat">
                <div class="stat-number"><?php echo $userCount; ?></div>
                <div class="stat-label">Utilisateurs Actifs</div>
            </div>
            <div class="stat">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support Disponible</div>
            </div>
        </section>

        <footer>
            <p>&copy; 2026 FindIN. Tous droits réservés. | Plateforme de gestion des compétences</p>
        </footer>
    </div>
</body>
</html>
