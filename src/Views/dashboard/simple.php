<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
$user_name = $_SESSION['user_name'] ?? 'Utilisateur';
$user_initial = strtoupper(substr($user_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIn - Welcome</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --bg-main: #e8eaed; --bg-sidebar: #1a1d29; --bg-card: #1a1d29; --text-dark: #1a1d29; --text-light: #6b7280; --text-sidebar: #a1a7b5; --accent-cyan: #00d4ff; --accent-purple: #8b5cf6; --accent-pink: #ec4899; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-main); color: var(--text-dark); min-height: 100vh; display: flex; }
        .sidebar { width: 220px; background: var(--bg-sidebar); min-height: 100vh; padding: 1.5rem 0; display: flex; flex-direction: column; position: fixed; left: 0; top: 0; bottom: 0; z-index: 100; }
        .logo { display: flex; align-items: center; gap: 0.75rem; padding: 0 1.5rem; margin-bottom: 2rem; text-decoration: none; }
        .logo-dot { width: 12px; height: 12px; background: var(--accent-cyan); border-radius: 50%; box-shadow: 0 0 10px var(--accent-cyan); }
        .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; }
        .user-profile { display: flex; align-items: center; gap: 0.75rem; padding: 0 1.5rem; margin-bottom: 2rem; }
        .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-purple), var(--accent-pink)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; }
        .user-name { color: #fff; font-weight: 500; font-size: 0.9rem; }
        .nav-menu { list-style: none; flex: 1; }
        .nav-item a { display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1.5rem; color: var(--text-sidebar); text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.2s; border-left: 3px solid transparent; }
        .nav-item a:hover, .nav-item a.active { color: #fff; background: rgba(255,255,255,0.05); border-left-color: var(--accent-cyan); }
        .nav-item a i { width: 20px; }
        .nav-section-title { padding: 1.5rem 1.5rem 0.5rem; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; color: #6b7280; }
        .sidebar-footer { padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); }
        .sidebar-footer a { display: flex; align-items: center; gap: 0.75rem; color: var(--text-sidebar); text-decoration: none; font-size: 0.9rem; padding: 0.5rem 0; }
        .sidebar-footer a:hover { color: #fff; }
        .main-content { flex: 1; margin-left: 220px; padding: 3rem 2rem; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .welcome-title { font-size: 2.5rem; font-weight: 700; margin-bottom: 2rem; text-align: center; }
        .search-container { width: 100%; max-width: 600px; margin-bottom: 3rem; }
        .search-box { display: flex; align-items: center; background: var(--bg-card); border-radius: 50px; padding: 0.5rem 0.5rem 0.5rem 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .search-box input { flex: 1; background: none; border: none; outline: none; font-size: 1rem; color: #fff; padding: 0.75rem 0; }
        .search-box input::placeholder { color: var(--text-sidebar); }
        .search-btn { width: 45px; height: 45px; background: var(--accent-cyan); border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
        .search-btn:hover { transform: scale(1.05); box-shadow: 0 0 15px var(--accent-cyan); }
        .search-btn i { color: #fff; font-size: 1.1rem; }
        .columns-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; width: 100%; max-width: 900px; }
        .column { text-align: center; }
        .column-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.2rem; }
        .column-icon.examples { background: rgba(0,212,255,0.15); color: var(--accent-cyan); }
        .column-icon.competences { background: rgba(139,92,246,0.15); color: var(--accent-purple); }
        .column-icon.limitations { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .column-title { font-size: 1rem; font-weight: 600; margin-bottom: 1rem; }
        .column-items { display: flex; flex-direction: column; gap: 0.75rem; }
        .column-item { background: var(--bg-card); color: #fff; padding: 0.85rem 1.25rem; border-radius: 10px; font-size: 0.85rem; text-align: left; line-height: 1.4; cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .column-item:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .footer { margin-top: auto; padding: 2rem 0; text-align: center; color: var(--text-light); font-size: 0.8rem; }
        .footer a { color: var(--accent-cyan); text-decoration: none; }
        @media (max-width: 900px) { .columns-container { grid-template-columns: 1fr; max-width: 400px; } }
        @media (max-width: 768px) { .sidebar { width: 70px; } .logo-text, .user-name, .nav-item span, .nav-section-title { display: none; } .user-profile, .logo { justify-content: center; padding: 0; } .nav-item a { justify-content: center; padding: 1rem; } .main-content { margin-left: 70px; padding: 2rem 1rem; } .welcome-title { font-size: 1.8rem; } }
    </style>
</head>
<body>
    <aside class="sidebar">
        <a href="/" class="logo"><div class="logo-dot"></div><span class="logo-text">FindIn</span></a>
        <div class="user-profile"><div class="user-avatar"><?php echo htmlspecialchars($user_initial); ?></div><span class="user-name"><?php echo htmlspecialchars($user_name); ?></span></div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/dashboard"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
            <li class="nav-item"><a href="/certifications"><i class="fas fa-certificate"></i><span>Certifications</span></a></li>
            <li class="nav-item"><a href="/competences"><i class="fas fa-puzzle-piece"></i><span>Compétences</span></a></li>
            <li class="nav-item"><a href="/profile"><i class="fas fa-user"></i><span>Mon Espace</span></a></li>
            <li class="nav-item"><a href="/plus"><i class="fas fa-ellipsis-h"></i><span>Plus</span></a></li>
            <li class="nav-section-title">Shortcuts</li>
            <li class="nav-item"><a href="/cvs"><i class="fas fa-file-alt"></i><span>CVs</span></a></li>
            <li class="nav-item"><a href="/reunions"><i class="fas fa-clock"></i><span>Réunions</span></a></li>
            <li class="nav-item"><a href="/tests"><i class="fas fa-clipboard-check"></i><span>Test de compétences</span></a></li>
            <li class="nav-item"><a href="/bilan"><i class="fas fa-chart-bar"></i><span>Bilan annuel</span></a></li>
        </ul>
        <div class="sidebar-footer"><a href="/settings"><i class="fas fa-cog"></i><span>Paramètres</span></a></div>
    </aside>
    <main class="main-content">
        <h1 class="welcome-title">Welcome to FindIn</h1>
        <div class="search-container">
            <form action="/search" method="GET" class="search-box">
                <input type="text" name="q" placeholder="Rechercher des compétences" autocomplete="off">
                <button type="submit" class="search-btn"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
        <div class="columns-container">
            <div class="column">
                <div class="column-icon examples"><i class="fas fa-lightbulb"></i></div>
                <h3 class="column-title">Examples</h3>
                <div class="column-items">
                    <div class="column-item">"Un expert en SQL"</div>
                    <div class="column-item">"Un junior en SSH"</div>
                    <div class="column-item">"Un stagiaire en RSE"</div>
                </div>
            </div>
            <div class="column">
                <div class="column-icon competences"><i class="fas fa-bolt"></i></div>
                <h3 class="column-title">Compétences</h3>
                <div class="column-items">
                    <a href="/competences" class="column-item">Compétences des salariés</a>
                    <a href="/bilan" class="column-item">Bilan de compétences</a>
                    <a href="/tests" class="column-item">Test de compétences</a>
                </div>
            </div>
            <div class="column">
                <div class="column-icon limitations"><i class="fas fa-exclamation-triangle"></i></div>
                <h3 class="column-title">Limitations</h3>
                <div class="column-items">
                    <div class="column-item">Peut occasionnellement générer des informations incorrectes.</div>
                    <div class="column-item">Peut produire des instructions nuisibles ou du contenu biaisé.</div>
                    <div class="column-item">Connaissance limitée de l'entreprise avant 2021.</div>
                </div>
            </div>
        </div>
        <footer class="footer"><p>&copy; 2025 FindIn. | <a href="/privacy">Confidentialité</a> | <a href="/terms">CGU</a> | <a href="/logout">Déconnexion</a></p></footer>
    </main>
</body>
</html>
