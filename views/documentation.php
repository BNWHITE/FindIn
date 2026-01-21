<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - FindIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg-primary: #0a0118; --bg-secondary: #1a0d2e; --bg-card: #241538; --text-primary: #ffffff; --text-secondary: #a0a0a0; --accent-purple: #9333ea; --accent-blue: #3b82f6; --accent-green: #10b981; --border-color: rgba(255,255,255,0.1); }
        [data-theme="light"] { --bg-primary: #f8fafc; --bg-secondary: #ffffff; --bg-card: #ffffff; --text-primary: #1e293b; --text-secondary: #64748b; --border-color: rgba(0,0,0,0.1); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); line-height: 1.8; }
        .header { background: var(--bg-secondary); border-bottom: 1px solid var(--border-color); padding: 1rem 2rem; position: sticky; top: 0; z-index: 100; }
        .header-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: var(--text-primary); font-weight: 700; font-size: 1.5rem; }
        .logo-icon { width: 40px; height: 40px; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: var(--text-secondary); text-decoration: none; font-weight: 500; }
        .theme-toggle { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; color: var(--text-primary); display: flex; align-items: center; justify-content: center; }
        .hero { padding: 4rem 2rem; text-align: center; background: linear-gradient(135deg, rgba(147,51,234,0.1), rgba(59,130,246,0.05)); }
        .hero h1 { font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--text-secondary); max-width: 600px; margin: 0 auto; }
        .content { max-width: 1000px; margin: 0 auto; padding: 3rem 2rem; }
        .search-box { max-width: 500px; margin: 0 auto 3rem; position: relative; }
        .search-box input { width: 100%; padding: 1rem 1rem 1rem 3rem; border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-primary); font-size: 1rem; }
        .search-box i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-secondary); }
        .doc-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
        .doc-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; transition: all 0.3s; text-decoration: none; }
        .doc-card:hover { transform: translateY(-5px); border-color: var(--accent-purple); }
        .doc-card-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem; }
        .doc-card-icon.purple { background: rgba(147,51,234,0.2); color: var(--accent-purple); }
        .doc-card-icon.blue { background: rgba(59,130,246,0.2); color: var(--accent-blue); }
        .doc-card-icon.green { background: rgba(16,185,129,0.2); color: var(--accent-green); }
        .doc-card h3 { font-size: 1.2rem; margin-bottom: 0.5rem; color: var(--text-primary); }
        .doc-card p { color: var(--text-secondary); font-size: 0.9rem; }
        .section-title { font-size: 1.5rem; margin: 3rem 0 1.5rem; color: var(--text-primary); }
        .faq-item { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 1rem; }
        .faq-question { padding: 1.25rem; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 500; }
        .faq-answer { padding: 0 1.25rem 1.25rem; color: var(--text-secondary); display: none; }
        .faq-item.active .faq-answer { display: block; }
        .faq-item.active .faq-icon { transform: rotate(180deg); }
        .footer { background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 3rem 2rem; text-align: center; margin-top: 3rem; }
        .footer-links { display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .footer-links a { color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; }
        @media (max-width: 768px) { .nav-links { display: none; } .hero h1 { font-size: 2rem; } }

        .doc-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 200;
}

.doc-modal-overlay.active {
    display: flex;
}

.doc-modal {
    background: var(--bg-secondary);
    border-radius: 20px;
    padding: 2rem;
    max-width: 800px;
    width: calc(100% - 4cm); 
    max-height: 80vh;
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    border: 1px solid var(--border-color);
}

.doc-modal-header {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.doc-modal-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.doc-modal-section {
  margin-bottom: 1.5rem;
}

.doc-modal-section h4 {
  font-size: 1rem;
  margin-bottom: 0.4rem;
  color: var(--text-primary);
}

.doc-modal-section p {
  margin: 0 0 0.5rem;
  font-size: 0.9rem;
  color: var(--text-secondary);
}

.doc-modal-steps {
  list-style: none;
  margin: 0;
  padding: 0;
}

.doc-modal-steps li {
  display: flex;
  align-items: flex-start;
  gap: 0.6rem;
  margin-bottom: 0.4rem;
}

.doc-step-badge {
  min-width: 22px;
  height: 22px;
  border-radius: 999px;
  background: rgba(147,51,234,0.18);
  color: var(--accent-purple);
  font-size: 0.8rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
}

.doc-modal-card {
  border-radius: 10px;
  border: 1px solid var(--border-color);
  padding: 0.75rem 0.9rem;
  background: rgba(15,23,42,0.35);
  font-size: 0.85rem;
}

[data-theme="light"] .doc-modal-card {
  background: rgba(148,163,184,0.08);
}

.doc-modal {
  background: var(--bg-secondary);
  border-radius: 20px;
  padding: 1.5rem 1.8rem;
  max-width: 800px;
  width: calc(100% - 4cm);
  max-height: 80vh;
  box-shadow: 0 20px 40px rgba(0,0,0,0.4);
  border: 1px solid var(--border-color);
  display: flex;
  flex-direction: column;
}

.doc-modal-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
  flex-shrink: 0;
}

