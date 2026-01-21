-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : lun. 10 nov. 2025 à 12:02
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_competences`
--

-- --------------------------------------------------------

--
-- Structure de la table `besoins_competences_projets`
--

CREATE TABLE `besoins_competences_projets` (
  `id_besoin` char(36) NOT NULL DEFAULT uuid(),
  `id_projet` char(36) NOT NULL,
  `id_competence` char(36) NOT NULL,
  `niveau_requis` tinyint(4) DEFAULT NULL CHECK (`niveau_requis` between 1 and 5),
  `description` text DEFAULT NULL,
  `statut` enum('ouvert','en_cours','comble') DEFAULT 'ouvert',
  `id_utilisateur_affecte` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories_competences`
--

CREATE TABLE `categories_competences` (
  `id_categorie` char(36) NOT NULL DEFAULT uuid(),
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `competences`
--

CREATE TABLE `competences` (
  `id_competence` char(36) NOT NULL DEFAULT uuid(),
  `nom` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `id_categorie` char(36) NOT NULL,
  `type_competence` enum('savoir_faire','savoir_etre','expertise') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `competences_modeles_poste`
--

CREATE TABLE `competences_modeles_poste` (
  `id_comp_modele` char(36) NOT NULL DEFAULT uuid(),
  `id_modele` char(36) NOT NULL,
  `id_competence` char(36) NOT NULL,
  `niveau_requis` tinyint(4) DEFAULT NULL CHECK (`niveau_requis` between 1 and 5),
  `est_critique` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `competences_objectifs`
--

CREATE TABLE `competences_objectifs` (
  `id_comp_objectif` char(36) NOT NULL DEFAULT uuid(),
  `id_objectif` char(36) NOT NULL,
  `id_competence` char(36) NOT NULL,
  `effectif_cible` int(11) DEFAULT NULL,
  `niveau_cible` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `competences_utilisateurs`
--

CREATE TABLE `competences_utilisateurs` (
  `id_comp_utilisateur` char(36) NOT NULL DEFAULT uuid(),
  `id_utilisateur` char(36) NOT NULL,
  `id_competence` char(36) NOT NULL,
  `niveau_declare` tinyint(4) DEFAULT NULL CHECK (`niveau_declare` between 1 and 5),
  `niveau_valide` tinyint(4) DEFAULT NULL CHECK (`niveau_valide` between 1 and 5),
  `id_manager_validateur` char(36) DEFAULT NULL,
  `date_validation` timestamp NULL DEFAULT NULL,
  `id_source` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contenus`
--

CREATE TABLE `contenus` (
  `cle_contenu` varchar(50) NOT NULL,
  `titre` text DEFAULT NULL,
  `corps` text NOT NULL,
  `derniere_mise_a_jour` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `departements`
--

CREATE TABLE `departements` (
  `id_departement` char(36) NOT NULL DEFAULT uuid(),
  `nom` varchar(150) NOT NULL,
  `id_entreprise` char(36) NOT NULL,
  `type_departement` enum('entreprise','division','departement','unite','equipe') NOT NULL,
  `id_parent` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `documents_utilisateurs`
--

CREATE TABLE `documents_utilisateurs` (
  `id_document` char(36) NOT NULL DEFAULT uuid(),
  `id_utilisateur` char(36) NOT NULL,
  `chemin_fichier` text NOT NULL,
  `nom_fichier` varchar(255) DEFAULT NULL,
  `type_document` varchar(50) DEFAULT NULL,
  `id_source_generee` char(36) DEFAULT NULL,
  `date_televersement` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `entreprises`
--

CREATE TABLE `entreprises` (
  `id_entreprise` char(36) NOT NULL DEFAULT uuid(),
  `nom` varchar(200) NOT NULL,
  `raison_sociale` varchar(200) DEFAULT NULL,
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `faq`
--

CREATE TABLE `faq` (
  `id_faq` char(36) NOT NULL DEFAULT uuid(),
  `question` text NOT NULL,
  `reponse` text NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `derniere_mise_a_jour` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `membres_projets`
--

CREATE TABLE `membres_projets` (
  `id_membre_projet` char(36) NOT NULL DEFAULT uuid(),
  `id_projet` char(36) NOT NULL,
  `id_utilisateur` char(36) NOT NULL,
  `role_dans_projet` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `modeles_poste`
--

CREATE TABLE `modeles_poste` (
  `id_modele` char(36) NOT NULL DEFAULT uuid(),
  `titre` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `id_departement` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `objectifs_strategiques`
--

CREATE TABLE `objectifs_strategiques` (
  `id_objectif` char(36) NOT NULL DEFAULT uuid(),
  `titre` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `date_cible` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parametres_application`
--

CREATE TABLE `parametres_application` (
  `cle_parametre` varchar(100) NOT NULL,
  `valeur` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `projets`
--

CREATE TABLE `projets` (
  `id_projet` char(36) NOT NULL DEFAULT uuid(),
  `nom` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('planifie','actif','termine','en_pause') DEFAULT 'actif',
  `id_responsable` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sources_donnees`
--

CREATE TABLE `sources_donnees` (
  `id_source` char(36) NOT NULL DEFAULT uuid(),
  `type_source` enum('import_cv','synchronisation_lms','entretien_annuel','feedback_360','declaration_utilisateur','validation_projet') NOT NULL,
  `description` text DEFAULT NULL,
  `date_source` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` char(36) NOT NULL DEFAULT uuid(),
  `email` varchar(255) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_departement` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `besoins_competences_projets`
--
ALTER TABLE `besoins_competences_projets`
  ADD PRIMARY KEY (`id_besoin`),
  ADD KEY `fk_besoins_projets_projet` (`id_projet`),
  ADD KEY `fk_besoins_projets_competence` (`id_competence`),
  ADD KEY `fk_besoins_projets_utilisateur` (`id_utilisateur_affecte`);

--
-- Index pour la table `categories_competences`
--
ALTER TABLE `categories_competences`
  ADD PRIMARY KEY (`id_categorie`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `competences`
--
ALTER TABLE `competences`
  ADD PRIMARY KEY (`id_competence`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD KEY `fk_competences_categorie` (`id_categorie`);

--
-- Index pour la table `competences_modeles_poste`
--
ALTER TABLE `competences_modeles_poste`
  ADD PRIMARY KEY (`id_comp_modele`),
  ADD UNIQUE KEY `unique_competence_modele` (`id_modele`,`id_competence`),
  ADD KEY `fk_comp_modele_competence` (`id_competence`);

--
-- Index pour la table `competences_objectifs`
--
ALTER TABLE `competences_objectifs`
  ADD PRIMARY KEY (`id_comp_objectif`),
  ADD KEY `fk_comp_objectif_objectif` (`id_objectif`),
  ADD KEY `fk_comp_objectif_competence` (`id_competence`);

--
-- Index pour la table `competences_utilisateurs`
--
ALTER TABLE `competences_utilisateurs`
  ADD PRIMARY KEY (`id_comp_utilisateur`),
  ADD UNIQUE KEY `unique_competence_utilisateur` (`id_utilisateur`,`id_competence`),
  ADD KEY `fk_comp_utilisateur_competence` (`id_competence`),
  ADD KEY `fk_comp_utilisateur_manager` (`id_manager_validateur`),
  ADD KEY `fk_comp_utilisateur_source` (`id_source`);

--
-- Index pour la table `contenus`
--
ALTER TABLE `contenus`
  ADD PRIMARY KEY (`cle_contenu`);

--
-- Index pour la table `departements`
--
ALTER TABLE `departements`
  ADD PRIMARY KEY (`id_departement`),
  ADD KEY `fk_departements_entreprise` (`id_entreprise`),
  ADD KEY `fk_departements_parent` (`id_parent`);

--
-- Index pour la table `documents_utilisateurs`
--
ALTER TABLE `documents_utilisateurs`
  ADD PRIMARY KEY (`id_document`),
  ADD KEY `fk_documents_utilisateurs_utilisateur` (`id_utilisateur`),
  ADD KEY `fk_documents_utilisateurs_source` (`id_source_generee`);

--
-- Index pour la table `entreprises`
--
ALTER TABLE `entreprises`
  ADD PRIMARY KEY (`id_entreprise`);

--
-- Index pour la table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id_faq`);

--
-- Index pour la table `membres_projets`
--
ALTER TABLE `membres_projets`
  ADD PRIMARY KEY (`id_membre_projet`),
  ADD UNIQUE KEY `unique_membre_projet` (`id_projet`,`id_utilisateur`),
  ADD KEY `fk_membres_projets_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `modeles_poste`
--
ALTER TABLE `modeles_poste`
  ADD PRIMARY KEY (`id_modele`),
  ADD KEY `fk_modeles_poste_departement` (`id_departement`);

--
-- Index pour la table `objectifs_strategiques`
--
ALTER TABLE `objectifs_strategiques`
  ADD PRIMARY KEY (`id_objectif`);

--
-- Index pour la table `parametres_application`
--
ALTER TABLE `parametres_application`
  ADD PRIMARY KEY (`cle_parametre`);

--
-- Index pour la table `projets`
--
ALTER TABLE `projets`
  ADD PRIMARY KEY (`id_projet`),
  ADD KEY `fk_projets_responsable` (`id_responsable`);

--
-- Index pour la table `sources_donnees`
--
ALTER TABLE `sources_donnees`
  ADD PRIMARY KEY (`id_source`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_utilisateurs_departement` (`id_departement`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `besoins_competences_projets`
--
ALTER TABLE `besoins_competences_projets`
  ADD CONSTRAINT `fk_besoins_projets_competence` FOREIGN KEY (`id_competence`) REFERENCES `competences` (`id_competence`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_besoins_projets_projet` FOREIGN KEY (`id_projet`) REFERENCES `projets` (`id_projet`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_besoins_projets_utilisateur` FOREIGN KEY (`id_utilisateur_affecte`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `competences`
--
ALTER TABLE `competences`
  ADD CONSTRAINT `fk_competences_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `categories_competences` (`id_categorie`);

--
-- Contraintes pour la table `competences_modeles_poste`
--
ALTER TABLE `competences_modeles_poste`
  ADD CONSTRAINT `fk_comp_modele_competence` FOREIGN KEY (`id_competence`) REFERENCES `competences` (`id_competence`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comp_modele_poste` FOREIGN KEY (`id_modele`) REFERENCES `modeles_poste` (`id_modele`) ON DELETE CASCADE;

--
-- Contraintes pour la table `competences_objectifs`
--
ALTER TABLE `competences_objectifs`
  ADD CONSTRAINT `fk_comp_objectif_competence` FOREIGN KEY (`id_competence`) REFERENCES `competences` (`id_competence`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comp_objectif_objectif` FOREIGN KEY (`id_objectif`) REFERENCES `objectifs_strategiques` (`id_objectif`) ON DELETE CASCADE;

--
-- Contraintes pour la table `competences_utilisateurs`
--
ALTER TABLE `competences_utilisateurs`
  ADD CONSTRAINT `fk_comp_utilisateur_competence` FOREIGN KEY (`id_competence`) REFERENCES `competences` (`id_competence`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comp_utilisateur_manager` FOREIGN KEY (`id_manager_validateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_comp_utilisateur_source` FOREIGN KEY (`id_source`) REFERENCES `sources_donnees` (`id_source`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_comp_utilisateur_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `departements`
--
ALTER TABLE `departements`
  ADD CONSTRAINT `fk_departements_entreprise` FOREIGN KEY (`id_entreprise`) REFERENCES `entreprises` (`id_entreprise`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_departements_parent` FOREIGN KEY (`id_parent`) REFERENCES `departements` (`id_departement`) ON DELETE SET NULL;

--
-- Contraintes pour la table `documents_utilisateurs`
--
ALTER TABLE `documents_utilisateurs`
  ADD CONSTRAINT `fk_documents_utilisateurs_source` FOREIGN KEY (`id_source_generee`) REFERENCES `sources_donnees` (`id_source`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_documents_utilisateurs_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `membres_projets`
--
ALTER TABLE `membres_projets`
  ADD CONSTRAINT `fk_membres_projets_projet` FOREIGN KEY (`id_projet`) REFERENCES `projets` (`id_projet`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_membres_projets_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `modeles_poste`
--
ALTER TABLE `modeles_poste`
  ADD CONSTRAINT `fk_modeles_poste_departement` FOREIGN KEY (`id_departement`) REFERENCES `departements` (`id_departement`) ON DELETE SET NULL;

--
-- Contraintes pour la table `projets`
--
ALTER TABLE `projets`
  ADD CONSTRAINT `fk_projets_responsable` FOREIGN KEY (`id_responsable`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `fk_utilisateurs_departement` FOREIGN KEY (`id_departement`) REFERENCES `departements` (`id_departement`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
