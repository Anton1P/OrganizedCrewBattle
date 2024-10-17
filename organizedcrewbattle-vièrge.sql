-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 17 oct. 2024 à 16:33
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
-- Base de données : `organizedcrewbattle`
--

-- --------------------------------------------------------

--
-- Structure de la table `checkin`
--

CREATE TABLE `checkin` (
  `id_checkin` int(8) NOT NULL,
  `id_tournoi` int(11) NOT NULL,
  `id_clan_demandeur` int(8) NOT NULL,
  `clan_demandeur_checkin` tinyint(1) NOT NULL,
  `id_clan_receveur` int(8) NOT NULL,
  `clan_receveur_checkin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `clans`
--

CREATE TABLE `clans` (
  `id_clan` int(8) NOT NULL COMMENT 'Identifiant unique du clan',
  `nom_clan` varchar(30) NOT NULL COMMENT 'Nom du clan',
  `wins` int(8) NOT NULL COMMENT 'Nombre de victoires',
  `loses` int(8) NOT NULL COMMENT '	Nombre de défaites',
  `elo_rating` int(8) NOT NULL COMMENT '	Points ELO',
  `elo_peak` int(11) NOT NULL,
  `top` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `moderation_access`
--

CREATE TABLE `moderation_access` (
  `id_modo` int(8) NOT NULL,
  `steam_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `moderation_access`
--

INSERT INTO `moderation_access` (`id_modo`, `steam_id`) VALUES
(1, '76561198877699338');

-- --------------------------------------------------------

--
-- Structure de la table `players`
--

CREATE TABLE `players` (
  `id_player` int(10) NOT NULL COMMENT 'Identifiant unique du joueur\r\n',
  `player_name` varchar(50) NOT NULL COMMENT 'Nom du joueur\r\n',
  `id_clan` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `player_tournoi`
--

CREATE TABLE `player_tournoi` (
  `id_tournoi` int(8) NOT NULL,
  `id_player` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tournoi`
--

CREATE TABLE `tournoi` (
  `id_tournoi` int(8) NOT NULL COMMENT 'Identifiant unique de la demande\r\n',
  `id_clan_demandeur` int(8) NOT NULL COMMENT 'Clan qui envoie la demande\r\n',
  `id_clan_receveur` int(8) NOT NULL COMMENT 'Clan qui reçoit la demande\r\n',
  `date_rencontre` datetime(6) NOT NULL COMMENT 'Date prévue pour la rencontre\r\n',
  `format` int(3) NOT NULL,
  `accepted` tinyint(1) NOT NULL,
  `brawlhalla_room` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tournoi_results`
--

CREATE TABLE `tournoi_results` (
  `id_results` int(11) NOT NULL,
  `id_tournoi` int(11) NOT NULL,
  `id_winner` int(11) NOT NULL,
  `id_loser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `verif_match`
--

CREATE TABLE `verif_match` (
  `id_verification` int(8) NOT NULL,
  `id_tournoi` int(8) NOT NULL,
  `id_clan_demandeur` int(8) NOT NULL,
  `demandeur_sendproof` tinyint(1) NOT NULL,
  `id_clan_receveur` int(11) NOT NULL,
  `receveur_sendproof` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `verif_report`
--

CREATE TABLE `verif_report` (
  `id_verifReport` int(8) NOT NULL,
  `id_tournoi` int(11) NOT NULL,
  `id_clan_demandeur` int(8) NOT NULL,
  `clan_demandeur_report` tinyint(1) NOT NULL,
  `clan_demandeur_result` tinyint(1) NOT NULL,
  `id_clan_receveur` int(8) NOT NULL,
  `clan_receveur_report` tinyint(1) NOT NULL,
  `clan_receveur_result` tinyint(1) NOT NULL,
  `report_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `checkin`
--
ALTER TABLE `checkin`
  ADD PRIMARY KEY (`id_checkin`),
  ADD KEY `id_clan_demandeur` (`id_clan_demandeur`),
  ADD KEY `id_clan_receveur` (`id_clan_receveur`),
  ADD KEY `id_tournoi` (`id_tournoi`);

--
-- Index pour la table `clans`
--
ALTER TABLE `clans`
  ADD PRIMARY KEY (`id_clan`);

--
-- Index pour la table `moderation_access`
--
ALTER TABLE `moderation_access`
  ADD PRIMARY KEY (`id_modo`);

--
-- Index pour la table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id_player`),
  ADD KEY `id_clan` (`id_clan`);

--
-- Index pour la table `player_tournoi`
--
ALTER TABLE `player_tournoi`
  ADD KEY `id_tournoi` (`id_tournoi`),
  ADD KEY `id_joueur` (`id_player`);

--
-- Index pour la table `tournoi`
--
ALTER TABLE `tournoi`
  ADD PRIMARY KEY (`id_tournoi`),
  ADD UNIQUE KEY `id_clan_receveur` (`id_clan_receveur`),
  ADD KEY `id_clan_demandeur` (`id_clan_demandeur`);

--
-- Index pour la table `tournoi_results`
--
ALTER TABLE `tournoi_results`
  ADD PRIMARY KEY (`id_results`),
  ADD KEY `id_winner` (`id_winner`),
  ADD KEY `id_loser` (`id_loser`),
  ADD KEY `id_tournoi` (`id_tournoi`) USING BTREE;

--
-- Index pour la table `verif_match`
--
ALTER TABLE `verif_match`
  ADD PRIMARY KEY (`id_verification`),
  ADD KEY `id_tournoi` (`id_tournoi`) USING BTREE,
  ADD KEY `id_clan_demandeur` (`id_clan_demandeur`),
  ADD KEY `id_clan_receveur` (`id_clan_receveur`);

--
-- Index pour la table `verif_report`
--
ALTER TABLE `verif_report`
  ADD PRIMARY KEY (`id_verifReport`),
  ADD KEY `id_clan_demandeur` (`id_clan_demandeur`),
  ADD KEY `id_clan_receveur` (`id_clan_receveur`),
  ADD KEY `id_tournoi` (`id_tournoi`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `checkin`
--
ALTER TABLE `checkin`
  MODIFY `id_checkin` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT pour la table `moderation_access`
--
ALTER TABLE `moderation_access`
  MODIFY `id_modo` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `tournoi`
--
ALTER TABLE `tournoi`
  MODIFY `id_tournoi` int(8) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant unique de la demande\r\n', AUTO_INCREMENT=2161917;

--
-- AUTO_INCREMENT pour la table `tournoi_results`
--
ALTER TABLE `tournoi_results`
  MODIFY `id_results` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT pour la table `verif_match`
--
ALTER TABLE `verif_match`
  MODIFY `id_verification` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT pour la table `verif_report`
--
ALTER TABLE `verif_report`
  MODIFY `id_verifReport` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `checkin`
--
ALTER TABLE `checkin`
  ADD CONSTRAINT `checkin_ibfk_1` FOREIGN KEY (`id_tournoi`) REFERENCES `tournoi` (`id_tournoi`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_clan_demandeur_checkin` FOREIGN KEY (`id_clan_demandeur`) REFERENCES `clans` (`id_clan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_clan_receveur_checkin` FOREIGN KEY (`id_clan_receveur`) REFERENCES `clans` (`id_clan`) ON DELETE CASCADE;

--
-- Contraintes pour la table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`id_clan`) REFERENCES `clans` (`id_clan`) ON DELETE CASCADE;

--
-- Contraintes pour la table `player_tournoi`
--
ALTER TABLE `player_tournoi`
  ADD CONSTRAINT `player_tournoi_ibfk_2` FOREIGN KEY (`id_player`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `player_tournoi_ibfk_3` FOREIGN KEY (`id_tournoi`) REFERENCES `tournoi` (`id_tournoi`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tournoi`
--
ALTER TABLE `tournoi`
  ADD CONSTRAINT `tournoi_ibfk_1` FOREIGN KEY (`id_clan_demandeur`) REFERENCES `clans` (`id_clan`),
  ADD CONSTRAINT `tournoi_ibfk_2` FOREIGN KEY (`id_clan_receveur`) REFERENCES `clans` (`id_clan`);

--
-- Contraintes pour la table `tournoi_results`
--
ALTER TABLE `tournoi_results`
  ADD CONSTRAINT `tournoi_results_ibfk_2` FOREIGN KEY (`id_winner`) REFERENCES `clans` (`id_clan`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournoi_results_ibfk_3` FOREIGN KEY (`id_loser`) REFERENCES `clans` (`id_clan`) ON DELETE CASCADE;

--
-- Contraintes pour la table `verif_match`
--
ALTER TABLE `verif_match`
  ADD CONSTRAINT `fk_clan_demandeur_match` FOREIGN KEY (`id_clan_demandeur`) REFERENCES `clans` (`id_clan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_clan_receveur_match` FOREIGN KEY (`id_clan_receveur`) REFERENCES `clans` (`id_clan`) ON DELETE CASCADE,
  ADD CONSTRAINT `verif_match_ibfk_4` FOREIGN KEY (`id_tournoi`) REFERENCES `tournoi` (`id_tournoi`) ON DELETE CASCADE;

--
-- Contraintes pour la table `verif_report`
--
ALTER TABLE `verif_report`
  ADD CONSTRAINT `fk_clan_demandeur` FOREIGN KEY (`id_clan_demandeur`) REFERENCES `clans` (`id_clan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_clan_receveur` FOREIGN KEY (`id_clan_receveur`) REFERENCES `clans` (`id_clan`) ON DELETE CASCADE,
  ADD CONSTRAINT `verif_report_ibfk_3` FOREIGN KEY (`id_tournoi`) REFERENCES `tournoi` (`id_tournoi`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
