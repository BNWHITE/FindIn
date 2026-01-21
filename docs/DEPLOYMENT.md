# 🚀 Guide de Déploiement FindIN

## Architecture Cloud Cible

```
┌─────────────┐      ┌─────────────┐      ┌─────────────┐
│   GitHub    │──────│   Railway   │──────│  Supabase   │
│   (Code)    │ push │   (PHP)     │ SQL  │ (PostgreSQL)│
└─────────────┘      └─────────────┘      └─────────────┘
```

---

## Étape 1 : Configurer Supabase

### 1.1 Créer un projet Supabase

1. Aller sur [supabase.com](https://supabase.com)
2. Se connecter avec GitHub
3. Cliquer "New Project"
4. Configurer :
   - **Name** : `findin`
   - **Password** : (choisir un mot de passe fort - **NOTEZ-LE**)
   - **Region** : `eu-west-3` (Paris)
5. Attendre 2 minutes

### 1.2 Créer les tables

1. Aller dans **SQL Editor** (icône terminal)
2. Cliquer **New query**
3. Copier-coller TOUT le contenu de `database/supabase_schema.sql`
4. Cliquer **Run**
5. Vérifier le message "Success"

### 1.3 Récupérer les credentials

1. Aller dans **Project Settings** (⚙️)
2. Cliquer **Database**
3. Section **Connection string** → onglet **URI**

Vous verrez :
```
postgresql://postgres.[PROJECT-REF]:[PASSWORD]@aws-0-eu-west-3.pooler.supabase.com:6543/postgres
```

**Notez ces valeurs :**
- Host : `aws-0-eu-west-3.pooler.supabase.com`
- Port : `6543`
- User : `postgres.[VOTRE-PROJECT-REF]`
- Password : votre mot de passe

---

## Étape 2 : Préparer GitHub

### 2.1 Commit du code

```bash
cd /Users/evanmse/Documents/Github/FindIn

# Vérifier les fichiers modifiés
git status

# Ajouter tous les fichiers
git add .

# Commit
git commit -m "feat: migration Supabase + config Railway"

# Push
git push origin main
```

### 2.2 Vérifier que les secrets ne sont PAS dans le repo

Le fichier `src/Config/database.php` doit être dans `.gitignore` !

---

## Étape 3 : Déployer sur Railway

### 3.1 Créer un compte Railway

1. Aller sur [railway.app](https://railway.app)
2. Se connecter avec GitHub

### 3.2 Créer un nouveau projet

1. Cliquer **New Project**
2. Choisir **Deploy from GitHub repo**
3. Sélectionner `BNWHITE/FindIn`
4. Railway détecte automatiquement PHP grâce à `nixpacks.toml`

### 3.3 Configurer les variables d'environnement

Dans Railway, aller dans **Variables** et ajouter :

| Variable | Valeur |
|----------|--------|
| `DB_TYPE` | `supabase` |
| `SUPABASE_HOST` | `aws-0-eu-west-3.pooler.supabase.com` |
| `SUPABASE_PORT` | `6543` |
| `SUPABASE_DB` | `postgres` |
| `SUPABASE_USER` | `postgres.VOTRE_PROJECT_REF` |
| `SUPABASE_PASS` | `VOTRE_MOT_DE_PASSE` |
| `APP_URL` | `https://findin-production.up.railway.app` |
| `DEBUG_MODE` | `false` |

### 3.4 Générer un domaine

1. Dans Railway, aller dans **Settings**
2. Section **Domains**
3. Cliquer **Generate Domain**
4. Vous obtiendrez une URL comme : `findin-production.up.railway.app`

---

## Étape 4 : Vérifier le déploiement

### 4.1 Tester l'application

1. Ouvrir votre URL Railway
2. Se connecter avec :
   - Email : `admin@findin.fr`
   - Mot de passe : `password`

### 4.2 Vérifier les logs

Dans Railway :
1. Aller dans **Deployments**
2. Cliquer sur le dernier déploiement
3. Onglet **Logs**

---

## 🔧 Dépannage

### Erreur "could not find driver"
→ L'extension `pdo_pgsql` manque. Vérifier `nixpacks.toml`.

### Erreur de connexion Supabase
→ Vérifier les variables d'environnement dans Railway.
→ Vérifier que l'IP Railway n'est pas bloquée dans Supabase (Settings > Database > Connection pooler).

### Page blanche
→ Activer `DEBUG_MODE=true` temporairement pour voir les erreurs.

### Tables non trouvées
→ Exécuter le SQL dans Supabase SQL Editor.

---

## 📊 Coûts estimés

| Service | Tier gratuit | Limites |
|---------|--------------|---------|
| **Supabase** | Gratuit | 500MB BDD, 1GB stockage |
| **Railway** | $5/mois crédits | 500h d'exécution |
| **GitHub** | Gratuit | Illimité repos publics |

💡 **Astuce** : Pour un projet étudiant, Railway offre $5/mois gratuits, ce qui est suffisant.

---

## 🔄 Workflow de développement

```
Local (XAMPP/SQLite)  →  GitHub  →  Railway (Supabase)
      Dev                 Push        Production
```

1. Développer en local avec SQLite ou MySQL
2. Commit et push sur GitHub
3. Railway déploie automatiquement
4. L'app en prod utilise Supabase

---

## Checklist finale

- [ ] Projet Supabase créé
- [ ] Tables créées via SQL Editor
- [ ] Code poussé sur GitHub
- [ ] Projet Railway créé et lié à GitHub
- [ ] Variables d'environnement configurées
- [ ] Domaine généré
- [ ] Test de connexion réussi
- [ ] Arrêter XAMPP ! 🎉