.doc-modal-body {
  overflow-y: auto;
  padding-right: 0.4rem;
}

    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="/" class="logo"><div class="logo-icon"><i class="fas fa-search"></i></div><span>FindIN</span></a>
            <nav class="nav-links">
                <a href="/">Accueil</a><a href="/about">À propos</a><a href="/contact">Contact</a>
                <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            </nav>
        </div>
    </header>

    <section class="hero">
        <h1>Documentation</h1>
        <p>Tout ce dont vous avez besoin pour maîtriser FindIN et optimiser votre recherche de compétences</p>
    </section>

    <div class="content">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Rechercher dans la documentation...">
        </div>

<div class="doc-grid">
  <a href="#" class="doc-card" data-popup="doc" data-doc-key="demarrage_rapide">
    <div class="doc-card-icon purple"><i class="fas fa-rocket"></i></div>
    <h3>Démarrage rapide</h3>
    <p>Créez votre compte et configurez votre profil en quelques minutes.</p>
  </a>

  <a href="#" class="doc-card" data-popup="doc" data-doc-key="gestion_profil">
    <div class="doc-card-icon blue"><i class="fas fa-user-circle"></i></div>
    <h3>Gestion du profil</h3>
    <p>Personnalisez votre profil, ajoutez vos compétences et téléchargez votre CV.</p>
  </a>

  <a href="#" class="doc-card" data-popup="doc" data-doc-key="recherche_avancee">
    <div class="doc-card-icon green"><i class="fas fa-search"></i></div>
    <h3>Recherche avancée</h3>
    <p>Maîtrisez les filtres et opérateurs pour des recherches précises.</p>
  </a>

  <a href="#" class="doc-card" data-popup="doc" data-doc-key="gestion_projets">
    <div class="doc-card-icon purple"><i class="fas fa-project-diagram"></i></div>
    <h3>Gestion de projets</h3>
    <p>Créez des projets et trouvez les bons collaborateurs.</p>
  </a>

  <a href="#" class="doc-card" data-popup="doc" data-doc-key="tableau_bord">
    <div class="doc-card-icon blue"><i class="fas fa-chart-line"></i></div>
    <h3>Tableau de bord</h3>
    <p>Analysez vos statistiques et suivez votre activité.</p>
  </a>

  <a href="#" class="doc-card" data-popup="doc" data-doc-key="parametres">
    <div class="doc-card-icon green"><i class="fas fa-cog"></i></div>
    <h3>Paramètres</h3>
    <p>Configurez vos préférences et gérez votre compte.</p>
  </a>
</div>


        <h2 class="section-title">Questions fréquentes</h2>

        <div class="faq-item">
            <div class="faq-question">Comment créer un compte ? <i class="fas fa-chevron-down faq-icon"></i></div>
            <div class="faq-answer">Cliquez sur "S'inscrire" en haut de la page, remplissez le formulaire avec vos informations et validez votre email.</div>
        </div>
        <div class="faq-item">
            <div class="faq-question">Comment ajouter mes compétences ? <i class="fas fa-chevron-down faq-icon"></i></div>
            <div class="faq-answer">Rendez-vous sur votre profil, cliquez sur "Gérer mes compétences" et ajoutez-les une par une avec votre niveau d'expertise.</div>
        </div>
        <div class="faq-item">
            <div class="faq-question">Comment rechercher un collaborateur ? <i class="fas fa-chevron-down faq-icon"></i></div>
            <div class="faq-answer">Utilisez la barre de recherche pour trouver des profils par compétence, département ou nom. Utilisez les filtres pour affiner.</div>
        </div>
        <div class="faq-item">
            <div class="faq-question">Mes données sont-elles sécurisées ? <i class="fas fa-chevron-down faq-icon"></i></div>
            <div class="faq-answer">Oui, nous utilisons le chiffrement SSL, conformité RGPD et suivons les principes de Privacy by Design.</div>
        </div>
    </div>

