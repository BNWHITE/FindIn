# 🔄 Exemple : Convertir `terms.php` aux règles

## Avant (❌ PROBLÉMATIQUE)

```php
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <!-- ❌ Redéclare les variables CSS -->
    <style>
        :root { 
            --bg-primary: #0a0118; 
            --bg-secondary: #1a0d2e; 
            /* ... etc ... */
        }
    </style>
</head>
<body>
    <header class="header"><!-- Réinventé localement --></header>
    <section class="hero"><!-- Style inline --></section>
    <div class="content"><!-- Styles dupliqués --></div>
    <footer class="footer"><!-- Style réinventé --></footer>
</body>
</html>
```

### Problèmes :
1. ❌ `<style>` avec `:root` en début de fichier
2. ❌ Variables CSS identiques au layout mais redéclarées
3. ❌ Header et footer "réinventés" au lieu d'être inclus
4. ❌ Classes CSS incompatibles avec le reste du site

---

## Après (✅ CORRECT)

```php
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php require_once __DIR__ . '/layouts/header.php'; ?>

<!-- 🎯 Contenu de la page (classe standard) -->
<section class="page-content">
    <div class="hero">
        <h1>Conditions d'Utilisation</h1>
        <p>Dernière mise à jour : 12 décembre 2024</p>
    </div>

    <div class="section">
        <h2>1. Objet</h2>
        <p>Les présentes conditions générales régissent l'utilisation de la plateforme FindIN...</p>
    </div>

    <div class="section">
        <h2>2. Accès au service</h2>
        <p>L'accès à FindIN nécessite la création d'un compte utilisateur...</p>
    </div>

    <!-- ... autres sections ... -->
</section>

<!-- 🔑 CSS SPÉCIFIQUE À CETTE PAGE (optionnel, sans redéclarer :root) -->
<style>
    .hero {
        padding: 4rem 2rem;
        text-align: center;
        background: linear-gradient(135deg, rgba(147,51,234,0.1), rgba(59,130,246,0.05));
    }

    .hero h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .section h2 {
        color: var(--accent-purple);
    }

    .section p, .section li {
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
    }

    .section ul {
        padding-left: 1.5rem;
    }
</style>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
```

---

## Différences Clés

| Avant | Après |
|-------|-------|
| Redéclare `<style>` avec `:root` | Pas de `:root`, utilise `var()` |
| Header et footer "en dur" | Include le layout |
| `<!DOCTYPE html>` en haut | Non nécessaire (dans layout) |
| Très long (100+ lignes CSS) | Court et propre |
| Variables CSS différentes par page | Variables cohérentes globalement |
| Copilot génère du chaos | Copilot génère du code propre |

---

## Structure de fichier après conversion

```
src/Views/
├── layouts/
│   ├── header.php          ← Inclut le <!DOCTYPE>, <meta>, CSS variables
│   └── footer.php
│
└── terms.php               ← SEULEMENT le contenu
```

### Ce qu'on trouve dans `terms.php` converti :
✅ Session start  
✅ Include header  
✅ `<section class="page-content">` (classe standardisée)  
✅ Contenu avec classes `.section`  
✅ CSS spécifique (optionnel) sans redéclarer `:root`  
✅ Include footer

---

## Avantages

1. **Maintenance** : Modifier le thème = 1 fichier (header.php)
2. **Cohérence** : Toutes les pages ont même header/footer
3. **Performance** : CSS partagé, pas de duplication
4. **Copilot-friendly** : Il comprend la structure
5. **Thème fonctionne** : Variables appliquées partout

---

## Checklist de conversion

- [ ] Supprimer `<!DOCTYPE html>`, `<meta>`, `<head>`, `<body>`
- [ ] Ajouter `<?php require_once __DIR__ . '/layouts/header.php'; ?>` en haut
- [ ] Ajouter `<?php require_once __DIR__ . '/layouts/footer.php'; ?>` en bas
- [ ] Remplacer `<header class="...">` par header du layout (supprimé)
- [ ] Remplacer `<footer class="...">` par footer du layout (supprimé)
- [ ] Envelopper le contenu dans `<section class="page-content">`
- [ ] Supprimer la `<style>` qui déclare `:root`
- [ ] Garder SEULEMENT le CSS spécifique à cette page
- [ ] Remplacer les couleurs en dur par `var(--accent-purple)` etc.
- [ ] Tester le thème toggle (dark/light)

---

## Template prêt à copier

```php
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php require_once __DIR__ . '/layouts/header.php'; ?>

<section class="page-content">
    <!-- TON CONTENU ICI -->
</section>

<style>
    /* Styles spécifiques (SANS :root) */
</style>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
```

---

**Cet exemple s'applique aussi à :**
- `privacy.php`
- `cgu.php`
- `mentions_legales.php`

Toutes ces pages peuvent être converties avec le même modèle !
