# 📋 FindIN - Règles de Codage & Architecture

> **Document d'onboarding pour équipe collaborant avec Copilot**

---

## ⚠️ PROBLÈME IDENTIFIÉ : CSS et PHP Décorrélés

### Le Problème Principal
**Chaque vue PHP est autonome et embarque son propre CSS inline** au lieu d'utiliser un système de layout centralisé.

#### Conséquences :
1. **Duplication CSS** → Chaque fichier a `:root { variables }` en double
2. **Incohérence de thème** → Les variables CSS diffèrent entre les vues
3. **Maintenance cauchemardesque** → Modifier le layout affecte uniquement les vues qui l'incluent
4. **Copilot génère du CSS répétitif** → Il ne connaît pas le système de layout global

#### Exemple du problème :
```php
// ❌ MAUVAIS - style.css via layout MAIS aussi styles inline

// Dans header.php (inclus) :
<link rel="stylesheet" href="/assets/css/style.css">
<style>
    :root { --bg-primary: #0a0118; ... }
</style>

// Dans terms.php (autonome) :
<style>
    :root { --bg-primary: #0a0118; ... }  // ← DUPLICATE !
</style>
```

**Quand tu modifies PHP → CSS se duplique ou se redéclare → Copilot génère des conflits.**

---

## ✅ SOLUTION : 3 Catégories de Vues

### 1️⃣ **Vues avec Layout** (Pages internes, auth)
Incluent le layout centralisé + CSS partagé.

**Fichiers concernés :**
- `src/Views/layouts/header.php` ← **LA SOURCE DE VÉRITÉ POUR CSS**
- `src/Views/layouts/footer.php`
- `src/Views/dashboard/*.php`
- `src/Views/auth/*.php`

**Règle :**
```php
<?php require_once __DIR__ . '/layouts/header.php'; ?>
<!-- Contenu page -->
<?php require_once __DIR__ . '/layouts/footer.php'; ?>
```

✅ **CSS centralisé dans `header.php`**  
✅ **Variables CSS définies une fois**  
✅ **Thème appliqué globalement**

---

### 2️⃣ **Vues Autonomes** (Pages marketing : about, features, pricing)
Pages standalone avec leur propre CSS (volontaire).

**Fichiers concernés :**
- `src/Views/index.php`
- `src/Views/about.php`
- `src/Views/features.php`
- `src/Views/product.php`
- `src/Views/pricing.php`
- `src/Views/blog.php`

**Règle :**
```php
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <link href="..." rel="stylesheet">
    <style>
        /* Complètement autonome - aucun layout */
        :root { /* Variables isolées */ }
    </style>
</head>
<body>
    <!-- Contenu -->
</body>
</html>
```

✅ **CSS auto-contenu**  
✅ **Ne touche pas au layout global**  
✅ **Design unique par page OK**

---

### 3️⃣ **Vues Légales** (Terms, Privacy, CGU, etc.)
Petites pages avec layout simple mais cohérent.

**Fichiers concernés :**
- `src/Views/terms.php`
- `src/Views/privacy.php`
- `src/Views/cgu.php`
- `src/Views/mentions_legales.php`
- `src/Views/contact.php`
- `src/Views/faq.php`

**Règle :**
```php
<?php require_once __DIR__ . '/layouts/header.php'; ?>

<section class="page-content">
    <!-- Contenu -->
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
```

✅ **Utilise le layout principal**  
✅ **CSS du header.php appliqué**  
✅ **Variables CSS cohérentes**

---

## 🎨 Structure CSS Correcte

### `layouts/header.php` : LA SOURCE DE VÉRITÉ

```php
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <style>
        /* 🔑 VARIABLES CSS GLOBALES - DÉFINIES UNE FOIS */
        :root {
            --bg-primary: #0a0118;
            --bg-secondary: #1a0d2e;
            --bg-card: #241538;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --accent-purple: #9333ea;
            --accent-blue: #3b82f6;
            --border-color: rgba(255,255,255,0.1);
        }
        
        [data-theme="light"] {
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: rgba(0,0,0,0.1);
        }
        
        /* ✅ Classes réutilisables */
        .page-content { max-width: 800px; margin: 0 auto; padding: 3rem 2rem; }
        .section { margin-bottom: 2.5rem; }
        .section h2 { color: var(--accent-purple); }
    </style>
</head>
<body>
    <!-- Header -->
</body>
</html>
```

---

## 🔴 NE PAS FAIRE

### ❌ Dupliquer CSS inline partout
```php
// MAUVAIS
<?php require_once __DIR__ . '/layouts/header.php'; ?>
<style>
    :root { --bg-primary: #0a0118; } /* ← Déjà dans header ! */
</style>
```

### ❌ Utiliser des noms de classes inconsistants
```php
// MAUVAIS
<div class="header"> <!-- Dans terms.php -->
<div class="findin-header"> <!-- Dans about.php -->
// → Copilot génère des conflits !
```

### ❌ Mélanger les approches
```php
// MAUVAIS
<?php require_once __DIR__ . '/layouts/header.php'; ?>
<link rel="stylesheet" href="/assets/css/style.css"> <!-- Déjà dans header ! -->
```

---

## ✅ À FAIRE

### 1. Ajouter une nouvelle page avec layout
```php
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php require_once __DIR__ . '/layouts/header.php'; ?>

<section class="page-content">
    <div class="section">
        <h2>Titre</h2>
        <p>Contenu...</p>
    </div>
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
```

### 2. Ajouter du CSS spécifique à une vue avec layout
```php
<?php require_once __DIR__ . '/layouts/header.php'; ?>

<style>
    /* 🔑 CSS SPÉCIFIQUE À CETTE PAGE UNIQUEMENT */
    .custom-section {
        background: var(--bg-card); /* Utilise les variables du layout ! */
        padding: 2rem;
    }
</style>

<section class="page-content">
    <div class="custom-section">...</div>
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
```

