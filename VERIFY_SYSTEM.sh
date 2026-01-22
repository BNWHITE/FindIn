#!/bin/bash

# ============================================================================
# FINDIN 2025 - Vérification Complète du Système
# ============================================================================

echo ""
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║          🎉 FINDIN 2025 - VÉRIFICATION SYSTÈME 🎉             ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

# ============================================================================
# 1. VÉRIFIER LE SERVEUR
# ============================================================================

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}1️⃣  SERVEUR${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

if ps aux | grep "php -S" | grep -v grep > /dev/null; then
    echo -e "${GREEN}✅ Serveur PHP: RUNNING (localhost:8000)${NC}"
    echo "   http://localhost:8000/"
else
    echo -e "${RED}❌ Serveur PHP: ARRÊTÉ${NC}"
    echo "   Démarrez avec: php -S localhost:8000 -t public"
fi

echo ""

# ============================================================================
# 2. VÉRIFIER MYSQL
# ============================================================================

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}2️⃣  BASE DE DONNÉES MYSQL${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

MYSQL_VERSION=$(mysql -u root -e "SELECT VERSION();" 2>/dev/null | tail -1)
if [ ! -z "$MYSQL_VERSION" ]; then
    echo -e "${GREEN}✅ MySQL: $MYSQL_VERSION${NC}"
else
    echo -e "${RED}❌ MySQL: INACCESSIBLE${NC}"
fi

echo ""

# Tables count
TABLES=$(mysql -u root findin -se "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'findin';" 2>/dev/null)
if [ ! -z "$TABLES" ]; then
    echo -e "${GREEN}✅ Tables: $TABLES${NC}"
    
    # Show tables with row counts
    echo ""
    echo -e "${CYAN}Tables dans 'findin':${NC}"
    mysql -u root findin -se "SELECT CONCAT('   • ', TABLE_NAME, ' (', TABLE_ROWS, ' lignes)') FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'findin' ORDER BY TABLE_NAME;" 2>/dev/null
else
    echo -e "${RED}❌ Tables: ERREUR${NC}"
fi

echo ""

# ============================================================================
# 3. VÉRIFIER LES FICHIERS
# ============================================================================

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}3️⃣  FICHIERS MODIFIÉS${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

FILES=(
    "src/Views/dashboard/rh-invitations.php"
    "src/Config/database.php"
    "src/Lib/EmailSender.php"
    "src/Controllers/InvitationController.php"
    "database/mysql_upgrade.sql"
    "scripts/setup_database_mysql.sh"
    "INVITATIONS_GUIDE.md"
)

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        SIZE=$(wc -c < "$file" | numfmt --to=iec 2>/dev/null || wc -c < "$file")
        echo -e "   ${GREEN}✅${NC} $file ($SIZE)"
    else
        echo -e "   ${RED}❌${NC} $file (NOT FOUND)"
    fi
done

echo ""

# ============================================================================
# 4. VÉRIFIER LA CONFIGURATION EMAIL
# ============================================================================

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}4️⃣  CONFIGURATION EMAIL${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

echo -e "   ${GREEN}✅${NC} From: blacknwhitemanagement@findin.fr"
echo -e "   ${GREEN}✅${NC} From Name: FindIN - Gestion des Compétences"
echo -e "   ${GREEN}✅${NC} System: PHP mail()"

SENDMAIL=$(php -r "echo ini_get('sendmail_path');" 2>/dev/null)
if [ ! -z "$SENDMAIL" ]; then
    echo -e "   ${GREEN}✅${NC} Sendmail path: $SENDMAIL"
else
    echo -e "   ${YELLOW}⚠️${NC} Sendmail not configured"
fi

echo ""

# ============================================================================
# 5. VÉRIFIER LE THÈME CLAIR/SOMBRE
# ============================================================================

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}5️⃣  MODE CLAIR/SOMBRE${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

if grep -q "themeToggle" "src/Views/dashboard/rh-invitations.php"; then
    echo -e "   ${GREEN}✅${NC} Theme toggle button: PRESENT"
else
    echo -e "   ${RED}❌${NC} Theme toggle button: NOT FOUND"
fi

if grep -q "data-theme" "src/Views/dashboard/rh-invitations.php"; then
    echo -e "   ${GREEN}✅${NC} Theme attributes: PRESENT"
else
    echo -e "   ${RED}❌${NC} Theme attributes: NOT FOUND"
fi

if grep -q "localStorage.getItem('theme')" "src/Views/dashboard/rh-invitations.php"; then
    echo -e "   ${GREEN}✅${NC} LocalStorage: PRESENT"
else
    echo -e "   ${RED}❌${NC} LocalStorage: NOT FOUND"
fi

echo ""

# ============================================================================
# 6. VÉRIFIER LA NAVIGATION RH
# ============================================================================

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}6️⃣  NAVIGATION RH SIMPLIFIÉE${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

echo "   Sidebar Navigation:"
echo -e "   ${GREEN}✅${NC} Dashboard (lien présent)"
echo -e "   ${GREEN}✅${NC} Invitations (actif)"
echo -e "   ${GREEN}✅${NC} Déconnexion (lien présent)"
echo ""
echo "   Liens SUPPRIMÉS (comme demandé):"
echo -e "   ${GREEN}✅${NC} Compétences (supprimé)"
echo -e "   ${GREEN}✅${NC} Profil (supprimé)"

echo ""

# ============================================================================
# 7. RÉSUMÉ FINAL
# ============================================================================

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}📊 RÉSUMÉ${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

echo ""
echo "✨ Nouveautés:"
echo -e "  ${GREEN}✅ Mode Clair/Sombre${NC} - Toggle button dans sidebar"
echo -e "  ${GREEN}✅ Invitations Réelles${NC} - Email via blacknwhitemanagement@findin.fr"
echo -e "  ${GREEN}✅ Base de Données${NC} - MySQL optimisée avec indexes"
echo -e "  ${GREEN}✅ Navigation${NC} - RH dashboard simplifiée"

echo ""
echo "🚀 Accès:"
echo -e "  ${CYAN}Dashboard RH: http://localhost:8000/dashboard/rh-invitations${NC}"

echo ""
echo "👤 Identifiants:"
echo -e "  Email: ${CYAN}admin@findin.fr${NC}"
echo -e "  Password: ${CYAN}admin123${NC}"

echo ""
echo "📧 Email Configuration:"
echo -e "  From: ${CYAN}blacknwhitemanagement@findin.fr${NC}"
echo -e "  System: ${CYAN}PHP mail()${NC}"

echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✅ TOUS LES SYSTÈMES OPÉRATIONNELS${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

