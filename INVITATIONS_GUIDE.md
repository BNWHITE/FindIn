# 📧 FindIN - Système d'Invitations Amélioré
**Version 2025 - Mode Clair/Sombre + Email + Base de Données Optimisée**

---

## ✨ Nouveautés

### 1. 🎨 Interface RH avec Mode Clair/Sombre
✅ **Page `/dashboard/rh-invitations`**
- **Thème Dark** (par défaut): Gradient purple/blue avec confort visuel optimal
- **Thème Light**: Mode clair professionnel pour utilisation en plein jour
- **Toggle Button**: Bouton theme à côté du logo dans la sidebar
- **Persistent**: Le choix du thème est sauvegardé en localStorage
- **Responsive**: Entièrement adaptatif (desktop, tablet, mobile)

**Améliorations CSS:**
- Utilisation de CSS variables pour les couleurs
- Transition smooth entre les thèmes
- Better contrast en mode clair
- Icons Font Awesome 6.4.0

### 2. ✉️ Système d'Invitations Réelles
✅ **Envoi d'emails authentiques**
- **Email From**: `blacknwhitemanagement@findin.fr`
- **Système**: PHP `mail()` avec headers HTML
- **Template**: HTML professionnel avec logo et branding
- **Lien d'acceptation**: Token unique valide 7 jours
- **Confirmation**: Message de succès avec copie du lien

**Configuration EmailSender:**
```php
From: blacknwhitemanagement@findin.fr
From Name: FindIN - Gestion des Compétences
Support: contact@findin.fr
```

### 3. 🗄️ Base de Données Optimisée
✅ **MySQL Upgrade complet**

**Améliorations:**
- ✅ Indexes optimisés pour la recherche rapide
- ✅ Constraints de clés étrangères
- ✅ Colonnes de gestion (created_at, updated_at)
- ✅ Charset UTF8MB4 pour l'unicode
- ✅ Statut de validation des compétences
- ✅ Gestion des départements
- ✅ Support connection pooling (PDO persistent)

**Nouvelles Colonnes:**
```sql
-- utilisateurs
- id_departement INT
- manager_id INT
- actif TINYINT(1)

-- competences_utilisateurs
- statut VARCHAR(50) -- 'en_attente', 'approuve', 'rejete'
- commentaire TEXT
- date_declaration TIMESTAMP

-- invitations
- departement VARCHAR(255)
- accepted_at TIMESTAMP

-- competences
- niveau_min INT (default: 1)
- niveau_max INT (default: 5)
```

---

## 🚀 Utilisation

### Accéder au Dashboard RH
```
http://localhost:8000/dashboard/rh-invitations
```

**Identifiants admin:**
```
Email: admin@findin.fr
Password: admin123
```

### Créer une Invitation

1. **Remplir le formulaire:**
   - Email: `nouveau.collaborateur@company.com`
   - Prénom: `Jean`
   - Nom: `Dupont`
   - Département: `IT`
   - Rôle: `employe` (ou `manager`, `rh`)
   - Manager: (optionnel)

2. **Soumettre**: Clique sur "Envoyer l'invitation"

3. **Email envoyé**: 
   - Template HTML professionnel
   - Lien d'acceptation unique
   - Valide pendant 7 jours

4. **Acceptation**:
   - Collaborateur clique le lien
   - Créer un mot de passe
   - Compte créé automatiquement

### Basculer Thème Clair/Sombre

- Cliquez l'icône 🌙 (lune) dans la sidebar
- Change en 💛 (soleil) quand en mode clair
- Choix sauvegardé automatiquement

---

## 🔧 Configuration Technique

### EmailSender (`src/Lib/EmailSender.php`)
```php
From email: blacknwhitemanagement@findin.fr
From name: FindIN - Gestion des Compétences

// Méthode statique pour envoyer
EmailSender::sendInvitation(
    $token, 
    $email, 
    $prenom, 
    $nom, 
    $lien_acceptation
);
```

### Database Configuration (`src/Config/database.php`)

**Connexion améliorée:**
```php
// Auto-config depuis variables d'environnement
DB_HOST   = localhost (or env: DB_HOST)
DB_PORT   = 3306 (or env: DB_PORT)
DB_NAME   = findin (or env: DB_NAME)
DB_USER   = root (or env: DB_USER)
DB_PASS   = '' (or env: DB_PASS)

// Options PDO optimisées:
- PDO::ATTR_ERRMODE => EXCEPTION
- PDO::ATTR_TIMEOUT => 10
- PDO::ATTR_EMULATE_PREPARES => false
- UTF8MB4 charset

// Support connection pooling (décommenter pour utiliser):
// PDO::ATTR_PERSISTENT => true
```

### Variables d'Environnement

```bash
# Créer .env ou exporter:
export DB_HOST=localhost
export DB_PORT=3306
export DB_NAME=findin
export DB_USER=root
export DB_PASS=''
export DB_TYPE=mysql
export APP_URL=http://localhost:8000
```

---

## 📊 Structure Base de Données

### Tables Principales

