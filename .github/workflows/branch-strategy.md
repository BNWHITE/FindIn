# Stratégie de Branches FindIN

## Structure des branches

### 📍 main (branche par défaut)
- **Objectif** : Code stable et testé
- **Protection** : Nécessite Pull Request + review
- **Déploiement** : Aucun (branche d'intégration)
- **Source** : Merge depuis `test` après validation

### 🧪 test  
- **Objectif** : Tests et validation avant production
- **Protection** : Merge depuis `main` ou feature branches
- **Déploiement** : Environnement de staging
- **Source** : Features branches validées

### 🚀 production
- **Objectif** : Code en production
- **Protection** : Merge uniquement depuis `test`
- **Déploiement** : https://insightful-gratitude-production-18b8.up.railway.app/
- **Source** : `test` après validation complète

## Workflow de développement

### 1. Créer une feature branch depuis main
```bash
git checkout main
git pull
git checkout -b feature/ma-fonctionnalite
```

### 2. Développer et commiter
```bash
git add .
git commit -m "feat: description"
```

### 3. Pousser et créer une PR vers main
```bash
git push origin feature/ma-fonctionnalite
# Créer PR sur GitHub vers main
```

### 4. Après merge dans main, déployer sur test
```bash
git checkout test
git merge main
git push origin test
```

### 5. Après validation sur test, déployer en production
```bash
git checkout production
git merge test
git push origin production
```

## Règles

- ❌ Pas de commit direct sur `main`, `test` ou `production`
- ✅ Toujours passer par des Pull Requests
- ✅ `test` doit toujours pouvoir merger dans `production`
- ✅ `main` → `test` → `production` (flux unidirectionnel)

## Commandes rapides

```bash
# Voir toutes les branches
git branch -a

# Synchroniser test avec main
git checkout test && git merge main && git push origin test

# Synchroniser production avec test
git checkout production && git merge test && git push origin production

# Revenir sur main
git checkout main
```
