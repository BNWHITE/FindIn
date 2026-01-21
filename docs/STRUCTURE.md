# 📁 Structure du Projet FindIN

## 🎯 Organisation Globale

Le projet suit une architecture **MVC moderne** avec séparation claire des responsabilités.

### Vue d'ensemble

```
FindIn/
├── 📄 README.md                    # Documentation principale
├── 📄 CHANGELOG.md                 # Historique des versions
├── 📄 .gitignore                   # Fichiers ignorés par Git
├── 📄 .htaccess                    # Configuration Apache racine
│
├── 📁 public/                      # 🌐 Point d'entrée web (SEUL dossier accessible)
│   ├── index.php                   # Front controller
│   ├── .htaccess                   # Règles de réécriture
│   ├── assets/                     # Ressources statiques
│   │   ├── css/                    # Feuilles de style
│   │   ├── js/                     # Scripts JavaScript
│   │   └── images/                 # Images
│   └── uploads/                    # Fichiers uploadés (lien symbolique)
│
├── 📁 src/                         # 💻 Code source applicatif
│   ├── Controllers/                # Contrôleurs MVC
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   └── ...
│   ├── Models/                     # Modèles de données
│   │   ├── User.php
│   │   ├── Database.php
│   │   └── ...
│   ├── Views/                      # Templates HTML
│   │   ├── auth/
│   │   ├── dashboard/
│   │   └── layouts/
│   ├── Config/                     # Configuration
│   │   └── database.php
│   ├── Lib/                        # Bibliothèques
│   └── Middleware/                 # Middlewares (à venir)
│
├── 📁 database/                    # 🗄️ Base de données
│   ├── schema/                     # Schémas SQL
│   │   └── gestion_competences.sql
│   ├── migrations/                 # Migrations
│   ├── seeds/                      # Données de test
│   ├── backups/                    # Sauvegardes
│   └── archive/                    # Anciens fichiers SQL
│
├── 📁 storage/                     # 💾 Stockage applicatif
│   ├── uploads/                    # Fichiers uploadés
│   ├── logs/                       # Logs applicatifs
│   └── cache/                      # Cache (futur)
│
├── 📁 docs/                        # 📚 Documentation
│   ├── README.md                   # Index documentation
│   ├── STRUCTURE.md                # Ce fichier
│   ├── guides/                     # Guides pratiques
│   │   ├── INSTALLATION.md
│   │   └── DEVELOPMENT.md
│   ├── technical/                  # Documentation technique
│   │   ├── ARCHITECTURE.md
│   │   └── DATABASE.md
│   └── archive/                    # Anciens documents
│
├── 📁 scripts/                     # 🛠️ Scripts utilitaires
│   ├── setup/                      # Scripts d'installation
│   │   └── setup_mysql.php
│   ├── maintenance/                # Scripts de maintenance
│   │   └── clean_root.sh
│   └── update_apache.sh            # Configuration Apache
│
└── 📁 archive/                     # 📦 Fichiers archivés
    └── old_structure/              # Ancienne structure (backup)
```

## 🎯 Philosophie d'Organisation

### ✅ Principes appliqués

1. **Séparation des responsabilités** : Chaque dossier a un rôle unique
2. **Sécurité** : Seul `public/` est accessible via web
3. **Maintenabilité** : Structure claire et documentée
4. **Scalabilité** : Facile d'ajouter de nouveaux modules

### 📍 Chemins importants

| Dossier | Accessible web | Usage |
|---------|---------------|-------|
| `public/` | ✅ Oui | Assets, point d'entrée |
| `src/` | ❌ Non | Code PHP applicatif |
| `database/` | ❌ Non | Schémas et données |
| `storage/` | ❌ Non | Fichiers dynamiques |
| `docs/` | ❌ Non | Documentation |

## 🔐 Sécurité

- Seul `public/` est dans le DocumentRoot Apache
- Fichiers `.htaccess` protègent les dossiers sensibles
- Uploads isolés dans `storage/` (hors de `public/`)
- Configuration dans `src/Config/` (non accessible web)

## 📖 Navigation Rapide

- **Installer** : [`docs/guides/INSTALLATION.md`](guides/INSTALLATION.md)
- **Développer** : [`docs/guides/DEVELOPMENT.md`](guides/DEVELOPMENT.md)
- **Architecture** : [`docs/technical/ARCHITECTURE.md`](technical/ARCHITECTURE.md)
- **Base de données** : [`docs/technical/DATABASE.md`](technical/DATABASE.md)

---

**Dernière mise à jour** : 18 décembre 2025
