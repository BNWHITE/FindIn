<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/User.php';

$db = Database::getInstance();
$userModel = new User();

$user_id = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? 'employe';

// Vérifier que c'est bien un Manager ou Admin
if (!in_array($userRole, ['manager', 'admin'])) {
    header('Location: /dashboard');
    exit;
}

$message = '';

// Traitement des validations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'validate') {
        $result = $userModel->validateCompetence(
            $_POST['validation_id'],
            $user_id,
            'approuve',
            $_POST['commentaire'] ?? null
        );
        $message = $result ? "✅ Compétence validée avec succès" : "❌ Erreur lors de la validation";
    }
    
    if ($action === 'refuse') {
        $result = $userModel->validateCompetence(
            $_POST['validation_id'],
            $user_id,
            'refuse',
            $_POST['commentaire'] ?? 'Compétence refusée'
        );
        $message = $result ? "✅ Compétence refusée" : "❌ Erreur";
    }
}

// Récupérer les données
$teamMembers = $userModel->getTeamMembers($user_id);
$pendingValidations = $userModel->getPendingValidations($user_id);
$teamCompetences = $userModel->getTeamCompetences($user_id);

// Stats
$teamCount = count($teamMembers);
$pendingCount = count($pendingValidations);
$validatedCount = count(array_filter($teamCompetences, fn($c) => $c['validee'] == 1));
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Équipe - FindIN</title>
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
        .header h1 i { color: var(--accent-blue); }
        
        .back-btn { 
            display: inline-flex; align-items: center; gap: 0.5rem; 
            padding: 0.75rem 1.5rem; background: var(--bg-card); 
            color: var(--text-primary); text-decoration: none; border-radius: 8px;
            transition: all 0.3s;
        }
        .back-btn:hover { background: var(--accent-purple); }
        
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1rem; background: rgba(16, 185, 129, 0.2); border: 1px solid var(--accent-green); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { 
            background: var(--bg-card); border-radius: 12px; padding: 1.5rem; 
            border: 1px solid var(--border-color); text-align: center;
        }
        .stat-card h3 { color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem; }
        .stat-card .value { font-size: 2.5rem; font-weight: 700; }
        .stat-card.blue .value { color: var(--accent-blue); }
        .stat-card.yellow .value { color: var(--accent-yellow); }
        .stat-card.green .value { color: var(--accent-green); }
        
        .section { background: var(--bg-card); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid var(--border-color); }
        .section h2 { margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .section h2 i { color: var(--accent-purple); }
        
        .validation-card {
            background: var(--bg-secondary); border-radius: 8px; padding: 1.5rem;
            margin-bottom: 1rem; border-left: 4px solid var(--accent-yellow);
        }
        .validation-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem; }
        .validation-info h3 { margin-bottom: 0.25rem; }
        .validation-info p { color: var(--text-secondary); font-size: 0.875rem; }
        .validation-competence { 
            background: var(--accent-purple); color: white; padding: 0.5rem 1rem; 
            border-radius: 20px; font-weight: 600;
        }
        .validation-actions { display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap; }
        
        .btn { 
            padding: 0.75rem 1.5rem; border: none; border-radius: 8px; 
            cursor: pointer; font-weight: 600; transition: all 0.3s;
            display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-success { background: var(--accent-green); color: white; }
        .btn-success:hover { background: #059669; }
        .btn-danger { background: var(--accent-red); color: white; }
        .btn-danger:hover { background: #dc2626; }
        
        .team-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; }
        .team-card {
            background: var(--bg-secondary); border-radius: 12px; padding: 1.5rem;
            border: 1px solid var(--border-color); text-align: center;
        }
        .team-avatar {
            width: 80px; height: 80px; border-radius: 50%; 
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem; font-size: 2rem; font-weight: 700;
        }
        .team-card h3 { margin-bottom: 0.25rem; }
        .team-card .email { color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem; }
        .team-stats { display: flex; justify-content: center; gap: 2rem; }
        .team-stat { text-align: center; }
        .team-stat .num { font-size: 1.5rem; font-weight: 700; color: var(--accent-purple); }
        .team-stat .label { font-size: 0.75rem; color: var(--text-secondary); }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { color: var(--text-secondary); font-weight: 500; font-size: 0.875rem; }
        
        .level-bar { 
            height: 8px; background: var(--bg-primary); border-radius: 4px; 
            overflow: hidden; width: 100px; display: inline-block; margin-right: 0.5rem;
        }
        .level-fill { height: 100%; background: var(--accent-purple); border-radius: 4px; }
        
        .badge { 
            display: inline-block; padding: 0.25rem 0.75rem; border-radius: 20px; 
            font-size: 0.75rem; font-weight: 600;
        }
        .badge-valid { background: rgba(16, 185, 129, 0.2); color: var(--accent-green); }
        .badge-pending { background: rgba(245, 158, 11, 0.2); color: var(--accent-yellow); }
        
        .empty-state { text-align: center; padding: 3rem; color: var(--text-secondary); }
        .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
        
        textarea { 
            width: 100%; padding: 0.75rem; background: var(--bg-primary); 
            border: 1px solid var(--border-color); border-radius: 8px; 
            color: var(--text-primary); resize: vertical; min-height: 60px;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users"></i> Gestion de Mon Équipe</h1>
            <a href="/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i> Retour Dashboard</a>
        </div>
        
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <h3>Membres de l'équipe</h3>
                <div class="value"><?= $teamCount ?></div>
            </div>
            <div class="stat-card yellow">
                <h3>Validations en attente</h3>
                <div class="value"><?= $pendingCount ?></div>
            </div>
            <div class="stat-card green">
                <h3>Compétences validées</h3>
                <div class="value"><?= $validatedCount ?></div>
            </div>
        </div>
        
        <!-- Demandes de validation en attente -->
        <div class="section">
            <h2><i class="fas fa-clock"></i> Demandes de validation en attente (<?= $pendingCount ?>)</h2>
            
            <?php if (empty($pendingValidations)): ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>Aucune demande de validation en attente</p>
                </div>
            <?php else: ?>
                <?php foreach ($pendingValidations as $v): ?>
                <div class="validation-card">
                    <div class="validation-header">
                        <div class="validation-info">
                            <h3><?= htmlspecialchars($v['prenom'] . ' ' . $v['nom']) ?></h3>
                            <p><?= htmlspecialchars($v['email']) ?></p>
                            <p><small>Demandé le <?= date('d/m/Y à H:i', strtotime($v['date_demande'])) ?></small></p>
                        </div>
                        <div class="validation-competence">
                            <?= htmlspecialchars($v['competence_nom']) ?> - Niveau <?= $v['niveau_demande'] ?>
                        </div>
                    </div>
                    <div class="validation-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="validate">
                            <input type="hidden" name="validation_id" value="<?= $v['id'] ?>">
                            <textarea name="commentaire" placeholder="Commentaire (optionnel)"></textarea>
                            <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Valider</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="refuse">
                            <input type="hidden" name="validation_id" value="<?= $v['id'] ?>">
                            <input type="hidden" name="commentaire" value="Compétence non validée">
                            <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Refuser</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Membres de l'équipe -->
        <div class="section">
            <h2><i class="fas fa-user-friends"></i> Membres de mon équipe (<?= $teamCount ?>)</h2>
            
            <!-- Barre de recherche -->
            <div class="search-bar">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchMembers" class="search-input" placeholder="Rechercher un membre par nom ou email...">
                </div>
            </div>
            
            <?php if (empty($teamMembers)): ?>
                <div class="empty-state">
                    <i class="fas fa-user-plus"></i>
                    <p>Aucun membre dans votre équipe</p>
                    <p><small>Demandez au RH d'assigner des employés à votre équipe</small></p>
                </div>
            <?php else: ?>
                <div class="team-grid" id="teamGrid">
                    <?php foreach ($teamMembers as $member): ?>
                    <div class="team-card" data-nom="<?= htmlspecialchars(strtolower($member['prenom'] . ' ' . $member['nom'])) ?>" data-email="<?= htmlspecialchars(strtolower($member['email'])) ?>">
                        <div class="team-avatar">
                            <?= strtoupper(substr($member['prenom'] ?? 'U', 0, 1) . substr($member['nom'] ?? '', 0, 1)) ?>
                        </div>
                        <h3><?= htmlspecialchars($member['prenom'] . ' ' . $member['nom']) ?></h3>
                        <p class="email"><?= htmlspecialchars($member['email']) ?></p>
                        <div class="team-stats">
                            <?php
                            $memberCompetences = array_filter($teamCompetences, fn($c) => $c['id_utilisateur'] === $member['id_utilisateur']);
                            $validated = count(array_filter($memberCompetences, fn($c) => $c['validee'] == 1));
                            ?>
                            <div class="team-stat">
                                <div class="num"><?= count($memberCompetences) ?></div>
                                <div class="label">Compétences</div>
                            </div>
                            <div class="team-stat">
                                <div class="num"><?= $validated ?></div>
                                <div class="label">Validées</div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Compétences de l'équipe -->
        <?php if (!empty($teamCompetences)): ?>
        <div class="section">
            <h2><i class="fas fa-chart-bar"></i> Compétences de l'équipe</h2>
            
            <!-- Barre de recherche compétences -->
            <div class="search-bar">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchCompetences" class="search-input" placeholder="Rechercher par collaborateur ou compétence...">
                </div>
                <select id="filterStatus" class="filter-select">
                    <option value="">Tous les statuts</option>
                    <option value="validee">Validées</option>
                    <option value="pending">En attente</option>
                </select>
            </div>
            
            <table id="competencesTable">>
                <thead>
                    <tr>
                        <th>Collaborateur</th>
                        <th>Compétence</th>
                        <th>Niveau</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody id="competencesBody">
                    <?php foreach ($teamCompetences as $c): ?>
                    <tr data-collaborateur="<?= htmlspecialchars(strtolower($c['prenom'] . ' ' . $c['nom'])) ?>" 
                        data-competence="<?= htmlspecialchars(strtolower($c['competence'])) ?>"
                        data-status="<?= $c['validee'] ? 'validee' : 'pending' ?>">
                        <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                        <td><?= htmlspecialchars($c['competence']) ?></td>
                        <td>
                            <div class="level-bar"><div class="level-fill" style="width: <?= ($c['niveau_declare'] ?? 1) * 20 ?>%"></div></div>
                            <?= $c['niveau_declare'] ?? 1 ?>/5
                        </td>
                        <td>
                            <?php if ($c['validee']): ?>
                                <span class="badge badge-valid"><i class="fas fa-check"></i> Validée</span>
                            <?php else: ?>
                                <span class="badge badge-pending"><i class="fas fa-clock"></i> En attente</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Styles pour la recherche -->
    <style>
        .search-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .search-input-wrapper {
            flex: 1;
            min-width: 250px;
            position: relative;
        }
        .search-input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }
        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        .search-input:focus {
            outline: none;
            border-color: var(--accent-purple);
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
        }
        .filter-select {
            padding: 0.75rem 1rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 0.95rem;
            cursor: pointer;
            min-width: 150px;
        }
        .filter-select:focus {
            outline: none;
            border-color: var(--accent-purple);
        }
        .no-results {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
        }
        .hidden { display: none !important; }
    </style>
    
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
            box-shadow: 0 6px 20px rgba(147, 51, 234, 0.6);
        }
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
            
            // Recherche des membres de l'équipe
            const searchMembers = document.getElementById('searchMembers');
            if (searchMembers) {
                searchMembers.addEventListener('keyup', filterMembers);
            }
            
            // Recherche des compétences
            const searchCompetences = document.getElementById('searchCompetences');
            const filterStatus = document.getElementById('filterStatus');
            if (searchCompetences) {
                searchCompetences.addEventListener('keyup', filterCompetences);
            }
            if (filterStatus) {
                filterStatus.addEventListener('change', filterCompetences);
            }
        });
        
        function filterMembers() {
            const searchTerm = document.getElementById('searchMembers').value.toLowerCase().trim();
            const teamGrid = document.getElementById('teamGrid');
            if (!teamGrid) return;
            
            const cards = teamGrid.querySelectorAll('.team-card');
            let visibleCount = 0;
            
            cards.forEach(card => {
                const nom = card.getAttribute('data-nom') || '';
                const email = card.getAttribute('data-email') || '';
                
                const matchSearch = searchTerm === '' || 
                    nom.includes(searchTerm) || 
                    email.includes(searchTerm);
                
                if (matchSearch) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            
            // Message si aucun résultat
            let noResults = teamGrid.querySelector('.no-results');
            if (visibleCount === 0) {
                if (!noResults) {
                    noResults = document.createElement('div');
                    noResults.className = 'no-results';
                    noResults.innerHTML = '<i class="fas fa-search"></i><p>Aucun membre trouvé</p>';
                    teamGrid.appendChild(noResults);
                }
                noResults.style.display = 'block';
            } else if (noResults) {
                noResults.style.display = 'none';
            }
        }
        
        function filterCompetences() {
            const searchTerm = document.getElementById('searchCompetences')?.value.toLowerCase().trim() || '';
            const statusFilter = document.getElementById('filterStatus')?.value || '';
            const tbody = document.getElementById('competencesBody');
            if (!tbody) return;
            
            const rows = tbody.querySelectorAll('tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const collaborateur = row.getAttribute('data-collaborateur') || '';
                const competence = row.getAttribute('data-competence') || '';
                const status = row.getAttribute('data-status') || '';
                
                const matchSearch = searchTerm === '' || 
                    collaborateur.includes(searchTerm) || 
                    competence.includes(searchTerm);
                const matchStatus = statusFilter === '' || status === statusFilter;
                
                if (matchSearch && matchStatus) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });
            
            // Message si aucun résultat
            let noResults = tbody.querySelector('.no-results-row');
            if (visibleCount === 0) {
                if (!noResults) {
                    noResults = document.createElement('tr');
                    noResults.className = 'no-results-row';
                    noResults.innerHTML = '<td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 2rem;">Aucune compétence trouvée</td>';
                    tbody.appendChild(noResults);
                }
                noResults.style.display = 'table-row';
            } else if (noResults) {
                noResults.style.display = 'none';
            }
        }
    </script>
</body>
</html>