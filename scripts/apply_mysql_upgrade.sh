#!/bin/bash

# Script d'application de la mise à jour MySQL FindIN
# Gère les erreurs et les indexes existants

echo "════════════════════════════════════════════════════════════════"
echo "  🔄 Application de la mise à jour MySQL FindIN"
echo "════════════════════════════════════════════════════════════════"

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Chemins
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
SQL_FILE="$PROJECT_DIR/database/mysql_upgrade.sql"

if [ ! -f "$SQL_FILE" ]; then
    echo -e "${RED}❌ Fichier SQL non trouvé: $SQL_FILE${NC}"
    exit 1
fi

echo "📝 Fichier SQL: $SQL_FILE"
echo ""

# Exécuter le SQL et capturer les erreurs
echo "🚀 Exécution du script SQL..."
echo ""

mysql -u root findin < "$SQL_FILE" 2>&1 | while IFS= read -r line; do
    if [[ $line == *"ERROR 1061"* ]]; then
        # Duplicate key name - c'est OK
        echo -e "${YELLOW}⚠️  Index déjà existant (1061) - Ignoré${NC}"
    elif [[ $line == *"ERROR"* ]]; then
        echo -e "${RED}❌ $line${NC}"
    elif [[ $line == "Query OK"* ]]; then
        echo -e "${GREEN}✅ $line${NC}"
    else
        [ -n "$line" ] && echo "$line"
    fi
done

# Vérifier les tables créées
echo ""
echo "📊 Vérification des tables..."
echo ""

mysql -u root findin -e "
SELECT 
    TABLE_NAME,
    TABLE_ROWS as ROWS,
    DATA_LENGTH,
    INDEX_LENGTH
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'findin'
ORDER BY TABLE_NAME;
" 2>&1

echo ""
echo "🔍 Vérification des indexes..."
echo ""

mysql -u root findin -e "
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    SEQ_IN_INDEX,
    NON_UNIQUE
FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = 'findin' 
AND INDEX_NAME != 'PRIMARY'
ORDER BY TABLE_NAME, INDEX_NAME;
" 2>&1

echo ""
echo "════════════════════════════════════════════════════════════════"
echo "✅ Mise à jour MySQL appliquée!"
echo "════════════════════════════════════════════════════════════════"
echo ""
echo "📌 Prochaines étapes:"
echo "   • Vérifier les tables créées ci-dessus"
echo "   • Vérifier les indexes de performance"
echo "   • Tester les connexions à la base de données"
echo ""
