<?php
// Sidebar component - inclure dans chaque page dashboard
$currentPage = $currentPage ?? 'dashboard';
$userName = $userName ?? $_SESSION['user_name'] ?? 'Utilisateur';
$userRole = $userRole ?? 'collaborateur';
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="/assets/images/logo.png" alt="FindIN">
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Principal</div>
            <a href="/dashboard" class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>"><i class="fas fa-home"></i> Dashboard</a>
            <a href="/dashboard/competences" class="nav-item <?= $currentPage === 'competences' ? 'active' : '' ?>"><i class="fas fa-brain"></i> Compétences</a>
            <a href="/dashboard/certifications" class="nav-item <?= $currentPage === 'certifications' ? 'active' : '' ?>"><i class="fas fa-certificate"></i> Certifications</a>
            <a href="/dashboard/mon-espace" class="nav-item <?= $currentPage === 'mon-espace' ? 'active' : '' ?>"><i class="fas fa-user"></i> Mon Espace</a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Outils</div>
            <a href="/dashboard/cvs" class="nav-item <?= $currentPage === 'cvs' ? 'active' : '' ?>"><i class="fas fa-file-alt"></i> CVs</a>
            <a href="/dashboard/reunions" class="nav-item <?= $currentPage === 'reunions' ? 'active' : '' ?>"><i class="fas fa-calendar"></i> Réunions</a>
            <a href="/dashboard/tests" class="nav-item <?= $currentPage === 'tests' ? 'active' : '' ?>"><i class="fas fa-clipboard-check"></i> Tests de compétences</a>
            <a href="/dashboard/bilan" class="nav-item <?= $currentPage === 'bilan' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Bilan annuel</a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Administration</div>
            <a href="/dashboard/projets" class="nav-item <?= $currentPage === 'projets' ? 'active' : '' ?>"><i class="fas fa-project-diagram"></i> Projets</a>
            <a href="/dashboard/equipe" class="nav-item <?= $currentPage === 'equipe' ? 'active' : '' ?>"><i class="fas fa-users"></i> Équipe</a>
            <a href="/dashboard/parametres" class="nav-item <?= $currentPage === 'parametres' ? 'active' : '' ?>"><i class="fas fa-cog"></i> Paramètres</a>
        </div>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($userName) ?></div>
                <div class="user-role"><?= ucfirst($userRole) ?></div>
            </div>
        </div>
        <a href="/logout" class="nav-item" style="padding: 0.5rem 0;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>
</aside>
