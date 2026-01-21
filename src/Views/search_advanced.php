<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Rechercher des Talents</title>
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

        .search-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        /* Sidebar Filters */
        .sidebar {
            background: rgba(26, 13, 46, 0.8);
            backdrop-filter: blur(10px);
            border-right: 1px solid var(--border-light);
            padding: 2rem 1rem;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-light);
        }

        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .filter-section {
            margin-bottom: 2rem;
        }

        .filter-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--accent-primary);
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .filter-option {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-option input {
            margin-right: 0.5rem;
            cursor: pointer;
        }

        .filter-option label {
            cursor: pointer;
            flex: 1;
        }

        .filter-option:hover {
            color: var(--accent-primary);
        }

        .filter-slider {
            width: 100%;
            margin: 1rem 0;
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .search-bar {
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
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            background: rgba(147, 51, 234, 0.1);
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

        /* Results */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-light);
        }

        .results-count {
            font-size: 0.95rem;
            color: var(--text-light);
        }

        .sort-select {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-light);
            border-radius: 6px;
            color: var(--text-white);
            font-family: inherit;
        }

        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .user-card {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 2rem;
            transition: all 0.3s ease;
            text-align: center;
        }

        .user-card:hover {
            border-color: var(--accent-primary);
            transform: translateY(-5px);
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            margin: 0 auto 1rem;
            border: 3px solid var(--border-light);
        }

        .user-name {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .user-title {
            color: var(--accent-primary);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .user-location {
            color: var(--text-light);
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .competences {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            background: rgba(147, 51, 234, 0.2);
            color: var(--accent-primary);
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .user-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-sm {
            flex: 1;
            padding: 0.5rem 1rem;
            background: rgba(147, 51, 234, 0.2);
            color: var(--accent-primary);
            border: 1px solid var(--accent-primary);
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-sm:hover {
            background: var(--accent-primary);
            color: white;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 3rem;
        }

        .pagination button {
            padding: 0.5rem 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-light);
            color: var(--text-white);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pagination button:hover,
        .pagination button.active {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
            color: white;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 3rem;
            color: var(--text-light);
        }

        .no-results i {
            font-size: 3rem;
            color: var(--accent-primary);
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .search-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: none;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .results-grid {
                grid-template-columns: 1fr;
            }

            .search-bar {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="search-container">
        <!-- Sidebar Filters -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-title">Filtres</div>
            </div>

            <!-- Compétences Filter -->
            <div class="filter-section">
                <div class="filter-title">Compétences</div>
                <div class="filter-option">
                    <input type="checkbox" id="php" checked>
                    <label for="php">PHP</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="js" checked>
                    <label for="js">JavaScript</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="react">
                    <label for="react">React</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="devops">
                    <label for="devops">DevOps</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="leadership">
                    <label for="leadership">Leadership</label>
                </div>
            </div>

            <!-- Level Filter -->
            <div class="filter-section">
                <div class="filter-title">Niveau</div>
                <div class="filter-option">
                    <input type="checkbox" id="expert">
                    <label for="expert">Expert</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="advanced" checked>
                    <label for="advanced">Avancé</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="intermediate" checked>
                    <label for="intermediate">Intermédiaire</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="beginner">
                    <label for="beginner">Débutant</label>
                </div>
            </div>

            <!-- Location Filter -->
            <div class="filter-section">
                <div class="filter-title">Localisation</div>
                <div class="filter-option">
                    <input type="checkbox" id="paris" checked>
                    <label for="paris">Paris</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="lyon">
                    <label for="lyon">Lyon</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="marseille">
                    <label for="marseille">Marseille</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="remote" checked>
                    <label for="remote">Télétravail</label>
                </div>
            </div>

            <!-- Department Filter -->
            <div class="filter-section">
                <div class="filter-title">Département</div>
                <div class="filter-option">
                    <input type="checkbox" id="engineering" checked>
                    <label for="engineering">Ingénierie</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="product">
                    <label for="product">Produit</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="marketing">
                    <label for="marketing">Marketing</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="sales">
                    <label for="sales">Ventes</label>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="page-header">
                <h1 class="page-title">Rechercher des Talents</h1>
            </div>

            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" class="search-input" placeholder="Rechercher par nom, compétence...">
                <button class="btn"><i class="fas fa-search"></i> Rechercher</button>
            </div>

            <!-- Results Header -->
            <div class="results-header">
                <span class="results-count">Affichage de 6 résultats sur 24</span>
                <select class="sort-select">
                    <option>Pertinence</option>
                    <option>Plus récents</option>
                    <option>Mieux évalués</option>
                </select>
            </div>

            <!-- Results Grid -->
            <div class="results-grid">
                <!-- User Card 1 -->
                <div class="user-card">
                    <div class="user-avatar">JD</div>
                    <div class="user-name">Jean Dupont</div>
                    <div class="user-title">Senior Software Developer</div>
                    <div class="user-location">Paris, France</div>
                    <div class="competences">
                        <span class="badge">PHP</span>
                        <span class="badge">JavaScript</span>
                        <span class="badge">React</span>
                    </div>
                    <div class="user-actions">
                        <button class="btn-sm"><i class="fas fa-user-plus"></i> Ajouter</button>
                        <button class="btn-sm"><i class="fas fa-eye"></i> Voir</button>
                    </div>
                </div>

                <!-- User Card 2 -->
                <div class="user-card">
                    <div class="user-avatar">MR</div>
                    <div class="user-name">Marie Roland</div>
                    <div class="user-title">Product Manager</div>
                    <div class="user-location">Lyon, France</div>
                    <div class="competences">
                        <span class="badge">Leadership</span>
                        <span class="badge">Communication</span>
                    </div>
                    <div class="user-actions">
                        <button class="btn-sm"><i class="fas fa-user-plus"></i> Ajouter</button>
                        <button class="btn-sm"><i class="fas fa-eye"></i> Voir</button>
                    </div>
                </div>

                <!-- User Card 3 -->
                <div class="user-card">
                    <div class="user-avatar">PC</div>
                    <div class="user-name">Pierre Collin</div>
                    <div class="user-title">Full Stack Developer</div>
                    <div class="user-location">Télétravail</div>
                    <div class="competences">
                        <span class="badge">JavaScript</span>
                        <span class="badge">DevOps</span>
                    </div>
                    <div class="user-actions">
                        <button class="btn-sm"><i class="fas fa-user-plus"></i> Ajouter</button>
                        <button class="btn-sm"><i class="fas fa-eye"></i> Voir</button>
                    </div>
                </div>

                <!-- User Card 4 -->
                <div class="user-card">
                    <div class="user-avatar">SB</div>
                    <div class="user-name">Sophie Bernard</div>
                    <div class="user-title">Marketing Lead</div>
                    <div class="user-location">Marseille, France</div>
                    <div class="competences">
                        <span class="badge">Leadership</span>
                        <span class="badge">Marketing</span>
                    </div>
                    <div class="user-actions">
                        <button class="btn-sm"><i class="fas fa-user-plus"></i> Ajouter</button>
                        <button class="btn-sm"><i class="fas fa-eye"></i> Voir</button>
                    </div>
                </div>

                <!-- User Card 5 -->
                <div class="user-card">
                    <div class="user-avatar">TM</div>
                    <div class="user-name">Thomas Martin</div>
                    <div class="user-title">QA Engineer</div>
                    <div class="user-location">Paris, France</div>
                    <div class="competences">
                        <span class="badge">DevOps</span>
                        <span class="badge">Communication</span>
                    </div>
                    <div class="user-actions">
                        <button class="btn-sm"><i class="fas fa-user-plus"></i> Ajouter</button>
                        <button class="btn-sm"><i class="fas fa-eye"></i> Voir</button>
                    </div>
                </div>

                <!-- User Card 6 -->
                <div class="user-card">
                    <div class="user-avatar">AJ</div>
                    <div class="user-name">Antoine Jacques</div>
                    <div class="user-title">DevOps Engineer</div>
                    <div class="user-location">Télétravail</div>
                    <div class="competences">
                        <span class="badge">DevOps</span>
                        <span class="badge">Leadership</span>
                    </div>
                    <div class="user-actions">
                        <button class="btn-sm"><i class="fas fa-user-plus"></i> Ajouter</button>
                        <button class="btn-sm"><i class="fas fa-eye"></i> Voir</button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button><i class="fas fa-chevron-left"></i></button>
                <button class="active">1</button>
                <button>2</button>
                <button>3</button>
                <button>4</button>
                <button><i class="fas fa-chevron-right"></i></button>
            </div>
        </main>
    </div>

    <script src="/assets/js/main.js"></script>
</body>
</html>
