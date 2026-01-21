# FindIN MCP Server

Serveur MCP (Model Context Protocol) pour analyser et améliorer le projet FindIN avec XAMPP.

## Installation

1. **Installer les dépendances Python** :
```bash
cd mcp
pip install -r requirements.txt
```

2. **Configurer XAMPP** :
   - Assurez-vous que MySQL est démarré dans XAMPP
   - Base de données : `gestion_competences`
   - Utilisateur : `root` (sans mot de passe par défaut)

## Configuration

Le serveur se connecte par défaut à :
- Host: `localhost`
- Port: `3306`
- Database: `gestion_competences`
- User: `root`
- Password: `` (vide)

Pour modifier la configuration, éditez `DB_CONFIG` dans `server.py`.

## Utilisation avec Claude Desktop

1. **Ajouter à votre configuration Claude Desktop** (`~/Library/Application Support/Claude/claude_desktop_config.json` sur macOS) :

```json
{
  "mcpServers": {
    "findin": {
      "command": "python",
      "args": ["/chemin/absolu/vers/FindIn/mcp/server.py"]
    }
  }
}
```

2. **Redémarrer Claude Desktop**

3. **Utiliser les outils** - Le serveur expose 8 outils :

### 🔍 Outils disponibles

#### `query_database`
Exécute une requête SQL SELECT sur la base de données.
```
Exemple: SELECT * FROM utilisateurs LIMIT 5
```

#### `get_table_structure`
Obtient la structure d'une table (colonnes, types, clés).
```
Exemple: utilisateurs
```

#### `list_tables`
Liste toutes les tables de la base de données.

#### `analyze_php_file`
Analyse un fichier PHP (classes, méthodes, lignes de code).
```
Exemple: src/Models/User.php
```

#### `get_project_stats`
Statistiques globales du projet (fichiers, lignes de code).

#### `check_database_consistency`
Vérifie la cohérence de la base de données (tables manquantes).

#### `get_user_competences`
Obtient les compétences d'un utilisateur spécifique.
```
Exemple: user-123456
```

#### `search_code_pattern`
Recherche un pattern dans les fichiers PHP.
```
Exemple: getInstance
```

## Exemples d'utilisation

### Vérifier la structure de la base

```
Utilisez l'outil list_tables pour voir toutes les tables
Puis get_table_structure pour analyser chaque table
```

### Analyser le code

```
Utilisez analyze_php_file pour examiner:
- src/Models/Database.php
- src/Controllers/DashboardController.php
- src/Models/User.php
```

### Rechercher des patterns

```
Utilisez search_code_pattern pour trouver:
- "password_hash" (gestion des mots de passe)
- "getInstance" (pattern Singleton)
- "session_start" (gestion des sessions)
```

### Vérifier les données

```
Utilisez query_database:
- SELECT * FROM utilisateurs WHERE role = 'admin'
- SELECT COUNT(*) FROM competences
- SELECT * FROM user_competences LIMIT 10
```

## Tests manuels

Pour tester le serveur en ligne de commande :

```bash
python server.py
```

Le serveur attend des commandes MCP sur stdin et répond sur stdout.

## Dépannage

### Erreur de connexion MySQL
- Vérifiez que XAMPP MySQL est démarré
- Vérifiez le nom de la base de données dans `DB_CONFIG`
- Testez la connexion : `mysql -u root -h localhost gestion_competences`

### Module 'mcp' non trouvé
```bash
pip install mcp
```

### Erreur mysql-connector
```bash
pip install mysql-connector-python
```

## Architecture

```
FindIN/
├── mcp/
│   ├── server.py          # Serveur MCP principal
│   ├── requirements.txt   # Dépendances Python
│   └── README.md          # Cette documentation
```

Le serveur implémente le protocole MCP standard et expose des outils spécifiques au projet FindIN.

## Sécurité

⚠️ **Attention** : Ce serveur est conçu pour un usage local en développement.

- Seules les requêtes SELECT sont autorisées (lecture seule)
- Pas d'INSERT, UPDATE ou DELETE via MCP
- Connexion locale uniquement (localhost)

## Support

Pour les problèmes ou questions :
1. Vérifiez les logs dans le terminal
2. Testez la connexion MySQL manuellement
3. Vérifiez que toutes les dépendances sont installées
