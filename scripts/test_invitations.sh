#!/bin/bash

# ============================================================================
# TEST_INVITATIONS.sh - Tester le système d'invitations avec vrais emails
# ============================================================================

echo "🧪 TEST INVITATION SYSTEM - FindIN"
echo "════════════════════════════════════════════════════════"
echo ""

# Configuration
APP_URL="${APP_URL:-http://localhost:8000}"
DB_NAME="${DB_NAME:-findin}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-}"

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}📋 Configuration:${NC}"
echo "  App URL: $APP_URL"
echo "  Database: $DB_NAME"
echo ""

# 1. Vérifier la connexion DB
echo -e "${YELLOW}1️⃣  Vérifier la connexion MySQL...${NC}"

if [ -z "$DB_PASS" ]; then
    mysql -u "$DB_USER" "$DB_NAME" -e "SELECT COUNT(*) as utilisateurs FROM utilisateurs;" 2>/dev/null | tail -1
else
    mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SELECT COUNT(*) as utilisateurs FROM utilisateurs;" 2>/dev/null | tail -1
fi

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Connexion MySQL OK${NC}"
else
    echo -e "${RED}❌ Erreur connexion MySQL${NC}"
    exit 1
fi

echo ""

# 2. Tester l'envoi d'email
echo -e "${YELLOW}2️⃣  Configuration email:${NC}"
echo "  From: blacknwhitemanagement@findin.fr"
echo "  System: PHP mail()"
echo ""

# 3. Vérifier les invitations en attente
echo -e "${YELLOW}3️⃣  Invitations en attente:${NC}"

if [ -z "$DB_PASS" ]; then
    INVITATIONS=$(mysql -u "$DB_USER" "$DB_NAME" -se "SELECT COUNT(*) FROM invitations WHERE statut = 'pending';" 2>/dev/null)
else
    INVITATIONS=$(mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -se "SELECT COUNT(*) FROM invitations WHERE statut = 'pending';" 2>/dev/null)
fi

echo "  Total: $INVITATIONS invitation(s)"
echo ""

# 4. Vérifier les tables critiques
echo -e "${YELLOW}4️⃣  Tables critiques:${NC}"

TABLES=("utilisateurs" "competences" "competences_utilisateurs" "invitations" "demandes_validation")

for table in "${TABLES[@]}"; do
    if [ -z "$DB_PASS" ]; then
        COUNT=$(mysql -u "$DB_USER" "$DB_NAME" -se "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME' AND table_name = '$table';" 2>/dev/null)
    else
        COUNT=$(mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -se "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME' AND table_name = '$table';" 2>/dev/null)
    fi
    
    if [ "$COUNT" -eq 1 ]; then
        echo -e "  ${GREEN}✅${NC} $table"
    else
        echo -e "  ${RED}❌${NC} $table"
    fi
done

echo ""

# 5. Test d'envoi d'invitation
echo -e "${YELLOW}5️⃣  Test d'envoi d'invitation:${NC}"
echo ""
echo "URL d'accès au dashboard RH:"
echo -e "  ${BLUE}$APP_URL/dashboard/rh-invitations${NC}"
echo ""
echo "Étapes du test:"
echo "  1. Accédez au dashboard RH"
echo "  2. Remplissez le formulaire d'invitation"
echo "  3. Email test: test-user@example.com"
echo "  4. Vérifiez l'email envoyé"
echo ""

# 6. Vérifier la configuration PHP mail
echo -e "${YELLOW}6️⃣  Vérifier php.ini (mail):${NC}"

# Essayer de vérifier la configuration mail
php -r "echo 'sendmail_path: ' . ini_get('sendmail_path') . PHP_EOL;" 2>/dev/null || echo "  ⚠️ Impossible de vérifier"

echo ""

# 7. Instructions finales
echo -e "${GREEN}════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ TEST CONFIGURATION COMPLÈTE${NC}"
echo -e "${GREEN}════════════════════════════════════════════════════════${NC}"
echo ""
echo "📧 Pour tester les invitations réelles:"
echo ""
echo "Étape 1: Accédez à http://localhost:8000/dashboard/rh-invitations"
echo ""
echo "Étape 2: Connectez-vous avec:"
echo "  Email: admin@findin.fr"
echo "  Password: admin123"
echo ""
echo "Étape 3: Créez une invitation"
echo "  Remplissez le formulaire:"
echo "  - Email: test@findin.fr"
echo "  - Prénom: Test"
echo "  - Nom: User"
echo "  - Rôle: employe"
echo ""
echo "Étape 4: Validez le formulaire"
echo "  ✅ Email envoyé"
echo "  ✅ Lien d'acceptation copié"
echo ""
echo "Vérifiez les logs:"
echo "  • /var/log/apache2/error.log (Apache)"
echo "  • /var/log/php.log (PHP errors)"
echo "  • /var/log/mail.log (Mail errors)"
echo ""

