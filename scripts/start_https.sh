#!/bin/bash
# Script pour démarrer le serveur FindIN en HTTPS
# Utilise Caddy comme reverse proxy HTTPS

echo ""
echo "🚀 Démarrage de FindIN en HTTPS..."
echo ""

# Arrêter les serveurs existants
pkill -f "php -S localhost:8000" 2>/dev/null
pkill caddy 2>/dev/null
sleep 1

# Démarrer le serveur PHP sur le port 8000
echo "📦 Démarrage du serveur PHP..."
php -S localhost:8000 router.php > /dev/null 2>&1 &
PHP_PID=$!

sleep 2

# Vérifier que PHP tourne
if ! ps -p $PHP_PID > /dev/null 2>&1; then
    echo "❌ Erreur: Le serveur PHP n'a pas démarré"
    exit 1
fi

echo "✅ Serveur PHP démarré sur http://localhost:8000"

# Vérifier si Caddy est installé
if ! command -v caddy &> /dev/null; then
    echo "❌ Caddy n'est pas installé. Installez-le avec:"
    echo "   brew install caddy"
    echo ""
    echo "En attendant, le site est accessible en HTTP:"
    echo "   http://localhost:8000"
    wait $PHP_PID
    exit 0
fi

# Démarrer Caddy en mode reverse proxy HTTPS
echo "🔒 Démarrage de Caddy HTTPS..."
caddy reverse-proxy --from :8443 --to :8000 2>/dev/null &
CADDY_PID=$!

sleep 2

if ! ps -p $CADDY_PID > /dev/null 2>&1; then
    echo "⚠️  Caddy n'a pas démarré, le site reste accessible en HTTP"
    echo ""
    echo "=========================================="
    echo "🌐 FindIN MVP - HTTP uniquement:"
    echo "   http://localhost:8000"
    echo "=========================================="
    wait $PHP_PID
    exit 0
fi

echo "✅ Caddy HTTPS démarré sur https://localhost:8443"
echo ""
echo "=========================================="
echo "🌐 FindIN MVP est accessible sur:"
echo ""
echo "   HTTP:  http://localhost:8000"
echo "   HTTPS: https://localhost:8443"
echo ""
echo "=========================================="
echo ""
echo "⚠️  Note: Le certificat est auto-signé."
echo "    Acceptez l'avertissement du navigateur."
echo ""
echo "Pour arrêter: Ctrl+C"
echo ""

# Trap pour nettoyer à l'arrêt
trap "echo ''; echo '🛑 Arrêt des serveurs...'; kill $PHP_PID $CADDY_PID 2>/dev/null; echo '✅ Serveurs arrêtés'; exit 0" SIGINT SIGTERM

# Attendre
wait