### 3. Ajouter une page autonome (pas de layout)
```php
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <style>
        /* Complètement isolé - ok d'avoir son propre :root */
        :root { --my-color: #123456; }
    </style>
</head>
<body>
    <!-- Contenu autonome -->
</body>
</html>
```

---

## 🗂️ Architecture Vue

```
src/Views/
├── layouts/
│   ├── header.php          ← 🔑 SOURCE CSS GLOBALE
│   └── footer.php          ← Réutilisable
│
├── auth/
│   ├── login.php           ← Inclut header + footer
│   └── register.php        ← Inclut header + footer
│
├── dashboard/
│   ├── index.php           ← Inclut header + footer
│   ├── competences.php     ← Inclut header + footer
│   └── _sidebar.php        ← Include sans layout
│
├── index.php               ← 🎨 AUTONOME (pas de layout)
├── about.php               ← 🎨 AUTONOME
├── features.php            ← 🎨 AUTONOME
├── terms.php               ← ❌ TRANSITOIRE (sera converti)
├── privacy.php             ← ❌ TRANSITOIRE (sera converti)
└── contact.php             ← Inclut header + footer
```

---

## 🔧 Checklist pour Copilot (Instructions à donner)

**Quand Copilot génère du code :**

### Pour une PAGE AVEC LAYOUT
- [ ] ✅ Utilise `<?php require_once __DIR__ . '/layouts/header.php'; ?>`
- [ ] ✅ Réutilise les variables CSS du `:root` défini dans header
- [ ] ✅ Pas de `<style>` qui redéclare `:root`
- [ ] ✅ Utilise les classes existantes : `.page-content`, `.section`, etc.
- [ ] ✅ N'ajoute pas `<link>` vers CSS (déjà en head)

### Pour une PAGE AUTONOME
- [ ] ✅ HTML complet avec `<!DOCTYPE html>`
- [ ] ✅ Son propre `<style>` avec `:root`
- [ ] ✅ Pas d'inclusion de layout
- [ ] ✅ Thème auto-contenu dans le fichier

### Instruction Copilot à donner
```
Tu travailles sur FindIN, une app PHP MVC sans framework.

RÈGLE D'OR : 
- Les vues incluent SOIT le layout (header + footer), 
  SOIT sont complètement autonomes (HTML standalone)
- JAMAIS les deux
- Les variables CSS sont définies en UN SEUL endroit : src/Views/layouts/header.php

Catégories :
1. Vues avec layout : auth/*, dashboard/*, termes, contact, faq...
   → Commence par: <?php require_once __DIR__ . '/layouts/header.php'; ?>
   
2. Vues autonomes : index.php, about.php, features.php, pricing.php...
   → HTML complet avec <style> auto-contenu

3. Ne JAMAIS dupliquer CSS ou :root
```

---

## 📊 Variables CSS Standardisées

Toutes les variables sont dans `header.php` :

```css
:root {
    /* Thème sombre (défaut) */
    --bg-primary: #0a0118;
    --bg-secondary: #1a0d2e;
    --bg-card: #241538;
    --text-primary: #ffffff;
    --text-secondary: #a0a0a0;
    --accent-purple: #9333ea;
    --accent-blue: #3b82f6;
    --border-color: rgba(255,255,255,0.1);
}

[data-theme="light"] {
    --bg-primary: #f8fafc;
    --bg-secondary: #ffffff;
    --bg-card: #ffffff;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --border-color: rgba(0,0,0,0.1);
}
```

**Pour ajouter une couleur nouvelle :**
1. La définir dans `:root` et `[data-theme="light"]`
2. L'utiliser dans les fichiers via `var(--ma-couleur)`
3. **NE PAS ajouter un nouveau `:root` dans une vue !**

---

## 🎯 Résumé Rapide

| Problème | Cause | Solution |
|----------|-------|----------|
| CSS dupliqué partout | Chaque vue a son `:root` | Layout centralisé en header.php |
| Thème se "déconne" | Variables CSS différentes par page | Source de vérité unique |
| Copilot génère du chaos | Il ne sait pas qu'il y a un layout | Lui dire explicitement dans instructions |
| Modifier layout impacte rien | Styles inline, pas de cascade | Inclure le layout dans les vues |

---

## 📝 Template pour Copilot

Avant chaque demande de code, dis à Copilot :

```
Contexte : FindIN - App PHP MVC, vues dans src/Views/

Cette nouvelle vue doit :
[ ] Inclure le layout (header + footer) ET/OU être autonome ?
[ ] Utiliser les variables CSS existantes (dans header.php) ?
[ ] Ajouter du CSS spécifique ? Si oui, sans redéclarer :root

Génère le code en respectant ces règles.
```

---

## 🚀 Phase de Transition

### Fichiers à convertir (vues "légales" mixtes) :
1. `terms.php` → Ajouter include layout
2. `privacy.php` → Ajouter include layout  
3. `cgu.php` → Ajouter include layout
4. `mentions_legales.php` → Ajouter include layout

### Après conversion :
- ✅ Variables CSS cohérentes partout
- ✅ Thème fonctionne correctement
- ✅ Copilot génère du code propre
- ✅ Maintenance simplifiée

---

## 📞 Support Copilot

Si Copilot génère du CSS dupliqué, rappelle-lui :

> "Les variables CSS sont déjà définies en haut du fichier header.php inclus. 
> N'ajoute pas un nouveau :root ou @media dans cette vue."

---

**Dernière maj : 21 janvier 2026**
