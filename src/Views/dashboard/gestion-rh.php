<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }

require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/../../Models/Database.php';
require_once __DIR__ . '/../../Models/User.php';

$db = Database::getInstance();
$userModel = new User();

$user_id = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? 'employe';

// Vérifier que c'est bien un RH ou Admin
if (!in_array($userRole, ['rh', 'admin'])) {
    header('Location: /dashboard');
    exit;
}

$message = '';
$error = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_user') {
        $result = $userModel->createUser([
            'email' => $_POST['email'],
            'prenom' => $_POST['prenom'],
            'nom' => $_POST['nom'],
            'password' => $_POST['password'],
            'role' => $_POST['role'],
            'manager_id' => $_POST['manager_id'] ?: null
        ]);
        $message = $result ? "✅ Utilisateur créé avec succès" : "❌ Erreur lors de la création";
    }
    
    if ($action === 'update_role') {
        $result = $userModel->setUserRole($_POST['user_id'], $_POST['new_role']);
        $message = $result ? "✅ Rôle mis à jour" : "❌ Erreur lors de la mise à jour";
    }
    
    if ($action === 'assign_manager') {
        $result = $userModel->setUserManager($_POST['user_id'], $_POST['manager_id']);
        $message = $result ? "✅ Manager assigné" : "❌ Erreur lors de l'assignation";
    }
    
    if ($action === 'delete_user') {
        $result = $userModel->deleteUser($_POST['user_id']);
        $message = $result ? "✅ Utilisateur supprimé" : "❌ Erreur lors de la suppression";
    }
}

