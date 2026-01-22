# 🚀 QUICK START - FindIN 2025

## ⚡ Démarrage Rapide

### 1. Accédez au Dashboard RH
```
http://localhost:8000/dashboard/rh-invitations
```

### 2. Connectez-vous
```
Email: admin@findin.fr
Password: admin123
```

---

## ✉️ Créer une Invitation

### Étape 1: Remplir le formulaire

| Champ | Exemple |
|-------|---------|
| **Email** | `nouveau@company.com` |
| **Prénom** | `Jean` |
| **Nom** | `Dupont` |
| **Département** | `IT` |
| **Rôle** | `employe` |
| **Manager** | (optionnel) |

### Étape 2: Soumettre

- Clique "Envoyer l'invitation"
- ✅ Email envoyé automatiquement
- 📋 Lien copié dans le formulaire

### Étape 3: Collaborateur reçoit email

- **From**: blacknwhitemanagement@findin.fr
- **Subject**: Invitation FindIN
- **Lien**: Accepter l'invitation (valide 7 jours)

### Étape 4: Accepter

- Collaborateur clique le lien
- Crée un mot de passe
- ✅ Compte créé automatiquement

---

## 🎨 Mode Clair/Sombre

### Basculer le thème

1. **Cliquez** l'icône 🌙 (lune) dans la sidebar
2. Page change en **mode clair**
3. Icône devient ☀️ (soleil)
4. **Choix sauvegardé** automatiquement

### Thèmes

- **Dark** (défaut): Gradient purple/blue, confortable pour la nuit
- **Light**: Blanc professionnel, idéal en plein jour

---

## 📊 Base de Données

### Status

✅ MySQL 9.5.0  
✅ Database: `findin`  
✅ Tables: 11  
✅ Utilisateurs: 5  

### Tables principales

```
utilisateurs (5 lignes)
├─ admin@findin.fr (admin)
├─ test@findin.fr (employe)
└─ ...

competences (5 lignes)
├─ PHP
├─ JavaScript
├─ MySQL
├─ Leadership
└─ Communication

invitations (vide)
├─ Email + Token
├─ Statut (pending, accepted, expired)
└─ Expiration (7 jours)

demandes_validation (vide)
├─ Compétence à valider
├─ Statut (en_attente, approuve, rejete)
└─ Commentaires manager
```

---

## 🔧 Configuration

### Email

```
From: blacknwhitemanagement@findin.fr
From Name: FindIN - Gestion des Compétences
System: PHP mail()
```

### Database

```
Host: localhost
Port: 3306
User: root
Password: (none)
Database: findin
Charset: utf8mb4
```

---

## 📱 Responsive Design

✅ Desktop (≥1024px): Sidebar fixe 260px  
✅ Tablet (768-1024px): Sidebar 220px  
✅ Mobile (<768px): Sidebar overlay  
✅ Small phone (<480px): Compact  

---

## 🆘 Aide

### Problème: Email non envoyé?

```bash
# Vérifier les logs
tail -f /var/log/mail.log

# Vérifier sendmail
which sendmail
/usr/sbin/sendmail -h
```

### Problème: Base de données?

```bash
# Vérifier MySQL
mysql -u root

# Vérifier findin DB
mysql -u root findin -e "SHOW TABLES;"

# Vérifier tables count
mysql -u root findin -e "SELECT COUNT(*) FROM utilisateurs;"
```

### Problème: Thème ne change pas?

```javascript
// Console browser (F12)
localStorage.getItem('theme')  // Check current
localStorage.removeItem('theme')  // Reset
location.reload()  // Reload page
```

---

## 📞 Informations Utiles

| Élément | Valeur |
|--------|--------|
| **App URL** | http://localhost:8000 |
| **Dashboard RH** | /dashboard/rh-invitations |
| **Admin Email** | admin@findin.fr |
| **Admin Password** | admin123 |
| **Email From** | blacknwhitemanagement@findin.fr |
| **MySQL Host** | localhost:3306 |
| **MySQL User** | root |
| **MySQL DB** | findin |

---

## ✨ Fonctionnalités

### Mode Clair/Sombre ✅
- Toggle button dans sidebar
- Stocké en localStorage
- Transition smooth

### Invitations Réelles ✅
- Email HTML professionnel
- Token unique + expiration
- Validation de email
- Mot de passe sécurisé

### Base de Données ✅
- MySQL 9.5.0 optimisée
- Indexes pour performance
- Foreign keys + cascade
- UTF8MB4 unicode

### Navigation RH ✅
- Dashboard
- Invitations (actif)
- Déconnexion
- *Competences & Profil supprimés*

---

## 🎯 Prochaines Étapes

1. ✅ Créer une invitation de test
2. ✅ Vérifier l'email reçu
3. ✅ Accepter l'invitation
4. ✅ Basculer le thème clair/sombre
5. ✅ Vérifier la base de données

---

**Dernière mise à jour:** 22 Janvier 2025  
**Statut:** ✅ Production Ready
