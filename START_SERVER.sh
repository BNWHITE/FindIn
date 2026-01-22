#!/bin/bash

cat << 'EOF'

╔════════════════════════════════════════════════════════╗
║  🚀 SOLUTION SIMPLE: Serveur PHP Intégré              ║
╚════════════════════════════════════════════════════════╝

Sans Apache, sans configuration, ça marche tout de suite!

📝 ÉTAPE 1: Démarre le serveur PHP
───────────────────────────────────

Copie-colle cette commande dans le terminal:

    cd /Applications/XAMPP/htdocs/FindIn/public && php -S localhost:8000

Tu dois voir:
    ✅ [Mon Jan 22 13:45:00 2026] PHP 8.x.x Development Server
    ✅ Listening on http://localhost:8000

🌐 ÉTAPE 2: Ouvre le navigateur
───────────────────────────────

  👉 http://localhost:8000/

Tu dois voir la page d'accueil FindIN!

🔐 ÉTAPE 3: Test les routes
─────────────────────────────

  ✅ http://localhost:8000/               (accueil)
  ✅ http://localhost:8000/login          (connexion)
  ✅ http://localhost:8000/register       (inscription)
  ✅ http://localhost:8000/invalid        (404)

═══════════════════════════════════════════════════════

⚠️  NOTES:

  • Ce serveur PHP est JUSTE POUR LE DÉVELOPPEMENT
  • Ne l'utilise pas en production!
  • Il ne peut servir qu'un client à la fois
  • Pour arrêter, appuie sur Ctrl+C dans le terminal

═══════════════════════════════════════════════════════

✨ C'EST TOUT! Laisse le serveur tourner et navigue!

EOF

echo ""
echo "🎯 Commande prête à copier-coller:"
echo ""
echo "cd /Applications/XAMPP/htdocs/FindIn/public && php -S localhost:8000"
echo ""
