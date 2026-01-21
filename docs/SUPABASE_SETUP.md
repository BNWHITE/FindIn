# 🚀 Configuration Supabase pour FindIN

## 1. Créer un compte Supabase

1. Aller sur [https://supabase.com](https://supabase.com)
2. Cliquer sur "Start your project"
3. Se connecter avec GitHub (recommandé) ou email

## 2. Créer un nouveau projet

1. Cliquer sur "New Project"
2. Remplir les informations :
   - **Name**: `findin` (ou autre nom de votre choix)
   - **Database Password**: Choisir un mot de passe fort (📝 **NOTEZ-LE !**)
   - **Region**: `eu-west-3` (Paris) pour la France
3. Cliquer sur "Create new project"
4. Attendre la création (~2 minutes)

## 3. Récupérer les credentials de connexion

1. Dans votre projet Supabase, aller dans **Project Settings** (icône engrenage ⚙️)
2. Cliquer sur **Database** dans le menu de gauche
3. Trouver la section **Connection string** 
4. Cliquer sur l'onglet **URI**

Vous verrez quelque chose comme :
```
postgresql://postgres.[PROJECT-REF]:[PASSWORD]@aws-0-eu-west-3.pooler.supabase.com:6543/postgres
```

Décomposition :
- **Host**: `aws-0-eu-west-3.pooler.supabase.com`
- **Port**: `6543` (Transaction pooler) ou `5432` (Session pooler)
- **Database**: `postgres`
- **User**: `postgres.[VOTRE_PROJECT_REF]`
- **Password**: Le mot de passe que vous avez créé

## 4. Configurer FindIN

Ouvrez le fichier `src/Config/database.php` et modifiez ces lignes :

```php
// =============================================================================
// SUPABASE Configuration (PostgreSQL)
// =============================================================================
define('SUPABASE_HOST', 'aws-0-eu-west-3.pooler.supabase.com');
define('SUPABASE_PORT', '6543');
define('SUPABASE_DB', 'postgres');
define('SUPABASE_USER', 'postgres.VOTRE_PROJECT_REF');  // ⬅️ Remplacez !
define('SUPABASE_PASS', 'VOTRE_MOT_DE_PASSE');          // ⬅️ Remplacez !
```

**Important** : `DB_TYPE` doit être configuré sur `'supabase'` :
```php
define('DB_TYPE', 'supabase');
```

## 5. Créer les tables dans Supabase

1. Dans Supabase, aller dans **SQL Editor** (icône terminal)
2. Cliquer sur **New query**
3. Copier-coller tout le contenu du fichier `database/supabase_schema.sql`
4. Cliquer sur **Run** (ou Ctrl+Enter)

Vérifier que vous voyez : ✅ `Success. No rows returned`

## 6. Vérifier les tables créées

1. Aller dans **Table Editor** (icône tableau)
2. Vous devriez voir les tables :
   - `utilisateurs`
   - `competences`
   - `competences_utilisateurs`
   - `departements`
   - `projets`
   - `categories_competences`
   - etc.

## 7. Vérifier la connexion PHP

Assurez-vous que l'extension PDO PostgreSQL est activée dans PHP :

```bash
php -m | grep pgsql
```

Si absent, activez-la dans `php.ini` :
```ini
extension=pdo_pgsql
```

## 8. Tester l'application

1. Lancer le serveur :
```bash
php -S localhost:8000 -t public
```

2. Ouvrir http://localhost:8000

3. Se connecter avec :
   - **Email**: `admin@findin.fr`
   - **Mot de passe**: `password`

## 🔧 Dépannage

### Erreur "could not find driver"
→ L'extension `pdo_pgsql` n'est pas installée.
- macOS: `brew install php@8.2` (inclut pgsql)
- Linux: `sudo apt install php-pgsql`
- XAMPP: Décommenter `extension=pdo_pgsql` dans php.ini

### Erreur "FATAL: password authentication failed"
→ Vérifiez que le mot de passe dans `database.php` est correct.
→ Vérifiez que vous utilisez le bon `PROJECT_REF` dans le username.

### Erreur "connection timed out"
→ Vérifiez votre connexion internet.
→ Essayez le port `5432` au lieu de `6543`.

### Erreur "relation does not exist"
→ Les tables n'ont pas été créées. Exécutez le SQL dans l'éditeur Supabase.

## 📊 Comparaison MySQL vs Supabase

| Aspect | XAMPP MySQL | Supabase |
|--------|-------------|----------|
| Installation | Locale | Cloud |
| Maintenance | Manuelle | Automatique |
| Backups | Manuels | Automatiques |
| Sécurité | À configurer | RLS intégré |
| Scalabilité | Limitée | Automatique |
| Prix | Gratuit | Gratuit (tier free) |

## 🔐 Variables d'environnement (Production)

Pour éviter de mettre les credentials dans le code, utilisez des variables d'environnement :

```bash
export SUPABASE_HOST="aws-0-eu-west-3.pooler.supabase.com"
export SUPABASE_PORT="6543"
export SUPABASE_DB="postgres"
export SUPABASE_USER="postgres.xxxx"
export SUPABASE_PASS="votre-mot-de-passe"
export DB_TYPE="supabase"
```

Le fichier `database.php` utilisera automatiquement ces variables via `getenv()`.
