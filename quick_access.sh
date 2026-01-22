#!/bin/bash

# quick_access.sh - Accès rapide à FindIN

cat << 'EOF'

╔════════════════════════════════════════════════════════╗
║      🚀 ACCÈS RAPIDE À FindIN                         ║
╚════════════════════════════════════════════════════════╝

📍 ACCUEIL (Page d'accueil)
   👉 http://localhost/FindIn/public/

🔐 AUTHENTIFICATION
   👉 Connexion:    http://localhost/FindIn/public/login
   👉 Inscription:  http://localhost/FindIn/public/register

📊 TABLEAU DE BORD (après connexion)
   👉 http://localhost/FindIn/public/dashboard

📋 AUTRES PAGES
   👉 À propos:    http://localhost/FindIn/public/about
   👉 Contact:     http://localhost/FindIn/public/contact
   👉 FAQ:         http://localhost/FindIn/public/faq

⚠️  DIAGNOSTIC & TEST
   👉 Diagnostic:  http://localhost/FindIn/public/diagnostic.php
   👉 Routes:      bash /Users/s.sy/Documents/ISEP/FindIn/test_routes.sh

════════════════════════════════════════════════════════

💡 CONSEIL: Pour accès plus rapide sur Mac, crée un alias:

  Ajoute cette ligne à ~/.zshrc :
  
  alias findin="open 'http://localhost/FindIn/public/'"
  
  Puis relance le terminal et tape simplement:
  
  findin

════════════════════════════════════════════════════════

✅ Prêt? Ouvre ton navigateur et va à:
   http://localhost/FindIn/public/

EOF
