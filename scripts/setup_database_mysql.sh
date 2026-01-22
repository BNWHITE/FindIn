#!/bin/bash

# ============================================================================
# SETUP_DATABASE.sh - Initialiser et upgrader la base de données MySQL
# ============================================================================

echo "🚀 Initialisation de la base de données FindIN..."
echo ""

# Couleurs pour l'output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration MySQL
DB_HOST="${DB_HOST:-localhost}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-}"
DB_NAME="${DB_NAME:-findin}"

echo -e "${BLUE}Configuration:${NC}"
echo "  Host: $DB_HOST"
echo "  Port: $DB_PORT"
echo "  User: $DB_USER"
echo "  Database: $DB_NAME"
echo ""

# Vérifier si MySQL est disponible
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}❌ Erreur: mysql CLI not found${NC}"
    echo "Veuillez installer MySQL client ou utiliser phpMyAdmin"
    exit 1
fi

# Créer la base de données et importer le schéma
echo -e "${YELLOW}📊 Création de la base de données...${NC}"

if [ -z "$DB_PASS" ]; then
    # Pas de mot de passe
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" << EOF
$(cat "$(dirname "$0")/../database/mysql_upgrade.sql")
EOF
else
    # Avec mot de passe
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" << EOF
$(cat "$(dirname "$0")/../database/mysql_upgrade.sql")
EOF
fi

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Base de données créée/mise à jour avec succès${NC}"
else
    echo -e "${RED}❌ Erreur lors de la création de la base de données${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}Vérification des tables...${NC}"

# Vérifier les tables créées
if [ -z "$DB_PASS" ]; then
    TABLE_COUNT=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" -se "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME';" 2>/dev/null)
else
    TABLE_COUNT=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -se "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME';" 2>/dev/null)
fi

if [ ! -z "$TABLE_COUNT" ] && [ "$TABLE_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✅ Tables trouvées: $TABLE_COUNT${NC}"
    echo ""
    echo -e "${BLUE}Listes des tables:${NC}"
    if [ -z "$DB_PASS" ]; then
        mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" -se "SHOW TABLES;" 2>/dev/null | sed 's/^/  • /'
    else
        mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -se "SHOW TABLES;" 2>/dev/null | sed 's/^/  • /'
    fi
else
    echo -e "${RED}⚠️ Impossible de vérifier les tables${NC}"
fi

echo ""
echo -e "${GREEN}════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ SETUP TERMINÉ!${NC}"
echo -e "${GREEN}════════════════════════════════════════════════════════${NC}"
echo ""
echo "La base de données est prête à être utilisée."
echo ""
echo "Identifiants de test:"
echo "  Email: admin@findin.fr"
echo "  Password: admin123"
echo ""
echo "  Email: test@findin.fr"
echo "  Password: test123"
echo ""

