<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
$currentPage = 'reunions';
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userRole = $_SESSION['user_role'] ?? 'collaborateur';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réunions - FindIN</title>
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="/assets/css/dashboard.css" rel="stylesheet">
    <style>
        .calendar-container { display: grid; grid-template-columns: 1fr 350px; gap: 1.5rem; }
        @media (max-width: 1024px) { .calendar-container { grid-template-columns: 1fr; } }
        .mini-calendar { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; }
        .cal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .cal-nav { display: flex; gap: 0.5rem; }
        .cal-nav button { background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: 8px; width: 32px; height: 32px; cursor: pointer; color: var(--text-primary); }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.25rem; text-align: center; }
        .cal-day-name { font-size: 0.75rem; color: var(--text-secondary); padding: 0.5rem; font-weight: 600; }
        .cal-day { padding: 0.5rem; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.2s; }
        .cal-day:hover { background: var(--bg-hover); }
        .cal-day.today { background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; }
        .cal-day.has-event::after { content: ''; display: block; width: 4px; height: 4px; background: var(--accent-purple); border-radius: 50%; margin: 2px auto 0; }
        .cal-day.other { opacity: 0.3; }
        .events-panel { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; }
        .events-date { font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .event-item { background: var(--bg-primary); border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; border-left: 4px solid var(--accent-purple); }
        .event-time { font-size: 0.8rem; color: var(--accent-purple); font-weight: 600; margin-bottom: 0.25rem; }
        .event-title { font-weight: 600; margin-bottom: 0.25rem; }
        .event-desc { font-size: 0.85rem; color: var(--text-secondary); }
        .event-item.blue { border-left-color: var(--accent-blue); }
        .event-item.blue .event-time { color: var(--accent-blue); }
        .event-item.green { border-left-color: var(--accent-green); }
        .event-item.green .event-time { color: var(--accent-green); }
        .upcoming { margin-top: 2rem; }
        .upcoming-item { display: flex; gap: 1rem; padding: 1rem; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 0.75rem; }
        .upcoming-date { background: var(--bg-primary); border-radius: 10px; padding: 0.75rem; text-align: center; min-width: 60px; }
        .upcoming-date .day { font-size: 1.5rem; font-weight: 700; line-height: 1; }
        .upcoming-date .month { font-size: 0.7rem; color: var(--text-secondary); text-transform: uppercase; }
        .upcoming-info { flex: 1; }
        .upcoming-info h4 { margin-bottom: 0.25rem; }
        .upcoming-info p { font-size: 0.85rem; color: var(--text-secondary); }
        .upcoming-actions { display: flex; gap: 0.5rem; align-items: center; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/_sidebar.php'; ?>
    <main class="main-content">
        <div class="page-header">
            <div><h1 class="page-title"><i class="fas fa-calendar-alt"></i> Réunions</h1><p class="page-subtitle">Planifiez et gérez vos réunions d'équipe</p></div>
            <div class="header-actions">
                <button class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle réunion</button>
                <button class="mobile-menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon"></i></button>
            </div>
        </div>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-calendar-check"></i></div><div class="stat-value">8</div><div class="stat-label">Réunions ce mois</div></div>
            <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-users"></i></div><div class="stat-value">24</div><div class="stat-label">Participants total</div></div>
            <div class="stat-card"><div class="stat-icon green"><i class="fas fa-clock"></i></div><div class="stat-value">12h</div><div class="stat-label">Temps en réunion</div></div>
            <div class="stat-card"><div class="stat-icon yellow"><i class="fas fa-video"></i></div><div class="stat-value">3</div><div class="stat-label">À venir cette semaine</div></div>
        </div>
        <div class="calendar-container">
            <div class="mini-calendar">
                <div class="cal-header"><h3>Décembre 2024</h3><div class="cal-nav"><button><i class="fas fa-chevron-left"></i></button><button><i class="fas fa-chevron-right"></i></button></div></div>
                <div class="cal-grid">
                    <div class="cal-day-name">Lun</div><div class="cal-day-name">Mar</div><div class="cal-day-name">Mer</div><div class="cal-day-name">Jeu</div><div class="cal-day-name">Ven</div><div class="cal-day-name">Sam</div><div class="cal-day-name">Dim</div>
                    <div class="cal-day other">25</div><div class="cal-day other">26</div><div class="cal-day other">27</div><div class="cal-day other">28</div><div class="cal-day other">29</div><div class="cal-day other">30</div><div class="cal-day">1</div>
                    <div class="cal-day">2</div><div class="cal-day has-event">3</div><div class="cal-day">4</div><div class="cal-day">5</div><div class="cal-day has-event">6</div><div class="cal-day">7</div><div class="cal-day">8</div>
                    <div class="cal-day">9</div><div class="cal-day has-event">10</div><div class="cal-day">11</div><div class="cal-day today">12</div><div class="cal-day">13</div><div class="cal-day">14</div><div class="cal-day">15</div>
                    <div class="cal-day has-event">16</div><div class="cal-day">17</div><div class="cal-day">18</div><div class="cal-day">19</div><div class="cal-day has-event">20</div><div class="cal-day">21</div><div class="cal-day">22</div>
                    <div class="cal-day">23</div><div class="cal-day">24</div><div class="cal-day">25</div><div class="cal-day">26</div><div class="cal-day">27</div><div class="cal-day">28</div><div class="cal-day">29</div>
                    <div class="cal-day">30</div><div class="cal-day">31</div><div class="cal-day other">1</div><div class="cal-day other">2</div><div class="cal-day other">3</div><div class="cal-day other">4</div><div class="cal-day other">5</div>
                </div>
            </div>
            <div class="events-panel">
                <div class="events-date"><i class="fas fa-calendar"></i> Aujourd'hui - 12 Décembre</div>
                <div class="event-item"><div class="event-time">09:00 - 10:00</div><div class="event-title">Daily Standup</div><div class="event-desc">Équipe Développement</div></div>
                <div class="event-item blue"><div class="event-time">14:00 - 15:30</div><div class="event-title">Review Sprint 12</div><div class="event-desc">Salle virtuelle Teams</div></div>
                <div class="event-item green"><div class="event-time">16:00 - 16:30</div><div class="event-title">1:1 avec Manager</div><div class="event-desc">Discussion objectifs Q1</div></div>
            </div>
        </div>
        <div class="upcoming">
            <h3 style="margin-bottom: 1rem;"><i class="fas fa-clock"></i> Prochaines réunions</h3>
            <div class="upcoming-item"><div class="upcoming-date"><div class="day">16</div><div class="month">Déc</div></div><div class="upcoming-info"><h4>Rétrospective Sprint</h4><p><i class="fas fa-clock"></i> 10:00 - 11:00 • <i class="fas fa-users"></i> 8 participants</p></div><div class="upcoming-actions"><button class="btn btn-outline btn-sm"><i class="fas fa-edit"></i></button><button class="btn btn-outline btn-sm"><i class="fas fa-video"></i></button></div></div>
            <div class="upcoming-item"><div class="upcoming-date"><div class="day">20</div><div class="month">Déc</div></div><div class="upcoming-info"><h4>Planning Q1 2025</h4><p><i class="fas fa-clock"></i> 14:00 - 16:00 • <i class="fas fa-users"></i> 12 participants</p></div><div class="upcoming-actions"><button class="btn btn-outline btn-sm"><i class="fas fa-edit"></i></button><button class="btn btn-outline btn-sm"><i class="fas fa-video"></i></button></div></div>
        </div>
    </main>
    <script>
        function toggleTheme(){const h=document.documentElement,n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);document.querySelector('.theme-toggle i').className=n==='dark'?'fas fa-moon':'fas fa-sun';}
        const t=localStorage.getItem('theme')||'dark';document.documentElement.setAttribute('data-theme',t);document.querySelector('.theme-toggle i').className=t==='dark'?'fas fa-moon':'fas fa-sun';
        function toggleSidebar(){document.querySelector('.sidebar').classList.toggle('open');}
    </script>
</body>
</html>
