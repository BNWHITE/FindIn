<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Administration</title>
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
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
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

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .search-box {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-light);
            border-radius: 8px;
            color: var(--text-white);
            font-family: inherit;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent-primary);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent-primary);
        }

        .stat-label {
            color: var(--text-light);
            margin-top: 0.5rem;
        }

        /* Table */
        .card {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 1.5rem;
            overflow: hidden;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid var(--border-light);
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-light);
            background: rgba(147, 51, 234, 0.05);
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-light);
        }

        .table-row:hover {
            background: rgba(147, 51, 234, 0.1);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .status-inactive {
            background: rgba(107, 114, 128, 0.2);
            color: #9ca3af;
        }

        .role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: rgba(147, 51, 234, 0.2);
            color: var(--accent-primary);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.5rem 0.75rem;
            background: rgba(147, 51, 234, 0.2);
            color: var(--accent-primary);
            border: 1px solid var(--accent-primary);
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: var(--accent-primary);
            color: white;
        }

        .action-btn.danger {
            color: #ef4444;
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }

        .action-btn.danger:hover {
            background: #ef4444;
            color: white;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: none;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table {
                font-size: 0.9rem;
            }

            .table th,
            .table td {
                padding: 0.75rem;
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
                    <a href="#" class="nav-item active">
                        <i class="fas fa-users"></i> Utilisateurs
                    </a>
                    <a href="#" class="nav-item">
                        <i class="fas fa-certificate"></i> Compétences
                    </a>
                    <a href="#" class="nav-item">
                        <i class="fas fa-cog"></i> Paramètres
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="page-header">
                <h1 class="page-title">Gestion des Utilisateurs</h1>
                <div class="header-actions">
                    <button class="btn"><i class="fas fa-user-plus"></i> Nouvel utilisateur</button>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">156</div>
                    <div class="stat-label">Total utilisateurs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">142</div>
                    <div class="stat-label">Actifs cette semaine</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">14</div>
                    <div class="stat-label">Inactifs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">9</div>
                    <div class="stat-label">Admins</div>
                </div>
            </div>

            <!-- Search -->
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Rechercher par nom, email...">
                <button class="btn"><i class="fas fa-search"></i> Rechercher</button>
            </div>

            <!-- Table -->
            <div class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Département</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Date d'inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-row">
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">JD</div>
                                    <div>
                                        <div><strong>Jean Dupont</strong></div>
                                        <small style="color: var(--text-light);">Senior Developer</small>
                                    </div>
                                </div>
                            </td>
                            <td>jean.dupont@example.com</td>
                            <td>Ingénierie</td>
                            <td><span class="role-badge">Admin</span></td>
                            <td><span class="status status-active">Actif</span></td>
                            <td>15 Jan 2024</td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn danger"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">MR</div>
                                    <div>
                                        <div><strong>Marie Roland</strong></div>
                                        <small style="color: var(--text-light);">Product Manager</small>
                                    </div>
                                </div>
                            </td>
                            <td>marie.roland@example.com</td>
                            <td>Produit</td>
                            <td><span class="role-badge">Modérateur</span></td>
                            <td><span class="status status-active">Actif</span></td>
                            <td>20 Jan 2024</td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn danger"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">PC</div>
                                    <div>
                                        <div><strong>Pierre Collin</strong></div>
                                        <small style="color: var(--text-light);">Junior Developer</small>
                                    </div>
                                </div>
                            </td>
                            <td>pierre.collin@example.com</td>
                            <td>Ingénierie</td>
                            <td><span class="role-badge">Utilisateur</span></td>
                            <td><span class="status status-active">Actif</span></td>
                            <td>28 Jan 2024</td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn danger"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">SB</div>
                                    <div>
                                        <div><strong>Sophie Bernard</strong></div>
                                        <small style="color: var(--text-light);">Marketing Lead</small>
                                    </div>
                                </div>
                            </td>
                            <td>sophie.bernard@example.com</td>
                            <td>Marketing</td>
                            <td><span class="role-badge">Utilisateur</span></td>
                            <td><span class="status status-inactive">Inactif</span></td>
                            <td>05 Feb 2024</td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn danger"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">TM</div>
                                    <div>
                                        <div><strong>Thomas Martin</strong></div>
                                        <small style="color: var(--text-light);">QA Engineer</small>
                                    </div>
                                </div>
                            </td>
                            <td>thomas.martin@example.com</td>
                            <td>Qualité</td>
                            <td><span class="role-badge">Utilisateur</span></td>
                            <td><span class="status status-active">Actif</span></td>
                            <td>12 Feb 2024</td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn danger"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="/assets/js/main.js"></script>
</body>
</html>
