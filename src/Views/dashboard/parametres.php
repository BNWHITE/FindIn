<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
$user = ['name' => $_SESSION['user_name'] ?? 'Utilisateur', 'email' => $_SESSION['user_email'] ?? '', 'role' => $_SESSION['user_role'] ?? 'employe'];
$currentPage = 'parametres';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - FindIN</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="/assets/css/dashboard.css" rel="stylesheet">
    <style>
        .settings-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
        }
        .settings-nav {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 1rem;
            height: fit-content;
            position: sticky;
            top: 2rem;
        }
        .settings-nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            color: var(--text-secondary);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 0.25rem;
        }
        .settings-nav-item:hover {
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }
        .settings-nav-item.active {
            background: var(--accent-primary);
            color: white;
        }
        .settings-nav-item i {
            width: 20px;
            text-align: center;
        }
        .settings-content {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 2rem;
        }
        .settings-section {
            display: none;
        }
        .settings-section.active {
            display: block;
        }
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        .section-subtitle {
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 0.9375rem;
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .profile-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }
        .profile-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            font-weight: 600;
            position: relative;
        }
        .avatar-edit {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 32px;
            height: 32px;
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 0.2s;
        }
        .avatar-edit:hover {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
        }
        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--bg-tertiary);
            transition: 0.3s;
            border-radius: 26px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        .toggle-switch input:checked + .toggle-slider {
            background-color: var(--accent-primary);
        }
        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }
        .setting-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .setting-row:last-child {
            border-bottom: none;
        }
        .setting-info h4 {
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        .setting-info p {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        .theme-options {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        .theme-option {
            flex: 1;
            padding: 1.5rem;
            border-radius: 12px;
            border: 2px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }
        .theme-option:hover {
            border-color: var(--accent-primary);
        }
        .theme-option.active {
            border-color: var(--accent-primary);
            background: rgba(99, 102, 241, 0.1);
        }
        .theme-preview {
            width: 60px;
            height: 40px;
            border-radius: 6px;
            margin: 0 auto 0.5rem;
        }
        .theme-preview.light {
            background: linear-gradient(135deg, #f8fafc 50%, #e2e8f0 50%);
        }
        .theme-preview.dark {
            background: linear-gradient(135deg, #1e1e2e 50%, #2d2d44 50%);
        }
        .theme-preview.auto {
            background: linear-gradient(135deg, #f8fafc 33%, #64748b 33%, #64748b 66%, #1e1e2e 66%);
        }
        .danger-zone {
            border: 1px solid #ef4444;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .danger-zone h4 {
            color: #ef4444;
            margin-bottom: 0.5rem;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .integration-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-tertiary);
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        .integration-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .integration-info {
            flex: 1;
        }
        .integration-info h4 {
            color: var(--text-primary);
            font-weight: 500;
        }
        .integration-info p {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        @media (max-width: 768px) {
            .settings-container {
                grid-template-columns: 1fr;
            }
            .settings-nav {
                position: static;
                display: flex;
                overflow-x: auto;
                padding: 0.5rem;
            }
            .settings-nav-item {
                white-space: nowrap;
                margin-bottom: 0;
                margin-right: 0.5rem;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <main class="main-content">
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>

        <div class="page-header">
            <div>
                <h1 class="page-title">Paramètres</h1>
                <p class="page-subtitle">Gérez vos préférences et votre compte</p>
            </div>
            <button class="btn btn-primary" onclick="saveSettings()">
                <i class="fas fa-save"></i> Enregistrer
            </button>
        </div>

        <div class="settings-container">
            <nav class="settings-nav">
                <div class="settings-nav-item active" onclick="showSection('profile')">
                    <i class="fas fa-user"></i> Profil
                </div>
                <div class="settings-nav-item" onclick="showSection('account')">
                    <i class="fas fa-lock"></i> Compte
                </div>
                <div class="settings-nav-item" onclick="showSection('notifications')">
                    <i class="fas fa-bell"></i> Notifications
                </div>
                <div class="settings-nav-item" onclick="showSection('appearance')">
                    <i class="fas fa-palette"></i> Apparence
                </div>
                <div class="settings-nav-item" onclick="showSection('integrations')">
                    <i class="fas fa-plug"></i> Intégrations
                </div>
                <div class="settings-nav-item" onclick="showSection('privacy')">
                    <i class="fas fa-shield-alt"></i> Confidentialité
                </div>
            </nav>

            <div class="settings-content">
                <!-- Section Profil -->
                <div class="settings-section active" id="profile">
                    <h2 class="section-title">Profil</h2>
                    <p class="section-subtitle">Gérez vos informations personnelles</p>
                    
                    <div class="profile-header">
                        <div class="profile-avatar-large">
                            <?= strtoupper(substr($user['prenom'] ?? 'U', 0, 1) . substr($user['nom'] ?? 'U', 0, 1)) ?>
                            <span class="avatar-edit"><i class="fas fa-camera"></i></span>
                        </div>
                        <div>
                            <h3 style="color: var(--text-primary); margin-bottom: 0.25rem;">
                                <?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?>
                            </h3>
                            <p style="color: var(--text-secondary);"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                            <button class="btn btn-secondary" style="margin-top: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem;">
                                Changer la photo
                            </button>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Prénom</label>
                            <input type="text" class="form-input" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-input" value="<?= htmlspecialchars($user['nom'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" class="form-input" placeholder="+33 6 12 34 56 78">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Poste</label>
                        <input type="text" class="form-input" value="<?= htmlspecialchars($user['poste'] ?? 'Développeur') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bio</label>
                        <textarea class="form-input" rows="4" placeholder="Parlez-nous de vous..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Section Compte -->
                <div class="settings-section" id="account">
                    <h2 class="section-title">Compte & Sécurité</h2>
                    <p class="section-subtitle">Gérez la sécurité de votre compte</p>

                    <div class="form-group">
                        <label class="form-label">Mot de passe actuel</label>
                        <input type="password" class="form-input" placeholder="••••••••">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-input" placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-input" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Authentification à deux facteurs</h4>
                            <p>Ajoutez une couche de sécurité supplémentaire</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Sessions actives</h4>
                            <p>Gérez vos appareils connectés</p>
                        </div>
                        <button class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                            Voir les sessions
                        </button>
                    </div>

                    <div class="danger-zone">
                        <h4><i class="fas fa-exclamation-triangle"></i> Zone de danger</h4>
                        <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                            Ces actions sont irréversibles. Procédez avec prudence.
                        </p>
                        <button class="btn-danger">
                            <i class="fas fa-trash"></i> Supprimer mon compte
                        </button>
                    </div>
                </div>

                <!-- Section Notifications -->
                <div class="settings-section" id="notifications">
                    <h2 class="section-title">Notifications</h2>
                    <p class="section-subtitle">Choisissez ce qui vous est notifié</p>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Notifications par email</h4>
                            <p>Recevez des mises à jour par email</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Notifications push</h4>
                            <p>Notifications dans le navigateur</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Nouveaux projets</h4>
                            <p>Être notifié des projets correspondant à vos compétences</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Rappels de réunions</h4>
                            <p>Recevoir des rappels avant les réunions</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Résultats de tests</h4>
                            <p>Être notifié des nouveaux résultats de tests</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Newsletter hebdomadaire</h4>
                            <p>Résumé de votre activité chaque semaine</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Section Apparence -->
                <div class="settings-section" id="appearance">
                    <h2 class="section-title">Apparence</h2>
                    <p class="section-subtitle">Personnalisez l'interface</p>

                    <h4 style="color: var(--text-primary); margin-bottom: 0.5rem;">Thème</h4>
                    <p style="color: var(--text-secondary); margin-bottom: 1rem;">Sélectionnez votre thème préféré</p>
                    
                    <div class="theme-options">
                        <div class="theme-option" onclick="setTheme('light')" id="theme-light">
                            <div class="theme-preview light"></div>
                            <span style="color: var(--text-primary);">Clair</span>
                        </div>
                        <div class="theme-option active" onclick="setTheme('dark')" id="theme-dark">
                            <div class="theme-preview dark"></div>
                            <span style="color: var(--text-primary);">Sombre</span>
                        </div>
                        <div class="theme-option" onclick="setTheme('auto')" id="theme-auto">
                            <div class="theme-preview auto"></div>
                            <span style="color: var(--text-primary);">Auto</span>
                        </div>
                    </div>

                    <div class="setting-row" style="margin-top: 2rem;">
                        <div class="setting-info">
                            <h4>Sidebar compacte</h4>
                            <p>Afficher uniquement les icônes dans la sidebar</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Animations</h4>
                            <p>Activer les animations d'interface</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Section Intégrations -->
                <div class="settings-section" id="integrations">
                    <h2 class="section-title">Intégrations</h2>
                    <p class="section-subtitle">Connectez vos outils préférés</p>

                    <div class="integration-card">
                        <div class="integration-icon" style="background: #0a66c2; color: white;">
                            <i class="fab fa-linkedin"></i>
                        </div>
                        <div class="integration-info">
                            <h4>LinkedIn</h4>
                            <p>Synchronisez votre profil professionnel</p>
                        </div>
                        <button class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                            Connecter
                        </button>
                    </div>

                    <div class="integration-card">
                        <div class="integration-icon" style="background: #333; color: white;">
                            <i class="fab fa-github"></i>
                        </div>
                        <div class="integration-info">
                            <h4>GitHub</h4>
                            <p>Importez vos projets et contributions</p>
                        </div>
                        <span class="badge badge-success"><i class="fas fa-check"></i> Connecté</span>
                    </div>

                    <div class="integration-card">
                        <div class="integration-icon" style="background: #4285f4; color: white;">
                            <i class="fab fa-google"></i>
                        </div>
                        <div class="integration-info">
                            <h4>Google Calendar</h4>
                            <p>Synchronisez vos réunions</p>
                        </div>
                        <button class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                            Connecter
                        </button>
                    </div>

                    <div class="integration-card">
                        <div class="integration-icon" style="background: #611f69; color: white;">
                            <i class="fab fa-slack"></i>
                        </div>
                        <div class="integration-info">
                            <h4>Slack</h4>
                            <p>Recevez des notifications sur Slack</p>
                        </div>
                        <button class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                            Connecter
                        </button>
                    </div>
                </div>

                <!-- Section Confidentialité -->
                <div class="settings-section" id="privacy">
                    <h2 class="section-title">Confidentialité</h2>
                    <p class="section-subtitle">Contrôlez la visibilité de vos données</p>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Profil public</h4>
                            <p>Permettre aux autres utilisateurs de voir votre profil</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Afficher mes compétences</h4>
                            <p>Rendre vos compétences visibles dans les recherches</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Afficher mon statut en ligne</h4>
                            <p>Montrer quand vous êtes disponible</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <h4>Historique de recherche</h4>
                            <p>Conserver l'historique de vos recherches</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="form-group" style="margin-top: 2rem;">
                        <label class="form-label">Exporter mes données</label>
                        <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                            Téléchargez une copie de toutes vos données
                        </p>
                        <button class="btn btn-secondary">
                            <i class="fas fa-download"></i> Exporter mes données
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeButtons(newTheme);
        }
        
        function toggleMobileMenu() {
            document.querySelector('.sidebar').classList.toggle('open');
        }
        
        function showSection(sectionId) {
            document.querySelectorAll('.settings-nav-item').forEach(item => item.classList.remove('active'));
            document.querySelectorAll('.settings-section').forEach(section => section.classList.remove('active'));
            
            event.target.closest('.settings-nav-item').classList.add('active');
            document.getElementById(sectionId).classList.add('active');
        }
        
        function setTheme(theme) {
            document.querySelectorAll('.theme-option').forEach(opt => opt.classList.remove('active'));
            document.getElementById('theme-' + theme).classList.add('active');
            
            if (theme === 'auto') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
            } else {
                document.documentElement.setAttribute('data-theme', theme);
            }
            localStorage.setItem('theme', theme);
        }
        
        function updateThemeButtons(theme) {
            document.querySelectorAll('.theme-option').forEach(opt => opt.classList.remove('active'));
            const themeBtn = document.getElementById('theme-' + theme);
            if (themeBtn) themeBtn.classList.add('active');
        }
        
        function saveSettings() {
            // Simulate save
            const btn = event.target.closest('.btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Enregistré !';
            btn.style.background = '#10b981';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '';
            }, 2000);
        }

        // Theme persistence
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeButtons(savedTheme);
    </script>
</body>
</html>
