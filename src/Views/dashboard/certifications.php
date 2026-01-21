<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
$currentPage = 'certifications';
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userRole = $_SESSION['user_role'] ?? 'collaborateur';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certifications - FindIN</title>
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
        .theme-toggle { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; color: var(--text-primary); display: flex; align-items: center; justify-content: center; }
        
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; text-align: center; }
        .stat-value { font-size: 2.5rem; font-weight: 700; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .stat-label { color: var(--text-secondary); margin-top: 0.5rem; }
        
        .cert-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; }
        .cert-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; transition: all 0.3s; }
        .cert-card:hover { transform: translateY(-3px); border-color: var(--accent-purple); }
        .cert-header { display: flex; gap: 1rem; margin-bottom: 1rem; }
        .cert-badge { width: 64px; height: 64px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .cert-badge.gold { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        .cert-badge.silver { background: linear-gradient(135deg, #94a3b8, #64748b); color: white; }
        .cert-badge.bronze { background: linear-gradient(135deg, #cd7c2f, #a16207); color: white; }
        .cert-info { flex: 1; }
        .cert-title { font-weight: 600; font-size: 1.1rem; margin-bottom: 0.25rem; }
        .cert-issuer { font-size: 0.85rem; color: var(--text-secondary); }
        .cert-meta { display: flex; gap: 1.5rem; margin: 1rem 0; font-size: 0.85rem; color: var(--text-secondary); }
        .cert-meta i { margin-right: 0.5rem; }
        .cert-status { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; }
        .status-valid { background: rgba(16,185,129,0.15); color: var(--accent-green); }
        .status-expiring { background: rgba(245,158,11,0.15); color: var(--accent-yellow); }
        .status-expired { background: rgba(239,68,68,0.15); color: var(--accent-red); }
        .cert-actions { display: flex; gap: 0.5rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color); }
        .cert-actions .btn { flex: 1; justify-content: center; padding: 0.5rem; font-size: 0.85rem; }
        .btn-outline { background: transparent; border: 1px solid var(--border-color); color: var(--text-primary); }
        
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } .stats-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php include __DIR__ . '/_sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Mes Certifications</h1>
                <p class="page-subtitle">Gérez vos certifications et badges professionnels</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
                <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            </div>
        </div>
        
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-value">5</div>
                <div class="stat-label">Certifications actives</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">2</div>
                <div class="stat-label">À renouveler</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">12</div>
                <div class="stat-label">Badges obtenus</div>
            </div>
        </div>
        
        <div class="cert-grid">
            <div class="cert-card">
                <div class="cert-header">
                    <div class="cert-badge gold"><i class="fas fa-award"></i></div>
                    <div class="cert-info">
                        <div class="cert-title">AWS Solutions Architect</div>
                        <div class="cert-issuer">Amazon Web Services</div>
                    </div>
                </div>
                <div class="cert-meta">
                    <span><i class="fas fa-calendar"></i> Obtenue: 15/03/2024</span>
                    <span><i class="fas fa-clock"></i> Expire: 15/03/2027</span>
                </div>
                <span class="cert-status status-valid"><i class="fas fa-check"></i> Valide</span>
                <div class="cert-actions">
                    <button class="btn btn-outline"><i class="fas fa-eye"></i> Voir</button>
                    <button class="btn btn-outline"><i class="fas fa-download"></i> PDF</button>
                </div>
            </div>
            
            <div class="cert-card">
                <div class="cert-header">
                    <div class="cert-badge silver"><i class="fas fa-certificate"></i></div>
                    <div class="cert-info">
                        <div class="cert-title">Scrum Master PSM I</div>
                        <div class="cert-issuer">Scrum.org</div>
                    </div>
                </div>
                <div class="cert-meta">
                    <span><i class="fas fa-calendar"></i> Obtenue: 20/06/2023</span>
                    <span><i class="fas fa-infinity"></i> Sans expiration</span>
                </div>
                <span class="cert-status status-valid"><i class="fas fa-check"></i> Valide</span>
                <div class="cert-actions">
                    <button class="btn btn-outline"><i class="fas fa-eye"></i> Voir</button>
                    <button class="btn btn-outline"><i class="fas fa-download"></i> PDF</button>
                </div>
            </div>
            
            <div class="cert-card">
                <div class="cert-header">
                    <div class="cert-badge gold"><i class="fas fa-database"></i></div>
                    <div class="cert-info">
                        <div class="cert-title">Oracle Database SQL</div>
                        <div class="cert-issuer">Oracle University</div>
                    </div>
                </div>
                <div class="cert-meta">
                    <span><i class="fas fa-calendar"></i> Obtenue: 10/01/2024</span>
                    <span><i class="fas fa-clock"></i> Expire: 10/01/2025</span>
                </div>
                <span class="cert-status status-expiring"><i class="fas fa-exclamation-triangle"></i> Expire bientôt</span>
                <div class="cert-actions">
                    <button class="btn btn-primary"><i class="fas fa-redo"></i> Renouveler</button>
                    <button class="btn btn-outline"><i class="fas fa-download"></i> PDF</button>
                </div>
            </div>
            
            <div class="cert-card">
                <div class="cert-header">
                    <div class="cert-badge bronze"><i class="fab fa-google"></i></div>
                    <div class="cert-info">
                        <div class="cert-title">Google Analytics</div>
                        <div class="cert-issuer">Google</div>
                    </div>
                </div>
                <div class="cert-meta">
                    <span><i class="fas fa-calendar"></i> Obtenue: 05/08/2022</span>
                    <span><i class="fas fa-clock"></i> Expirée: 05/08/2023</span>
                </div>
                <span class="cert-status status-expired"><i class="fas fa-times"></i> Expirée</span>
                <div class="cert-actions">
                    <button class="btn btn-primary"><i class="fas fa-redo"></i> Repasser</button>
                    <button class="btn btn-outline"><i class="fas fa-trash"></i> Supprimer</button>
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