// Récupérer les données
$allUsers = $userModel->getAllUsers();
$managers = $userModel->getManagers();
$stats = $userModel->getStats();
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion RH - FindIN</title>
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
            --border-color: rgba(255,255,255,0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); min-height: 100vh; }
        
        .container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .header h1 { font-size: 2rem; display: flex; align-items: center; gap: 0.5rem; }
        .header h1 i { color: var(--accent-purple); }
        
        .back-btn { 
            display: inline-flex; align-items: center; gap: 0.5rem; 
            padding: 0.75rem 1.5rem; background: var(--bg-card); 
            color: var(--text-primary); text-decoration: none; border-radius: 8px;
            transition: all 0.3s;
        }
        .back-btn:hover { background: var(--accent-purple); }
        
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .message.success { background: rgba(16, 185, 129, 0.2); border: 1px solid var(--accent-green); }
        .message.error { background: rgba(239, 68, 68, 0.2); border: 1px solid var(--accent-red); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { 
            background: var(--bg-card); border-radius: 12px; padding: 1.5rem; 
            border: 1px solid var(--border-color);
        }
        .stat-card h3 { color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem; }
        .stat-card .value { font-size: 2rem; font-weight: 700; }
        .stat-card.purple .value { color: var(--accent-purple); }
        .stat-card.green .value { color: var(--accent-green); }
        .stat-card.blue .value { color: var(--accent-blue); }
        .stat-card.yellow .value { color: var(--accent-yellow); }
        
        .section { background: var(--bg-card); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid var(--border-color); }
        .section h2 { margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .section h2 i { color: var(--accent-purple); }
        
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.875rem; }
        .form-group input, .form-group select { 
            width: 100%; padding: 0.75rem; background: var(--bg-secondary); 
            border: 1px solid var(--border-color); border-radius: 8px; 
            color: var(--text-primary); font-size: 1rem;
        }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--accent-purple); }
        
        .btn { 
            padding: 0.75rem 1.5rem; border: none; border-radius: 8px; 
            cursor: pointer; font-weight: 600; transition: all 0.3s;
        }
        .btn-primary { background: var(--accent-purple); color: white; }
        .btn-primary:hover { background: #7c22c9; }
        .btn-danger { background: var(--accent-red); color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-success { background: var(--accent-green); color: white; }
        .btn-success:hover { background: #059669; }
        .btn-sm { padding: 0.5rem 1rem; font-size: 0.875rem; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { color: var(--text-secondary); font-weight: 500; font-size: 0.875rem; }
        
        .role-badge { 
            display: inline-block; padding: 0.25rem 0.75rem; border-radius: 20px; 
            font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
        }
        .role-admin { background: rgba(239, 68, 68, 0.2); color: var(--accent-red); }
        .role-rh { background: rgba(147, 51, 234, 0.2); color: var(--accent-purple); }
        .role-manager { background: rgba(59, 130, 246, 0.2); color: var(--accent-blue); }
        .role-employe { background: rgba(16, 185, 129, 0.2); color: var(--accent-green); }
        
        .actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: var(--bg-card); border-radius: 12px; padding: 2rem; max-width: 500px; width: 90%; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .modal-close { background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users-cog"></i> Gestion des Ressources Humaines</h1>
            <a href="/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i> Retour Dashboard</a>
        </div>
        
        <?php if ($message): ?>
            <div class="message success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card purple">
                <h3>Total Utilisateurs</h3>
                <div class="value"><?= $stats['total_users'] ?? 0 ?></div>
            </div>
            <div class="stat-card green">
                <h3>Employés</h3>
                <div class="value"><?= $stats['employes'] ?? 0 ?></div>
            </div>
            <div class="stat-card blue">
                <h3>Managers</h3>
                <div class="value"><?= $stats['managers'] ?? 0 ?></div>
            </div>
            <div class="stat-card yellow">
                <h3>Validations en attente</h3>
                <div class="value"><?= $stats['pending_validations'] ?? 0 ?></div>
            </div>
        </div>
        
        <!-- Créer un utilisateur -->
        <div class="section">
            <h2><i class="fas fa-user-plus"></i> Créer un nouvel utilisateur</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create_user">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" required placeholder="Prénom">
                    </div>
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" required placeholder="Nom">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="email@findin.fr">
                    </div>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password" required placeholder="••••••••" minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Rôle</label>
                        <select name="role" required>
                            <option value="employe">Employé</option>
                            <option value="manager">Manager</option>
                            <option value="rh">RH</option>
                            <?php if ($userRole === 'admin'): ?>
                            <option value="admin">Administrateur</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Manager (optionnel)</label>
                        <select name="manager_id">
                            <option value="">-- Aucun --</option>
                            <?php foreach ($managers as $m): ?>
                            <option value="<?= $m['id_utilisateur'] ?>"><?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Créer l'utilisateur</button>
            </form>
        </div>
        
        <!-- Liste des utilisateurs -->
        <div class="section">
            <h2><i class="fas fa-users"></i> Liste des utilisateurs (<?= count($allUsers) ?>)</h2>
            
            <!-- Barre de recherche -->
            <div class="search-bar" style="margin-bottom: 1.5rem;">
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <div style="flex: 2; min-width: 200px;">
                        <input type="text" id="searchInput" placeholder="🔍 Rechercher par nom, email..." 
                               style="width: 100%; padding: 0.75rem 1rem; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 1rem;"
                               onkeyup="filterTable()">
                    </div>
                    <div style="min-width: 150px;">
                        <select id="roleFilter" onchange="filterTable()" 
                                style="width: 100%; padding: 0.75rem; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary);">
                            <option value="">Tous les rôles</option>
                            <option value="admin">Admin</option>
                            <option value="rh">RH</option>
                            <option value="manager">Manager</option>
                            <option value="employe">Employé</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Manager</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allUsers as $u): ?>
                    <tr data-role="<?= $u['role'] ?>">
                        <td><strong><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></strong></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><span class="role-badge role-<?= $u['role'] ?>"><?= $u['role'] ?></span></td>
                        <td>
                            <?php 
                            if ($u['manager_id']) {
                                $manager = $userModel->getUserById($u['manager_id']);
                                echo $manager ? htmlspecialchars($manager['prenom'] . ' ' . $manager['nom']) : '-';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td class="actions">
                            <!-- Changer le rôle -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update_role">
                                <input type="hidden" name="user_id" value="<?= $u['id_utilisateur'] ?>">
                                <select name="new_role" onchange="this.form.submit()" style="padding: 0.4rem; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-primary);">
                                    <option value="employe" <?= $u['role'] === 'employe' ? 'selected' : '' ?>>Employé</option>
                                    <option value="manager" <?= $u['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
                                    <option value="rh" <?= $u['role'] === 'rh' ? 'selected' : '' ?>>RH</option>
                                    <?php if ($userRole === 'admin'): ?>
                                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <?php endif; ?>
                                </select>
                            </form>
                            
                            <!-- Assigner un manager -->
                            <?php if ($u['role'] === 'employe'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="assign_manager">
                                <input type="hidden" name="user_id" value="<?= $u['id_utilisateur'] ?>">
                                <select name="manager_id" onchange="this.form.submit()" style="padding: 0.4rem; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-primary);">
                                    <option value="">Manager...</option>
                                    <?php foreach ($managers as $m): ?>
                                    <option value="<?= $m['id_utilisateur'] ?>" <?= $u['manager_id'] === $m['id_utilisateur'] ? 'selected' : '' ?>><?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                            <?php endif; ?>
                            
                            <!-- Supprimer -->
                            <?php if ($u['id_utilisateur'] !== $user_id): ?>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="user_id" value="<?= $u['id_utilisateur'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
            box-shadow: 0 6px 20px rgba(147, 51, 234, 0.6);
        }
    </style>
    
    <script>
        // Gestion du thème
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
        
        // Charger le thème sauvegardé
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            const icon = document.getElementById('theme-icon');
            icon.className = savedTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        });
    </script>
</body>
</html>