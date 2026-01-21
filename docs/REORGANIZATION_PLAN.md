# 📋 Plan de réorganisation du projet FindIN

## 🎯 Objectif
Créer une structure propre, maintenable et professionnelle pour le projet FindIN.

---

## 📂 Nouvelle structure proposée

```
FindIn/
│
├── 📂 config/                      # Configuration de l'application
│   ├── database.php                # Configuration base de données
│   ├── app.php                     # Configuration générale
│   └── routes.php                  # Définition des routes (futur)
│
├── 📂 src/                         # Code source principal
│   │
│   ├── 📂 Controllers/             # Contrôleurs (PascalCase)
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── SearchController.php
│   │   ├── ProfileController.php
│   │   ├── AdminController.php
│   │   └── BaseController.php
│   │
│   ├── 📂 Models/                  # Modèles (PascalCase)
│   │   ├── Database.php
│   │   ├── User.php
│   │   ├── Competence.php
│   │   ├── Project.php
│   │   ├── Department.php
│   │   └── Validation.php
│   │
│   ├── 📂 Services/                # Services métier
│   │   ├── AuthService.php
│   │   ├── CompetenceService.php
│   │   ├── SearchService.php
│   │   └── CVParserService.php
│   │
│   ├── 📂 Helpers/                 # Fonctions utilitaires
│   │   ├── StringHelper.php
│   │   ├── DateHelper.php
│   │   ├── ValidationHelper.php
│   │   └── SecurityHelper.php
│   │
│   └── 📂 Middleware/              # Middlewares
│       ├── AuthMiddleware.php
│       ├── RoleMiddleware.php
│       └── CSRFMiddleware.php
│
├── 📂 public/                      # Ressources publiques (accessible web)
│   ├── index.php                   # Point d'entrée unique
│   ├── .htaccess                   # Configuration Apache
│   │
│   ├── 📂 assets/                  # Assets frontend
│   │   ├── 📂 css/
│   │   │   ├── style.css
│   │   │   ├── dashboard.css
│   │   │   └── components.css
│   │   │
│   │   ├── 📂 js/
│   │   │   ├── main.js
│   │   │   ├── dashboard.js
│   │   │   └── search.js
│   │   │
│   │   └── 📂 images/
│   │       ├── logo.png
│   │       └── ...
│   │
│   └── 📂 uploads/                 # Fichiers uploadés (CV, documents)
│       └── .htaccess               # Sécurité uploads
│
├── 📂 views/                       # Templates et vues
│   │
│   ├── 📂 layouts/                 # Layouts de base
│   │   ├── app.php                 # Layout principal
│   │   ├── dashboard.php           # Layout dashboard
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── sidebar.php
│   │
│   ├── 📂 auth/                    # Pages authentification
│   │   ├── login.php
│   │   └── register.php
│   │
│   ├── 📂 dashboard/               # Pages tableau de bord
│   │   ├── index.php
│   │   ├── profile.php
│   │   ├── settings.php
│   │   └── bilan.php
│   │
│   ├── 📂 competences/             # Gestion compétences
│   │   ├── index.php
│   │   ├── create.php
│   │   └── edit.php
│   │
│   ├── 📂 search/                  # Recherche
│   │   └── index.php
│   │
│   ├── 📂 admin/                   # Administration
│   │   ├── users.php
│   │   ├── competences.php
│   │   └── settings.php
│   │
│   ├── 📂 components/              # Composants réutilisables
│   │   ├── alert.php
│   │   ├── modal.php
│   │   ├── card.php
│   │   └── form_input.php
│   │
│   ├── 📂 errors/                  # Pages d'erreur
│   │   ├── 404.php
│   │   ├── 403.php
│   │   └── 500.php
│   │
│   └── index.php                   # Page d'accueil
│
├── 📂 database/                    # Base de données
│   │
│   ├── 📂 migrations/              # Migrations SQL
│   │   ├── 20251216_create_users_table.sql
│   │   ├── 20251216_create_competences_table.sql
│   │   └── ...
│   │
│   ├── 📂 seeds/                   # Données de test
│   │   ├── users_seed.sql
│   │   └── competences_seed.sql
│   │
│   ├── 📂 backups/                 # Sauvegardes automatiques
│   │   └── backup_20251216.sql
│   │
│   └── schema.sql                  # Schéma complet actuel
│
├── 📂 tests/                       # Tests automatisés
│   ├── 📂 Unit/                    # Tests unitaires
│   │   ├── UserTest.php
│   │   └── CompetenceTest.php
│   │
│   ├── 📂 Feature/                 # Tests fonctionnels
│   │   ├── AuthTest.php
│   │   └── SearchTest.php
│   │
│   └── bootstrap.php               # Configuration tests
│
├── 📂 docs/                        # Documentation
│   ├── README.md                   # Index documentation
│   │
│   ├── 📂 guides/                  # Guides utilisateur
│   │   ├── INSTALLATION.md
│   │   ├── DEVELOPMENT.md
│   │   └── DEPLOYMENT.md
│   │
│   ├── 📂 technical/               # Documentation technique
│   │   ├── ARCHITECTURE.md
│   │   ├── DATABASE.md
│   │   ├── CONVENTIONS.md
│   │   ├── SECURITY.md
│   │   └── BEST_PRACTICES.md
│   │
│   ├── 📂 api/                     # Documentation API
│   │   └── ENDPOINTS.md
│   │
│   └── 📂 screenshots/             # Captures d'écran
│       ├── home.png
│       ├── dashboard.png
│       └── search.png
│
├── 📂 scripts/                     # Scripts utilitaires
│   ├── backup_db.sh                # Sauvegarde DB
│   ├── deploy.sh                   # Script de déploiement
│   └── setup.sh                    # Configuration initiale
│
├── 📂 storage/                     # Stockage temporaire
│   ├── 📂 logs/                    # Logs applicatifs
│   │   ├── app.log
│   │   └── error.log
│   │
│   └── 📂 cache/                   # Cache applicatif
│       └── .gitkeep
│
├── 📂 archive/                     # Fichiers obsolètes (à ne PAS déployer)
│   ├── old_documentation/
│   └── legacy_files/
│
├── .gitignore                      # Fichiers ignorés par Git
├── .htaccess                       # Configuration Apache racine
├── composer.json                   # Dépendances PHP (futur)
├── LICENSE.md                      # Licence du projet
├── README.md                       # Documentation principale
├── CHANGELOG.md                    # Historique des versions
├── CONTRIBUTING.md                 # Guide de contribution
└── ROADMAP.md                      # Feuille de route
```

