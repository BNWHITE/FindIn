<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
$user = ['name' => $_SESSION['user_name'] ?? 'Utilisateur', 'email' => $_SESSION['user_email'] ?? '', 'role' => $_SESSION['user_role'] ?? 'employe'];
$currentPage = 'equipe';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Équipe - FindIN</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="/assets/css/dashboard.css" rel="stylesheet">
    <style>
        .member-stat.match-stat {
    display: none; /* Masqué par défaut */
}

.member-stat.match-stat.active {
    display: block; /* Affiché lors de la recherche */
}

.match-value {
    font-weight: 700;
    font-size: 1.25rem;
    transition: color 0.3s;
}

.selected-skills {
    min-height: 30px;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}


.skill-badge {
    padding: 6px 12px;
    background: var(--accent-primary);
    color: white;
    border-radius: 20px;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}


.skill-badge button {
    background: rgba(255, 255, 255, 0.3);
    border: none;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.75rem;
    line-height: 1;
}


.skill-badge button:hover {
    background: rgba(255, 255, 255, 0.5);
}

        .team-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .view-toggle {
            display: flex;
            background: var(--bg-tertiary);
            border-radius: 8px;
            padding: 4px;
        }
        .view-btn {
            padding: 8px 16px;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .view-btn.active {
            background: var(--accent-primary);
            color: white;
        }
        .team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    width: 100%;
}


.member-card {
    background: var(--bg-secondary);
    border-radius: 16px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s;
    border: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    min-width: 0; /* Important pour éviter l'overflow */
}

