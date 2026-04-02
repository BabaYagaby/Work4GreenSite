-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 02 avr. 2026 à 20:37
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
(1, 'The Dill Dough Company', 530, 0);

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
(1, 'Beaurain', 'Tom', 'tom.beaurain@gmail.com', '$2y$10$yefjP7TWHUFVRflQyx/ua.h/M15DpyIbjh6O2kWpJmjS3tX0HDKm2', 0, 220, 2, 2, 1, 3, 9, NULL, 8),
(2, 'Gardes', 'Gabriel', 'gabrielchaprongardes@gmail.com', '$2y$10$4hTP7g0Pc/.0ik/jgZCRQevPUlVGrqu1bSQBhuzDDaLSKQ0k7UWJ6', 1, 310, 3, 0, 1, 1, 8, 14, 11),
(3, 'Coutelier', 'Enzo', 'enzo.coutelier@gmail.com', '$2y$10$lBoiKzEnMS/E3i16wjvrJukCLtQf8BS7RlvvHQlPt4dHZ9d7cyX4O', 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
(6, 'yo', 'yoyo', 'yo@gyo.yo', '$2y$10$kMDPffX66tDWL0E8gs8CheC9Yjb/sm.iwi.pngfQoBWAuXQ06RaAm', 0, 0, 0, 0, 0, 3, NULL, NULL, NULL),
(7, 'lala', 'lala', 'lala@lala', '$2y$10$r0t0UvUt6Dqn5QOb0Dk14.Jo5vqYu8hUVq/DP9AwSaM4B9vJZ7BeW', 0, 0, 0, 0, 0, 3, NULL, NULL, NULL),
(8, 'qqGERZGRE', 'ZERGQG', 'GQRZEG@G', '$2y$10$8rUJ1rfX/.OiEm2evI3TXuxAW4T5eeXth1ihm0f6jc0mhJivDvxx2', 0, 0, 0, 0, 0, 3, NULL, NULL, NULL);

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
(1, 1, '2026-04-02 20:21:22'),
(1, 2, '2026-04-02 20:21:14'),
(1, 3, '2026-04-02 20:21:14'),
(1, 8, '2026-04-02 20:21:14'),
(1, 9, '2026-04-02 20:21:22'),
(2, 1, '2026-04-02 18:31:21'),
(2, 2, '2026-04-02 18:38:28'),
(2, 3, '2026-04-02 18:45:55'),
(2, 4, '2026-04-02 18:45:55'),
(2, 5, '2026-04-02 18:45:55'),
(2, 6, '2026-04-02 18:45:55'),
(2, 7, '2026-04-02 18:45:55'),
(2, 8, '2026-04-02 18:45:55'),
(2, 9, '2026-04-02 18:45:55'),
(2, 10, '2026-04-02 18:45:55'),
(2, 11, '2026-04-02 18:52:43'),
(2, 12, '2026-04-02 18:52:43'),
(2, 13, '2026-04-02 18:52:43'),
(2, 14, '2026-04-02 18:52:43'),
(2, 15, '2026-04-02 18:52:43'),
(5, 3, '2026-04-02 19:01:59'),
(6, 3, '2026-04-02 19:02:57'),
(7, 3, '2026-04-02 19:05:27'),
(8, 3, '2026-04-02 19:06:54');

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
-- Déchargement des données de la table `user_quests`
--

INSERT INTO `user_quests` (`id`, `user_id`, `quest_id`, `date_completion`) VALUES
(22, 1, 1, '2026-04-02'),
(23, 1, 3, '2026-04-02'),
(24, 1, 4, '2026-04-02'),
(25, 1, 5, '2026-04-02'),
(14, 2, 1, '2026-04-01'),
(17, 2, 1, '2026-04-02'),
(15, 2, 2, '2026-04-01'),
(18, 2, 3, '2026-04-02'),
(19, 2, 4, '2026-04-02'),
(21, 2, 5, '2026-04-02');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `user_quests`
--
ALTER TABLE `user_quests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
