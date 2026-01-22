<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../layouts/header.php';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Invitations - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<style>
:root {
    --bg-primary: #0a0118;
    --bg-secondary: #1a0d2e;
    --bg-card: #241538;
    --text-primary: #ffffff;
    --text-secondary: #a0a0a0;
    --border-color: rgba(255,255,255,0.1);
    --accent-purple: #9333ea;
    --accent-blue: #3b82f6;
}

[data-theme="light"] {
    --bg-primary: #f8f9fa;
    --bg-secondary: #ffffff;
    --bg-card: #f1f5f9;
    --text-primary: #1a1a1a;
    --text-secondary: #5a5a5a;
    --border-color: rgba(0,0,0,0.1);
    --accent-purple: #7c3aed;
    --accent-blue: #2563eb;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: var(--bg-primary);
    color: var(--text-primary);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    transition: background-color 0.3s, color 0.3s;
}

.dashboard-wrapper {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 260px;
    background: var(--bg-secondary);
    border-right: 1px solid var(--border-color);
    padding: 20px;
    overflow-y: auto;
    position: fixed;
    height: 100vh;
    left: 0;
    top: 0;
    transition: background-color 0.3s;
}

.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 30px;
    font-size: 20px;
    font-weight: 700;
    justify-content: space-between;
}

.theme-toggle {
    width: 36px;
    height: 36px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: transparent;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.theme-toggle:hover {
    background: rgba(147, 51, 234, 0.1);
    color: var(--accent-purple);
}

.sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.sidebar-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 8px;
    text-decoration: none;
    color: var(--text-secondary);
    transition: all 0.2s;
    font-size: 14px;
}

.sidebar-item:hover,
.sidebar-item.active {
    background: rgba(147, 51, 234, 0.2);
    color: var(--accent-purple);
}

.sidebar-item i {
    width: 20px;
}

.main-content {
    flex: 1;
    margin-left: 260px;
    padding: 30px;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header p {
    color: var(--text-secondary);
    font-size: 16px;
}

.card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    margin-bottom: 24px;
    overflow: hidden;
    transition: all 0.3s;
}

.card:hover {
    border-color: rgba(147, 51, 234, 0.3);
    background: var(--bg-secondary);
}