```
utilisateurs
├─ id_utilisateur (PK)
├─ email (UNIQUE)
├─ prenom, nom
├─ role (employe, manager, rh, admin)
├─ manager_id (FK)
├─ departement_id (FK)
└─ actif (TINYINT)

competences
├─ id_competence (PK)
├─ nom (UNIQUE)
├─ description
├─ type_competence (technique, soft, métier)
├─ niveau_min, niveau_max
└─ cree_le

competences_utilisateurs (Junction)
├─ user_id (FK)
├─ id_competence (FK)
├─ niveau_declare (1-5)
├─ niveau_valide (1-5)
├─ statut (en_attente, approuve, rejete)
├─ commentaire
└─ date_validation

invitations
├─ id (PK)
├─ email, prenom, nom
├─ token (UNIQUE)
├─ role
├─ manager_id (FK)
├─ statut (pending, accepted, expired)
├─ expires_at
├─ accepted_at
├─ user_id (FK après acceptation)
└─ cree_le

departements
├─ id_departement (PK)
├─ nom (UNIQUE)
├─ description
├─ responsable_id (FK)
├─ actif
└─ cree_le

demandes_validation
├─ id (PK)
├─ user_id (FK)
├─ manager_id (FK)
├─ competence_id (FK)
├─ niveau_declare
├─ statut (en_attente, approuve, rejete)
├─ commentaire
└─ validee_le
```

---

## 🧪 Tests

### Test Complet du Système

```bash
# 1. Vérifier les tables
bash scripts/test_invitations.sh

# 2. Vérifier la connexion MySQL
mysql -u root -e "USE findin; SELECT COUNT(*) FROM utilisateurs;"

# 3. Consulter les logs
tail -f /var/log/mail.log  # Emails envoyés
tail -f /var/log/apache2/error.log  # Erreurs
```

### Créer une Invitation de Test

```sql
-- Insérer une invitation de test
INSERT INTO invitations (
    email, prenom, nom, token, role, statut, expires_at
) VALUES (
    'test@company.com', 
    'Test', 
    'User',
    'test_token_' + MD5(RAND()),
    'employe',
    'pending',
    DATE_ADD(NOW(), INTERVAL 7 DAY)
);
```

---

## 🎯 Flux d'Utilisation

### Cas 1: RH crée une invitation

```
RH → Dashboard RH → Formulaire invitation → Email envoyé ✅
                                    ↓
                            Collaborateur reçoit email
                                    ↓
                        Clique lien d'acceptation
                                    ↓
                        Crée mot de passe
                                    ↓
                        Compte créé ✅
```

### Cas 2: Collaborateur déclare compétence

```
Collaborateur → Déclare compétence + niveau
                        ↓
                Manager reçoit demande validation
                        ↓
                Manager approuve/rejette
                        ↓
                Compétence validée ✅
```

---

## 📱 Responsive Design

### Breakpoints

```css
Desktop    ≥ 1024px  /* Sidebar 260px */
Tablet     768-1024  /* Sidebar 220px */
Mobile     480-768   /* Sidebar overlay */
Small      < 480px   /* Full-width mobile */
```

### Ajustements

- ✅ Sidebar: Fixed → Overlay sur mobile
- ✅ Forms: 2 colonnes → 1 colonne < 768px
- ✅ Tables: Scroll horizontal < 768px
- ✅ Buttons: Touch-friendly padding
- ✅ Theme toggle: Visible partout

---

## 🔐 Sécurité

### Invitations

- ✅ Token unique + random
- ✅ Expiration 7 jours
- ✅ Hash password (PASSWORD_DEFAULT)
- ✅ Prepared statements (PDO)
- ✅ Email validation

### Permissions

- ✅ Seul RH/Admin peut créer invitations
- ✅ Seul manager peut valider compétences
- ✅ Seul user peut modifier son profil

---

## 📝 Logs

### Où trouver les logs

```bash
# Erreurs PHP
/var/log/php.log
tail -f /var/log/php.log

# Erreurs Apache
/var/log/apache2/error.log
tail -f /var/log/apache2/error.log

# Emails envoyés
/var/log/mail.log
tail -f /var/log/mail.log

# PHP error log (si configuré)
php -r "echo ini_get('error_log');"
```

### Dans les logs

```
✅ Email envoyé à test@company.com: Invitation FindIN
✅ Invitation créée et email envoyé pour: test@company.com
✅ Invitation acceptée: test@company.com
❌ Erreur lors de l'envoi de l'email à test@company.com
```

---

## 🆘 Dépannage

### Problème: Emails non envoyés

```bash
# 1. Vérifier sendmail
which sendmail
/usr/sbin/sendmail -h

# 2. Vérifier configuration PHP
php -i | grep mail

# 3. Vérifier les logs
tail -f /var/log/mail.log

# 4. Tester manuellement
echo "test" | /usr/sbin/sendmail -v test@company.com
```

### Problème: Base de données inaccessible

```bash
# 1. Vérifier MySQL running
ps aux | grep mysql

# 2. Vérifier connexion
mysql -u root -p

# 3. Vérifier findin DB exists
mysql -u root -e "SHOW DATABASES LIKE 'findin';"

# 4. Vérifier tables
mysql -u root findin -e "SHOW TABLES;"
```

### Problème: Thème ne change pas

```bash
# Vérifier localStorage dans la console:
localStorage.getItem('theme')

# Réinitialiser:
localStorage.removeItem('theme')
# Puis recharger la page
```

---

## 📞 Support

Pour plus d'infos:
- 📧 Email: blacknwhitemanagement@findin.fr
- 🌐 Dashboard: http://localhost:8000/dashboard
- 📚 Docs: `/docs/` folder

---

**Dernière mise à jour:** 22 Janvier 2025  
**Version:** 2.0 - Mode Clair/Sombre + Email Upgrade  
**Statut:** ✅ Production Ready

