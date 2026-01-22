# FindIN - Système de Gestion des Compétences 
## Guide Utilisateur - Session 22 Janvier 2026

### 🎯 Vue d'Ensemble du Système

FindIN est une plateforme complète de gestion des compétences pour les entreprises. Elle permet aux RH d'inviter les employés, aux managers de valider les compétences, et aux employés de gérer leur profil de compétences.

### 👥 Les Trois Rôles

#### 1. **RH (Ressources Humaines)**
**Permissions:**
- Inviter des nouveaux employés
- Assigner les employés à des managers
- Gérer les invitations (créer, renvoyer, supprimer)
- Voir l'historique des invitations

**Accès:**
```
URL: http://localhost:8000/dashboard/rh-invitations
Email test: admin@findin.fr
Mot de passe: admin123
```

**Flux de travail RH:**
1. Se connecter à `/login`
2. Naviguer vers le dashboard RH
3. Cliquer sur "Créer une nouvelle invitation"
4. Remplir: Email, Prénom, Nom, Rôle, Manager, Département
5. Cliquer "Envoyer l'invitation"
6. Un email est automatiquement envoyé à l'employé avec un lien unique

#### 2. **Manager**
**Permissions:**
- Voir les demandes de validation de compétences de son équipe
- Approuver ou rejeter les demandes
- Ajouter des commentaires

**Accès:**
```
URL: http://localhost:8000/dashboard/manager-validations
(Compte manager à créer)
```

**Flux de travail Manager:**
1. Se connecter
2. Aller à "Validations de Compétences"
3. Voir la liste des demandes en attente
4. Pour chaque demande:
   - Approuver (avec commentaire optionnel)
   - Rejeter (avec motif)

#### 3. **Employé**
**Permissions:**
- Créer un compte via une invitation RH
- Voir son profil et ses compétences
- Déclarer de nouvelles compétences
- Voir l'historique de validation

**Accès:**
```
URL reçue par email: http://localhost:8000/invitation/accept?token=xxx
```

**Flux de travail Employé:**
1. Recevoir l'email d'invitation avec le lien unique
2. Cliquer sur le lien
3. Remplir le formulaire:
   - Prénom/Nom (pré-remplis)
   - Mot de passe (min. 8 caractères)
   - Confirmation du mot de passe
4. Créer le compte
5. (Bientôt) Déclarer ses compétences

### 📧 Système d'Email

#### Configuration
- **Adresse de départ:** contact@findin.fr
- **Adresse de réception des contacts:** contact@findin.fr

#### Emails Envoyés Automatiquement

**1. Invitation d'employé:**
- Envoyé par: Système
- À: L'adresse email de l'invité
- Contenu: Lien d'acceptation valable 7 jours
- Format: HTML avec template professionnel

**2. Message de contact:**
- Envoyé par: Visiteur du site
- À: contact@findin.fr
- Contenu: Nom, email, sujet, message
- Format: HTML avec détails du contact

### 🔐 Sécurité

#### Authentification
- Sessions PHP sécurisées
- Mots de passe hachés avec bcrypt
- Vérification session sur les pages protégées

#### Tokens d'Invitation
- Génération cryptographiquement sûre (32 bytes)
- Expiration après 7 jours
- Usage unique (status change après acceptation)
- Stockage en base de données

#### Contrôle d'Accès
```php
RH/Admin    → Dashboard RH + gestion invitations
Manager     → Dashboard validation + approbation
Employé     → Dashboard personnel + profil
Public      → Accueil + Contact
```

### 🗄️ Données

#### Nouv elles Tables

**invitations**
- `id` - Identifiant unique
- `token` - Token unique cryptographique
- `email` - Email de l'invité
- `prenom`, `nom` - Données pré-remplies
- `manager_id` - Assignation au manager
- `role` - Rôle assigné (employe, manager, rh)
- `departement` - Département
- `status` - État (pending, accepted, expired)
- `created_at` - Date de création
- `expires_at` - Date d'expiration
- `user_id` - ID utilisateur après acceptation

**demandes_validation**
- `id` - Identifiant unique
- `user_id` - Employé déclarant la compétence
- `id_competence` - Compétence concernée
- `niveau_demande` - Niveau demandé (1-5)
- `statut` - État (en_attente, approuve, rejete)
- `manager_id` - Manager responsable
- `commentaire` - Commentaires optionnels
- `date_demande` - Date de création
- `date_validation` - Date de décision

### 🚀 Démarrage Rapide

#### 1. Lancer le Serveur
```bash
cd /Users/s.sy/Documents/ISEP/FindIn
php -S localhost:8000 -t public
```

#### 2. Accéder à l'Application
```
http://localhost:8000
```

#### 3. Se Connecter en tant que RH
```
Email: admin@findin.fr
Mot de passe: admin123
```