.card-header {
    padding: 24px;
    background: rgba(0, 0, 0, 0.15);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h2 {
    font-size: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.badge-count {
    background: #9333ea;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.card-body {
    padding: 24px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 8px;
    font-weight: 500;
    font-size: 14px;
    color: var(--text-primary);
}

.form-group input,
.form-group select {
    padding: 12px 16px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-primary);
    color: var(--text-primary);
    font-size: 14px;
    transition: all 0.2s;
}

.form-group input::placeholder {
    color: var(--text-secondary);
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--accent-purple);
    background: var(--bg-card);
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.btn-primary {
    background: linear-gradient(135deg, #9333ea, #3b82f6);
    color: white;
    width: 100%;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(147, 51, 234, 0.3);
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.invitations-table {
    width: 100%;
    border-collapse: collapse;
}

.invitations-table thead {
    background: rgba(0, 0, 0, 0.2);
}

.invitations-table th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #a0a0a0;
    font-size: 13px;
    text-transform: uppercase;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.invitations-table td {
    padding: 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    font-size: 14px;
}

.invitations-table tbody tr:hover {
    background: rgba(147, 51, 234, 0.05);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.status-pending {
    background: rgba(234, 179, 8, 0.15);
    color: #fbbf24;
}

.status-accepted {
    background: rgba(16, 185, 129, 0.15);
    color: #4ade80;
}

.status-expired {
    background: rgba(239, 68, 68, 0.15);
    color: #ff6b6b;
}

.role-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.role-employe {
    background: rgba(59, 130, 246, 0.15);
    color: #60a5fa;
}

.role-manager {
    background: rgba(147, 51, 234, 0.15);
    color: #d8b4fe;
}

.role-rh {
    background: rgba(168, 85, 247, 0.15);
    color: #e9d5ff;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    background: rgba(0, 0, 0, 0.2);
    color: #a0a0a0;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
}

.btn-icon:hover {
    background: rgba(147, 51, 234, 0.2);
    color: #d8b4fe;
    border-color: rgba(147, 51, 234, 0.5);
}

.text-muted {
    color: #a0a0a0;
    text-align: center;
    padding: 40px 20px;
    font-size: 14px;
}

code {
    background: rgba(0, 0, 0, 0.3);
    padding: 2px 6px;
    border-radius: 3px;
    color: #60a5fa;
    font-family: 'Courier New', monospace;
    font-size: 13px;
}

@media (max-width: 1024px) {
    .sidebar {
        width: 220px;
    }
    .main-content {
        margin-left: 220px;
        padding: 20px;
    }
}

@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100%;
        width: 250px;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s;
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .main-content {
        margin-left: 0;
        padding: 20px;
    }

    .page-header h1 {
        font-size: 24px;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .invitations-table {
        font-size: 12px;
    }

    .invitations-table th,
    .invitations-table td {
        padding: 12px;
    }

    .action-buttons {
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .sidebar {
        width: 100%;
    }

    .main-content {
        margin-left: 0;
        padding: 15px;
    }

    .page-header h1 {
        font-size: 20px;
    }

    .card-header {
        padding: 16px;
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .card-body {
        padding: 16px;
    }

    .btn {
        font-size: 13px;
        padding: 10px 16px;
    }

    .invitations-table {
        font-size: 11px;
    }

    .invitations-table th,
    .invitations-table td {
        padding: 8px;
    }
}
</style>

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-search"></i> FindIN
            <button id="themeToggle" class="theme-toggle" title="Activer le mode clair/sombre">
                <i class="fas fa-moon"></i>
            </button>
        </div>
        <nav class="sidebar-menu">
            <a href="/dashboard" class="sidebar-item">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="/dashboard/rh-invitations" class="sidebar-item active">
                <i class="fas fa-envelope"></i> Invitations
            </a>
            <a href="/logout" class="sidebar-item">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-user-tie"></i> Gestion des Employés</h1>
                <p>Invitez et gérez les nouveaux employés</p>
            </div>
        </div>

        <!-- New Invitation Form -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-envelope-circle-check"></i> Créer une nouvelle invitation</h2>
            </div>
            <div class="card-body">
                <form id="invitationForm" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" placeholder="employe@example.com" required>
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" placeholder="Jean" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" placeholder="Dupont" required>
                        </div>
                        <div class="form-group">
                            <label for="departement">Département</label>
                            <input type="text" id="departement" name="departement" placeholder="IT, RH, Ventes...">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="role">Rôle *</label>
                            <select id="role" name="role" required>
                                <option value="employe">Employé</option>
                                <option value="manager">Manager</option>
                                <option value="rh">RH</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="manager_id">Manager</label>
                            <select id="manager_id" name="manager_id">
                                <option value="">-- Pas de manager --</option>
                                <?php foreach ($managers as $manager): ?>
                                    <option value="<?php echo $manager['id_utilisateur']; ?>">
                                        <?php echo htmlspecialchars($manager['prenom'] . ' ' . $manager['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Envoyer l'invitation
                    </button>
                </form>
            </div>
        </div>

        <!-- Invitations List -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-list"></i> Invitations en cours</h2>
            </div>
            <div class="card-body">
                <?php if (empty($invitations)): ?>
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i> Aucune invitation pour le moment
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="invitations-table">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Nom</th>
                                    <th>Rôle</th>
                                    <th>Status</th>
                                    <th>Expiration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invitations as $inv): ?>
                                    <tr class="invitation-row" data-status="<?php echo $inv['status']; ?>">
                                        <td>
                                            <code><?php echo htmlspecialchars($inv['email']); ?></code>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($inv['prenom'] . ' ' . $inv['nom']); ?>
                                        </td>
                                        <td>
                                            <span class="role-badge role-<?php echo $inv['role']; ?>">
                                                <?php echo ucfirst($inv['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $inv['status']; ?>">
                                                <i class="fas fa-circle-dot"></i>
                                                <?php 
                                                    $status_text = [
                                                        'pending' => 'En attente',
                                                        'accepted' => 'Acceptée',
                                                        'expired' => 'Expirée'
                                                    ];
                                                    echo $status_text[$inv['status']] ?? $inv['status'];
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                                $expires = strtotime($inv['expires_at']);
                                                $now = time();
                                                $days_left = floor(($expires - $now) / 86400);
                                                echo $days_left >= 0 ? $days_left . ' jour(s)' : 'Expiré';
                                            ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if ($inv['status'] === 'pending'): ?>
                                                    <button class="btn-icon copy-link" data-token="<?php echo $inv['token']; ?>" title="Copier le lien">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <button class="btn-icon resend" data-id="<?php echo $inv['id']; ?>" title="Renvoyer l'invitation">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                    <button class="btn-icon delete" data-id="<?php echo $inv['id']; ?>" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== Theme Toggle ==========
    const html = document.documentElement;
    const themeToggle = document.getElementById('themeToggle');
    const savedTheme = localStorage.getItem('theme') || 'dark';
    
    html.setAttribute('data-theme', savedTheme);
    updateThemeIcon();
    
    themeToggle.addEventListener('click', function() {
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon();
    });
    
    function updateThemeIcon() {
        const theme = html.getAttribute('data-theme');
        const icon = themeToggle.querySelector('i');
        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        themeToggle.title = theme === 'dark' ? 'Passer au mode clair' : 'Passer au mode sombre';
    }

    // ========== Copy invitation link ==========
    document.querySelectorAll('.copy-link').forEach(btn => {
        btn.addEventListener('click', function() {
            const token = this.dataset.token;
            const link = window.location.origin + '/invitation/accept?token=' + token;
            navigator.clipboard.writeText(link).then(() => {
                alert('Lien copié: ' + link);
            });
        });
    });

    // ========== Delete invitation ==========
    document.querySelectorAll('.delete').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette invitation?')) {
                const id = this.dataset.id;
                fetch('/invitation/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur: ' + (data.error || 'Impossible de supprimer'));
                    }
                });
            }
        });
    });

    // ========== Submit invitation form ==========
    const form = document.getElementById('invitationForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Disable submit button
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            fetch('/invitation/create', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            }).then(r => r.json()).then(result => {
                if (result.success) {
                    alert('✅ Invitation créée et email envoyé à ' + data.email);
                    this.reset();
                    location.reload();
                } else {
                    alert('❌ Erreur: ' + (result.error || 'Impossible de créer l\'invitation'));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }).catch(err => {
                alert('❌ Erreur réseau: ' + err.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
});
</script>
