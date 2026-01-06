<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Competence.php';

$db = Database::getInstance();
$userModel = new User();
$competenceModel = new Competence();

$user_id = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? 'employe';

// Vérifier que c'est bien un Admin
if ($userRole !== 'admin') {
    header('Location: /dashboard');
    exit;
}

// Stats globales
$stats = [];
try {
    $stats['users'] = $db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
    $stats['competences'] = $db->query("SELECT COUNT(*) FROM competences")->fetchColumn();
    $stats['certifications'] = $db->query("SELECT COUNT(*) FROM certifications")->fetchColumn();
    $stats['projects'] = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    $stats['pending_validations'] = $db->query("SELECT COUNT(*) FROM demandes_validation WHERE statut = 'en_attente'")->fetchColumn();
    $stats['validated_competences'] = $db->query("SELECT COUNT(*) FROM user_competences WHERE validee = 1")->fetchColumn();
    
    // Par rôle
    $stats['admins'] = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'admin'")->fetchColumn();
    $stats['rhs'] = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'rh'")->fetchColumn();
    $stats['managers'] = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'manager'")->fetchColumn();
    $stats['employes'] = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'employe'")->fetchColumn();
    
    // Derniers utilisateurs
    $recentUsers = $db->query("SELECT * FROM utilisateurs ORDER BY cree_le DESC LIMIT 5")->fetchAll();
    
    // Dernières demandes de validation
    $recentValidations = $db->query("SELECT dv.*, u.prenom, u.nom, c.nom as competence 
                                      FROM demandes_validation dv 
                                      JOIN utilisateurs u ON dv.user_id = u.id_utilisateur 
                                      JOIN competences c ON dv.competence_id = c.id 
                                      ORDER BY dv.date_demande DESC LIMIT 10")->fetchAll();
} catch (Exception $e) {
    // Tables peuvent ne pas exister
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0118;
            --bg-secondary: #1a0d2e;
            --bg-card: #241538;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --accent-purple: #9333ea;
            --accent-green: #10b981;
            --accent-red: #ef4444;
            --accent-blue: #3b82f6;
            --accent-yellow: #f59e0b;
            --border-color: rgba(255,255,255,0.1);
        }
        [data-theme="light"] {
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); min-height: 100vh; }
        
        .container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
        .header h1 { font-size: 2rem; display: flex; align-items: center; gap: 0.5rem; }
        .header h1 i { color: var(--accent-red); }
        
        .back-btn { 
            display: inline-flex; align-items: center; gap: 0.5rem; 
            padding: 0.75rem 1.5rem; background: var(--bg-card); 
            color: var(--text-primary); text-decoration: none; border-radius: 8px;
        }
        .back-btn:hover { background: var(--accent-purple); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { 
            background: var(--bg-card); border-radius: 12px; padding: 1.5rem; 
            border: 1px solid var(--border-color); text-align: center;
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-4px); }
        .stat-card h3 { color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 0.5rem; text-transform: uppercase; }
        .stat-card .value { font-size: 2.5rem; font-weight: 700; }
        .stat-card .icon { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .stat-card.purple .value, .stat-card.purple .icon { color: var(--accent-purple); }
        .stat-card.green .value, .stat-card.green .icon { color: var(--accent-green); }
        .stat-card.blue .value, .stat-card.blue .icon { color: var(--accent-blue); }
        .stat-card.yellow .value, .stat-card.yellow .icon { color: var(--accent-yellow); }
        .stat-card.red .value, .stat-card.red .icon { color: var(--accent-red); }
        
        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        
        .section { background: var(--bg-card); border-radius: 12px; padding: 1.5rem; border: 1px solid var(--border-color); }
        .section h2 { margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; font-size: 1.1rem; }
        .section h2 i { color: var(--accent-purple); }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { color: var(--text-secondary); font-weight: 500; font-size: 0.8rem; text-transform: uppercase; }
        
        .role-badge { 
            display: inline-block; padding: 0.2rem 0.6rem; border-radius: 12px; 
            font-size: 0.7rem; font-weight: 600; text-transform: uppercase;
        }
        .role-admin { background: rgba(239, 68, 68, 0.2); color: var(--accent-red); }
        .role-rh { background: rgba(147, 51, 234, 0.2); color: var(--accent-purple); }
        .role-manager { background: rgba(59, 130, 246, 0.2); color: var(--accent-blue); }
        .role-employe { background: rgba(16, 185, 129, 0.2); color: var(--accent-green); }
        
        .status-badge { padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
        .status-en_attente { background: rgba(245, 158, 11, 0.2); color: var(--accent-yellow); }
        .status-approuve { background: rgba(16, 185, 129, 0.2); color: var(--accent-green); }
        .status-refuse { background: rgba(239, 68, 68, 0.2); color: var(--accent-red); }
        
        .quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; }
        .quick-link {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 1.5rem; background: var(--bg-secondary); border-radius: 12px;
            text-decoration: none; color: var(--text-primary); border: 1px solid var(--border-color);
            transition: all 0.3s;
        }
        .quick-link:hover { border-color: var(--accent-purple); background: var(--bg-card); }
        .quick-link i { font-size: 2rem; margin-bottom: 0.75rem; color: var(--accent-purple); }
        .quick-link span { font-weight: 500; }
        
        .chart-placeholder { 
            height: 200px; background: var(--bg-secondary); border-radius: 8px; 
            display: flex; align-items: center; justify-content: center; color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shield-alt"></i> Panneau d'Administration</h1>
            <a href="/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>
        
        <!-- Stats principales -->
        <div class="stats-grid">
            <div class="stat-card purple">
                <div class="icon"><i class="fas fa-users"></i></div>
                <h3>Utilisateurs</h3>
                <div class="value"><?= $stats['users'] ?? 0 ?></div>
            </div>
            <div class="stat-card blue">
                <div class="icon"><i class="fas fa-brain"></i></div>
                <h3>Compétences</h3>
                <div class="value"><?= $stats['competences'] ?? 0 ?></div>
            </div>
            <div class="stat-card green">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <h3>Validées</h3>
                <div class="value"><?= $stats['validated_competences'] ?? 0 ?></div>
            </div>
            <div class="stat-card yellow">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <h3>En attente</h3>
                <div class="value"><?= $stats['pending_validations'] ?? 0 ?></div>
            </div>
            <div class="stat-card red">
                <div class="icon"><i class="fas fa-certificate"></i></div>
                <h3>Certifications</h3>
                <div class="value"><?= $stats['certifications'] ?? 0 ?></div>
            </div>
        </div>
        
        <!-- Répartition des rôles -->
        <div class="section" style="margin-bottom: 2rem;">
            <h2><i class="fas fa-chart-pie"></i> Répartition des utilisateurs</h2>
            <div class="stats-grid">
                <div class="stat-card red">
                    <h3>Administrateurs</h3>
                    <div class="value"><?= $stats['admins'] ?? 0 ?></div>
                </div>
                <div class="stat-card purple">
                    <h3>RH</h3>
                    <div class="value"><?= $stats['rhs'] ?? 0 ?></div>
                </div>
                <div class="stat-card blue">
                    <h3>Managers</h3>
                    <div class="value"><?= $stats['managers'] ?? 0 ?></div>
                </div>
                <div class="stat-card green">
                    <h3>Employés</h3>
                    <div class="value"><?= $stats['employes'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        
        <!-- Accès rapides -->
        <div class="section" style="margin-bottom: 2rem;">
            <h2><i class="fas fa-bolt"></i> Accès rapides</h2>
            <div class="quick-links">
                <a href="/dashboard/gestion-rh" class="quick-link">
                    <i class="fas fa-users-cog"></i>
                    <span>Gestion RH</span>
                </a>
                <a href="/dashboard/gestion-equipe" class="quick-link">
                    <i class="fas fa-user-check"></i>
                    <span>Validations</span>
                </a>
                <a href="/dashboard/competences" class="quick-link">
                    <i class="fas fa-brain"></i>
                    <span>Compétences</span>
                </a>
                <a href="/dashboard/projets" class="quick-link">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projets</span>
                </a>
            </div>
        </div>
        
        <div class="grid-2">
            <!-- Derniers utilisateurs -->
            <div class="section">
                <h2><i class="fas fa-user-plus"></i> Derniers utilisateurs inscrits</h2>
                <div class="search-bar-small">
                    <input type="text" id="searchUsers" class="search-input-small" placeholder="Rechercher...">
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Rôle</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <?php if (!empty($recentUsers)): ?>
                            <?php foreach ($recentUsers as $u): ?>
                            <tr data-nom="<?= htmlspecialchars(strtolower(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? ''))) ?>" data-role="<?= strtolower($u['role'] ?? 'employe') ?>">
                                <td><?= htmlspecialchars(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? '')) ?></td>
                                <td><span class="role-badge role-<?= $u['role'] ?? 'employe' ?>"><?= $u['role'] ?? 'employe' ?></span></td>
                                <td><?= date('d/m/Y', strtotime($u['cree_le'] ?? 'now')) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align: center; color: var(--text-secondary);">Aucun utilisateur</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Dernières demandes de validation -->
            <div class="section">
                <h2><i class="fas fa-clipboard-list"></i> Dernières demandes de validation</h2>
                <div class="search-bar-small">
                    <input type="text" id="searchValidations" class="search-input-small" placeholder="Rechercher...">
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Compétence</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody id="validationsTableBody">
                        <?php if (!empty($recentValidations)): ?>
                            <?php foreach ($recentValidations as $v): ?>
                            <tr data-user="<?= htmlspecialchars(strtolower($v['prenom'] . ' ' . $v['nom'])) ?>" 
                                data-competence="<?= htmlspecialchars(strtolower($v['competence'])) ?>"
                                data-statut="<?= strtolower($v['statut']) ?>">
                                <td><?= htmlspecialchars($v['prenom'] . ' ' . $v['nom']) ?></td>
                                <td><?= htmlspecialchars($v['competence']) ?></td>
                                <td><span class="status-badge status-<?= $v['statut'] ?>"><?= $v['statut'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align: center; color: var(--text-secondary);">Aucune demande</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Toggle Theme Button -->
    <button class="theme-toggle" onclick="toggleTheme()" title="Changer le thème">
        <i class="fas fa-moon" id="theme-icon"></i>
    </button>
    
    <style>
        .theme-toggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--accent-purple);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1.25rem;
            box-shadow: 0 4px 15px rgba(147, 51, 234, 0.4);
            transition: all 0.3s;
            z-index: 1000;
        }
        .theme-toggle:hover {
            transform: scale(1.1);
        }
        
        /* Styles pour la recherche */
        .search-bar-small {
            margin-bottom: 1rem;
        }
        .search-input-small {
            width: 100%;
            padding: 0.6rem 1rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        .search-input-small:focus {
            outline: none;
            border-color: var(--accent-purple);
            box-shadow: 0 0 0 2px rgba(147, 51, 234, 0.2);
        }
        .hidden { display: none !important; }
    </style>
    
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            const currentTheme = html.getAttribute('data-theme');
            
            if (currentTheme === 'dark') {
                html.setAttribute('data-theme', 'light');
                icon.className = 'fas fa-sun';
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                icon.className = 'fas fa-moon';
                localStorage.setItem('theme', 'dark');
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            const icon = document.getElementById('theme-icon');
            icon.className = savedTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
            
            // Recherche utilisateurs
            const searchUsers = document.getElementById('searchUsers');
            if (searchUsers) {
                searchUsers.addEventListener('keyup', filterUsers);
            }
            
            // Recherche validations
            const searchValidations = document.getElementById('searchValidations');
            if (searchValidations) {
                searchValidations.addEventListener('keyup', filterValidations);
            }
        });
        
        function filterUsers() {
            const searchTerm = document.getElementById('searchUsers').value.toLowerCase().trim();
            const tbody = document.getElementById('usersTableBody');
            if (!tbody) return;
            
            const rows = tbody.querySelectorAll('tr[data-nom]');
            
            rows.forEach(row => {
                const nom = row.getAttribute('data-nom') || '';
                const role = row.getAttribute('data-role') || '';
                
                const matchSearch = searchTerm === '' || 
                    nom.includes(searchTerm) || 
                    role.includes(searchTerm);
                
                row.classList.toggle('hidden', !matchSearch);
            });
        }
        
        function filterValidations() {
            const searchTerm = document.getElementById('searchValidations').value.toLowerCase().trim();
            const tbody = document.getElementById('validationsTableBody');
            if (!tbody) return;
            
            const rows = tbody.querySelectorAll('tr[data-user]');
            
            rows.forEach(row => {
                const user = row.getAttribute('data-user') || '';
                const competence = row.getAttribute('data-competence') || '';
                const statut = row.getAttribute('data-statut') || '';
                
                const matchSearch = searchTerm === '' || 
                    user.includes(searchTerm) || 
                    competence.includes(searchTerm) ||
                    statut.includes(searchTerm);
                
                row.classList.toggle('hidden', !matchSearch);
            });
        }
    </script>
</body>
</html>