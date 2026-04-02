-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 02 avr. 2026 à 23:38
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `work4green`
--

-- --------------------------------------------------------

--
-- Structure de la table `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `companyname` varchar(100) NOT NULL,
  `xp` int(11) NOT NULL,
  `level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `company`
--

INSERT INTO `company` (`id`, `companyname`, `xp`, `level`) VALUES
(1, 'Eco-Tech Solutions', 2750, 6),
(2, 'Green Horizon', 5800, 12),
(3, 'Petit Potager SARL', 150, 1);

-- --------------------------------------------------------

--
-- Structure de la table `items_catalog`
--

CREATE TABLE `items_catalog` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `type` enum('avatar','badge') DEFAULT NULL,
  `level_required` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `items_catalog`
--

INSERT INTO `items_catalog` (`id`, `name`, `image_path`, `type`, `level_required`) VALUES
(1, 'Scout Doe', './Images/perso.svg', 'avatar', 2),
(2, 'Happy Doe', './Images/perso-01.svg', 'avatar', 1),
(3, 'Classic Doe', './Images/perso-02.svg', 'avatar', 0),
(4, 'Curieux', './Images/perso-03.svg', 'avatar', 4),
(5, 'Interrogatif', './Images/perso-04.svg', 'avatar', 5),
(6, 'Observateur', './Images/perso-05.svg', 'avatar', 6),
(7, 'Fan Work4Green', './Images/perso-06.svg', 'avatar', 7),
(8, 'Badge Surprise', './Images/Badge-6.png', 'badge', 1),
(9, 'Badge Joie', './Images/Badge-7.png', 'badge', 2),
(10, 'Badge Scout', './Images/Badge-8.png', 'badge', 3),
(11, 'Badge Trophée', './Images/Badge-1.png', 'badge', 4),
(12, 'Badge Couronne', './Images/Badge-2.png', 'badge', 5),
(13, 'Badge Feuille', './Images/Badge-3.png', 'badge', 6),
(14, 'Badge Vélo', './Images/Badge-4.png', 'badge', 7),
(15, 'Badge Étoile', './Images/Badge-5.png', 'badge', 8);

-- --------------------------------------------------------

--
-- Structure de la table `quest`
--

CREATE TABLE `quest` (
  `quest_id` int(11) NOT NULL,
  `quest_name` varchar(50) NOT NULL,
  `quest_description` varchar(300) NOT NULL,
  `quest_xp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `quest`
--

INSERT INTO `quest` (`quest_id`, `quest_name`, `quest_description`, `quest_xp`) VALUES
(1, 'Déplacement non polluant', 'Vélo, marche ou trottinette', 50),
(2, 'Déplacement transports en commun', 'Bus, train ou tram', 40),
(3, 'Déplacement covoiturage', 'Voyager à plusieurs en voiture', 80),
(4, 'Éteindre les appareils électriques', 'Veille et lumières coupées', 60),
(5, 'Nettoyage de la boîte mail professionnelle', 'Suppression des mails inutiles', 30);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `lastname` varchar(25) NOT NULL,
  `firstname` varchar(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `verified` tinyint(1) NOT NULL,
  `xp` int(100) NOT NULL,
  `level` int(100) NOT NULL,
  `invitation_status` int(3) NOT NULL,
  `company_id` int(11) NOT NULL,
  `current_avatar_id` int(11) DEFAULT NULL,
  `fav_badge_1` int(11) DEFAULT NULL,
  `fav_badge_2` int(11) DEFAULT NULL,
  `fav_badge_3` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `lastname`, `firstname`, `email`, `password`, `verified`, `xp`, `level`, `invitation_status`, `company_id`, `current_avatar_id`, `fav_badge_1`, `fav_badge_2`, `fav_badge_3`) VALUES
(1, 'Dupont', 'Marc', 'marc.dupont@ecotech.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 450, 5, 0, 1, 1, NULL, NULL, NULL),
(2, 'Lemoine', 'Sophie', 's.lemoine@greenh.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 890, 10, 0, 2, 6, NULL, NULL, NULL),
(3, 'Rivière', 'Jean', 'jean@potager.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 120, 2, 0, 3, 3, NULL, NULL, NULL),
(4, 'Martin', 'Lucas', 'l.martin@ecotech.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 230, 3, 1, 1, 2, NULL, NULL, NULL),
(5, 'Bernard', 'Emma', 'e.bernard@ecotech.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 150, 2, 1, 1, 3, NULL, NULL, NULL),
(6, 'Petit', 'Chloé', 'c.petit@greenh.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 600, 7, 1, 2, 5, NULL, NULL, NULL),
(7, 'Garnier', 'Thomas', 't.garnier@mail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 50, 1, 0, 0, 3, NULL, NULL, NULL),
(8, 'Faure', 'Julie', 'j.faure@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 10, 0, 0, 0, 2, NULL, NULL, NULL),
(9, 'Rousseau', 'Antoine', 'a.rousseau@outlook.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 0, 0, 0, 0, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_inventory`
--

CREATE TABLE `user_inventory` (
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `obtained_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `user_inventory`
--

INSERT INTO `user_inventory` (`user_id`, `item_id`, `obtained_at`) VALUES
(1, 1, '2026-04-02 23:36:09'),
(1, 3, '2026-04-02 23:36:09'),
(2, 3, '2026-04-02 23:36:09'),
(2, 6, '2026-04-02 23:36:09'),
(3, 3, '2026-04-02 23:36:09'),
(4, 2, '2026-04-02 23:36:09'),
(4, 3, '2026-04-02 23:36:09'),
(5, 3, '2026-04-02 23:36:09'),
(6, 3, '2026-04-02 23:36:09'),
(6, 5, '2026-04-02 23:36:09'),
(7, 3, '2026-04-02 23:36:09'),
(8, 2, '2026-04-02 23:36:09'),
(8, 3, '2026-04-02 23:36:09'),
(9, 1, '2026-04-02 23:36:09'),
(9, 3, '2026-04-02 23:36:09');

-- --------------------------------------------------------

--
-- Structure de la table `user_quests`
--

CREATE TABLE `user_quests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quest_id` int(11) NOT NULL,
  `date_completion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `items_catalog`
--
ALTER TABLE `items_catalog`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `quest`
--
ALTER TABLE `quest`
  ADD PRIMARY KEY (`quest_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user_inventory`
--
ALTER TABLE `user_inventory`
  ADD PRIMARY KEY (`user_id`,`item_id`);

--
-- Index pour la table `user_quests`
--
ALTER TABLE `user_quests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`quest_id`,`date_completion`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `items_catalog`
--
ALTER TABLE `items_catalog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `quest`
--
ALTER TABLE `quest`
  MODIFY `quest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `user_quests`
--
ALTER TABLE `user_quests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
