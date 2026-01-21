<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <meta name="theme-color" content="#9333ea">
    <title>FindIN - Gestion des Compétences</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        :root {
            --bg-primary: #0a0118;
            --bg-secondary: #1a0d2e;
            --bg-card: #241538;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --accent-purple: #9333ea;
            --accent-blue: #3b82f6;
            --accent-green: #10b981;
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
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); margin: 0; }
        .findin-header { 
            background: var(--bg-secondary); 
            border-bottom: 1px solid var(--border-color); 
            padding: 1rem 2rem; 
            position: sticky; 
            top: 0; 
            z-index: 100; 
        }
        .header-container { 
            max-width: 1200px; 
            margin: 0 auto; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .findin-logo { 
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
            gap: 1.5rem; 
            align-items: center; 
        }
        .nav-link { 
            color: var(--text-secondary); 
            text-decoration: none; 
            font-weight: 500; 
            transition: color 0.3s; 
        }
        .nav-link:hover { 
            color: var(--accent-purple); 
        }
        .btn-primary { 
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); 
            color: white; 
            padding: 0.6rem 1.25rem; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 600; 
            transition: all 0.3s; 
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 5px 20px rgba(147,51,234,0.3); 
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
            transition: all 0.3s; 
        }
        .theme-toggle:hover { 
            border-color: var(--accent-purple); 
        }
        .nav-toggle { 
            display: none; 
            flex-direction: column; 
            gap: 5px; 
            background: none; 
            border: none; 
            cursor: pointer; 
            padding: 5px; 
        }
        .nav-toggle span { 
            width: 25px; 
            height: 3px; 
            background: var(--text-primary); 
            border-radius: 3px; 
            transition: all 0.3s; 
        }
        .nav-panel { 
            display: none; 
            flex-direction: column; 
            gap: 1rem; 
            padding: 1.5rem; 
            background: var(--bg-secondary); 
            border-top: 1px solid var(--border-color); 
        }
        .nav-panel a { 
            color: var(--text-secondary); 
            text-decoration: none; 
            font-weight: 500; 
            padding: 0.5rem 0; 
        }
        .nav-panel.active { 
            display: flex; 
        }
        @media (max-width: 768px) { 
            .nav-links { display: none; } 
            .nav-toggle { display: flex; } 
        }
    </style>
</head>
<body>
    <header class="findin-header">
        <div class="header-container">
            <a href="/" class="findin-logo">
                <div class="logo-icon"><i class="fas fa-search"></i></div>
                <span>FindIN</span>
            </a>

            <nav class="nav-links">
                <a href="/" class="nav-link">Accueil</a>
                <a href="/about" class="nav-link">À propos</a>
                <a href="/pricing" class="nav-link">Tarifs</a>
                <a href="/contact" class="nav-link">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/dashboard" class="btn-primary">Tableau de bord</a>
                <?php else: ?>
                    <a href="/login" class="btn-primary">Se connecter</a>
                <?php endif; ?>
                <button class="theme-toggle" id="themeToggle" aria-label="Basculer thème">
                    <i class="fas fa-moon theme-icon"></i>
                </button>
            </nav>

            <button class="nav-toggle" id="navToggle" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>

        <nav class="nav-panel" id="navPanel">
            <a href="/">Accueil</a>
            <a href="/about">À propos</a>
            <a href="/pricing">Tarifs</a>
            <a href="/contact">Contact</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/dashboard">Tableau de bord</a>
                <a href="/logout">Déconnexion</a>
            <?php else: ?>
                <a href="/login">Se connecter</a>
                <a href="/register">S'inscrire</a>
            <?php endif; ?>
        </nav>
    </header>

    <script>
        // Theme toggle
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        const icon = themeToggle.querySelector('i');
        const savedTheme = localStorage.getItem('theme') || 'dark';
        html.setAttribute('data-theme', savedTheme);
        icon.className = savedTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        
        themeToggle.addEventListener('click', () => {
            const newTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            icon.className = newTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        });

        // Mobile nav toggle
        const navToggle = document.getElementById('navToggle');
        const navPanel = document.getElementById('navPanel');
        navToggle.addEventListener('click', () => {
            navPanel.classList.toggle('active');
        });
    </script>