.member-name {
    font-size: 1.1rem;  /* Réduit de 1.25rem */
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.member-role {
    color: var(--accent-primary);
    font-weight: 500;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.member-department {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 1rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.member-stats {
    padding-top: 15px;
    margin-top: auto;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 0.9rem;
}



.skill-tag {
    padding: 3px 8px;
    background: var(--bg-tertiary);
    border-radius: 20px;
    font-size: 0.7rem;
    color: var(--text-secondary);
    white-space: nowrap;
}


.team-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    width: 100%;
}

.member-card {
    background: var(--bg-secondary);
    border-radius: 16px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s;
    border: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    min-width: 0;
    max-width: 100%; /* Important ! */
    height: 100%; /* Important pour garder la même hauteur */
}

        .member-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .member-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 0.75rem;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            font-weight: 600;
            position: relative;
        }
        .member-status {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 3px solid var(--bg-secondary);
        }
        .status-online { background: #10b981; }
        .status-away { background: #f59e0b; }
        .status-busy { background: #ef4444; }
        .status-offline { background: #6b7280; }
        .member-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        .member-role {
            color: var(--accent-primary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .member-department {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        .member-skills {
    font-size: 0.85rem;
    color: #a0a0a0;
    margin: 10px 0;
    line-height: 1.4;
    min-height: 70px;
    max-height: 70px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
.member-skills {
    font-size: 0.85rem;
    color: #a0a0a0;
    margin: 10px 0;
    line-height: 1.5;
    height: 60px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    white-space: normal;
    word-wrap: break-word;
}


        .skill-tag {
            padding: 4px 10px;
            background: var(--bg-tertiary);
            border-radius: 20px;
            font-size: 0.75rem;
            color: var(--text-secondary);
        }
        .member-stats {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
        .member-stat {
            text-align: center;
        }
        .member-stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .member-stat-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }
        .member-actions {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .action-btn:hover {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
        }
        .team-list {
            display: none;
        }
        .team-list.active {
            display: block;
        }
        .team-grid.active {
            display: grid;
        }
        .list-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 100px;
            padding: 1rem 1.5rem;
            background: var(--bg-tertiary);
            border-radius: 12px 12px 0 0;
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        .list-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 100px;
            padding: 1rem 1.5rem;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            align-items: center;
            transition: background 0.2s;
        }
        .list-row:hover {
            background: var(--bg-tertiary);
        }
        .list-row:last-child {
            border-radius: 0 0 12px 12px;
        }
        .list-member {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .list-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        .org-chart {
            display: none;
            padding: 2rem;
        }
        .org-chart.active {
            display: block;
        }
        .org-level {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            position: relative;
        }
        .org-card {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            min-width: 150px;
            border: 2px solid var(--border-color);
        }
        .org-card.manager {
            border-color: var(--accent-primary);
        }
        .department-section {
            margin-bottom: 2rem;
        }
        .department-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent-primary);
            display: inline-block;
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
                <h1 class="page-title">Mon Équipe</h1>
                <p class="page-subtitle">Découvrez les talents de votre organisation</p>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <button class="btn btn-secondary" onclick="toggleTheme()">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Inviter
                </button>
            </div>
        </div>

        <!-- Filtres et vue -->
        <div class="team-header">
            <div class="filters">
                <div class="search-box" style="min-width: 250px;">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher un collaborateur...">
                </div>
                <select id="departmentFilter" class="filter-select" style="padding: 10px 15px; border-radius: 8px; background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);" onchange="filterByDepartment()">
                    <option>Tous les départements</option>
                    <option>Développement</option>
                    <option>Design</option>
                    <option>Product</option>
                    <option>Infrastructure</option>
                    <option>Data</option>
                    <option>Mobile</option>
                </select>
            </div>
            <div class="skills-filter-container" style="margin-top: 1rem;">
    <div style="margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.875rem;">
        Filtrer par compétences :
    </div>
    <div id="selectedSkills" class="selected-skills" style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem;">
        <!-- Les compétences sélectionnées apparaîtront ici -->
    </div>
    <select id="skillsFilter" class="filter-select" style="padding: 10px 15px; border-radius: 8px; background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary); width: 250px;">
        <option value="">Ajouter une compétence...</option>
        <option value="React">React</option>
        <option value="Node.js">Node.js</option>
        <option value="TypeScript">TypeScript</option>
        <option value="Vue.js">Vue.js</option>
        <option value="Python">Python</option>
        <option value="Docker">Docker</option>
        <option value="Angular">Angular</option>
        <option value="Java">Java</option>
        <option value="PostgreSQL">PostgreSQL</option>
        <option value="PHP">PHP</option>
        <option value="Laravel">Laravel</option>
        <option value="MySQL">MySQL</option>
        <option value="CSS">CSS</option>
        <option value="Webpack">Webpack</option>
        <option value="C++">C++</option>
        <option value="Qt">Qt</option>
        <option value="Git">Git</option>
        <option value="JavaScript">JavaScript</option>
        <option value="HTML">HTML</option>
        <option value="REST">REST</option>
        <option value="GraphQL">GraphQL</option>
        <option value="Architecture">Architecture</option>
        <option value="Microservices">Microservices</option>
        <option value="AWS">AWS</option>
        <option value="WordPress">WordPress</option>
        <option value="jQuery">jQuery</option>
        <option value="MERN">MERN</option>
        <option value="MongoDB">MongoDB</option>
        <option value="Express">Express</option>
        <option value="Ruby">Ruby</option>
        <option value="Rails">Rails</option>
        <option value="Redis">Redis</option>
        <option value="Jenkins">Jenkins</option>
        <option value="Terraform">Terraform</option>
        <option value="Ansible">Ansible</option>
        <option value="Figma">Figma</option>
        <option value="UI/UX">UI/UX</option>
        <option value="Prototyping">Prototyping</option>
        <option value="Sketch">Sketch</option>
        <option value="Adobe XD">Adobe XD</option>
        <option value="Illustrator">Illustrator</option>
        <option value="Design System">Design System</option>
        <option value="User Research">User Research</option>
        <option value="Photoshop">Photoshop</option>
        <option value="InDesign">InDesign</option>
        <option value="Branding">Branding</option>
        <option value="After Effects">After Effects</option>
        <option value="Premiere">Premiere</option>
        <option value="Animation">Animation</option>
        <option value="Design Leadership">Design Leadership</option>
        <option value="Strategy">Strategy</option>
        <option value="User Testing">User Testing</option>
        <option value="Analytics">Analytics</option>
        <option value="Surveys">Surveys</option>
        <option value="Agile">Agile</option>
        <option value="Scrum">Scrum</option>
        <option value="Roadmap">Roadmap</option>
        <option value="Backlog">Backlog</option>
        <option value="Jira">Jira</option>
        <option value="Sprint Planning">Sprint Planning</option>
        <option value="Product Strategy">Product Strategy</option>
        <option value="UX">UX</option>
        <option value="Wireframing">Wireframing</option>
        <option value="SQL">SQL</option>
        <option value="A/B Testing">A/B Testing</option>
        <option value="Leadership">Leadership</option>
        <option value="Vision">Vision</option>
        <option value="Kubernetes">Kubernetes</option>
        <option value="Azure">Azure</option>
        <option value="GCP">GCP</option>
        <option value="Monitoring">Monitoring</option>
        <option value="Prometheus">Prometheus</option>
        <option value="Grafana">Grafana</option>
    </select>
</div>

            <div class="view-toggle">
                <button class="view-btn active" onclick="showView('grid')">
                    <i class="fas fa-th-large"></i>
                </button>
                <button class="view-btn" onclick="showView('list')">
                    <i class="fas fa-list"></i>
                </button>
                <button class="view-btn" onclick="showView('org')">
                    <i class="fas fa-sitemap"></i>
                </button>
            </div>
        </div>

 <!-- Stats équipe -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">71</div>  <!-- Changé de 24 à 100 -->
            <div class="stat-label">Membres</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
            <i class="fas fa-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">62</div>  <!-- Changé de 18 à 67 -->
            <div class="stat-label">En ligne</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">7</div>  <!-- Changé de 5 à 7 -->
            <div class="stat-label">Départements</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <i class="fas fa-lightbulb"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">320</div>  <!-- Changé de 156 à 320 -->
            <div class="stat-label">Compétences uniques</div>
        </div>
    </div>
</div>


<!-- Vue Grille -->
<div class="team-grid active" id="gridView">
    <!-- DÉVELOPPEMENT -->
    <div class="member-card" data-department="Développement" data-skills="React,Node.js,TypeScript">
        <div class="member-avatar">SL<span class="member-status status-online"></span></div>
        <div class="member-name">Sophie Laurent</div>
        <div class="member-role">Lead Developer</div>
        <div class="member-department"><i class="fas fa-code"></i> Développement</div>
        <div class="member-skills"><span class="skill-tag">React</span><span class="skill-tag">Node.js</span><span class="skill-tag">TypeScript</span></div>
        <div class="member-stats">
            <div class="member-stat">
                <div class="member-stat-value">12</div>
                <div class="member-stat-label">Projets</div>
            </div>
            <div class="member-stat match-stat">
                <div class="member-stat-value match-value">0%</div>
                <div class="member-stat-label">Match</div>
            </div>
        </div>
        <div class="member-actions">
            <button class="action-btn"><i class="fas fa-comment"></i></button>
            <button class="action-btn"><i class="fas fa-user"></i></button>
            <button class="action-btn"><i class="fas fa-envelope"></i></button>
        </div>
    </div>

    <div class="member-card" data-department="Développement" data-skills="Vue.js,Python,Docker">
        <div class="member-avatar">PD<span class="member-status status-online"></span></div>
        <div class="member-name">Pierre Dubois</div>
        <div class="member-role">Senior Developer</div>
        <div class="member-department"><i class="fas fa-code"></i> Développement</div>
        <div class="member-skills"><span class="skill-tag">Vue.js</span><span class="skill-tag">Python</span><span class="skill-tag">Docker</span></div>
        <div class="member-stats">
            <div class="member-stat">
                <div class="member-stat-value">15</div>
                <div class="member-stat-label">Projets</div>
            </div>
            <div class="member-stat match-stat">
                <div class="member-stat-value match-value">0%</div>
                <div class="member-stat-label">Match</div>
            </div>
        </div>
        <div class="member-actions">
            <button class="action-btn"><i class="fas fa-comment"></i></button>
            <button class="action-btn"><i class="fas fa-user"></i></button>
            <button class="action-btn"><i class="fas fa-envelope"></i></button>
        </div>
    </div>

    <div class="member-card" data-department="Développement" data-skills="Angular,Java,PostgreSQL">
        <div class="member-avatar">CM<span class="member-status status-away"></span></div>
        <div class="member-name">Claire Moreau</div>
        <div class="member-role">Full Stack Developer</div>
        <div class="member-department"><i class="fas fa-code"></i> Développement</div>
        <div class="member-skills"><span class="skill-tag">Angular</span><span class="skill-tag">Java</span><span class="skill-tag">PostgreSQL</span></div>
        <div class="member-stats">
            <div class="member-stat">
                <div class="member-stat-value">9</div>
                <div class="member-stat-label">Projets</div>
            </div>
            <div class="member-stat match-stat">
                <div class="member-stat-value match-value">0%</div>
                <div class="member-stat-label">Match</div>
            </div>
        </div>
        <div class="member-actions">
            <button class="action-btn"><i class="fas fa-comment"></i></button>
            <button class="action-btn"><i class="fas fa-user"></i></button>
            <button class="action-btn"><i class="fas fa-envelope"></i></button>
        </div>
    </div>

    <div class="member-card" data-department="Développement" data-skills="PHP,Laravel,MySQL">
        <div class="member-avatar">AL<span class="member-status status-online"></span></div>
        <div class="member-name">Antoine Lefebvre</div>
        <div class="member-role">Backend Developer</div>
        <div class="member-department"><i class="fas fa-code"></i> Développement</div>
        <div class="member-skills"><span class="skill-tag">PHP</span><span class="skill-tag">Laravel</span><span class="skill-tag">MySQL</span></div>
        <div class="member-stats">
            <div class="member-stat">
                <div class="member-stat-value">11</div>
                <div class="member-stat-label">Projets</div>
            </div>
            <div class="member-stat match-stat">
                <div class="member-stat-value match-value">0%</div>
                <div class="member-stat-label">Match</div>
            </div>
        </div>
        <div class="member-actions">
            <button class="action-btn"><i class="fas fa-comment"></i></button>
            <button class="action-btn"><i class="fas fa-user"></i></button>
            <button class="action-btn"><i class="fas fa-envelope"></i></button>
        </div>
    </div>

    <div class="member-card" data-department="Développement" data-skills="React,CSS,Webpack">
        <div class="member-avatar">JR<span class="member-status status-online"></span></div>
        <div class="member-name">Julien Roux</div>
        <div class="member-role">Frontend Developer</div>
        <div class="member-department"><i class="fas fa-code"></i> Développement</div>
        <div class="member-skills"><span class="skill-tag">React</span><span class="skill-tag">CSS</span><span class="skill-tag">Webpack</span></div>
        <div class="member-stats">
            <div class="member-stat">
                <div class="member-stat-value">8</div>
                <div class="member-stat-label">Projets</div>
            </div>
            <div class="member-stat match-stat">
                <div class="member-stat-value match-value">0%</div>
                <div class="member-stat-label">Match</div>
            </div>
        </div>
        <div class="member-actions">
            <button class="action-btn"><i class="fas fa-comment"></i></button>
            <button class="action-btn"><i class="fas fa-user"></i></button>
            <button class="action-btn"><i class="fas fa-envelope"></i></button>
        </div>
    </div>

    <div class="member-card" data-department="Développement" data-skills="C++,Qt,Git">
        <div class="member-avatar">MB<span class="member-status status-busy"></span></div>
        <div class="member-name">Marie Bernard</div>
        <div class="member-role">Software Engineer</div>
        <div class="member-department"><i class="fas fa-code"></i> Développement</div>
        <div class="member-skills"><span class="skill-tag">C++</span><span class="skill-tag">Qt</span><span class="skill-tag">Git</span></div>
        <div class="member-stats">
            <div class="member-stat">
                <div class="member-stat-value">13</div>
                <div class="member-stat-label">Projets</div>
            </div>
            <div class="member-stat match-stat">
                <div class="member-stat-value match-value">0%</div>
                <div class="member-stat-label">Match</div>
            </div>
        </div>
        <div class="member-actions">
            <button class="action-btn"><i class="fas fa-comment"></i></button>
            <button class="action-btn"><i class="fas fa-user"></i></button>
            <button class="action-btn"><i class="fas fa-envelope"></i></button>
        </div>
    </div>

    <div class="member-card" data-department="Développement" data-skills="JavaScript,HTML,CSS">
        <div class="member-avatar">LG<span class="member-status status-online"></span></div>
        <div class="member-name">Lucas Garcia</div>
        <div class="member-role">Junior Developer</div>
        <div class="member-department"><i class="fas fa-code"></i> Développement</div>
        <div class="member-skills"><span class="skill-tag">JavaScript</span><span class="skill-tag">HTML</span><span class="skill-tag">CSS</span></div>
        <div class="member-stats">
            <div class="member-stat">
                <div class="member-stat-value">4</div>
                <div class="member-stat-label">Projets</div>
            </div>
            <div class="member-stat match-stat">
                <div class="member-stat-value match-value">0%</div>
                <div class="member-stat-label">Match</div>
            </div>
        </div>
        <div class="member-actions">
            <button class="action-btn"><i class="fas fa-comment"></i></button>
            <button class="action-btn"><i class="fas fa-user"></i></button>
            <button class="action-btn"><i class="fas fa-envelope"></i></button>
        </div>
    </div>

    <div class="member-card" data-department="Développement" data-skills="REST,GraphQL,Node.js">
        <div class="member-avatar">ED<span class="member-status status-online"></span></div>
        <div class="member-name">Emma Durand</div>
        <div class="member-role">API Developer</div>
        <div class="member-department"><i class="fas fa-code"></i> Développement</div>
        <div class="member-skills"><span class="skill-tag">REST</span><span class="skill-tag">GraphQL</span><span class="skill-tag">Node.js</span></div>
        <div class="member-stats">
            <div class="member-stat">
                <div class="member-stat-value">10</div>
                <div class="member-stat-label">Projets</div>
            </div>
            <div class="member-stat match-stat">
                <div class="member-stat-value match-value">0%</div>
                <div class="member-stat-label">Match</div>
            </div>
        </div>
        <div class="member-actions">
            <button class="action-btn"><i class="fas fa-comment"></i></button>
            <button class="action-btn"><i class="fas fa-user"></i></button>
            <button class="action-btn"><i class="fas fa-envelope"></i></button>
        </div>
    </div>

    <!-- Utilisateurs 13-32 -->
<div class="member-card" data-department="Développement" data-skills="Ruby,Rails,Redis">
    <div class="member-avatar">LM<span class="member-status status-offline"></span></div>
    <div class="member-name">Laura Martinez</div>
    <div class="member-role">Backend Developer</div>
    <div class="member-department"><i class="fas fa-code"></i> Développement</div>
    <div class="member-skills"><span class="skill-tag">Ruby</span><span class="skill-tag">Rails</span><span class="skill-tag">Redis</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">9</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Infrastructure" data-skills="Kubernetes,Docker,Terraform">
    <div class="member-avatar">HD<span class="member-status status-online"></span></div>
    <div class="member-name">Hugo Dubois</div>
    <div class="member-role">DevOps Engineer</div>
    <div class="member-department"><i class="fas fa-server"></i> Infrastructure</div>
    <div class="member-skills"><span class="skill-tag">Kubernetes</span><span class="skill-tag">Docker</span><span class="skill-tag">Terraform</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">11</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Design" data-skills="Figma,UI/UX,Prototyping">
    <div class="member-avatar">MD<span class="member-status status-online"></span></div>
    <div class="member-name">Marc Dubois</div>
    <div class="member-role">UX Designer</div>
    <div class="member-department"><i class="fas fa-paint-brush"></i> Design</div>
    <div class="member-skills"><span class="skill-tag">Figma</span><span class="skill-tag">UI/UX</span><span class="skill-tag">Prototyping</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">8</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Design" data-skills="Sketch,Adobe XD,Illustrator">
    <div class="member-avatar">SG<span class="member-status status-online"></span></div>
    <div class="member-name">Sarah Girard</div>
    <div class="member-role">UI Designer</div>
    <div class="member-department"><i class="fas fa-paint-brush"></i> Design</div>
    <div class="member-skills"><span class="skill-tag">Sketch</span><span class="skill-tag">Adobe XD</span><span class="skill-tag">Illustrator</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">11</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Product" data-skills="Agile,Scrum,Roadmap">
    <div class="member-avatar">JP<span class="member-status status-away"></span></div>
    <div class="member-name">Julie Petit</div>
    <div class="member-role">Product Manager</div>
    <div class="member-department"><i class="fas fa-briefcase"></i> Product</div>
    <div class="member-skills"><span class="skill-tag">Agile</span><span class="skill-tag">Scrum</span><span class="skill-tag">Roadmap</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">15</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Data" data-skills="Python,SQL,Tableau">
    <div class="member-avatar">AM<span class="member-status status-online"></span></div>
    <div class="member-name">Antoine Martin</div>
    <div class="member-role">Data Analyst</div>
    <div class="member-department"><i class="fas fa-chart-bar"></i> Data</div>
    <div class="member-skills"><span class="skill-tag">Python</span><span class="skill-tag">SQL</span><span class="skill-tag">Tableau</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">10</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Mobile" data-skills="Swift,iOS,Xcode">
    <div class="member-avatar">ES<span class="member-status status-online"></span></div>
    <div class="member-name">Emma Simon</div>
    <div class="member-role">iOS Developer</div>
    <div class="member-department"><i class="fas fa-mobile-alt"></i> Mobile</div>
    <div class="member-skills"><span class="skill-tag">Swift</span><span class="skill-tag">iOS</span><span class="skill-tag">Xcode</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">7</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Mobile" data-skills="Kotlin,Android,Flutter">
    <div class="member-avatar">LB<span class="member-status status-online"></span></div>
    <div class="member-name">Lucas Bernard</div>
    <div class="member-role">Android Developer</div>
    <div class="member-department"><i class="fas fa-mobile-alt"></i> Mobile</div>
    <div class="member-skills"><span class="skill-tag">Kotlin</span><span class="skill-tag">Android</span><span class="skill-tag">Flutter</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">9</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Développement" data-skills="Go,Microservices,gRPC">
    <div class="member-avatar">TR<span class="member-status status-busy"></span></div>
    <div class="member-name">Thomas Robert</div>
    <div class="member-role">Backend Engineer</div>
    <div class="member-department"><i class="fas fa-code"></i> Développement</div>
    <div class="member-skills"><span class="skill-tag">Go</span><span class="skill-tag">Microservices</span><span class="skill-tag">gRPC</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">14</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Infrastructure" data-skills="AWS,Azure,GCP">
    <div class="member-avatar">ML<span class="member-status status-online"></span></div>
    <div class="member-name">Marie Laurent</div>
    <div class="member-role">Cloud Architect</div>
    <div class="member-department"><i class="fas fa-server"></i> Infrastructure</div>
    <div class="member-skills"><span class="skill-tag">AWS</span><span class="skill-tag">Azure</span><span class="skill-tag">GCP</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">16</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Data" data-skills="Python,TensorFlow,Keras">
    <div class="member-avatar">CD<span class="member-status status-online"></span></div>
    <div class="member-name">Chloé Durand</div>
    <div class="member-role">ML Engineer</div>
    <div class="member-department"><i class="fas fa-chart-bar"></i> Data</div>
    <div class="member-skills"><span class="skill-tag">Python</span><span class="skill-tag">TensorFlow</span><span class="skill-tag">Keras</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">12</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>



    <div class="member-card" data-department="Développement" data-skills="Architecture,Microservices,AWS">
        <div class="member-avatar">NF<span class="member-status status-away"></span></div>
        <div class="member-name">Nicolas Fournier</div>
        <div class="member-role">Tech Lead</div>
        <div class="member-department"><i class="fas fa-code"></i> Développement</div>
        <div class="member-skills"><span class="skill-tag">Architecture</span><span class="skill-tag">Microservices</span><span class="skill-tag">AWS</span></div>
        <div class="member-stats">
            <div class="member-stat">
                <div class="member-stat-value">18</div>
                <div class="member-stat-label">Projets</div>
            </div>
            <div class="member-stat match-stat">
                <div class="member-stat-value match-value">0%</div>
                <div class="member-stat-label">Match</div>
            </div>
        </div>
        <div class="member-actions">
            <button class="action-btn"><i class="fas fa-comment"></i></button>
            <button class="action-btn"><i class="fas fa-user"></i></button>
            <button class="action-btn"><i class="fas fa-envelope"></i></button>
        </div>
    </div>

    <!-- GROUPE 2 : Utilisateurs 33-52 -->
<div class="member-card" data-department="Design" data-skills="Design System,Figma,User Research">
    <div class="member-avatar">AR<span class="member-status status-away"></span></div>
    <div class="member-name">Alexandre Robert</div>
    <div class="member-role">Product Designer</div>
    <div class="member-department"><i class="fas fa-paint-brush"></i> Design</div>
    <div class="member-skills"><span class="skill-tag">Design System</span><span class="skill-tag">Figma</span><span class="skill-tag">User Research</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">13</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Product" data-skills="Backlog,Jira,Sprint Planning">
    <div class="member-avatar">FR<span class="member-status status-online"></span></div>
    <div class="member-name">François Roy</div>
    <div class="member-role">Product Owner</div>
    <div class="member-department"><i class="fas fa-briefcase"></i> Product</div>
    <div class="member-skills"><span class="skill-tag">Backlog</span><span class="skill-tag">Jira</span><span class="skill-tag">Sprint Planning</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">12</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Développement" data-skills="Rust,WebAssembly,Systems">
    <div class="member-avatar">VD<span class="member-status status-online"></span></div>
    <div class="member-name">Valérie Dupont</div>
    <div class="member-role">Systems Developer</div>
    <div class="member-department"><i class="fas fa-code"></i> Développement</div>
    <div class="member-skills"><span class="skill-tag">Rust</span><span class="skill-tag">WebAssembly</span><span class="skill-tag">Systems</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">10</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Infrastructure" data-skills="Prometheus,Grafana,Monitoring">
    <div class="member-avatar">JM<span class="member-status status-online"></span></div>
    <div class="member-name">Julien Moreau</div>
    <div class="member-role">SRE Engineer</div>
    <div class="member-department"><i class="fas fa-server"></i> Infrastructure</div>
    <div class="member-skills"><span class="skill-tag">Prometheus</span><span class="skill-tag">Grafana</span><span class="skill-tag">Monitoring</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">13</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Design" data-skills="Photoshop,InDesign,Branding">
    <div class="member-avatar">LT<span class="member-status status-online"></span></div>
    <div class="member-name">Léa Thomas</div>
    <div class="member-role">Graphic Designer</div>
    <div class="member-department"><i class="fas fa-paint-brush"></i> Design</div>
    <div class="member-skills"><span class="skill-tag">Photoshop</span><span class="skill-tag">InDesign</span><span class="skill-tag">Branding</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">16</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Data" data-skills="R,Statistics,DataViz">
    <div class="member-avatar">PG<span class="member-status status-busy"></span></div>
    <div class="member-name">Paul Girard</div>
    <div class="member-role">Data Scientist</div>
    <div class="member-department"><i class="fas fa-chart-bar"></i> Data</div>
    <div class="member-skills"><span class="skill-tag">R</span><span class="skill-tag">Statistics</span><span class="skill-tag">DataViz</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">11</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Mobile" data-skills="React Native,JavaScript,Redux">
    <div class="member-avatar">IN<span class="member-status status-online"></span></div>
    <div class="member-name">Isabelle Noir</div>
    <div class="member-role">Mobile Developer</div>
    <div class="member-department"><i class="fas fa-mobile-alt"></i> Mobile</div>
    <div class="member-skills"><span class="skill-tag">React Native</span><span class="skill-tag">JavaScript</span><span class="skill-tag">Redux</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">8</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Développement" data-skills="Scala,Spark,Kafka">
    <div class="member-avatar">AB<span class="member-status status-online"></span></div>
    <div class="member-name">Arthur Blanc</div>
    <div class="member-role">Data Engineer</div>
    <div class="member-department"><i class="fas fa-code"></i> Développement</div>
    <div class="member-skills"><span class="skill-tag">Scala</span><span class="skill-tag">Spark</span><span class="skill-tag">Kafka</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">14</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Product" data-skills="Product Strategy,UX,Wireframing">
    <div class="member-avatar">AF<span class="member-status status-online"></span></div>
    <div class="member-name">Amélie Fontaine</div>
    <div class="member-role">Product Designer</div>
    <div class="member-department"><i class="fas fa-briefcase"></i> Product</div>
    <div class="member-skills"><span class="skill-tag">Product Strategy</span><span class="skill-tag">UX</span><span class="skill-tag">Wireframing</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">9</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Infrastructure" data-skills="Ansible,Chef,Puppet">
    <div class="member-avatar">RV<span class="member-status status-away"></span></div>
    <div class="member-name">Romain Vidal</div>
    <div class="member-role">Infrastructure Engineer</div>
    <div class="member-department"><i class="fas fa-server"></i> Infrastructure</div>
    <div class="member-skills"><span class="skill-tag">Ansible</span><span class="skill-tag">Chef</span><span class="skill-tag">Puppet</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">12</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Design" data-skills="After Effects,Premiere,Animation">
    <div class="member-avatar">MP<span class="member-status status-online"></span></div>
    <div class="member-name">Maxime Petit</div>
    <div class="member-role">Motion Designer</div>
    <div class="member-department"><i class="fas fa-paint-brush"></i> Design</div>
    <div class="member-skills"><span class="skill-tag">After Effects</span><span class="skill-tag">Premiere</span><span class="skill-tag">Animation</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">10</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        
    <div class="member-card" data-department="Développement" data-skills="C#,.NET,Azure">
    <div class="member-avatar">SB<span class="member-status status-online"></span></div>
    <div class="member-name">Sophie Blanc</div>
    <div class="member-role">.NET Developer</div>
    <div class="member-department"><i class="fas fa-code"></i> Développement</div>
    <div class="member-skills"><span class="skill-tag">C#</span><span class="skill-tag">.NET</span><span class="skill-tag">Azure</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">13</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Data" data-skills="BigQuery,SQL,ETL">
    <div class="member-avatar">ND<span class="member-status status-online"></span></div>
    <div class="member-name">Nathan Dumas</div>
    <div class="member-role">Data Engineer</div>
    <div class="member-department"><i class="fas fa-chart-bar"></i> Data</div>
    <div class="member-skills"><span class="skill-tag">BigQuery</span><span class="skill-tag">SQL</span><span class="skill-tag">ETL</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">11</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Design" data-skills="Design Leadership,Figma,Strategy">
    <div class="member-avatar">CB<span class="member-status status-busy"></span></div>
    <div class="member-name">Charlotte Blanc</div>
    <div class="member-role">Lead Designer</div>
    <div class="member-department"><i class="fas fa-paint-brush"></i> Design</div>
    <div class="member-skills"><span class="skill-tag">Design Leadership</span><span class="skill-tag">Figma</span><span class="skill-tag">Strategy</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">19</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Product" data-skills="SQL,Analytics,A/B Testing">
    <div class="member-avatar">RD<span class="member-status status-away"></span></div>
    <div class="member-name">Raphaël Dumas</div>
    <div class="member-role">Product Analyst</div>
    <div class="member-department"><i class="fas fa-briefcase"></i> Product</div>
    <div class="member-skills"><span class="skill-tag">SQL</span><span class="skill-tag">Analytics</span><span class="skill-tag">A/B Testing</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">11</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Mobile" data-skills="Flutter,Dart,Firebase">
    <div class="member-avatar">OL<span class="member-status status-online"></span></div>
    <div class="member-name">Olivier Leroy</div>
    <div class="member-role">Flutter Developer</div>
    <div class="member-department"><i class="fas fa-mobile-alt"></i> Mobile</div>
    <div class="member-skills"><span class="skill-tag">Flutter</span><span class="skill-tag">Dart</span><span class="skill-tag">Firebase</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">10</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Infrastructure" data-skills="Linux,Bash,Networking">
    <div class="member-avatar">GF<span class="member-status status-online"></span></div>
    <div class="member-name">Gabriel Fournier</div>
    <div class="member-role">Systems Administrator</div>
    <div class="member-department"><i class="fas fa-server"></i> Infrastructure</div>
    <div class="member-skills"><span class="skill-tag">Linux</span><span class="skill-tag">Bash</span><span class="skill-tag">Networking</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">15</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Développement" data-skills="Elixir,Phoenix,Erlang">
    <div class="member-avatar">MM<span class="member-status status-away"></span></div>
    <div class="member-name">Manon Martin</div>
    <div class="member-role">Backend Developer</div>
    <div class="member-department"><i class="fas fa-code"></i> Développement</div>
    <div class="member-skills"><span class="skill-tag">Elixir</span><span class="skill-tag">Phoenix</span><span class="skill-tag">Erlang</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">8</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Design" data-skills="User Testing,Analytics,Surveys">
    <div class="member-avatar">VM<span class="member-status status-online"></span></div>
    <div class="member-name">Vincent Mercier</div>
    <div class="member-role">UX Researcher</div>
    <div class="member-department"><i class="fas fa-paint-brush"></i> Design</div>
    <div class="member-skills"><span class="skill-tag">User Testing</span><span class="skill-tag">Analytics</span><span class="skill-tag">Surveys</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">7</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Data" data-skills="Airflow,Spark,Hadoop">
    <div class="member-avatar">LC<span class="member-status status-online"></span></div>
    <div class="member-name">Louise Cohen</div>
    <div class="member-role">Data Platform Engineer</div>
    <div class="member-department"><i class="fas fa-chart-bar"></i> Data</div>
    <div class="member-skills"><span class="skill-tag">Airflow</span><span class="skill-tag">Spark</span><span class="skill-tag">Hadoop</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">12</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

<div class="member-card" data-department="Développement" data-skills="Django,Python,PostgreSQL">
    <div class="member-avatar">AV<span class="member-status status-online"></span></div>
    <div class="member-name">Alexandre Vidal</div>
    <div class="member-role">Python Developer</div>
    <div class="member-department"><i class="fas fa-code"></i> Développement</div>
    <div class="member-skills"><span class="skill-tag">Django</span><span class="skill-tag">Python</span><span class="skill-tag">PostgreSQL</span></div>
    <div class="member-stats">
        <div class="member-stat">
            <div class="member-stat-value">14</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

    <div class="member-card" data-department="Développement" data-skills="WordPress,PHP,jQuery">
        <div class="member-avatar">CL<span class="member-status status-online"></span></div>
        <div class="member-name">Camille Leroux</div>
        <div class="member-role">Web Developer</div>
        <div class="member-department"><i class="fas fa-code"></i> Développement</div>
        <div class="member-skills"><span class="skill-tag">WordPress</span><span class="skill-tag">PHP</span><span class="skill-tag">jQuery</span></div>
        <div class="member-stats">
            <div class="member-stat">
                <div class="member-stat-value">7</div>
                <div class="member-stat-label">Projets</div>
            </div>
            <div class="member-stat match-stat">
                <div class="member-stat-value match-value">0%</div>
                <div class="member-stat-label">Match</div>
            </div>
        </div>
        <div class="member-actions">
            <button class="action-btn"><i class="fas fa-comment"></i></button>
            <button class="action-btn"><i class="fas fa-user"></i></button>
            <button class="action-btn"><i class="fas fa-envelope"></i></button>
        </div>
    </div>

    <div class="member-card" data-department="Développement" data-skills="MERN,MongoDB,Express">
        <div class="member-avatar">TB<span class="member-status status-online"></span></div>
        <div class="member-name">Thomas Bonnet</div>
        <div class="member-role">Full Stack Developer</div>
        <div class="member-department"><i class="fas fa-code"></i> Développement</div>
        <div class="member-skills"><span class="skill-tag">MERN</span><span class="skill-tag">MongoDB</span><span class="skill-tag">Express</span></div>
        <div class="member-stats">
                    <div class="member-stat">
            <div class="member-stat-value">14</div>
            <div class="member-stat-label">Projets</div>
        </div>
        <div class="member-stat match-stat">
            <div class="member-stat-value match-value">0%</div>
            <div class="member-stat-label">Match</div>
        </div>
    </div>
    <div class="member-actions">
        <button class="action-btn"><i class="fas fa-comment"></i></button>
        <button class="action-btn"><i class="fas fa-user"></i></button>
        <button class="action-btn"><i class="fas fa-envelope"></i></button>
    </div>
</div>

        

                    </div> <!-- fin de team-grid -->
    
    </main> <!-- fin de main-content -->

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
        
        function toggleMobileMenu() {
            document.querySelector('.sidebar').classList.toggle('open');
        }
        
        function showView(view) {
            document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
            event.target.closest('.view-btn').classList.add('active');
            
            document.getElementById('gridView').classList.remove('active');
            document.getElementById('listView').classList.remove('active');
            document.getElementById('orgView').classList.remove('active');
            
            if (view === 'grid') {
                document.getElementById('gridView').classList.add('active');
            } else if (view === 'list') {
                document.getElementById('listView').classList.add('active');
            } else if (view === 'org') {
                document.getElementById('orgView').classList.add('active');
            }
        }

        let selectedSkills = [];
        let currentDepartment = 'Tous les départements';

        document.addEventListener('DOMContentLoaded', function() {
            // Gestionnaire pour le sélecteur de compétences
            document.getElementById('skillsFilter').addEventListener('change', function() {
                const skill = this.value;
                if (skill && !selectedSkills.includes(skill)) {
                    selectedSkills.push(skill);
                    updateSelectedSkillsDisplay();
                    applyFilters();
                }
                this.value = '';
            });
            
            applyFilters();
        });

        function updateSelectedSkillsDisplay() {
            const container = document.getElementById('selectedSkills');
            container.innerHTML = '';
            
            selectedSkills.forEach(skill => {
                const badge = document.createElement('div');
                badge.className = 'skill-badge';
                badge.innerHTML = `${skill}<button type="button" onclick="removeSkill('${skill}')">×</button>`;
                container.appendChild(badge);
            });
        }

        function removeSkill(skill) {
            selectedSkills = selectedSkills.filter(s => s !== skill);
            updateSelectedSkillsDisplay();
            applyFilters();
        }

        function getCardSkills(card) {
            const skillTags = card.querySelectorAll('.skill-tag');
            return Array.from(skillTags).map(tag => tag.textContent.trim());
        }
        
        function calculateMatch(cardSkills, searchSkills) {
            if (searchSkills.length === 0) return 0;
            let matchCount = 0;
            searchSkills.forEach(searchSkill => {
                if (cardSkills.includes(searchSkill)) {
                    matchCount++;
                }
            });
            return Math.round((matchCount / searchSkills.length) * 100);
        }

        function getMatchColor(percentage) {
            if (percentage >= 80) return '#10b981';
            else if (percentage >= 60) return '#f59e0b';
            else if (percentage >= 40) return '#ef4444';
            else return '#dc2626';
        }

        function applyFilters() {
            const gridContainer = document.getElementById('gridView');
            if (!gridContainer) return;
            
            const memberCards = Array.from(document.querySelectorAll('.member-card'));
            const isSearchActive = selectedSkills.length > 0;
            
            const cardsWithMatch = memberCards.map(card => {
                const cardDepartment = card.getAttribute('data-department');
                const cardSkills = getCardSkills(card);
                
                let departmentMatch = (currentDepartment === 'Tous les départements' || 
                                      cardDepartment === currentDepartment);
                
                let matchPercentage = 0;
                let skillsMatch = true;
                
                if (isSearchActive) {
                    matchPercentage = calculateMatch(cardSkills, selectedSkills);
                    skillsMatch = matchPercentage > 0;
                }
                
                return {
                    card: card,
                    matchPercentage: matchPercentage,
                    departmentMatch: departmentMatch,
                    skillsMatch: skillsMatch,
                    visible: departmentMatch && (skillsMatch || !isSearchActive)
                };
            });
            
            if (isSearchActive) {
                cardsWithMatch.sort((a, b) => b.matchPercentage - a.matchPercentage);
            }
            
            cardsWithMatch.forEach(item => {
                const card = item.card;
                
                if (item.visible) {
                    card.style.display = '';
                    
                    const matchStatDiv = card.querySelector('.match-stat');
                    const matchValueDiv = card.querySelector('.match-value');
                    
                    if (isSearchActive && matchStatDiv && matchValueDiv) {
                        matchStatDiv.classList.add('active');
                        matchValueDiv.textContent = item.matchPercentage + '%';
                        matchValueDiv.style.color = getMatchColor(item.matchPercentage);
                    } else if (matchStatDiv) {
                        matchStatDiv.classList.remove('active');
                    }
                    
                    gridContainer.appendChild(card);
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function filterByDepartment() {
            const select = document.getElementById('departmentFilter');
            currentDepartment = select.value;
            applyFilters();
        }

        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>

</body>
</html>
