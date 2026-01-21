<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard');
    exit;
}
$error = $_SESSION['register_error'] ?? null;
$success = $_SESSION['register_success'] ?? null;
unset($_SESSION['register_error'], $_SESSION['register_success']);
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - FindIN</title>
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
            --accent-green: #10b981;
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
        .auth-container { display: flex; width: 100%; min-height: 100vh; }
        .auth-visual {
            flex: 1;
            background: linear-gradient(135deg, #ec4899 0%, #9333ea 50%, #3b82f6 100%);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        @media (min-width: 1024px) { .auth-visual { display: flex; } }
        .auth-visual::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.1;
        }
        .visual-content { position: relative; z-index: 1; text-align: center; color: white; max-width: 420px; }
        .visual-icon {
            width: 120px; height: 120px;
            background: rgba(255,255,255,0.2);
            border-radius: 2rem;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 2rem;
            backdrop-filter: blur(10px);
        }
        .visual-icon i { font-size: 3rem; }
        .visual-content h2 { font-size: 2rem; font-weight: 700; margin-bottom: 1rem; }
        .visual-content p { opacity: 0.9; line-height: 1.7; margin-bottom: 2rem; }
        .features-list { text-align: left; }
        .feature-item { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .feature-item i { font-size: 1.25rem; }
        .auth-form-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: var(--bg-secondary);
            overflow-y: auto;
        }
        .auth-card { width: 100%; max-width: 480px; }
        .auth-header { text-align: center; margin-bottom: 2rem; }
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }
        .logo-icon {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1.25rem;
        }
        .logo span { font-size: 1.5rem; font-weight: 700; }
        .auth-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; }
        .auth-header p { color: var(--text-secondary); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block; margin-bottom: 0.5rem;
            font-weight: 500; font-size: 0.9rem;
            color: var(--text-primary);
        }
        .input-wrapper { position: relative; }
        .input-wrapper i {
            position: absolute; left: 1rem; top: 50%;
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
        select.form-control {
            padding-left: 2.75rem;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23888' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
        }
        .password-strength { margin-top: 0.5rem; }
        .strength-bar {
            height: 4px; background: var(--border-color);
            border-radius: 2px; overflow: hidden;
        }
        .strength-fill { height: 100%; width: 0; transition: all 0.3s ease; border-radius: 2px; }
        .strength-text { font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem; }
        .terms-check {
            display: flex; align-items: flex-start; gap: 0.75rem;
            margin-bottom: 1.5rem; font-size: 0.9rem;
        }
        .terms-check input { width: 18px; height: 18px; margin-top: 2px; accent-color: var(--accent-purple); }
        .terms-check a { color: var(--accent-purple); text-decoration: none; }
        .terms-check a:hover { text-decoration: underline; }
        .btn-primary {
            width: 100%; padding: 1rem;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            color: white; border: none; border-radius: 12px;
            font-size: 1rem; font-weight: 600; cursor: pointer;
            transition: all 0.3s ease;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(147, 51, 234, 0.3);
        }
        .divider {
            display: flex; align-items: center; gap: 1rem;
            margin: 1.5rem 0; color: var(--text-secondary); font-size: 0.875rem;
        }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border-color); }
        .social-login { display: flex; gap: 1rem; }
        .btn-social {
            flex: 1; padding: 0.875rem 1.5rem;
            background: var(--input-bg); border: 1px solid var(--border-color);
            border-radius: 12px; font-size: 1rem; color: var(--text-primary);
            cursor: pointer; transition: all 0.3s ease;
            display: flex; align-items: center; justify-content: center;
            gap: 0.75rem; text-decoration: none; font-weight: 500;
        }
        .btn-social i { font-size: 1.25rem; }
        .btn-social:hover { background: var(--border-color); transform: translateY(-2px); }
        .btn-google {
            background: #fff; border-color: #dadce0; color: #3c4043;
        }
        .btn-google:hover {
            background: #f8f9fa; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-google i {
            background: linear-gradient(135deg, #4285f4, #34a853, #fbbc05, #ea4335);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        [data-theme="dark"] .btn-google {
            background: #1a1a2e; border-color: #3d3d5c; color: #fff;
        }
        [data-theme="dark"] .btn-google:hover { background: #252545; }
        .auth-footer { text-align: center; margin-top: 1.5rem; color: var(--text-secondary); }
        .auth-footer a { color: var(--accent-purple); text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }
        .error-message, .success-message {
            padding: 1rem; border-radius: 12px;
            margin-bottom: 1.5rem; text-align: center; font-size: 0.9rem;
        }
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }
        .success-message {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--accent-green);
        }
        .theme-toggle {
            position: fixed; top: 1.5rem; right: 1.5rem;
            width: 44px; height: 44px;
            background: var(--bg-card); border: 1px solid var(--border-color);
            border-radius: 12px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-primary); font-size: 1.1rem;
            transition: all 0.3s ease; z-index: 100;
        }
        .theme-toggle:hover { transform: scale(1.05); }
        .back-home {
            position: fixed; top: 1.5rem; left: 1.5rem;
            display: flex; align-items: center; gap: 0.5rem;
            color: var(--text-secondary); text-decoration: none;
            font-size: 0.9rem; transition: color 0.3s ease; z-index: 100;
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
                <div class="visual-icon"><i class="fas fa-rocket"></i></div>
                <h2>Rejoignez FindIN</h2>
                <p>Créez votre compte gratuitement et commencez à exploiter le potentiel de vos équipes.</p>
                <div class="features-list">
                    <div class="feature-item"><i class="fas fa-check-circle"></i> <span>Analyse intelligente des compétences</span></div>
                    <div class="feature-item"><i class="fas fa-check-circle"></i> <span>Matching automatique des talents</span></div>
                    <div class="feature-item"><i class="fas fa-check-circle"></i> <span>Tableaux de bord personnalisés</span></div>
                    <div class="feature-item"><i class="fas fa-check-circle"></i> <span>Export et rapports détaillés</span></div>
                </div>
            </div>
        </div>

        <div class="auth-form-section">
            <div class="auth-card">
                <div class="auth-header">
                    <a href="/" class="logo">
                        <div class="logo-icon"><i class="fas fa-search"></i></div>
                        <span>FindIN</span>
                    </a>
                    <h1>Créer un compte</h1>
                    <p>Inscription gratuite en 30 secondes</p>
                </div>

                <?php if ($error): ?>
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="success-message"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="/register" id="registerForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="prenom">Prénom</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user"></i>
                                <input type="text" id="prenom" name="prenom" class="form-control" placeholder="Jean" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user"></i>
                                <input type="text" id="nom" name="nom" class="form-control" placeholder="Dupont" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Adresse email professionnelle</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" class="form-control" placeholder="vous@entreprise.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="departement">Département</label>
                        <div class="input-wrapper">
                            <i class="fas fa-building"></i>
                            <select id="departement" name="departement" class="form-control">
                                <option value="">Sélectionner...</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Product">Product</option>
                                <option value="Design">Design</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Sales">Sales</option>
                                <option value="HR">Ressources Humaines</option>
                                <option value="Finance">Finance</option>
                                <option value="Other">Autre</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Min. 8 caractères" required minlength="8">
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                            <div class="strength-text" id="strengthText">Entrez un mot de passe</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">Confirmer le mot de passe</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="terms-check">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">J'accepte les <a href="/cgu">CGU</a>, la <a href="/privacy">Politique de confidentialité</a> et les <a href="/terms">Conditions d'utilisation</a></label>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-user-plus"></i> Créer mon compte
                    </button>
                </form>

                <div class="divider">ou s'inscrire avec</div>

                <div class="social-login">
                    <a href="/auth/google" class="btn-social btn-google">
                        <i class="fab fa-google"></i>
                        <span>Google</span>
                    </a>
                </div>

                <div class="auth-footer">
                    Déjà un compte ? <a href="/login">Se connecter</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Theme toggle
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

        // Password strength
        const pwd = document.getElementById('password');
        const fill = document.getElementById('strengthFill');
        const text = document.getElementById('strengthText');
        pwd.addEventListener('input', () => {
            const val = pwd.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (val.length >= 12) score++;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
            if (/\d/.test(val)) score++;
            if (/[^a-zA-Z0-9]/.test(val)) score++;
            const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#10b981'];
            const labels = ['Très faible', 'Faible', 'Moyen', 'Fort', 'Excellent'];
            fill.style.width = (score * 20) + '%';
            fill.style.background = colors[Math.max(0, score - 1)] || colors[0];
            text.textContent = labels[Math.max(0, score - 1)] || 'Entrez un mot de passe';
        });
    </script>
</body>
</html>
