<!-- views/search/index.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Recherche</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        /* Styles spécifiques à la recherche */
        .search-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            border-radius: 0 0 20px 20px;
            margin-bottom: 2rem;
        }
        
        .search-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .search-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .search-box-large {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .search-input-large {
            width: 100%;
            padding: 1.25rem;
            border: 2px solid #ddd;
            border-radius: 15px;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }
        
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .result-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }
        
        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .user-avatar-large {
            width: 60px;
            height: 60px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .user-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .user-department {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .competence-tag {
            display: inline-block;
            background: #f1f5f9;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <?php include 'views/layouts/header.php'; ?>
    
    <div class="search-header">
        <h1 class="search-title">🔍 Recherche Intelligente</h1>
        <p class="search-subtitle">
            Trouvez les bons profils en langage naturel. Exemple : "Expert Python avec expérience Agile"
        </p>
    </div>
    
    <div class="search-container">
        <div class="search-box-large">
            <form action="/search" method="GET">
                <input type="text" 
                       name="q" 
                       class="search-input-large" 
                       placeholder="Rechercher une compétence, un expert, un projet..."
                       value="<?= htmlspecialchars($searchTerm ?? '') ?>">
                
                <div class="filters">
                    <div class="filter-group">
                        <label>Département</label>
                        <select name="departement" class="form-control">
                            <option value="">Tous les départements</option>
                            <?php foreach ($departements as $dept): ?>
                                <option value="<?= $dept['nom'] ?>" 
                                    <?= ($filters['departement'] ?? '') === $dept['nom'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dept['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Niveau minimum</label>
                        <select name="niveau_min" class="form-control">
                            <option value="1" <?= ($filters['niveau_min'] ?? 1) == 1 ? 'selected' : '' ?>>Niveau 1 (Débutant)</option>
                            <option value="2" <?= ($filters['niveau_min'] ?? 1) == 2 ? 'selected' : '' ?>>Niveau 2 (Intermédiaire)</option>
                            <option value="3" <?= ($filters['niveau_min'] ?? 1) == 3 ? 'selected' : '' ?>>Niveau 3 (Confirmé)</option>
                            <option value="4" <?= ($filters['niveau_min'] ?? 1) == 4 ? 'selected' : '' ?>>Niveau 4 (Expert)</option>
                            <option value="5" <?= ($filters['niveau_min'] ?? 1) == 5 ? 'selected' : '' ?>>Niveau 5 (Maître)</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem;">
                    🔍 Lancer la recherche
                </button>
            </form>
        </div>
        
        <?php if (!empty($searchResults)): ?>
            <h2 style="margin-bottom: 1rem;">Résultats de recherche</h2>
            <div class="results-grid">
                <?php foreach ($searchResults as $result): ?>
                    <div class="result-card">
                        <div class="user-avatar-large">
                            <?= strtoupper(substr($result['prenom'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="user-name">
                            <?= htmlspecialchars($result['prenom'] ?? '') ?> <?= htmlspecialchars($result['nom'] ?? '') ?>
                        </div>
                        <div class="user-department">
                            <?= htmlspecialchars($result['departement_nom'] ?? 'Non spécifié') ?>
                        </div>
                        <div style="margin-top: 1rem;">
                            <strong>Compétence :</strong>
                            <span class="competence-tag">
                                <?= htmlspecialchars($result['competence_nom'] ?? '') ?> (Niveau <?= $result['niveau'] ?? '?' ?>)
                            </span>
                        </div>
                        <a href="#" style="display: block; margin-top: 1rem; color: var(--primary); text-decoration: none;">
                            Voir le profil complet →
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (isset($searchTerm) && $searchTerm !== ''): ?>
            <div style="text-align: center; padding: 3rem; color: var(--gray);">
                <p style="font-size: 1.2rem;">Aucun résultat trouvé pour "<?= htmlspecialchars($searchTerm) ?>"</p>
                <p>Essayez avec d'autres termes ou élargissez vos filtres.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'views/layouts/footer.php'; ?>
</body>
</html>
