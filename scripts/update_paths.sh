#!/bin/bash

# Script pour mettre à jour tous les chemins après réorganisation

cd /Users/evanmse/Documents/Github/FindIn

echo "🔄 Mise à jour des chemins dans src/Controllers/..."
# Dans les Controllers : models/ -> __DIR__ . '/../Models/'
find src/Controllers -name "*.php" -exec sed -i '' "s|require_once 'models/|require_once __DIR__ . '/../Models/|g" {} \;
find src/Controllers -name "*.php" -exec sed -i '' "s|require_once 'controllers/|require_once __DIR__ . '/../Controllers/|g" {} \;
find src/Controllers -name "*.php" -exec sed -i '' "s|require_once 'views/|require_once __DIR__ . '/../Views/|g" {} \;
find src/Controllers -name "*.php" -exec sed -i '' "s|require_once 'lib/|require_once __DIR__ . '/../Lib/|g" {} \;

echo "🔄 Mise à jour des chemins dans src/Models/..."
# Dans les Models : models/ -> __DIR__ .
find src/Models -name "*.php" -exec sed -i '' "s|require_once 'models/|require_once __DIR__ . '/|g" {} \;

echo "🔄 Mise à jour des chemins relatifs dans src/Views/..."
# Dans les Views : chemins relatifs depuis la racine
find src/Views -name "*.php" -exec sed -i '' "s|require_once 'models/|require_once __DIR__ . '/../../Models/|g" {} \;
find src/Views -name "*.php" -exec sed -i '' "s|require_once 'controllers/|require_once __DIR__ . '/../../Controllers/|g" {} \;
find src/Views -name "*.php" -exec sed -i '' "s|require_once 'config/|require_once __DIR__ . '/../../Config/|g" {} \;
find src/Views -name "*.php" -exec sed -i '' "s|require_once 'lib/|require_once __DIR__ . '/../../Lib/|g" {} \;

echo "🔄 Mise à jour des chemins dans src/Lib/..."
find src/Lib -name "*.php" -exec sed -i '' "s|require_once 'models/|require_once __DIR__ . '/../Models/|g" {} \;
find src/Lib -name "*.php" -exec sed -i '' "s|require_once 'config/|require_once __DIR__ . '/../Config/|g" {} \;

echo "✅ Chemins mis à jour!"
