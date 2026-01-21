#!/bin/bash

# Affichage de la structure finale propre

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m' # No Color

clear

echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║                                                                ║${NC}"
echo -e "${BLUE}║${NC}  ${BOLD}✨  FindIN - Structure Propre et Organisée !${NC}  ${BLUE}║${NC}"
echo -e "${BLUE}║                                                                ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""

echo -e "${GREEN}📁 Racine du projet (${BOLD}PROPRE${NC}${GREEN}) :${NC}"
echo ""
echo "   FindIn/"
echo "   ├── 📄 README.md          → Documentation principale"
echo "   ├── 📄 CHANGELOG.md       → Historique des versions"
echo "   ├── 📄 composer.json      → Dépendances PHP"
echo "   │"
echo "   ├── 📁 public/            → Point d'entrée web"
echo "   ├── 📁 src/               → Code source (MVC)"
echo "   ├── 📁 database/          → Schémas SQL"
echo "   ├── 📁 storage/           → Uploads & logs"
echo "   ├── 📁 docs/              → Documentation"
echo "   ├── 📁 scripts/           → Scripts utilitaires"
echo "   ├── 📁 tests/             → Tests (futur)"
echo "   └── 📁 archive/           → Anciens fichiers"
echo ""

echo -e "${CYAN}📊 Statistiques :${NC}"
TOTAL_DIRS=$(find . -maxdepth 1 -type d ! -name ".*" ! -name "." | wc -l | xargs)
TOTAL_FILES=$(ls -p | grep -v / | wc -l | xargs)
echo "   • ${TOTAL_DIRS} dossiers principaux"
echo "   • ${TOTAL_FILES} fichiers à la racine"
echo ""

echo -e "${GREEN}✅ Organisation Réussie :${NC}"
echo "   ✓ Scripts PHP → scripts/setup/"
echo "   ✓ Fichiers SQL → database/archive/"
echo "   ✓ Documentation → docs/archive/"
echo "   ✓ Ancienne structure → archive/old_structure/"
echo "   ✓ Fichiers divers → archive/"
echo ""

echo -e "${YELLOW}📚 Documentation :${NC}"
echo "   • docs/STRUCTURE.md        → Structure détaillée"
echo "   • docs/guides/             → Guides pratiques"
echo "   • docs/technical/          → Documentation technique"
echo ""

echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${BOLD}Structure professionnelle prête !${NC} 🎉"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo ""

# Afficher le contenu de chaque dossier principal
echo -e "${CYAN}📦 Contenu des dossiers :${NC}"
echo ""

echo -e "${BOLD}public/${NC}"
ls -1 public/ | head -5 | sed 's/^/   ├── /'
echo ""

echo -e "${BOLD}src/${NC}"
ls -1 src/ | head -5 | sed 's/^/   ├── /'
echo ""

echo -e "${BOLD}scripts/${NC}"
ls -1 scripts/ | head -5 | sed 's/^/   ├── /'
echo ""

echo -e "${GREEN}💡 Commandes utiles :${NC}"
echo "   • bash scripts/update_apache.sh    → Configurer Apache"
echo "   • bash scripts/show_status.sh      → Voir le statut"
echo "   • tree -L 2 -d                     → Voir l'arborescence"
echo ""
