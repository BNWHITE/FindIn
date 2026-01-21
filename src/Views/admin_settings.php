<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Paramètres Administrateur</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0a0118;
            --bg-card: #1a0d2e;
            --text-white: #ffffff;
            --text-light: #e0e0e0;
            --accent-primary: #9333ea;
            --accent-blue: #3b82f6;
            --accent-pink: #ec4899;
            --border-light: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0118 0%, #1a0d2e 100%);
            color: var(--text-white);
            line-height: 1.6;
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background: rgba(26, 13, 46, 0.8);
            backdrop-filter: blur(10px);
            border-right: 1px solid var(--border-light);
            padding: 2rem 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .sidebar-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 0 1rem;
        }

        .nav-item {
            padding: 0.75rem 1rem;
            color: var(--text-light);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(147, 51, 234, 0.2);
            color: var(--accent-primary);
        }

        .sidebar-section {
            margin-top: 2rem;
        }

        .sidebar-section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
            overflow-y: auto;
            max-width: 1000px;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .settings-section {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: var(--accent-primary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-white);
        }

        .form-label small {
            color: var(--text-light);
            font-weight: 400;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-light);
            border-radius: 8px;
            color: var(--text-white);
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--accent-primary);
            background: rgba(147, 51, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group-switch {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            border: 1px solid var(--border-light);
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 28px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid var(--border-light);
        }

        .toggle-switch.active {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: white;
            top: 1.5px;
            left: 2px;
            transition: left 0.3s ease;
        }

        .toggle-switch.active::after {
            left: 24px;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-light);
            color: var(--text-white);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border-left-color: #22c55e;
            color: #22c55e;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-left-color: #f59e0b;
            color: #f59e0b;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: #ef4444;
            color: #ef4444;
        }

        .info-box {
            background: rgba(147, 51, 234, 0.05);
            border: 1px solid var(--border-light);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: none;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">F</div>
                <div class="sidebar-title">FindIN</div>
            </div>

            <nav class="sidebar-nav">
                <a href="/dashboard" class="nav-item">
                    <i class="fas fa-chart-line"></i> Tableau de bord
                </a>
            </nav>

            <div class="sidebar-section">
                <div class="sidebar-section-title">Admin</div>
                <nav class="sidebar-nav">
                    <a href="/admin_users" class="nav-item">
                        <i class="fas fa-users"></i> Utilisateurs
                    </a>
                    <a href="/admin_competences" class="nav-item">
                        <i class="fas fa-certificate"></i> Compétences
                    </a>
                    <a href="#" class="nav-item active">
                        <i class="fas fa-cog"></i> Paramètres
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="page-header">
                <h1 class="page-title">Paramètres Administrateur</h1>
                <p class="page-subtitle">Gérez les paramètres généraux de la plateforme</p>
            </div>

            <!-- Paramètres Généraux -->
            <div class="settings-section">
                <h2 class="section-title">
                    <i class="fas fa-globe"></i> Paramètres Généraux
                </h2>

                <div class="form-group">
                    <label class="form-label">Nom de la plateforme</label>
                    <input type="text" class="form-input" value="FindIN" placeholder="Nom de la plateforme">
                </div>

                <div class="form-group">
                    <label class="form-label">Description courte</label>
                    <textarea class="form-textarea" placeholder="Description de la plateforme">Une plateforme révolutionnaire pour découvrir et valider les talents cachés.</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email de support</label>
                        <input type="email" class="form-input" value="support@findin.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" class="form-input" value="+33 1 XX XX XX XX">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Adresse</label>
                        <input type="text" class="form-input" value="Paris, France">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pays</label>
                        <select class="form-select">
                            <option selected>France</option>
                            <option>Belgique</option>
                            <option>Suisse</option>
                            <option>Canada</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Paramètres de Sécurité -->
            <div class="settings-section">
                <h2 class="section-title">
                    <i class="fas fa-shield-alt"></i> Sécurité
                </h2>

                <div class="form-group-switch">
                    <div>
                        <label class="form-label" style="margin: 0;">Authentification à deux facteurs</label>
                        <small style="color: var(--text-light);">Renforcer la sécurité des comptes</small>
                    </div>
                    <div class="toggle-switch"></div>
                </div>

                <div class="form-group-switch" style="margin-top: 1rem;">
                    <div>
                        <label class="form-label" style="margin: 0;">Connexion par email uniquement</label>
                        <small style="color: var(--text-light);">Désactiver la connexion par Google/SAML</small>
                    </div>
                    <div class="toggle-switch"></div>
                </div>

                <div class="form-group-switch" style="margin-top: 1rem;">
                    <div>
                        <label class="form-label" style="margin: 0;">Sessions multiples</label>
                        <small style="color: var(--text-light);">Permettre l'utilisation sur plusieurs appareils</small>
                    </div>
                    <div class="toggle-switch active"></div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label class="form-label">Durée de session (en heures)</label>
                    <input type="number" class="form-input" value="24" min="1" max="168">
                </div>

                <div class="info-box">
                    <i class="fas fa-info-circle"></i> Les modifications de sécurité s'appliqueront aux nouvelles sessions.
                </div>
            </div>

            <!-- Paramètres d'Email -->
            <div class="settings-section">
                <h2 class="section-title">
                    <i class="fas fa-envelope"></i> Paramètres d'Email
                </h2>

                <div class="form-group">
                    <label class="form-label">Adresse expéditeur</label>
                    <input type="email" class="form-input" value="noreply@findin.com">
                </div>

                <div class="form-group-switch">
                    <div>
                        <label class="form-label" style="margin: 0;">Notifications par email</label>
                        <small style="color: var(--text-light);">Envoyer des emails de notification</small>
                    </div>
                    <div class="toggle-switch active"></div>
                </div>

                <div class="form-group-switch" style="margin-top: 1rem;">
                    <div>
                        <label class="form-label" style="margin: 0;">Digests hebdomadaires</label>
                        <small style="color: var(--text-light);">Envoyer un résumé hebdomadaire aux utilisateurs</small>
                    </div>
                    <div class="toggle-switch active"></div>
                </div>
            </div>

            <!-- Paramètres de Maintenance -->
            <div class="settings-section">
                <h2 class="section-title">
                    <i class="fas fa-tools"></i> Maintenance
                </h2>

                <div class="form-group-switch">
                    <div>
                        <label class="form-label" style="margin: 0;">Mode maintenance</label>
                        <small style="color: var(--text-light);">Mettre la plateforme en maintenance</small>
                    </div>
                    <div class="toggle-switch"></div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label class="form-label">Message de maintenance</label>
                    <textarea class="form-textarea" placeholder="Message affiché pendant la maintenance">La plateforme est en maintenance. Veuillez réessayer plus tard.</textarea>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Adresses IP autorisées <small style="color: var(--text-light);">(optionnel)</small></label>
                    <input type="text" class="form-input" placeholder="192.168.1.1, 10.0.0.1">
                    <small style="color: var(--text-light); display: block; margin-top: 0.5rem;">Une par ligne, séparées par des virgules</small>
                </div>
            </div>

            <!-- Actions -->
            <div class="settings-section">
                <div class="btn-group">
                    <button class="btn"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                    <button class="btn btn-secondary"><i class="fas fa-redo"></i> Réinitialiser</button>
                </div>
                <div class="btn-group" style="margin-top: 1rem;">
                    <button class="btn btn-secondary">
                        <i class="fas fa-database"></i> Exporter les données
                    </button>
                    <button class="btn btn-danger" style="margin-left: auto;">
                        <i class="fas fa-trash"></i> Supprimer toutes les données
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script src="/assets/js/main.js"></script>
    <script>
        // Toggle switches functionality
        document.querySelectorAll('.toggle-switch').forEach(toggle => {
            toggle.addEventListener('click', function() {
                this.classList.toggle('active');
            });
        });
    </script>
</body>
</html>
