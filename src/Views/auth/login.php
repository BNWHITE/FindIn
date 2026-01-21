<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard');
    exit;
}
$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - FindIN</title>
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
            --input-bg: rgba(255,255,255,0.05);
        }
        [data-theme="light"] {
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: rgba(0,0,0,0.1);
            --input-bg: #f1f5f9;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            display: flex;
            color: var(--text-primary);
        }
        .auth-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        .auth-visual {
            flex: 1;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 50%, #ec4899 100%);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        @media (min-width: 1024px) {
            .auth-visual { display: flex; }
        }
        .auth-visual::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.1;
        }
        .visual-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
            max-width: 400px;
        }
        .visual-icon {
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.2);
            border-radius: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            backdrop-filter: blur(10px);
        }
        .visual-icon i { font-size: 3rem; }
        .visual-content h2 { font-size: 2rem; font-weight: 700; margin-bottom: 1rem; }
        .visual-content p { opacity: 0.9; line-height: 1.7; }
        .auth-form-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: var(--bg-secondary);
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-primary);
            margin-bottom: 2rem;
        }
        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }
        .logo span { font-size: 1.5rem; font-weight: 700; }
        .auth-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; }
        .auth-header p { color: var(--text-secondary); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-primary);
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }
        .form-control {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-size: 1rem;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent-purple);
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.15);
        }
        .form-control::placeholder { color: var(--text-secondary); }
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        .remember-me input {
            width: 18px;
            height: 18px;
            accent-color: var(--accent-purple);
        }
        .forgot-link {
            color: var(--accent-purple);
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover { text-decoration: underline; }
        .btn-primary {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(147, 51, 234, 0.3);
        }
        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 2rem 0;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }
        .social-login {
            display: flex;
            gap: 1rem;
        }
        .btn-social {
            flex: 1;
            padding: 0.875rem 1.5rem;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-size: 1rem;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-decoration: none;
            font-weight: 500;
        }
        .btn-social i {
            font-size: 1.25rem;
        }
        .btn-social:hover {
            background: var(--border-color);
            transform: translateY(-2px);
        }
        .btn-google {
            background: #fff;
            border-color: #dadce0;
            color: #3c4043;
        }
        .btn-google:hover {
            background: #f8f9fa;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-google i {
            background: linear-gradient(135deg, #4285f4, #34a853, #fbbc05, #ea4335);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        [data-theme="dark"] .btn-google {
            background: #1a1a2e;
            border-color: #3d3d5c;
            color: #fff;
        }
        [data-theme="dark"] .btn-google:hover {
            background: #252545;
        }
        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-secondary);
        }
        .auth-footer a {
            color: var(--accent-purple);
            text-decoration: none;
            font-weight: 600;
        }
        .auth-footer a:hover { text-decoration: underline; }
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
        }
        .theme-toggle {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            width: 44px;
            height: 44px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
            font-size: 1.1rem;
            transition: all 0.3s ease;
            z-index: 100;
        }
        .theme-toggle:hover { transform: scale(1.05); }
        .back-home {
            position: fixed;
            top: 1.5rem;
            left: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
            z-index: 100;
        }
        .back-home:hover { color: var(--accent-purple); }
    </style>
</head>
<body>
    <a href="/" class="back-home"><i class="fas fa-arrow-left"></i> Retour</a>
    <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>

    <div class="auth-container">
        <div class="auth-visual">
            <div class="visual-content">
                <div class="visual-icon"><i class="fas fa-users"></i></div>
                <h2>Bienvenue sur FindIN</h2>
                <p>La plateforme intelligente de gestion des compétences. Identifiez les talents, optimisez vos équipes et boostez la productivité.</p>
            </div>
        </div>

        <div class="auth-form-section">
            <div class="auth-card">
                <div class="auth-header">
                    <a href="/" class="logo">
                        <div class="logo-icon"><i class="fas fa-search"></i></div>
                        <span>FindIN</span>
                    </a>
                    <h1>Connexion</h1>
                    <p>Accédez à votre espace personnel</p>
                </div>

                <?php if ($error): ?>
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="/login">
                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" class="form-control" placeholder="vous@exemple.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Se souvenir de moi
                        </label>
                        <a href="/forgot-password" class="forgot-link">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                </form>

                <div class="divider">ou continuer avec</div>

                <div class="social-login">
                    <a href="/auth/google" class="btn-social btn-google">
                        <i class="fab fa-google"></i>
                        <span>Google</span>
                    </a>
                </div>

                <div class="auth-footer">
                    Pas encore de compte ? <a href="/register">Créer un compte</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const toggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        const icon = toggle.querySelector('i');
        const saved = localStorage.getItem('theme') || 'dark';
        html.setAttribute('data-theme', saved);
        icon.className = saved === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        toggle.addEventListener('click', () => {
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            icon.className = next === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        });
    </script>
</body>
</html>
