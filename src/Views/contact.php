<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
$success = $error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && $email && $message) {
        $success = "Merci ! Votre message a été envoyé. Nous vous répondrons sous 48h.";
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg-primary: #0a0118; --bg-secondary: #1a0d2e; --bg-card: #241538; --text-primary: #ffffff; --text-secondary: #a0a0a0; --accent-purple: #9333ea; --accent-blue: #3b82f6; --accent-green: #10b981; --border-color: rgba(255,255,255,0.1); --input-bg: rgba(255,255,255,0.05); }
        [data-theme="light"] { --bg-primary: #f8fafc; --bg-secondary: #ffffff; --bg-card: #ffffff; --text-primary: #1e293b; --text-secondary: #64748b; --border-color: rgba(0,0,0,0.1); --input-bg: #f1f5f9; }
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
        .hero { padding: 5rem 2rem; text-align: center; }
        .hero h1 { font-size: 3rem; font-weight: 800; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--text-secondary); font-size: 1.25rem; max-width: 600px; margin: 0 auto; }
        .content { max-width: 1000px; margin: 0 auto; padding: 0 2rem 4rem; display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; }
        @media (max-width: 768px) { .content { grid-template-columns: 1fr; } .nav-links { display: none; } .hero h1 { font-size: 2rem; } }
        .contact-info { display: flex; flex-direction: column; gap: 2rem; }
        .info-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; display: flex; gap: 1rem; align-items: flex-start; }
        .info-icon { width: 50px; height: 50px; background: linear-gradient(135deg, rgba(147,51,234,0.2), rgba(59,130,246,0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--accent-purple); font-size: 1.25rem; flex-shrink: 0; }
        .info-card h3 { font-size: 1.1rem; margin-bottom: 0.25rem; }
        .info-card p { color: var(--text-secondary); font-size: 0.95rem; }
        .contact-form { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; padding: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 0.875rem 1rem; background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 10px; font-size: 1rem; color: var(--text-primary); transition: all 0.3s; }
        .form-control:focus { outline: none; border-color: var(--accent-purple); box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.15); }
        textarea.form-control { min-height: 150px; resize: vertical; }
        .btn-submit { width: 100%; padding: 1rem; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(147, 51, 234, 0.3); }
        .alert { padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; }
        .alert-success { background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: var(--accent-green); }
        .alert-error { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; }
        .footer { background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 3rem 2rem; text-align: center; }
        .footer-links { display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .footer-links a { color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; }
        .footer p { color: var(--text-secondary); font-size: 0.85rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="/" class="logo"><div class="logo-icon"><i class="fas fa-search"></i></div><span>FindIN</span></a>
            <nav class="nav-links">
                <a href="/">Accueil</a><a href="/pricing">Tarifs</a><a href="/about">À propos</a>
                <?php if (isset($_SESSION['user_id'])): ?><a href="/dashboard" class="btn-primary">Dashboard</a><?php else: ?><a href="/login" class="btn-primary">Connexion</a><?php endif; ?>
                <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            </nav>
        </div>
    </header>

    <section class="hero">
        <h1>Contactez-nous</h1>
        <p>Une question ? Une demande de démo ? Notre équipe vous répond sous 48h.</p>
    </section>

    <div class="content">
        <div class="contact-info">
            <div class="info-card">
                <div class="info-icon"><i class="fas fa-envelope"></i></div>
                <div><h3>Email</h3><p>contact@findin.io</p></div>
            </div>
            <div class="info-card">
                <div class="info-icon"><i class="fas fa-phone"></i></div>
                <div><h3>Téléphone</h3><p>+33 1 23 45 67 89</p></div>
            </div>
            <div class="info-card">
                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div><h3>Adresse</h3><p>28 rue de la Paix<br>75002 Paris, France</p></div>
            </div>
            <div class="info-card">
                <div class="info-icon"><i class="fas fa-clock"></i></div>
                <div><h3>Horaires</h3><p>Lun - Ven : 9h - 18h</p></div>
            </div>
        </div>

        <div class="contact-form">
            <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="name">Nom complet *</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Jean Dupont" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="vous@entreprise.com" required>
                </div>
                <div class="form-group">
                    <label for="subject">Sujet</label>
                    <select id="subject" name="subject" class="form-control">
                        <option value="">Sélectionner...</option>
                        <option value="demo">Demande de démo</option>
                        <option value="support">Support technique</option>
                        <option value="sales">Commercial</option>
                        <option value="partnership">Partenariat</option>
                        <option value="other">Autre</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" class="form-control" placeholder="Votre message..." required></textarea>
                </div>
                <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Envoyer le message</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-links"><a href="/about">À propos</a><a href="/carrieres">Carrières</a><a href="/presse">Presse</a><a href="/privacy">Confidentialité</a><a href="/terms">Conditions</a><a href="/cgu">CGU</a></div>
        <p>&copy; 2024 FindIN. Tous droits réservés.</p>
    </footer>

    <script>const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});</script>
</body>
</html>
