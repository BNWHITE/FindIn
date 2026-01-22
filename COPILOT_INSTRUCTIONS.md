#!/usr/bin/env markdown
# 🤖 Instructions pour Copilot / Collaborateurs utilisant l'IA

**Pour que Copilot génère du code cohérent et que CSS ne "déconne" plus, copie cette section dans tes instructions Copilot ou système prompt.**

---

## Contexte Projet

```
Projet : FindIN - Plateforme de gestion des compétences (PHP MVC sans framework)
Type : Web app français avec thème sombre/clair switchable
Structure : public/ (router) → src/Controllers/ → src/Views/ (templates)
```

---

## RÈGLE D'OR : CSS Centralisé

**Le problème :** Chaque vue PHP peut avoir son CSS. Si chacune redéclare les variables CSS, le thème se désynchronise.

**La solution :** Un seul endroit définit les variables CSS globales : `src/Views/layouts/header.php`

---

## Règle 1️⃣ : Deux Types de Vues

### Type A : Vues avec Layout (80% du code)
**Inclut le layout centralisé pour header/footer/CSS.**

✅ Fichiers : `dashboard/*.php`, `auth/*.php`, pages légales (`terms.php`, etc.)

Début du fichier DOIT être :
```php
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php require_once __DIR__ . '/layouts/header.php'; ?>

<section class="page-content">
    <!-- CONTENU ICI -->
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
```

**CSS à ne JAMAIS ajouter :**
- ❌ `<!DOCTYPE html>`
- ❌ `<head>`
- ❌ `<body>`
- ❌ `<style>` avec `:root`
- ❌ `<link rel="stylesheet">`
- ❌ `<header>`, `<footer>` (déjà dans le layout)

**CSS à réutiliser :**
```css
var(--bg-primary)           /* Fond sombre/clair */
var(--bg-secondary)         /* Fond secondaire */
var(--text-primary)         /* Texte principal */
var(--text-secondary)       /* Texte gris */
var(--accent-purple)        /* #9333ea - boutons */
var(--accent-blue)          /* #3b82f6 - accents */
var(--border-color)         /* Bordures adaptées au thème */
```

Exemple :
```php
<style>
    .my-box {
        background: var(--bg-card);     /* ✅ Bon */
        color: var(--text-primary);     /* ✅ Bon */
        border: 1px solid var(--border-color);  /* ✅ Bon */
    }
    
    .my-box:hover {
        background: var(--accent-purple);  /* ✅ Bon */
    }
</style>
```

---

### Type B : Vues Autonomes (20% du code)
**Complètement indépendantes, pas de layout.**

✅ Fichiers : `index.php`, `about.php`, `features.php`, `pricing.php`, `blog.php`, etc.

Structure **complète** :
```php
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="...">
    <style>
        :root { /* C'est OK ici car pas de layout inclus */ }
    </style>
</head>
<body>
    <!-- CONTENU AUTONOME -->
</body>
</html>
```

**Attention :**
- Contient TOUTES les balises HTML
- Son propre `:root` (c'est OK, c'est volontaire)
- **N'inclut PAS le layout**
- **N'inclut PAS les vues de type A**

---

## Règle 2️⃣ : Jamais Dupliquer CSS

**MAUVAIS ❌** (provoque les conflits Copilot) :
```php
<?php require_once __DIR__ . '/layouts/header.php'; ?>

<style>
    :root {  /* ← ERREUR ! Déjà dans header.php */
        --bg-primary: #0a0118;
    }
</style>
```

**BON ✅** :
```php
<?php require_once __DIR__ . '/layouts/header.php'; ?>

<style>
    /* Seulement CSS spécifique à CETTE vue */
    .my-component {
        color: var(--text-primary);  /* Réutilise les variables */
    }
</style>
```

---

## Règle 3️⃣ : Noms de Classe Cohérents

**Partout dans le projet, les mêmes classes :**
- `.page-content` ← Conteneur principal (max 800px center)
- `.section` ← Bloc de contenu
- `.section h2` ← Titre section (violet)
- `.btn-primary` ← Bouton principal
- `.hero` ← Section hero (optionnel, spécifique)

