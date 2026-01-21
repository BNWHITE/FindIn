#!/bin/bash

echo "🔧 Configuration Apache pour la nouvelle structure"
echo ""

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Vérifier si le fichier existe
if [ ! -f "httpd-vhosts-new.conf" ]; then
    echo -e "${RED}❌ Erreur: httpd-vhosts-new.conf introuvable${NC}"
    exit 1
fi

echo -e "${YELLOW}⚠️  Cette opération va modifier la configuration Apache${NC}"
echo "Fichier à modifier: /Applications/XAMPP/etc/extra/httpd-vhosts.conf"
echo ""
echo "Changements:"
echo "  - DocumentRoot: /Applications/XAMPP/htdocs/findin → /Applications/XAMPP/htdocs/findin/public"
echo ""

read -p "Continuer? (o/N) " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Oo]$ ]]; then
    echo "Opération annulée"
    exit 0
fi

echo ""
echo "🔄 Copie de la nouvelle configuration..."
sudo cp httpd-vhosts-new.conf /Applications/XAMPP/etc/extra/httpd-vhosts.conf

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Configuration copiée${NC}"
    echo ""
    echo "🔄 Redémarrage d'Apache..."
    sudo /Applications/XAMPP/xamppfiles/bin/apachectl restart
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Apache redémarré avec succès${NC}"
        echo ""
        echo -e "${GREEN}🎉 Configuration terminée !${NC}"
        echo ""
        echo "Testez maintenant:"
        echo "  👉 http://findin.local/"
        echo "  👉 http://findin.local/login"
        echo ""
    else
        echo -e "${RED}❌ Erreur lors du redémarrage d'Apache${NC}"
        echo "Vérifiez la configuration avec: sudo apachectl configtest"
        exit 1
    fi
else
    echo -e "${RED}❌ Erreur lors de la copie de la configuration${NC}"
    exit 1
fi
