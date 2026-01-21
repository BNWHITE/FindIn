<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
$currentPage = 'mon-espace';
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userEmail = $_SESSION['user_email'] ?? 'utilisateur@findin.fr';
$userRole = $_SESSION['user_role'] ?? 'collaborateur';
$userId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg-primary: #0a0118; --bg-secondary: #1a0d2e; --bg-card: #241538; --bg-hover: #2d1b47; --text-primary: #ffffff; --text-secondary: #a0a0a0; --accent-purple: #9333ea; --accent-blue: #3b82f6; --accent-green: #10b981; --accent-yellow: #f59e0b; --accent-red: #ef4444; --border-color: rgba(255,255,255,0.1); }
        [data-theme="light"] { --bg-primary: #f1f5f9; --bg-secondary: #ffffff; --bg-card: #ffffff; --bg-hover: #f8fafc; --text-primary: #1e293b; --text-secondary: #64748b; --border-color: rgba(0,0,0,0.1); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); min-height: 100vh; display: flex; }
        .sidebar { width: 280px; background: var(--bg-secondary); border-right: 1px solid var(--border-color); height: 100vh; position: fixed; left: 0; top: 0; display: flex; flex-direction: column; z-index: 100; }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid var(--border-color); }
        .sidebar-header img { height: 32px; }
        .sidebar-nav { flex: 1; padding: 1rem 0; overflow-y: auto; }
        .nav-section { padding: 0.5rem 1rem; margin-top: 0.5rem; }
        .nav-section-title { font-size: 0.7rem; text-transform: uppercase; color: var(--text-secondary); letter-spacing: 0.05em; margin-bottom: 0.5rem; }
        .nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.5rem; color: var(--text-secondary); text-decoration: none; transition: all 0.2s; border-left: 3px solid transparent; }
        .nav-item:hover { background: var(--bg-hover); color: var(--text-primary); }
        .nav-item.active { background: rgba(147,51,234,0.1); color: var(--accent-purple); border-left-color: var(--accent-purple); }
        .nav-item i { width: 20px; text-align: center; }
        .sidebar-footer { padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); }
        .user-card { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: var(--bg-card); border-radius: 12px; margin-bottom: 0.75rem; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); display: flex; align-items: center; justify-content: center; font-weight: 600; color: white; }
        .user-info { flex: 1; }
        .user-name { font-weight: 600; font-size: 0.9rem; }
        .user-role { font-size: 0.75rem; color: var(--text-secondary); }
        .main-content { flex: 1; margin-left: 280px; padding: 2rem; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-title { font-size: 1.75rem; font-weight: 700; }
        .page-subtitle { color: var(--text-secondary); margin-top: 0.25rem; }
        .header-actions { display: flex; gap: 0.75rem; align-items: center; }
        .btn { padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; border: none; font-size: 0.9rem; transition: all 0.2s; }
        .btn-primary { background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; }
        .btn-outline { background: transparent; border: 1px solid var(--border-color); color: var(--text-primary); }
        .theme-toggle { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; color: var(--text-primary); display: flex; align-items: center; justify-content: center; }
        
        .profile-grid { display: grid; grid-template-columns: 350px 1fr; gap: 2rem; }
        .profile-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; text-align: center; height: fit-content; }
        .profile-avatar-large { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; color: white; margin: 0 auto 1.5rem; }
        .profile-name { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; }
        .profile-title { color: var(--text-secondary); margin-bottom: 1rem; }
        .profile-stats { display: flex; justify-content: center; gap: 2rem; margin: 1.5rem 0; padding: 1.5rem 0; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); }
        .p-stat { text-align: center; }
        .p-stat-value { font-size: 1.5rem; font-weight: 700; color: var(--accent-purple); }
        .p-stat-label { font-size: 0.8rem; color: var(--text-secondary); }
        .profile-actions { display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1.5rem; }
        
        .content-section { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; }
        .section-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .section-title i { color: var(--accent-purple); }
        
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .info-item { padding: 1rem; background: var(--bg-primary); border-radius: 8px; }
        .info-label { font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.25rem; }
        .info-value { font-weight: 500; }
        
        .activity-list { list-style: none; }
        .activity-item { display: flex; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid var(--border-color); }
        .activity-item:last-child { border-bottom: none; }
        .activity-icon { width: 36px; height: 36px; border-radius: 50%; background: rgba(147,51,234,0.15); display: flex; align-items: center; justify-content: center; color: var(--accent-purple); }
        .activity-content { flex: 1; }
        .activity-text { font-size: 0.9rem; }
        .activity-time { font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem; }
        
        .progress-section { margin-top: 1rem; }
        .progress-item { margin-bottom: 1rem; }
        .progress-header { display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .progress-bar { height: 8px; background: var(--bg-primary); border-radius: 4px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 4px; transition: width 0.5s; }
        .fill-purple { background: linear-gradient(90deg, var(--accent-purple), var(--accent-blue)); }
        .fill-green { background: var(--accent-green); }
        .fill-yellow { background: var(--accent-yellow); }
        
        @media (max-width: 1024px) { .profile-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <?php include __DIR__ . '/_sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Mon Espace</h1>
                <p class="page-subtitle">Gérez votre profil et vos informations personnelles</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary"><i class="fas fa-edit"></i> Modifier</button>
                <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            </div>
        </div>
        
        <div class="profile-grid">
            <div>
                <div class="profile-card">
                    <div class="profile-avatar-large"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                    <h2 class="profile-name"><?= htmlspecialchars($userName) ?></h2>
                    <p class="profile-title">Développeur Full Stack</p>
                    <div class="profile-stats">
                        <div class="p-stat">
                            <div class="p-stat-value">12</div>
                            <div class="p-stat-label">Compétences</div>
                        </div>
                        <div class="p-stat">
                            <div class="p-stat-value">5</div>
                            <div class="p-stat-label">Projets</div>
                        </div>
                        <div class="p-stat">
                            <div class="p-stat-value">3</div>
                            <div class="p-stat-label">Certifs</div>
                        </div>
                    </div>
                    <div class="profile-actions">
                        <button class="btn btn-primary"><i class="fas fa-camera"></i> Changer la photo</button>
                        <button class="btn btn-outline"><i class="fas fa-download"></i> Exporter profil</button>
                    </div>
                </div>
                
                <div class="content-section" style="margin-top: 1.5rem;">
                    <h3 class="section-title"><i class="fas fa-chart-line"></i> Progression</h3>
                    <div class="progress-section">
                        <div class="progress-item">
                            <div class="progress-header"><span>Profil complété</span><span>85%</span></div>
                            <div class="progress-bar"><div class="progress-fill fill-purple" style="width: 85%"></div></div>
                        </div>
                        <div class="progress-item">
                            <div class="progress-header"><span>Objectifs annuels</span><span>60%</span></div>
                            <div class="progress-bar"><div class="progress-fill fill-green" style="width: 60%"></div></div>
                        </div>
                        <div class="progress-item">
                            <div class="progress-header"><span>Formations en cours</span><span>40%</span></div>
                            <div class="progress-bar"><div class="progress-fill fill-yellow" style="width: 40%"></div></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="content-section">
                    <h3 class="section-title"><i class="fas fa-user"></i> Informations personnelles</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?= htmlspecialchars($userEmail) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Téléphone</div>
                            <div class="info-value">+33 6 12 34 56 78</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Département</div>
                            <div class="info-value">Développement IT</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Manager</div>
                            <div class="info-value">Sophie Martin</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Date d'arrivée</div>
                            <div class="info-value">15 Mars 2022</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Localisation</div>
                            <div class="info-value">Paris, France</div>
                        </div>
                    </div>
                </div>
                
                <div class="content-section">
                    <h3 class="section-title"><i class="fas fa-history"></i> Activité récente</h3>
                    <ul class="activity-list">
                        <li class="activity-item">
                            <div class="activity-icon"><i class="fas fa-star"></i></div>
                            <div class="activity-content">
                                <div class="activity-text">Compétence <strong>Docker</strong> validée par votre manager</div>
                                <div class="activity-time">Il y a 2 heures</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon"><i class="fas fa-certificate"></i></div>
                            <div class="activity-content">
                                <div class="activity-text">Certification <strong>AWS</strong> ajoutée</div>
                                <div class="activity-time">Hier</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon"><i class="fas fa-project-diagram"></i></div>
                            <div class="activity-content">
                                <div class="activity-text">Ajouté au projet <strong>Migration Cloud</strong></div>
                                <div class="activity-time">Il y a 3 jours</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon"><i class="fas fa-file-alt"></i></div>
                            <div class="activity-content">
                                <div class="activity-text">CV mis à jour</div>
                                <div class="activity-time">Il y a 1 semaine</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';
        h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';
        t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});
    </script>
</body>
</html>
