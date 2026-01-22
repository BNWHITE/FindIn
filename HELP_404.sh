#!/bin/bash

cat << 'EOF'

╔════════════════════════════════════════════════════════╗
║       🆘 RÉSOUDRE LE PROBLÈME 404                     ║
╚════════════════════════════════════════════════════════╝

⚠️  PROBLÈME: http://localhost/FindIn/public → 404

🔍 DIAGNOSTIC PAS À PAS:

ÉTAPE 1️⃣: Vérifie qu'Apache fonctionne
───────────────────────────────────────
  ✅ Lance XAMPP et démarre Apache
  ✅ Va à: http://localhost/
  ✅ Tu dois voir la page XAMPP par défaut

ÉTAPE 2️⃣: Test le debug script
──────────────────────────────
  ✅ Va à: http://localhost/FindIn/public/debug.php
  ✅ Tu dois voir les informations REQUEST_URI

ÉTAPE 3️⃣: Vérifie que le .htaccess fonctionne
───────────────────────────────────────────────
  ✅ Si debug.php montre que REQUEST_URI = /FindIn/public/debug.php
     → Apache lit le fichier directement ✅
  ✅ Si tu vois "NOT SET" partout
     → Problème Apache ❌

ÉTAPE 4️⃣: Test les fichiers PHP directement
──────────────────────────────────────────────
  Depuis le terminal, exécute:
  
  php /Applications/XAMPP/htdocs/FindIn/public/index.php
  
  Tu dois voir du HTML (la page d'accueil)

ÉTAPE 5️⃣: Si tout échoue → Reconfigure Apache
────────────────────────────────────────────────
  Les problèmes possibles:
  
  ❌ DocumentRoot ne pointe pas vers FindIn
  ❌ mod_rewrite n'est pas activé
  ❌ .htaccess n'est pas autorisé (AllowOverride)
  ❌ PHP n'est pas installé correctement

═══════════════════════════════════════════════════════

🚀 SOLUTION RAPIDE: Utilise directement le PHP

  Tape ceci dans le terminal:
  
  php -S localhost:8000 -t /Applications/XAMPP/htdocs/FindIn/public
  
  Puis va à: http://localhost:8000/

  Cela lance un serveur PHP intégré sans Apache!

═══════════════════════════════════════════════════════

📋 FICHIERS À VÉRIFIER:

  ✅ /Applications/XAMPP/htdocs/FindIn/public/index.php        (22KB)
  ✅ /Applications/XAMPP/htdocs/FindIn/public/.htaccess        (439B)
  ✅ /Applications/XAMPP/htdocs/FindIn/src/Views/index.php     (2.4KB)
  ✅ /Applications/XAMPP/htdocs/FindIn/src/Views/404.php       (2.3KB)

═══════════════════════════════════════════════════════

💬 Si rien ne marche, dis-moi:
  1. Quel message d'erreur tu vois?
  2. Le résultat de: http://localhost/FindIn/public/debug.php
  3. Le résultat de: php -v (version PHP)

EOF
