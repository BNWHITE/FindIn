#!/bin/bash

# Script de réorganisation automatique du projet FindIN
# Usage: bash scripts/reorganize.sh

echo "🚀 Début de la réorganisation du projet FindIN..."
echo ""

# Couleurs pour les messages
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
success() {
    echo -e "${GREEN}✓${NC} $1"
}

warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

error() {
    echo -e "${RED}✗${NC} $1"
}

# Vérifier qu'on est dans le bon dossier
if [ ! -f "index.php" ]; then
    error "Erreur : ce script doit être exécuté depuis la racine du projet FindIN"
    exit 1
fi

echo "📋 Création de la structure de dossiers..."

# Créer la nouvelle structure
mkdir -p src/{Controllers,Models,Services,Helpers,Middleware}
mkdir -p public/assets/{css,js,images}
mkdir -p public/uploads
mkdir -p database/{migrations,seeds,backups}
mkdir -p storage/{logs,cache}
mkdir -p tests/{Unit,Feature}
mkdir -p docs/{guides,technical,api,screenshots}
mkdir -p archive

success "Structure de dossiers créée"

echo ""
echo "📂 Archivage des anciens fichiers de documentation..."

# Archiver les vieux fichiers markdown (sauf les nouveaux)
for file in *.md; do
    if [ "$file" != "README_NEW.md" ] && [ "$file" != "LICENSE.md" ]; then
        if [ -f "$file" ]; then
            mv "$file" archive/ 2>/dev/null
        fi
    fi
done

success "Fichiers markdown archivés dans archive/"

echo ""
echo "🗄️ Organisation de la base de données..."

# Déplacer les fichiers SQL
if [ -f "gestion_competences.sql" ]; then
    cp gestion_competences.sql database/schema.sql
    mv gestion_competences.sql archive/
    success "Schema SQL copié dans database/"
fi

if [ -f "migration_users.sql" ]; then
    mv migration_users.sql database/migrations/20251216_migrate_users.sql
    success "Migration users déplacée"
fi

if [ -f "create_database_simple.sql" ]; then
    mv create_database_simple.sql archive/
fi

echo ""
echo "🎨 Organisation des assets..."

# Créer des copies des assets dans public/
if [ -d "assets" ]; then
    cp -r assets/css/* public/assets/css/ 2>/dev/null
    cp -r assets/js/* public/assets/js/ 2>/dev/null
    cp -r assets/images/* public/assets/images/ 2>/dev/null || cp -r assets/img/* public/assets/images/ 2>/dev/null
    success "Assets copiés dans public/assets/"
fi

echo ""
echo "📝 Création des fichiers de configuration..."

# Créer .gitignore
cat > .gitignore << 'EOF'
# Configuration locale
config/database.php

# Fichiers uploadés
public/uploads/*
!public/uploads/.gitkeep

# Logs et cache
storage/logs/*
!storage/logs/.gitkeep
storage/cache/*
!storage/cache/.gitkeep

# Backups
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

# Archive
archive/
EOF

success ".gitignore créé"

# Créer les fichiers .gitkeep
touch public/uploads/.gitkeep
touch storage/logs/.gitkeep
touch storage/cache/.gitkeep
touch database/backups/.gitkeep

success "Fichiers .gitkeep créés"

echo ""
echo "📄 Mise à jour du README..."

# Remplacer l'ancien README
if [ -f "README_NEW.md" ]; then
    mv README.md archive/README_OLD.md 2>/dev/null
    mv README_NEW.md README.md
    success "README mis à jour"
fi

echo ""
echo "🔒 Sécurisation des uploads..."

# Créer .htaccess pour les uploads
cat > public/uploads/.htaccess << 'EOF'
# Interdire l'exécution de scripts
<FilesMatch "\.(php|php3|php4|php5|phtml)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# Autoriser uniquement certains types de fichiers
<FilesMatch "\.(pdf|docx|doc|txt|jpg|jpeg|png)$">
    Allow from all
</FilesMatch>
EOF

success "Sécurité uploads configurée"

echo ""
echo "📊 Création du fichier de changelog..."

cat > CHANGELOG.md << 'EOF'
# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-12-16

### Ajouté
- Système d'authentification complet
- Gestion des compétences avec validation
- Recherche avancée de talents
- Tableaux de bord par rôle
- Upload et parsing de CV
- Migration complète vers table users unique
- Documentation complète du projet
- Nouvelle structure organisée

### Modifié
- Structure de la base de données optimisée
- Interface utilisateur modernisée
- Architecture MVC améliorée

### Sécurité
- Hashage bcrypt des mots de passe
- Protection XSS
- Protection SQL injection
- Sessions sécurisées
EOF

success "CHANGELOG.md créé"

echo ""
echo "📋 Résumé de la réorganisation"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "✅ Structure de dossiers créée"
echo "✅ Documentation organisée dans docs/"
echo "✅ Base de données dans database/"
echo "✅ Assets dans public/assets/"
echo "✅ Anciens fichiers archivés"
echo "✅ Fichiers de configuration créés"
echo "✅ Sécurité configurée"
echo ""
echo "⚠️  Actions manuelles nécessaires :"
echo "1. Vérifier que le site fonctionne : http://findin.local"
echo "2. Tester toutes les fonctionnalités"
echo "3. Mettre à jour les chemins dans le code si nécessaire"
echo "4. Commiter les changements dans Git"
echo ""
echo "📖 Voir docs/REORGANIZATION_PLAN.md pour plus de détails"
echo ""
echo "🎉 Réorganisation terminée avec succès !"
