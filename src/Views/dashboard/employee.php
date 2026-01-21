<!-- views/dashboard/employee.php -->
<?php include 'views/layouts/header.php'; ?>

<div class="container">
    <h1>Tableau de bord</h1>
    <p>Bienvenue, <?= htmlspecialchars($_SESSION['user_name']) ?></p>
    
    <div class="dashboard-grid">
        <!-- Carte Profil -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Mon Profil</h2>
                <a href="/profile/edit" class="btn btn-primary">Modifier</a>
            </div>
            <p><strong>Email :</strong> <?= htmlspecialchars($_SESSION['user_email']) ?></p>
            <p><strong>Département :</strong> <?= htmlspecialchars($_SESSION['departement'] ?? 'Non défini') ?></p>
            <p><strong>Rôle :</strong> Employé</p>
        </div>
        
        <!-- Carte Compétences -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Mes Compétences</h2>
                <a href="/profile/competences" class="btn btn-secondary">Gérer</a>
            </div>
            
            <?php if (!empty($data['competences'])): ?>
                <ul>
                    <?php foreach (array_slice($data['competences'], 0, 5) as $comp): ?>
                        <li style="margin-bottom: 0.5rem; padding: 0.5rem; background: #f9fafb; border-radius: 0.25rem;">
                            <strong><?= htmlspecialchars($comp['competence_nom']) ?></strong>
                            <br>
                            <small>Catégorie : <?= htmlspecialchars($comp['categorie_nom']) ?></small>
                            <br>
                            <span class="level-badge level-<?= $comp['niveau_valide'] ?? $comp['niveau_declare'] ?>">
                                Niveau <?= $comp['niveau_valide'] ?? $comp['niveau_declare'] ?>
                                <?= $comp['niveau_valide'] ? '(Validé)' : '(En attente)' ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <?php if (count($data['competences']) > 5): ?>
                    <p style="text-align: center; margin-top: 1rem;">
                        <a href="/profile/competences">Voir toutes les compétences</a>
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <p>Vous n'avez pas encore déclaré de compétences.</p>
                <a href="/profile/competences" class="btn btn-secondary">Ajouter des compétences</a>
            <?php endif; ?>
        </div>
        
        <!-- Carte En attente -->
        <?php if (!empty($data['pendingValidations'])): ?>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Validations en attente</h2>
            </div>
            <p>Vous avez <?= count($data['pendingValidations']) ?> compétences en attente de validation par votre manager.</p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Recherche rapide -->
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h2 class="card-title">Rechercher des experts</h2>
        </div>
        <form method="GET" action="/search" class="search-box">
            <input type="text" name="q" placeholder="Rechercher une compétence ou une personne..." class="search-input">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
