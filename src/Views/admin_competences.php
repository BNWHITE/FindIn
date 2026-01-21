<?php if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../views/layouts/header.php';

// Inline page styles kept here so header/footer remain consistent
?>
<style>
    :root { --bg-dark: #0a0118; --bg-card: #1a0d2e; --text-white: #ffffff; --text-light: #e0e0e0; --accent-primary: #9333ea; --accent-blue: #3b82f6; --accent-pink: #ec4899; --border-light: rgba(255,255,255,0.1); }
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;background:linear-gradient(135deg,#0a0118 0%,#1a0d2e 100%);color:var(--text-white);line-height:1.6}
    .dashboard-container{display:grid;grid-template-columns:280px 1fr;min-height:calc(100vh - 120px)}
    .sidebar{background:rgba(26,13,46,0.8);backdrop-filter:blur(10px);border-right:1px solid var(--border-light);padding:2rem 0;position:sticky;top:0;height:100vh;overflow-y:auto}
    .sidebar-header{padding:0 1.5rem;margin-bottom:2rem;display:flex;align-items:center;gap:.75rem}
    .sidebar-logo{width:40px;height:40px;background:linear-gradient(135deg,#9333ea 0%,#3b82f6 100%);border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1.25rem}
    .sidebar-title{font-size:1.25rem;font-weight:700}
    .sidebar-nav{display:flex;flex-direction:column;gap:.5rem;padding:0 1rem}
    .nav-item{padding:.75rem 1rem;color:var(--text-light);text-decoration:none;border-radius:8px;transition:all .3s;display:flex;align-items:center;gap:.75rem;cursor:pointer}
    .nav-item:hover,.nav-item.active{background:rgba(147,51,234,.2);color:var(--accent-primary)}
    .sidebar-section{margin-top:2rem}
    .sidebar-section-title{padding:.5rem 1.5rem;font-size:.75rem;font-weight:700;text-transform:uppercase;color:rgba(255,255,255,.5)}
    .main-content{padding:2rem;overflow-y:auto}
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem}
    .page-title{font-size:2rem;font-weight:800}
    .header-actions{display:flex;gap:1rem}
    .btn{padding:.75rem 1.5rem;background:linear-gradient(135deg,#9333ea 0%,#3b82f6 100%);color:white;border:none;border-radius:8px;cursor:pointer;font-weight:600;transition:all .3s}
    .btn:hover{transform:translateY(-2px)}
    .btn-sm{padding:.5rem 1rem;font-size:.85rem}
    .btn-danger{background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%)}
    .search-box{display:flex;gap:1rem;margin-bottom:2rem}
    .search-input{flex:1;padding:.75rem 1rem;background:rgba(255,255,255,.05);border:1px solid var(--border-light);border-radius:8px;color:var(--text-white);font-family:inherit}
    .search-input:focus{outline:none;border-color:var(--accent-primary)}
    .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.5rem;margin-bottom:2rem}
    .stat-card{background:linear-gradient(135deg,rgba(147,51,234,.1) 0%,rgba(59,130,246,.05) 100%);border:1px solid var(--border-light);border-radius:12px;padding:1.5rem}
    .stat-value{font-size:2rem;font-weight:800;color:var(--accent-primary)}
    .stat-label{color:var(--text-light);margin-top:.5rem}
    .card{background:linear-gradient(135deg,rgba(147,51,234,.1) 0%,rgba(59,130,246,.05) 100%);border:1px solid var(--border-light);border-radius:12px;padding:1.5rem;overflow:hidden}
    .table{width:100%;border-collapse:collapse}
    .table th{text-align:left;padding:1rem;border-bottom:1px solid var(--border-light);font-weight:600;font-size:.85rem;color:var(--text-light);background:rgba(147,51,234,.05)}
    .table td{padding:1rem;border-bottom:1px solid var(--border-light)}
    .table-row:hover{background:rgba(147,51,234,.1)}
    .competence-icon{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#9333ea 0%,#3b82f6 100%);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem}
    .competence-info{display:flex;align-items:center;gap:.75rem}
    .level-bar{background:rgba(255,255,255,.1);height:6px;border-radius:3px;overflow:hidden;width:100px}
    .level-fill{height:100%;background:linear-gradient(to right,var(--accent-primary),var(--accent-blue))}
    .category-badge{display:inline-block;padding:.25rem .75rem;background:rgba(147,51,234,.2);color:var(--accent-primary);border-radius:20px;font-size:.8rem;font-weight:600}
    .status-badge{display:inline-block;padding:.25rem .75rem;border-radius:20px;font-size:.8rem;font-weight:600}
    .status-active{background:rgba(34,197,94,.2);color:#22c55e}.status-pending{background:rgba(245,158,11,.2);color:#f59e0b}
    .actions{display:flex;gap:.5rem}.action-btn{padding:.5rem .75rem;background:rgba(147,51,234,.2);color:var(--accent-primary);border:1px solid var(--accent-primary);border-radius:6px;cursor:pointer;font-size:.8rem;transition:all .3s}
    .action-btn:hover{background:var(--accent-primary);color:white}.action-btn.danger{color:#ef4444;border-color:#ef4444;background:rgba(239,68,68,.1)}.action-btn.danger:hover{background:#ef4444;color:white}
    @media (max-width:768px){.dashboard-container{grid-template-columns:1fr}.sidebar{display:none}.page-header{flex-direction:column;align-items:flex-start}.stats-grid{grid-template-columns:1fr}.table{font-size:.9rem}.table th,.table td{padding:.75rem}}
</style>

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
                    <a href="#" class="nav-item active">
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
                <h1 class="page-title">Gestion des Compétences</h1>
                <div class="header-actions">
                    <button class="btn"><i class="fas fa-plus"></i> Nouvelle compétence</button>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">48</div>
                    <div class="stat-label">Compétences totales</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">156</div>
                    <div class="stat-label">Utilisateurs avec compétences</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">42</div>
                    <div class="stat-label">Compétences validées</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">6</div>
                    <div class="stat-label">En attente de validation</div>
                </div>
            </div>

            <!-- Search -->
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Rechercher une compétence...">
                <button class="btn"><i class="fas fa-search"></i> Rechercher</button>
            </div>

            <!-- Table -->
            <div class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Compétence</th>
                            <th>Catégorie</th>
                            <th>Utilisateurs</th>
                            <th>Niveau moyen</th>
                            <th>Validées</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-row">
                            <td>
                                <div class="competence-info">
                                    <div class="competence-icon"><i class="fab fa-php"></i></div>
                                    <div>
                                        <div><strong>PHP</strong></div>
                                        <small style="color: var(--text-light);">Backend</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="category-badge">Programmation</span></td>
                            <td><strong>24</strong></td>
                            <td>
                                <div class="level-bar">
                                    <div class="level-fill" style="width: 85%;"></div>
                                </div>
                            </td>
                            <td>23</td>
                            <td><span class="status-badge status-active">Actif</span></td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn danger"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td>
                                <div class="competence-info">
                                    <div class="competence-icon"><i class="fab fa-js"></i></div>
                                    <div>
                                        <div><strong>JavaScript</strong></div>
                                        <small style="color: var(--text-light);">Frontend</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="category-badge">Programmation</span></td>
                            <td><strong>21</strong></td>
                            <td>
                                <div class="level-bar">
                                    <div class="level-fill" style="width: 78%;"></div>
                                </div>
                            </td>
                            <td>19</td>
                            <td><span class="status-badge status-active">Actif</span></td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn danger"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td>
                                <div class="competence-info">
                                    <div class="competence-icon"><i class="fab fa-react"></i></div>
                                    <div>
                                        <div><strong>React.js</strong></div>
                                        <small style="color: var(--text-light);">Framework</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="category-badge">Programmation</span></td>
                            <td><strong>18</strong></td>
                            <td>
                                <div class="level-bar">
                                    <div class="level-fill" style="width: 80%;"></div>
                                </div>
                            </td>
                            <td>16</td>
                            <td><span class="status-badge status-active">Actif</span></td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn danger"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td>
                                <div class="competence-info">
                                    <div class="competence-icon"><i class="fas fa-users"></i></div>
                                    <div>
                                        <div><strong>Leadership</strong></div>
                                        <small style="color: var(--text-light);">Soft Skills</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="category-badge">Compétences</span></td>
                            <td><strong>15</strong></td>
                            <td>
                                <div class="level-bar">
                                    <div class="level-fill" style="width: 65%;"></div>
                                </div>
                            </td>
                            <td>8</td>
                            <td><span class="status-badge status-pending">En attente</span></td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn danger"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td>
                                <div class="competence-info">
                                    <div class="competence-icon"><i class="fas fa-cogs"></i></div>
                                    <div>
                                        <div><strong>DevOps</strong></div>
                                        <small style="color: var(--text-light);">Ops</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="category-badge">Infrastructure</span></td>
                            <td><strong>12</strong></td>
                            <td>
                                <div class="level-bar">
                                    <div class="level-fill" style="width: 88%;"></div>
                                </div>
                            </td>
                            <td>11</td>
                            <td><span class="status-badge status-active">Actif</span></td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn danger"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td>
                                <div class="competence-info">
                                    <div class="competence-icon"><i class="fas fa-comment"></i></div>
                                    <div>
                                        <div><strong>Communication</strong></div>
                                        <small style="color: var(--text-light);">Soft Skills</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="category-badge">Compétences</span></td>
                            <td><strong>28</strong></td>
                            <td>
                                <div class="level-bar">
                                    <div class="level-fill" style="width: 90%;"></div>
                                </div>
                            </td>
                            <td>26</td>
                            <td><span class="status-badge status-active">Actif</span></td>
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
</div>

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?>
