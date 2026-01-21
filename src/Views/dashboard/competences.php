<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
$currentPage = 'competences';
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userRole = $_SESSION['user_role'] ?? 'collaborateur';

// Catégories de compétences basées sur la BDD
$categories = [
    'Développement' => ['PHP', 'JavaScript', 'Python', 'Java', 'C++', 'React', 'Vue.js', 'Node.js', 'Laravel', 'Symfony'],
    'Base de données' => ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'Oracle'],
    'DevOps' => ['Docker', 'Kubernetes', 'AWS', 'Azure', 'CI/CD', 'Linux', 'Git'],
    'Soft Skills' => ['Communication', 'Leadership', 'Gestion de projet', 'Travail en équipe', 'Résolution de problèmes'],
];
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compétences - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg-primary: #0a0118; --bg-secondary: #1a0d2e; --bg-card: #241538; --bg-hover: #2d1b47; --text-primary: #ffffff; --text-secondary: #a0a0a0; --accent-purple: #9333ea; --accent-blue: #3b82f6; --accent-green: #10b981; --accent-yellow: #f59e0b; --border-color: rgba(255,255,255,0.1); }
        [data-theme="light"] { --bg-primary: #f1f5f9; --bg-secondary: #ffffff; --bg-card: #ffffff; --bg-hover: #f8fafc; --text-primary: #1e293b; --text-secondary: #64748b; --border-color: rgba(0,0,0,0.1); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); min-height: 100vh; display: flex; }
        .sidebar { width: 280px; background: var(--bg-secondary); border-right: 1px solid var(--border-color); height: 100vh; position: fixed; left: 0; top: 0; display: flex; flex-direction: column; z-index: 100; }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.75rem; }
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
        .user-info { flex: 1; min-width: 0; }
        .user-name { font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 0.75rem; color: var(--text-secondary); }
        .main-content { flex: 1; margin-left: 280px; padding: 2rem; min-height: 100vh; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-title { font-size: 1.75rem; font-weight: 700; }
        .page-subtitle { color: var(--text-secondary); margin-top: 0.25rem; }
        .header-actions { display: flex; gap: 0.75rem; align-items: center; }
        .btn { padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; border: none; font-size: 0.9rem; transition: all 0.2s; }
        .btn-primary { background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(147,51,234,0.3); }
        .theme-toggle { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; color: var(--text-primary); display: flex; align-items: center; justify-content: center; }
        
        .search-bar { display: flex; gap: 1rem; margin-bottom: 2rem; }
        .search-input { flex: 1; padding: 0.75rem 1rem 0.75rem 2.5rem; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 0.95rem; position: relative; }
        .search-wrapper { flex: 1; position: relative; }
        .search-wrapper i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-secondary); }
        .filter-btn { padding: 0.75rem 1.25rem; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); cursor: pointer; display: flex; align-items: center; gap: 0.5rem; }
        
        .competence-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
        .competence-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; transition: all 0.3s; }
        .competence-card:hover { transform: translateY(-3px); border-color: var(--accent-purple); }
        .competence-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .competence-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .competence-icon.purple { background: rgba(147,51,234,0.15); color: var(--accent-purple); }
        .competence-icon.blue { background: rgba(59,130,246,0.15); color: var(--accent-blue); }
        .competence-icon.green { background: rgba(16,185,129,0.15); color: var(--accent-green); }
        .competence-icon.yellow { background: rgba(245,158,11,0.15); color: var(--accent-yellow); }
        .competence-title { font-weight: 600; font-size: 1.1rem; }
        .competence-category { font-size: 0.8rem; color: var(--text-secondary); }
        .competence-level { margin: 1rem 0; }
        .level-bar { height: 8px; background: var(--border-color); border-radius: 4px; overflow: hidden; }
        .level-fill { height: 100%; background: linear-gradient(90deg, var(--accent-purple), var(--accent-blue)); border-radius: 4px; transition: width 0.3s; }
        .level-text { display: flex; justify-content: space-between; font-size: 0.8rem; margin-top: 0.5rem; }
        .level-label { color: var(--text-secondary); }
        .competence-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--border-color); }
        .competence-status { font-size: 0.8rem; padding: 0.25rem 0.75rem; border-radius: 20px; }
        .status-validated { background: rgba(16,185,129,0.15); color: var(--accent-green); }
        .status-pending { background: rgba(245,158,11,0.15); color: var(--accent-yellow); }
        .competence-actions { display: flex; gap: 0.5rem; }
        .action-btn { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); background: transparent; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .action-btn:hover { background: var(--bg-hover); color: var(--text-primary); }

        .section-title { font-size: 1.25rem; font-weight: 600; margin: 2rem 0 1rem; }
        
        /* Modal */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: var(--bg-card); border-radius: 20px; padding: 2rem; width: 90%; max-width: 500px; max-height: 80vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .modal-title { font-size: 1.25rem; font-weight: 600; }
        .modal-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-secondary); }
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; }
        .form-input, .form-select { width: 100%; padding: 0.75rem 1rem; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 0.95rem; }
        .form-select option { background: var(--bg-secondary); }
        .level-slider { width: 100%; margin-top: 0.5rem; }
        
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <?php include __DIR__ . '/_sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Mes Compétences</h1>
                <p class="page-subtitle">Gérez et déclarez vos compétences professionnelles</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="openModal()"><i class="fas fa-plus"></i> Ajouter</button>
                <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            </div>
        </div>
        
        <div class="search-bar">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" class="search-input" id="searchCompetence" placeholder="Rechercher une compétence..." style="padding-left: 2.5rem; width: 100%;">
            </div>
            <select class="filter-btn" id="filterCategory" style="cursor: pointer;">
                <option value="">Toutes catégories</option>
                <option value="developpement">Développement</option>
                <option value="base de donnees">Base de données</option>
                <option value="devops">DevOps</option>
                <option value="savoir-etre">Soft Skills</option>
            </select>
            <select class="filter-btn" id="filterStatus" style="cursor: pointer;">
                <option value="">Tous les statuts</option>
                <option value="validated">Validés</option>
                <option value="pending">En attente</option>
            </select>
        </div>
        
        <h2 class="section-title">Compétences Techniques</h2>
        <div class="competence-grid" id="technicalGrid">
            <div class="competence-card" data-name="php laravel" data-category="developpement" data-status="validated">
                <div class="competence-header">
                    <div class="competence-icon purple"><i class="fab fa-php"></i></div>
                    <div>
                        <div class="competence-title">PHP / Laravel</div>
                        <div class="competence-category">Développement Backend</div>
                    </div>
                </div>
                <div class="competence-level">
                    <div class="level-bar"><div class="level-fill" style="width: 80%;"></div></div>
                    <div class="level-text"><span class="level-label">Niveau</span><span>4/5 - Avancé</span></div>
                </div>
                <div class="competence-footer">
                    <span class="competence-status status-validated"><i class="fas fa-check"></i> Validé</span>
                    <div class="competence-actions">
                        <button class="action-btn"><i class="fas fa-edit"></i></button>
                        <button class="action-btn"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="competence-card" data-name="javascript react" data-category="developpement" data-status="validated">
                <div class="competence-header">
                    <div class="competence-icon blue"><i class="fab fa-js"></i></div>
                    <div>
                        <div class="competence-title">JavaScript / React</div>
                        <div class="competence-category">Développement Frontend</div>
                    </div>
                </div>
                <div class="competence-level">
                    <div class="level-bar"><div class="level-fill" style="width: 60%;"></div></div>
                    <div class="level-text"><span class="level-label">Niveau</span><span>3/5 - Intermédiaire</span></div>
                </div>
                <div class="competence-footer">
                    <span class="competence-status status-validated"><i class="fas fa-check"></i> Validé</span>
                    <div class="competence-actions">
                        <button class="action-btn"><i class="fas fa-edit"></i></button>
                        <button class="action-btn"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="competence-card" data-name="mysql postgresql" data-category="base de donnees" data-status="validated">
                <div class="competence-header">
                    <div class="competence-icon green"><i class="fas fa-database"></i></div>
                    <div>
                        <div class="competence-title">MySQL / PostgreSQL</div>
                        <div class="competence-category">Base de données</div>
                    </div>
                </div>
                <div class="competence-level">
                    <div class="level-bar"><div class="level-fill" style="width: 100%;"></div></div>
                    <div class="level-text"><span class="level-label">Niveau</span><span>5/5 - Expert</span></div>
                </div>
                <div class="competence-footer">
                    <span class="competence-status status-validated"><i class="fas fa-check"></i> Validé</span>
                    <div class="competence-actions">
                        <button class="action-btn"><i class="fas fa-edit"></i></button>
                        <button class="action-btn"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="competence-card" data-name="docker kubernetes" data-category="devops" data-status="pending">
                <div class="competence-header">
                    <div class="competence-icon yellow"><i class="fab fa-docker"></i></div>
                    <div>
                        <div class="competence-title">Docker / Kubernetes</div>
                        <div class="competence-category">DevOps</div>
                    </div>
                </div>
                <div class="competence-level">
                    <div class="level-bar"><div class="level-fill" style="width: 40%;"></div></div>
                    <div class="level-text"><span class="level-label">Niveau</span><span>2/5 - Débutant</span></div>
                </div>
                <div class="competence-footer">
                    <span class="competence-status status-pending"><i class="fas fa-clock"></i> En attente</span>
                    <div class="competence-actions">
                        <button class="action-btn"><i class="fas fa-edit"></i></button>
                        <button class="action-btn"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
        
        <h2 class="section-title">Soft Skills</h2>
        <div class="competence-grid" id="softSkillsGrid">
            <div class="competence-card" data-name="travail en equipe" data-category="savoir-etre" data-status="validated">
                <div class="competence-header">
                    <div class="competence-icon purple"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="competence-title">Travail en équipe</div>
                        <div class="competence-category">Savoir-être</div>
                    </div>
                </div>
                <div class="competence-level">
                    <div class="level-bar"><div class="level-fill" style="width: 80%;"></div></div>
                    <div class="level-text"><span class="level-label">Niveau</span><span>4/5 - Avancé</span></div>
                </div>
                <div class="competence-footer">
                    <span class="competence-status status-validated"><i class="fas fa-check"></i> Validé</span>
                    <div class="competence-actions">
                        <button class="action-btn"><i class="fas fa-edit"></i></button>
                        <button class="action-btn"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="competence-card" data-name="communication" data-category="savoir-etre" data-status="validated">
                <div class="competence-header">
                    <div class="competence-icon blue"><i class="fas fa-comments"></i></div>
                    <div>
                        <div class="competence-title">Communication</div>
                        <div class="competence-category">Savoir-être</div>
                    </div>
                </div>
                <div class="competence-level">
                    <div class="level-bar"><div class="level-fill" style="width: 60%;"></div></div>
                    <div class="level-text"><span class="level-label">Niveau</span><span>3/5 - Intermédiaire</span></div>
                </div>
                <div class="competence-footer">
                    <span class="competence-status status-validated"><i class="fas fa-check"></i> Validé</span>
                    <div class="competence-actions">
                        <button class="action-btn"><i class="fas fa-edit"></i></button>
                        <button class="action-btn"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Modal Ajouter Compétence -->
    <div class="modal" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Ajouter une compétence</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form>
                <div class="form-group">
                    <label class="form-label">Nom de la compétence</label>
                    <input type="text" class="form-input" placeholder="Ex: Python, Gestion de projet...">
                </div>
                <div class="form-group">
                    <label class="form-label">Catégorie</label>
                    <select class="form-select">
                        <option>Développement</option>
                        <option>Base de données</option>
                        <option>DevOps</option>
                        <option>Soft Skills</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select class="form-select">
                        <option value="savoir_faire">Savoir-faire</option>
                        <option value="savoir_etre">Savoir-être</option>
                        <option value="expertise">Expertise</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Niveau (1-5)</label>
                    <input type="range" class="level-slider" min="1" max="5" value="3">
                    <div class="level-text"><span>Débutant</span><span>Expert</span></div>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Ajouter la compétence</button>
            </form>
        </div>
    </div>
    
    <script>
        const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';
        h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';
        t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});
        
        function openModal() { document.getElementById('addModal').classList.add('active'); }
        function closeModal() { document.getElementById('addModal').classList.remove('active'); }
        
        // Recherche et filtrage des compétences
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchCompetence');
            const filterCategory = document.getElementById('filterCategory');
            const filterStatus = document.getElementById('filterStatus');
            
            if (searchInput) searchInput.addEventListener('keyup', filterCompetences);
            if (filterCategory) filterCategory.addEventListener('change', filterCompetences);
            if (filterStatus) filterStatus.addEventListener('change', filterCompetences);
        });
        
        function filterCompetences() {
            const searchTerm = document.getElementById('searchCompetence')?.value.toLowerCase().trim() || '';
            const categoryFilter = document.getElementById('filterCategory')?.value.toLowerCase() || '';
            const statusFilter = document.getElementById('filterStatus')?.value.toLowerCase() || '';
            
            // Obtenir toutes les cartes de compétences
            const allCards = document.querySelectorAll('.competence-card');
            
            allCards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const category = card.getAttribute('data-category') || '';
                const status = card.getAttribute('data-status') || '';
                
                const matchSearch = searchTerm === '' || name.includes(searchTerm);
                const matchCategory = categoryFilter === '' || category === categoryFilter;
                const matchStatus = statusFilter === '' || status === statusFilter;
                
                if (matchSearch && matchCategory && matchStatus) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Vérifier si des sections sont vides
            checkEmptySections();
        }
        
        function checkEmptySections() {
            const technicalGrid = document.getElementById('technicalGrid');
            const softSkillsGrid = document.getElementById('softSkillsGrid');
            
            if (technicalGrid) {
                const visibleTech = technicalGrid.querySelectorAll('.competence-card[style*="display: block"], .competence-card:not([style*="display"])');
                const actualVisible = Array.from(visibleTech).filter(c => c.style.display !== 'none');
                const techSection = technicalGrid.previousElementSibling;
                if (techSection && techSection.classList.contains('section-title')) {
                    techSection.style.display = actualVisible.length > 0 ? 'block' : 'none';
                }
            }
            
            if (softSkillsGrid) {
                const visibleSoft = softSkillsGrid.querySelectorAll('.competence-card[style*="display: block"], .competence-card:not([style*="display"])');
                const actualVisible = Array.from(visibleSoft).filter(c => c.style.display !== 'none');
                const softSection = softSkillsGrid.previousElementSibling;
                if (softSection && softSection.classList.contains('section-title')) {
                    softSection.style.display = actualVisible.length > 0 ? 'block' : 'none';
                }
            }
        }
    </script>
</body>
</html>
