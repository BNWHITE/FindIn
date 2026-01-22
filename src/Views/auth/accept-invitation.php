<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accepter l'invitation - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0118;
            --bg-secondary: #1a0d2e;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --accent-purple: #9333ea;
            --accent-blue: #3b82f6;
            --border-color: rgba(255,255,255,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a0118 0%, #1a0d2e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
        }

        .container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }

        .card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }

        .form-text {
            background: rgba(147, 51, 234, 0.1);
            border: 1px solid rgba(147, 51, 234, 0.3);
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 8px;
        }

        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.2);
            color: var(--text-primary);
            font-size: 14px;
            transition: all 0.2s;
        }

        input[type="password"]:focus,
        input[type="text"]:focus {
            outline: none;
            border-color: var(--accent-purple);
            background: rgba(0, 0, 0, 0.3);
        }

        .error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #ff6b6b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.5);
            color: #4ade80;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .btn {
            width: 100%;
            padding: 12px 16px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(147, 51, 234, 0.3);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .divider {
            text-align: center;
            color: var(--text-secondary);
            margin: 20px 0;
            font-size: 12px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .login-link a {
            color: var(--accent-purple);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .password-hint {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo">💼</div>
                <div class="title">Créer votre compte</div>
                <div class="subtitle">Acceptez votre invitation FindIN</div>
            </div>

            <?php if ($error): ?>
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($invitation): ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Prénom</label>
                        <div class="form-text">
                            <strong><?php echo htmlspecialchars($invitation['prenom']); ?></strong>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nom</label>
                        <div class="form-text">
                            <strong><?php echo htmlspecialchars($invitation['nom']); ?></strong>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <div class="form-text">
                            <strong><?php echo htmlspecialchars($invitation['email']); ?></strong>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••" 
                            required
                            minlength="8"
                        >
                        <div class="password-hint">
                            <i class="fas fa-info-circle"></i> Minimum 8 caractères
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">Confirmer le mot de passe</label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            placeholder="••••••••" 
                            required
                            minlength="8"
                        >
                    </div>

                    <button type="submit" class="btn">
                        <i class="fas fa-check"></i> Créer mon compte
                    </button>
                </form>
            <?php else: ?>
                <div class="error">
                    <i class="fas fa-times-circle"></i>
                    Cette invitation est invalide, expirée ou a déjà été utilisée.
                </div>

                <button class="btn" onclick="window.location.href='/FindIn/public/login'" style="margin-top: 20px;">
                    <i class="fas fa-arrow-left"></i> Retour à la connexion
                </button>
            <?php endif; ?>

            <div class="login-link">
                Vous avez déjà un compte? <a href="/FindIn/public/login">Connectez-vous</a>
            </div>
        </div>
    </div>
</body>
</html>
