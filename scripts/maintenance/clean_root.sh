#!/bin/bash

echo "🧹 Nettoyage de la racine du projet..."

cd "$(dirname "$0")/../.."

# Fichiers à garder à la racine
KEEP_FILES=(
    "README.md"
    "CHANGELOG.md"
    ".gitignore"
    ".htaccess"
    "composer.json"
    "package.json"
    "LICENSE"
)

# Créer les dossiers si nécessaire
mkdir -p archive/old_structure
mkdir -p scripts/setup
mkdir -p database/archive
mkdir -p database/schema
mkdir -p docs/archive

echo "📦 Archivage des anciens dossiers..."
# Déplacer les anciens dossiers s'ils existent encore
for dir in controllers models views config lib; do
    if [ -d "$dir" ]; then
        echo "  ↳ $dir/ → archive/old_structure/"
        mv "$dir" archive/old_structure/
    fi
done

echo ""
echo "📄 Rangement des scripts PHP..."
# Ranger les scripts PHP de setup
for file in setup_*.php init_*.php install.php migrate_*.php test_*.php check.php; do
    if [ -f "$file" ]; then
        echo "  ↳ $file → scripts/setup/"
        mv "$file" scripts/setup/
    fi
done

echo ""
echo "📄 Rangement des scripts shell..."
# Ranger les scripts shell
for file in *.sh; do
    if [ -f "$file" ] && [[ "$file" != "start"* ]]; then
        echo "  ↳ $file → scripts/maintenance/"
        mv "$file" scripts/maintenance/ 2>/dev/null
    fi
done

echo ""
echo "📄 Rangement des fichiers SQL..."
# Ranger les fichiers SQL
if [ -f "gestion_competences.sql" ]; then
    echo "  ↳ gestion_competences.sql → database/schema/"
    mv "gestion_competences.sql" database/schema/
fi

for file in *.sql; do
    if [ -f "$file" ]; then
        echo "  ↳ $file → database/archive/"
        mv "$file" database/archive/
    fi
done

echo ""
echo "📄 Rangement de la documentation supplémentaire..."
# Ranger les MD supplémentaires
for file in *.md; do
    if [ -f "$file" ] && [[ ! " ${KEEP_FILES[@]} " =~ " ${file} " ]]; then
        echo "  ↳ $file → docs/archive/"
        mv "$file" docs/archive/
    fi
done

echo ""
echo "🗑️  Suppression des fichiers temporaires..."
# Supprimer les fichiers temporaires
rm -f test.php debug.php temp.* *.tmp *.bak httpd-vhosts-new.conf 2>/dev/null

echo ""
echo "✅ Nettoyage terminé !"
echo ""
echo "📁 Fichiers restants à la racine :"
ls -1 | grep -v "^\." | head -15

echo ""
echo "📊 Résumé :"
echo "  • Ancienne structure → archive/old_structure/"
echo "  • Scripts PHP → scripts/setup/"
echo "  • Fichiers SQL → database/archive/"
echo "  • Documentation → docs/archive/"