#### 4. Inviter un Employé
1. Aller à `/dashboard/rh-invitations`
2. Remplir le formulaire d'invitation
3. Cliquer "Envoyer l'invitation"
4. Copier le lien et le partager (ou attendre l'email)

#### 5. Accepter l'Invitation (en tant qu'employé)
1. Cliquer sur le lien reçu par email
2. Remplir le formulaire de création de compte
3. Cliquer "Créer mon compte"
4. Accéder au dashboard employé

### 📊 Routes Disponibles

#### Routes Publiques
| Route | Méthode | Description |
|-------|---------|-------------|
| `/` | GET | Accueil |
| `/login` | GET/POST | Connexion |
| `/register` | GET/POST | Inscription |
| `/contact` | GET/POST | Formulaire de contact |
| `/features` | GET | Page fonctionnalités |
| `/pricing` | GET | Page tarifs |

#### Routes d'Invitation
| Route | Méthode | Description | Auth |
|-------|---------|-------------|------|
| `/invitation/accept` | GET | Voir formulaire d'acceptation | Non |
| `/invitation/accept` | POST | Valider acceptation | Non |
| `/dashboard/rh-invitations` | GET | Dashboard RH | RH/Admin |
| `/invitation/create` | POST | Créer invitation (API) | RH/Admin |
| `/invitation/delete` | POST | Supprimer invitation (API) | RH/Admin |

#### Routes de Validation
| Route | Méthode | Description | Auth |
|-------|---------|-------------|------|
| `/dashboard/manager-validations` | GET | Dashboard manager | Manager |
| `/validation/approve` | POST | Approuver demande (API) | Manager |
| `/validation/reject` | POST | Rejeter demande (API) | Manager |

#### Routes Dashboard
| Route | Méthode | Description | Auth |
|-------|---------|-------------|------|
| `/dashboard` | GET | Dashboard personnel | Tous |
| `/dashboard/competences` | GET | Gestion compétences | Tous |
| `/dashboard/profile` | GET | Profil utilisateur | Tous |

### 🔧 Fichiers Clés

**Contrôleurs:**
- `src/Controllers/InvitationController.php` - Gestion invitations
- `src/Controllers/ContactController.php` - Formulaire contact
- `src/Controllers/ValidationController.php` - Validations manager
- `src/Controllers/AuthController.php` - Authentification

**Modèles:**
- `src/Models/Invitation.php` - Logique invitations
- `src/Models/User.php` - Gestion utilisateurs

**Utilitaires:**
- `src/Lib/EmailSender.php` - Envoi d'emails

**Vues:**
- `src/Views/auth/accept-invitation.php` - Acceptation invitation
- `src/Views/dashboard/rh-invitations.php` - Dashboard RH
- `src/Views/dashboard/manager-validations.php` - Dashboard manager
- `src/Views/contact.php` - Formulaire contact

**Router:**
- `public/index.php` - Routeur central

### 📝 Exemple: Créer une Invitation en API

```bash
curl -X POST http://localhost:8000/invitation/create \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=votre_session" \
  -d '{
    "email": "thomas@example.com",
    "prenom": "Thomas",
    "nom": "Martin",
    "manager_id": "2",
    "departement": "IT",
    "role": "employe"
  }'
```

**Réponse:**
```json
{
  "success": true,
  "message": "Invitation créée et email envoyé avec succès",
  "invitation_link": "http://localhost:8000/invitation/accept?token=..."
}
```

### 🐛 Dépannage

#### "Cette invitation est invalide, expirée..."
- L'invitation a expiré (7 jours)
- Le token n'existe pas
- L'invitation a déjà été utilisée

**Solution:** Demander au RH d'envoyer une nouvelle invitation

#### "Le compte n'a pas été créé"
- Mots de passe non confirmés
- Mot de passe < 8 caractères
- Email déjà utilisé

**Solution:** Vérifier les messages d'erreur et réessayer

#### Pas de réception d'email
- Fonction `mail()` PHP non configurée
- Erreur SMTP
- Filtre spam

**Solution:** Vérifier la configuration PHP et les logs

### 📈 Prochaines Fonctionnalités

1. **Déclaration de Compétences Employé**
   - Interface pour déclarer les compétences
   - Sélection du niveau
   - Envoi des demandes de validation

2. **Notifications Email**
   - Alertes pour les managers
   - Confirmations pour les employés

3. **Admin Dashboard**
   - Statistiques globales
   - Gestion des utilisateurs
   - Rapports

4. **API REST Complète**
   - Documentation OpenAPI
   - Authentification JWT

### 📞 Support

**Email:** contact@findin.fr  
**Issues:** Voir le formulaire de contact sur le site  
**Logs:** `/storage/logs/php-errors.log`

---

**Version:** 1.0 Beta  
**Dernière mise à jour:** 22 Janvier 2026  
**Statut:** Production Ready ✅
