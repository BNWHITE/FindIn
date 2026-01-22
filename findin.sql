-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 22 jan. 2026 à 11:32
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
-- Base de données : `findin`
--

-- --------------------------------------------------------

--
-- Structure de la table `certifications`
--

CREATE TABLE `certifications` (
  `id_certification` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `organisme` varchar(255) DEFAULT NULL,
  `date_obtention` date DEFAULT NULL,
  `date_expiration` date DEFAULT NULL,
  `url_verification` varchar(255) DEFAULT NULL,
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `competences`
--

CREATE TABLE `competences` (
  `id_competence` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type_competence` varchar(50) DEFAULT 'technique',
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `competences`
--

INSERT INTO `competences` (`id_competence`, `nom`, `description`, `type_competence`, `cree_le`) VALUES
(1, 'PHP', 'Programmation PHP', 'technique', '2026-01-21 23:28:50'),
(2, 'JavaScript', 'Programmation JavaScript', 'technique', '2026-01-21 23:28:50'),
(3, 'Python', 'Programmation Python', 'technique', '2026-01-21 23:28:50'),
(4, 'Communication', 'Compétences de communication', 'soft_skill', '2026-01-21 23:28:50'),
(5, 'Leadership', 'Leadership et gestion d\'équipe', 'soft_skill', '2026-01-21 23:28:50'),
(6, 'SQL', 'Base de données SQL', 'technique', '2026-01-21 23:28:50'),
(7, 'React', 'Framework React', 'technique', '2026-01-21 23:28:50'),
(8, 'Anglais', 'Langue anglaise', 'langue', '2026-01-21 23:28:50');

-- --------------------------------------------------------

--
-- Structure de la table `competences_utilisateurs`
--

CREATE TABLE `competences_utilisateurs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_competence` int(11) NOT NULL,
  `niveau_declare` int(11) DEFAULT 1,
  `niveau_valide` int(11) DEFAULT NULL,
  `id_manager_validateur` int(11) DEFAULT NULL,
  `date_validation` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes_validation`
--

CREATE TABLE `demandes_validation` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `competence_id` int(11) NOT NULL,
  `niveau_declare` int(11) NOT NULL,
  `statut` varchar(50) DEFAULT 'en_attente',
  `manager_id` int(11) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `date_demande` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_validation` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `departements`
--

CREATE TABLE `departements` (
  `id_departement` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `departements`
--

INSERT INTO `departements` (`id_departement`, `nom`, `description`, `cree_le`) VALUES
(1, 'IT', 'Informatique et Technologie', '2026-01-22 08:52:55'),
(2, 'RH', 'Ressources Humaines', '2026-01-22 08:52:55'),
(3, 'Ventes', 'Département Commercial', '2026-01-22 08:52:55'),
(4, 'Marketing', 'Marketing et Communication', '2026-01-22 08:52:55');

-- --------------------------------------------------------

--
-- Structure de la table `documents`
--

CREATE TABLE `documents` (
  `id_document` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `url_fichier` varchar(255) NOT NULL,
  `date_upload` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `invitations`
--

CREATE TABLE `invitations` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL COMMENT 'Email invité',
  `prenom` varchar(100) NOT NULL COMMENT 'Prénom',
  `nom` varchar(100) NOT NULL COMMENT 'Nom',
  `token` varchar(255) NOT NULL COMMENT 'Token d''acceptation unique',
  `role` varchar(50) DEFAULT 'employe' COMMENT 'Rôle attribué',
  `manager_id` int(11) DEFAULT NULL COMMENT 'Manager assigné',
  `departement` varchar(255) DEFAULT NULL COMMENT 'Département',
  `statut` varchar(50) DEFAULT 'pending' COMMENT 'pending, accepted, expired',
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Expiration du token',
  `accepted_at` timestamp NULL DEFAULT NULL COMMENT 'Date d''acceptation',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID utilisateur après acceptation',
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp(),
  `modifie_le` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Invitations pour les nouveaux utilisateurs';

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `sujet` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `projets`
--

CREATE TABLE `projets` (
  `id_projet` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `responsable_id` int(11) DEFAULT NULL,
  `statut` varchar(50) DEFAULT 'en_cours',
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp(),
  `modifie_le` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `projets_utilisateurs`
--

CREATE TABLE `projets_utilisateurs` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_projet` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reunions`
--

CREATE TABLE `reunions` (
  `id_reunion` int(11) NOT NULL,
  `employe_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date_reunion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `duree_minutes` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'planifiee',
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL COMMENT 'Nom du rôle: employe, manager, rh, admin',
  `description` text DEFAULT NULL COMMENT 'Description du rôle',
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Permissions JSON' CHECK (json_valid(`permissions`)),
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp(),
  `modifie_le` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Rôles disponibles dans le système';

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id_role`, `nom`, `description`, `permissions`, `cree_le`, `modifie_le`) VALUES
(1, 'employe', 'Employé standard', '[\"view_profile\", \"declare_competencies\", \"view_dashboard\"]', '2026-01-22 08:56:45', '2026-01-22 08:56:45'),
(2, 'manager', 'Manager/Responsable', '[\"view_profile\", \"declare_competencies\", \"view_dashboard\", \"validate_competencies\", \"manage_team\"]', '2026-01-22 08:56:45', '2026-01-22 08:56:45'),
(3, 'rh', 'Ressources Humaines', '[\"manage_users\", \"manage_invitations\", \"view_reports\", \"manage_competencies\", \"view_dashboard\"]', '2026-01-22 08:56:45', '2026-01-22 08:56:45'),
(4, 'admin', 'Administrateur système', '[\"*\"]', '2026-01-22 08:56:45', '2026-01-22 08:56:45');

-- --------------------------------------------------------

--
-- Structure de la table `tests`
--

CREATE TABLE `tests` (
  `id_test` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `competence_id` int(11) DEFAULT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `score_obtenu` int(11) DEFAULT NULL,
  `score_maximum` int(11) DEFAULT NULL,
  `date_test` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_completion` timestamp NULL DEFAULT NULL,
  `status` varchar(50) DEFAULT 'en_cours'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `id_departement` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'employe',
  `photo` varchar(255) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp(),
  `modifie_le` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `email`, `prenom`, `nom`, `mot_de_passe`, `id_departement`, `role`, `photo`, `manager_id`, `cree_le`, `modifie_le`) VALUES
(1, 'admin@findin.fr', 'Admin', 'FindIN', '$2y$12$xsrkjb39.4ixWYJxszjU0eRAB6nWRsgJrCVjG03OYaCYXuC6HMC/i', NULL, 'admin', NULL, NULL, '2026-01-22 09:53:59', '2026-01-22 09:53:59'),
(2, 'test@findin.fr', 'Test', 'User', '$2y$12$xsrkjb39.4ixWYJxszjU0eRAB6nWRsgJrCVjG03OYaCYXuC6HMC/i', NULL, 'employe', NULL, NULL, '2026-01-22 09:53:59', '2026-01-22 09:53:59'),
(3, 'henry@test.fr', 'Henry', 'Taylor', '$2y$12$s4IFWsLfSP7jXjQN4G4NYeeY0yWe61D1G.3YSlWjqjSJG99EHspIG', NULL, 'employe', NULL, NULL, '2026-01-22 09:53:59', '2026-01-22 09:53:59'),
(4, 'isabelle@test.fr', 'Isabelle', 'Martin', '$2y$12$BwoFaiMTP0qHHcBmLgW4LeV/6c8wUWk7FECK1GzdM4C7PFRGjdH.a', NULL, 'employe', NULL, NULL, '2026-01-22 09:53:59', '2026-01-22 09:53:59'),
(5, 'j.dupont@findin.fr', 'Jean', 'Dupont', '$2y$12$ozJkjvNThuGHq79Rl/tJUuDH6iQpEnlgrFTMHw26TqC4t4Ty7V4Oy', NULL, 'employe', NULL, NULL, '2026-01-22 09:53:59', '2026-01-22 09:53:59'),
(6, 'p.lechauve@findin.fr', 'Paul', 'Lechauve', '$2y$12$SOHhOc2EOQ/xXx3z9kiZtOfLGYP30MRdetrywXv9v7QQfm/a9tss2', NULL, 'employe', NULL, NULL, '2026-01-22 09:53:59', '2026-01-22 09:53:59'),
(7, 'blacknwhitemanagement@gmail.com', 'Seydina', 'Seydina Sy', '$2y$12$L4ZbD0p6SFfIRRvdaamUZOxkJvbmuaqhGp5QRGaevidQ7Ce2jSZZa', NULL, 'employe', NULL, NULL, '2026-01-22 09:53:59', '2026-01-22 09:53:59'),
(8, 'directtest_1769073651@findin.fr', 'Direct', 'Test', '$2y$12$ytJzcuNlZ0AW9EczK85PIueC/QQUZuT833xBH2VkWhYMxACrUeuPq', NULL, 'employe', NULL, NULL, '2026-01-22 09:53:59', '2026-01-22 09:53:59'),
(9, 'simulate_test_1769073685@findin.fr', 'Simulated', 'User', '$2y$12$rG/Tgq5.nyykwNG8gsPo6.a1ebMgzj66VNbzuiQBMu5cgoGJuoteu', NULL, 'employe', NULL, NULL, '2026-01-22 09:53:59', '2026-01-22 09:53:59'),
(10, 'y.ha@findin.fr', 'Yas', 'Harissi', '$2y$12$9Vp/PloQCQ9bf.Qw1bN/cuwIkh2LDH5rkqP6mA1PkUGvBMfUjPNDS', NULL, 'employe', NULL, NULL, '2026-01-22 09:53:59', '2026-01-22 09:53:59'),
(11, 'finaltest@findin.fr', 'j', 's', '$2y$12$ayKMUuzCgUF2TJ/m.y8C6O6SDynbPcGkPibdS0Uidgls.JUwwv4em', NULL, 'employe', NULL, NULL, '2026-01-22 09:53:59', '2026-01-22 09:53:59');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `certifications`
--
ALTER TABLE `certifications`
  ADD PRIMARY KEY (`id_certification`),
  ADD KEY `idx_user` (`user_id`);

--
-- Index pour la table `competences`
--
ALTER TABLE `competences`
  ADD PRIMARY KEY (`id_competence`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD KEY `idx_nom` (`nom`),
  ADD KEY `idx_type` (`type_competence`);

--
-- Index pour la table `competences_utilisateurs`
--
ALTER TABLE `competences_utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_comp` (`user_id`,`id_competence`),
  ADD KEY `id_competence` (`id_competence`),
  ADD KEY `id_manager_validateur` (`id_manager_validateur`),
  ADD KEY `idx_user` (`user_id`);

--
-- Index pour la table `demandes_validation`
--
ALTER TABLE `demandes_validation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_comp` (`user_id`,`competence_id`),
  ADD KEY `competence_id` (`competence_id`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_manager` (`manager_id`);

--
-- Index pour la table `departements`
--
ALTER TABLE `departements`
  ADD PRIMARY KEY (`id_departement`);

--
-- Index pour la table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id_document`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_type` (`type`);

--
-- Index pour la table `invitations`
--
ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD UNIQUE KEY `idx_token` (`token`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_expires` (`expires_at`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `manager_id` (`manager_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- Index pour la table `projets`
--
ALTER TABLE `projets`
  ADD PRIMARY KEY (`id_projet`),
  ADD KEY `responsable_id` (`responsable_id`),
  ADD KEY `idx_statut` (`statut`);

--
-- Index pour la table `projets_utilisateurs`
--
ALTER TABLE `projets_utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_projet_user` (`projet_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `reunions`
--
ALTER TABLE `reunions`
  ADD PRIMARY KEY (`id_reunion`),
  ADD KEY `manager_id` (`manager_id`),
  ADD KEY `idx_date` (`date_reunion`),
  ADD KEY `idx_employe` (`employe_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD UNIQUE KEY `idx_nom` (`nom`),
  ADD KEY `idx_cree_le` (`cree_le`);

--
-- Index pour la table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id_test`),
  ADD KEY `competence_id` (`competence_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_manager` (`manager_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `certifications`
--
ALTER TABLE `certifications`
  MODIFY `id_certification` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `competences`
--
ALTER TABLE `competences`
  MODIFY `id_competence` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `competences_utilisateurs`
--
ALTER TABLE `competences_utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `demandes_validation`
--
ALTER TABLE `demandes_validation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `departements`
--
ALTER TABLE `departements`
  MODIFY `id_departement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `documents`
--
ALTER TABLE `documents`
  MODIFY `id_document` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `invitations`
--
ALTER TABLE `invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `projets`
--
ALTER TABLE `projets`
  MODIFY `id_projet` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `projets_utilisateurs`
--
ALTER TABLE `projets_utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reunions`
--
ALTER TABLE `reunions`
  MODIFY `id_reunion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `tests`
--
ALTER TABLE `tests`
  MODIFY `id_test` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `certifications`
--
ALTER TABLE `certifications`
  ADD CONSTRAINT `certifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `competences_utilisateurs`
--
ALTER TABLE `competences_utilisateurs`
  ADD CONSTRAINT `competences_utilisateurs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `competences_utilisateurs_ibfk_2` FOREIGN KEY (`id_competence`) REFERENCES `competences` (`id_competence`) ON DELETE CASCADE,
  ADD CONSTRAINT `competences_utilisateurs_ibfk_3` FOREIGN KEY (`id_manager_validateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `demandes_validation`
--
ALTER TABLE `demandes_validation`
  ADD CONSTRAINT `demandes_validation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `demandes_validation_ibfk_2` FOREIGN KEY (`competence_id`) REFERENCES `competences` (`id_competence`) ON DELETE CASCADE,
  ADD CONSTRAINT `demandes_validation_ibfk_3` FOREIGN KEY (`manager_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `invitations`
--
ALTER TABLE `invitations`
  ADD CONSTRAINT `invitations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL,
  ADD CONSTRAINT `invitations_ibfk_2` FOREIGN KEY (`manager_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `projets`
--
ALTER TABLE `projets`
  ADD CONSTRAINT `projets_ibfk_1` FOREIGN KEY (`responsable_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `projets_utilisateurs`
--
ALTER TABLE `projets_utilisateurs`
  ADD CONSTRAINT `projets_utilisateurs_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id_projet`) ON DELETE CASCADE,
  ADD CONSTRAINT `projets_utilisateurs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reunions`
--
ALTER TABLE `reunions`
  ADD CONSTRAINT `reunions_ibfk_1` FOREIGN KEY (`employe_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `reunions_ibfk_2` FOREIGN KEY (`manager_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `tests_ibfk_2` FOREIGN KEY (`competence_id`) REFERENCES `competences` (`id_competence`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