---

## 🔄 Étapes de migration

### Phase 1 : Préparation (Fait ✅)
- [x] Créer l'arborescence de dossiers
- [x] Créer la documentation
- [x] Sauvegarder la base de données

### Phase 2 : Migration des fichiers
```bash
# À exécuter manuellement ou via script

# 1. Déplacer les fichiers sources
mkdir -p src/{Controllers,Models,Services,Helpers,Middleware}
mv controllers/* src/Controllers/
mv models/* src/Models/

# 2. Réorganiser public/
mkdir -p public/assets/{css,js,images}
mv assets/css/* public/assets/css/
mv assets/js/* public/assets/js/
mv assets/images/* public/assets/images/

# 3. Organiser la base de données
mkdir -p database/{migrations,seeds,backups}
mv gestion_competences.sql database/schema.sql
mv migration_users.sql database/migrations/

# 4. Archiver les anciens fichiers
mkdir -p archive/old_documentation
mv *.md archive/old_documentation/ (sauf README.md, LICENSE.md)

# 5. Créer les dossiers vides nécessaires
mkdir -p storage/{logs,cache}
touch storage/logs/.gitkeep storage/cache/.gitkeep
```

### Phase 3 : Mise à jour des chemins
Mettre à jour tous les `require_once` et `include` dans le code.

### Phase 4 : Tests
- [ ] Tester toutes les pages
- [ ] Vérifier les uploads
- [ ] Tester l'authentification
- [ ] Vérifier les requêtes DB

---

## 📝 Fichiers à créer

### .gitignore
```gitignore
# Configuration locale
config/database.php

# Fichiers générés
public/uploads/*
!public/uploads/.gitkeep

storage/logs/*
!storage/logs/.gitkeep

storage/cache/*
!storage/cache/.gitkeep

database/backups/*
!database/backups/.gitkeep

# IDE
.vscode/
.idea/
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db

# Composer
/vendor/
composer.lock
```

### composer.json
```json
{
    "name": "findin/skills-management",
    "description": "Plateforme de gestion des compétences",
    "type": "project",
    "license": "MIT",
    "require": {
        "php": ">=8.0",
        "ext-pdo": "*",
        "ext-pdo_mysql": "*",
        "ext-mbstring": "*",
        "ext-json": "*"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.0"
    },
    "autoload": {
        "psr-4": {
            "FindIn\\": "src/"
        }
    }
}
```

---

## 🎯 Avantages de la nouvelle structure

### ✅ Organisation
- Séparation claire des responsabilités
- Code source dans `src/`
- Assets publics dans `public/`
- Documentation complète dans `docs/`

### ✅ Sécurité
- Point d'entrée unique (`public/index.php`)
- Code source hors de la racine web
- Uploads sécurisés
- Logs et cache protégés

### ✅ Maintenabilité
- Structure standardisée
- Facile à comprendre pour nouveaux développeurs
- Documentation accessible
- Tests organisés

### ✅ Scalabilité
- Prêt pour Composer/autoloading
- Structure pour ajouter des services
- Middleware pour fonctionnalités transversales
- Cache et logs séparés

---

## 🚀 Prochaines améliorations

### Court terme
- [ ] Implémenter autoloading PSR-4
- [ ] Ajouter système de routing
- [ ] Créer helpers réutilisables
- [ ] Implémenter middlewares

### Moyen terme
- [ ] API REST
- [ ] Tests automatisés
- [ ] CI/CD
- [ ] Docker

### Long terme
- [ ] Microservices
- [ ] Cache Redis
- [ ] Queue system
- [ ] ElasticSearch

---

## 📞 Contact

Pour toute question sur la réorganisation :
- Documentation : `docs/`
- Issues GitHub
- Email équipe dev

**Bonne restructuration ! 🎉**