**Exemple :**
```php
<section class="page-content">
    <div class="section">
        <h2>Titre</h2>
        <p>Texte...</p>
    </div>
</section>
```

---

## Règle 4️⃣ : Gestion du Thème

**Toujours inclure dans les vues autonomes :**
```php
<script>
    const toggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    
    // Charger thème sauvegardé
    const savedTheme = localStorage.getItem('theme') || 'dark';
    html.setAttribute('data-theme', savedTheme);
    
    // Toggle au clic
    toggle.addEventListener('click', () => {
        const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
    });
</script>
```

**Pour les vues avec layout :** Ce script est dans `header.php`, rien à faire.

---

## Checklist : Avant de Générer du Code

Demande-toi / demande à Copilot :

1. **Type de vue ?**
   - [ ] Avec layout (dashboard, auth, pages légales) ?
   - [ ] Autonome (marketing pages) ?

2. **Si avec layout :**
   - [ ] J'inclus `layouts/header.php` ET `layouts/footer.php` ?
   - [ ] Je n'ajoute pas `<!DOCTYPE html>` ?
   - [ ] Je n'ajoute pas de `:root` en CSS ?
   - [ ] J'utilise les variables existantes (`var(--...)`) ?

3. **Si autonome :**
   - [ ] C'est un HTML complet ?
   - [ ] J'ai mon propre `:root` ?
   - [ ] Je n'inclus pas le layout ?

4. **Cohérence :**
   - [ ] J'utilise `.page-content` pour le conteneur ?
   - [ ] Les titres utilisent `var(--accent-purple)` ?
   - [ ] Je n'ai pas copié du CSS d'une autre vue ?

---

## Instruction pour Copilot

**Copie ça et donne-le à Copilot avant chaque demande de code :**

```
Tu es un expert PHP/HTML/CSS pour un projet FindIN.

RÈGLES STRICTES :
1. Les vues PHP peuvent être TYPE A (avec layout) OU TYPE B (autonomes)
   
   TYPE A : Inclut layouts/header.php + layouts/footer.php
   - Pas de <!DOCTYPE>, <head>, <body>, <style> avec :root
   - CSS spécifique seulement (réutilise var(--...))
   
   TYPE B : Autonomes
   - HTML complet
   - Son propre :root OK
   - Pas d'inclusion de layout

2. Variables CSS existantes (dans header.php) :
   --bg-primary, --bg-secondary, --text-primary, --text-secondary,
   --accent-purple, --accent-blue, --border-color

3. Classes standards :
   .page-content (conteneur), .section, .section h2, .btn-primary

4. Ne JAMAIS dupliquer :root ou déclarer les mêmes variables deux fois

Génère du code qui respecte ces règles.
```

---

## Exemples Rapides

### ❌ MAUVAIS (duplique CSS)
```php
<?php require_once __DIR__ . '/layouts/header.php'; ?>
<style>
    :root { --bg-primary: #0a0118; }  ← ERREUR !
    .my-div { background: #0a0118; }  ← ERREUR !
</style>
```

### ✅ BON
```php
<?php require_once __DIR__ . '/layouts/header.php'; ?>
<style>
    .my-div { background: var(--bg-primary); }  ← Réutilise les variables
</style>
```

### ✅ BON (autonome)
```php
<!DOCTYPE html>
<html>
<head>
    <style>
        :root { --my-color: #123456; }  ← OK, c'est autonome
    </style>
</head>
<body>
    ...
</body>
</html>
```

---

## Résumé Ultra-Rapide

| Do | Don't |
|----|-------|
| Inclure layout pour auth/dashboard | Redéclarer :root partout |
| Réutiliser `var(--...)` | Dupliquer les variables CSS |
| Utiliser `.page-content` | Inventer des noms de classe |
| HTML autonome pour marketing pages | Inclure layout dans pages autonomes |
| Un `:root` central (header.php) | `:root` dans chaque fichier |

---

**Sauvegarde ce fichier et partage-le à tous les collaborateurs utilisant Copilot.**

**Dernière maj : 21 janvier 2026**