<div class="doc-modal-overlay" id="docModalOverlay">
  <div class="doc-modal">
    <div class="doc-modal-header">
      <div class="doc-modal-icon" id="docModalIcon"></div>
      <h3 id="docModalTitle"></h3>
    </div>
    <div class="doc-modal-body">
      <div id="docModalContent"></div>
    </div>
  </div>
</div>




    <footer class="footer">
        <div class="footer-links"><a href="/about">À propos</a><a href="/contact">Contact</a><a href="/privacy">Confidentialité</a><a href="/terms">Conditions</a><a href="/mentions_legales">Mentions légales</a></div>
        <p>&copy; 2024 FindIN. Tous droits réservés.</p>
    </footer>

 <script>
  const docPopupContents = {
    demarrage_rapide: {
  title: "Démarrage rapide",
  html: `
    <div class="doc-modal-section">
      <p>En 4 étapes, passez de la création de votre compte FindIN à vos premières recherches de talents.</p>
    </div>

    <div class="doc-modal-section">
      <h4>Schéma : 1ère utilisation</h4>
      <ul class="doc-modal-steps">
        <li>
          <span class="doc-step-badge">1</span>
          <div>
            <strong>Créer votre compte</strong><br>
            Depuis la page d’accueil, cliquez sur « S'inscrire » puis utilisez votre adresse professionnelle.
          </div>
        </li>
        <li>
          <span class="doc-step-badge">2</span>
          <div>
            <strong>Valider votre e-mail</strong><br>
            Ouvrez l’e‑mail de confirmation et cliquez sur le lien pour activer votre compte.
          </div>
        </li>
        <li>
          <span class="doc-step-badge">3</span>
          <div>
            <strong>Compléter les infos clés</strong><br>
            Indiquez votre rôle, votre équipe et votre localisation pour être identifié plus facilement.
          </div>
        </li>
        <li>
          <span class="doc-step-badge">4</span>
          <div>
            <strong>Faire une 1ère recherche</strong><br>
            Saisissez une compétence ou un rôle (ex. « Data », « UX », « Chef de projet ») dans la barre de recherche.
          </div>
        </li>
      </ul>
    </div>

    <div class="doc-modal-section doc-modal-card">
      <h4>Exemple concret</h4>
      <p>Vous arrivez dans une nouvelle équipe ? Créez votre compte, complétez votre profil, puis recherchez « Produit » pour identifier vos interlocuteurs clés.</p>
    </div>

    <div class="doc-modal-section">
      <h4>Bonnes pratiques</h4>
      <ul>
        <li>Bloquer 5 minutes pour configurer votre profil dès la première connexion.</li>
        <li>Tester au moins 2 ou 3 recherches différentes pour comprendre la logique des résultats.</li>
        <li>Mettre en favoris les profils ou compétences intéressants pour les retrouver plus vite.</li>
      </ul>
    </div>
  `
},

    gestion_profil: {
  title: "Gestion du profil",
  html: `
    <div class="doc-modal-section">
      <p>Votre profil est la carte d’identité FindIN : il doit permettre à n’importe quel collègue de comprendre qui vous êtes, ce que vous faites et comment collaborer avec vous.</p>
    </div>

    <div class="doc-modal-section">
      <h4>Schéma : construire un profil clair</h4>
      <ul class="doc-modal-steps">
        <li>
          <span class="doc-step-badge">1</span>
          <div>
            <strong>Mettre une photo reconnaissable</strong><br>
            Importez une photo nette, récente, où l’on vous identifie facilement (évitez les avatars ou photos de groupe).
          </div>
        </li>
        <li>
          <span class="doc-step-badge">2</span>
          <div>
            <strong>Clarifier votre intitulé de poste</strong><br>
            Utilisez un titre compréhensible par tous (ex. « Product Owner – App mobile » plutôt que « PO Squad 3 »).
          </div>
        </li>
        <li>
          <span class="doc-step-badge">3</span>
          <div>
            <strong>Rédiger un résumé court</strong><br>
            En 3–4 lignes, décrivez votre rôle, votre équipe, votre périmètre et vos sujets principaux.
          </div>
        </li>
        <li>
          <span class="doc-step-badge">4</span>
          <div>
            <strong>Structurer vos compétences</strong><br>
            Regroupez vos compétences par catégories (technique, métier, soft skills) et renseignez votre niveau.
          </div>
        </li>
      </ul>
    </div>

    <div class="doc-modal-section doc-modal-card">
      <h4>Exemple concret</h4>
      <p>Pour un profil « Data Analyst », mettez en avant vos outils (SQL, Power BI, Python), vos domaines (finance, marketing) et 2–3 projets clés (dashboards, automatisations, études).</p>
    </div>

    <div class="doc-modal-section">
      <h4>Checklist rapide</h4>
      <ul>
        <li>Votre photo est à jour et professionnelle.</li>
        <li>Votre titre de poste est compréhensible pour quelqu’un en dehors de votre équipe.</li>
        <li>Vous avez renseigné vos compétences principales avec un niveau réaliste.</li>
        <li>Votre résumé donne envie de vous contacter pour les bons sujets.</li>
      </ul>
    </div>
  `
},

    recherche_avancee: {
  title: "Recherche avancée",
  html: `
    <div class="doc-modal-section">
      <p>La recherche avancée vous permet de passer d’une idée floue (« j’ai besoin de quelqu’un en data ») à une liste de profils précis et exploitables.</p>
    </div>

    <div class="doc-modal-section">
      <h4>Schéma : construire une bonne recherche</h4>
      <ul class="doc-modal-steps">
        <li>
          <span class="doc-step-badge">1</span>
          <div>
            <strong>Définir la compétence principale</strong><br>
            Exemple : « Python », « UX Research », « Gestion de projet », « Sécurité réseau ».
          </div>
        </li>
        <li>
          <span class="doc-step-badge">2</span>
          <div>
            <strong>Ajouter le contexte</strong><br>
            Exemple : domaine (data, mobile, produit, finance), type de produit ou environnement.
          </div>
        </li>
        <li>
          <span class="doc-step-badge">3</span>
          <div>
            <strong>Appliquer les filtres</strong><br>
            Localisation, équipe, années d’expérience, disponibilité, langue, etc. selon ce dont vous avez besoin.
          </div>
        </li>
        <li>
          <span class="doc-step-badge">4</span>
          <div>
            <strong>Ajuster selon les résultats</strong><br>
            Si la liste est trop longue, ajoutez un filtre ou affinez la compétence. Si elle est trop courte, élargissez le contexte.
          </div>
        </li>
      </ul>
    </div>

    <div class="doc-modal-section doc-modal-card">
      <h4>Exemples de requêtes utiles</h4>
      <ul>
        <li><strong>Profil précis</strong> : « Data Engineer » avec 3+ ans d’expérience dans le périmètre data.</li>
        <li><strong>Exploration</strong> : tous les profils avec « UX » dans une direction ou un pays précis.</li>
        <li><strong>Relais internes</strong> : recherche par outil (ex. « Snowflake », « Figma », « Terraform »).</li>
      </ul>
    </div>

    <div class="doc-modal-section">
      <h4>Bonnes pratiques</h4>
      <ul>
        <li>Commencer par une recherche simple, puis ajouter des filtres si nécessaire.</li>
        <li>Tester plusieurs formulations (ex. « Product Manager » / « Chef de produit »).</li>
        <li>Conserver ou noter les combinaisons qui donnent de bons résultats pour les réutiliser.</li>
      </ul>
    </div>
  `
},

    gestion_projets: {
  title: "Gestion de projets",
  html: `
    <div class="doc-modal-section">
      <p>Les projets vous aident à regrouper les profils autour d’un même besoin (mission, poste, initiative interne) et à suivre leur avancement.</p>
    </div>

    <div class="doc-modal-section">
      <h4>Schéma : cycle de vie d’un projet</h4>
      <ul class="doc-modal-steps">
        <li>
          <span class="doc-step-badge">1</span>
          <div>
            <strong>Créer le projet</strong><br>
            Donnez un nom explicite (ex. « Recrutement Dev Backend Q3 ») et décrivez le contexte en quelques lignes.
          </div>
        </li>
        <li>
          <span class="doc-step-badge">2</span>
          <div>
            <strong>Définir les besoins</strong><br>
            Listez les compétences clés, le niveau attendu, les contraintes (langue, site, disponibilité).
          </div>
        </li>
        <li>
          <span class="doc-step-badge">3</span>
          <div>
            <strong>Ajouter des profils</strong><br>
            Depuis la recherche FindIN, ajoutez les personnes pertinentes au projet pour centraliser vos options.
          </div>
        </li>
        <li>
          <span class="doc-step-badge">4</span>
          <div>
            <strong>Suivre l’avancement</strong><br>
            Mettez à jour le statut des profils (à contacter, en échange, retenu, non retenu) au fur et à mesure.
          </div>
        </li>
      </ul>
    </div>

    <div class="doc-modal-section doc-modal-card">
      <h4>Exemple concret</h4>
      <p>Pour une mission interne, créez un projet dédié, ajoutez tous les profils potentiels, notez les retours après chaque échange et suivez qui est disponible à la bonne période.</p>
    </div>

    <div class="doc-modal-section">
      <h4>Bonnes pratiques</h4>
      <ul>
        <li>Un projet par besoin clair plutôt qu’un « grand fourre‑tout ».</li>
        <li>Mettre les notes et décisions directement dans le projet pour garder l’historique.</li>
        <li>Partager le projet avec les personnes impliquées pour éviter les doublons et les malentendus.</li>
      </ul>
    </div>
  `
},

    tableau_bord: {
  title: "Tableau de bord",
  html: `
    <div class="doc-modal-section">
      <p>Le tableau de bord vous donne une vision synthétique de votre usage de FindIN et de la dynamique des talents autour de vous.</p>
    </div>

    <div class="doc-modal-section">
      <h4>Schéma : lire son tableau de bord</h4>
      <ul class="doc-modal-steps">
        <li>
          <span class="doc-step-badge">1</span>
          <div>
            <strong>Observer le volume de recherches</strong><br>
            Identifiez les périodes où vos recherches sont les plus fréquentes et les thèmes récurrents.
          </div>
        </li>
        <li>
          <span class="doc-step-badge">2</span>
          <div>
            <strong>Suivre les profils consultés / ajoutés</strong><br>
            Vérifiez si vos recherches aboutissent réellement à des profils ajoutés à des projets ou suivis.
          </div>
        </li>
        <li>
          <span class="doc-step-badge">3</span>
          <div>
            <strong>Analyser l’engagement</strong><br>
            Regardez combien de contacts, messages ou collaborations naissent de vos recherches.
          </div>
        </li>
      </ul>
    </div>

    <div class="doc-modal-section doc-modal-card">
      <h4>Exemple d’utilisation</h4>
      <p>Si vous faites beaucoup de recherches mais ajoutez peu de profils à vos projets, cela peut indiquer que vos critères sont trop stricts ou mal formulés.</p>
    </div>

    <div class="doc-modal-section">
      <h4>Bonnes pratiques</h4>
      <ul>
        <li>Regarder régulièrement les tendances plutôt qu’un seul chiffre isolé.</li>
        <li>Tester de nouvelles façons de chercher si certaines requêtes donnent peu de résultats actionnables.</li>
        <li>Utiliser les données du tableau de bord pour argumenter sur vos besoins de ressources ou de compétences.</li>
      </ul>
    </div>
  `
},

    parametres: {
  title: "Paramètres",
  html: `
    <div class="doc-modal-section">
      <p>Les paramètres vous permettent de personnaliser FindIN à votre contexte de travail et de contrôler la visibilité de vos informations.</p>
    </div>

    <div class="doc-modal-section">
      <h4>Schéma : 3 zones à vérifier</h4>
      <ul class="doc-modal-steps">
        <li>
          <span class="doc-step-badge">1</span>
          <div>
            <strong>Compte</strong><br>
            Vérifiez votre e‑mail, votre mot de passe, la langue et le fuseau horaire pour que tout soit aligné avec votre organisation.
          </div>
        </li>
        <li>
          <span class="doc-step-badge">2</span>
          <div>
            <strong>Visibilité</strong><br>
            Choisissez qui peut voir votre profil complet et quelles informations sont visibles par défaut.
          </div>
        </li>
        <li>
          <span class="doc-step-badge">3</span>
          <div>
            <strong>Notifications</strong><br>
            Décidez quels événements déclenchent un e‑mail ou une notification (projets, messages, nouveautés, etc.).
          </div>
        </li>
      </ul>
    </div>

    <div class="doc-modal-section doc-modal-card">
      <h4>Exemple de réglage utile</h4>
      <p>Si vous êtes très sollicité, gardez les notifications pour les messages et projets, mais désactivez les alertes moins prioritaires pour rester concentré.</p>
    </div>

    <div class="doc-modal-section">
      <h4>Bonnes pratiques</h4>
      <ul>
        <li>Mettre à jour votre mot de passe selon les recommandations de votre organisation.</li>
        <li>Revoir votre visibilité si vous changez d’équipe, de pays ou de périmètre.</li>
        <li>Ajuster vos notifications pour ne pas louper les informations critiques sans être submergé.</li>
      </ul>
    </div>
  `
}};


 document.addEventListener("DOMContentLoaded", () => {
    const modalOverlay = document.getElementById('docModalOverlay');
    const modalTitle   = document.getElementById('docModalTitle');
    const modalIcon    = document.getElementById('docModalIcon');
    const modalContent = document.getElementById('docModalContent');

    document.querySelectorAll('.doc-card[data-popup="doc"]').forEach(card => {
      card.addEventListener('click', (e) => {
        e.preventDefault();

        const key    = card.getAttribute('data-doc-key');
        const config = docPopupContents[key];
        if (!config) return;

        modalTitle.textContent = config.title;

        const iconDiv   = card.querySelector('.doc-card-icon');
        const iconClass = iconDiv.className.replace('doc-card-icon', '').trim();
        const icon      = card.querySelector('.doc-card-icon i').className;
        modalIcon.className = 'doc-modal-icon ' + iconClass;
        modalIcon.innerHTML = `<i class="${icon}"></i>`;

        modalContent.innerHTML = config.html;
        modalOverlay.classList.add('active');
      });
    });

    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) {
        modalOverlay.classList.remove('active');
      }
    });

    const t=document.getElementById('themeToggle'),h=document.documentElement,i=t.querySelector('i'),s=localStorage.getItem('theme')||'dark';
    h.setAttribute('data-theme',s);i.className=s==='dark'?'fas fa-moon':'fas fa-sun';
    t.addEventListener('click',()=>{const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);i.className=n==='dark'?'fas fa-moon':'fas fa-sun';});
    document.querySelectorAll('.faq-question').forEach(q => q.addEventListener('click', () => q.parentElement.classList.toggle('active')));

    const docSearchIndex = [
      { key: "demarrage_rapide", labels: ["créer un compte","creer un compte","inscription","s'inscrire","je me crée un compte","démarrage","demarrage","premiers pas","premiere connexion"] },
      { key: "gestion_profil",   labels: ["profil","mon profil","gestion du profil","changer ma photo","ajouter mes compétences","modifier mon titre"] },
      { key: "recherche_avancee",labels: ["recherche","rechercher un collaborateur","recherche avancée","filtres","trouver quelqu'un","trouver un profil"] },
      { key: "gestion_projets",  labels: ["projet","projets","gestion de projets","mission","créer un projet","staffing"] },
      { key: "tableau_bord",     labels: ["tableau de bord","statistiques","stats","indicateurs","suivre mon activité"] },
      { key: "parametres",       labels: ["paramètres","parametres","configuration","notifications","confidentialité","mot de passe"] }
    ];

    const docSearchInput = document.querySelector('.search-box input');
    if (docSearchInput) {
      docSearchInput.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter') return;

        const value = docSearchInput.value.trim().toLowerCase();
        if (!value) return;

        let matchedKey = null;

        for (const entry of docSearchIndex) {
          for (const label of entry.labels) {
            if (value.includes(label.toLowerCase())) {
              matchedKey = entry.key;
              break;
            }
          }
          if (matchedKey) break;
        }

        if (!matchedKey) {
          if (value.includes("compte") || value.includes("inscri")) {
            matchedKey = "demarrage_rapide";
          } else if (value.includes("profil")) {
            matchedKey = "gestion_profil";
          } else if (value.includes("recherche") || value.includes("collaborateur")) {
            matchedKey = "recherche_avancee";
          }
        }

        if (!matchedKey) return;

        const targetCard = document.querySelector(`.doc-card[data-doc-key="${matchedKey}"]`);
        if (targetCard) {
          targetCard.click();
        }
      });
    }
  });
</script>



</body>
</html>
