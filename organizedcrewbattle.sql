-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 28 sep. 2024 à 20:56
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
-- Structure de la table `clans`
--

CREATE TABLE `clans` (
  `id_clan` int(8) NOT NULL COMMENT 'Identifiant unique du clan',
  `nom_clan` varchar(30) NOT NULL COMMENT 'Nom du clan',
  `wins` int(8) NOT NULL COMMENT 'Nombre de victoires',
  `loses` int(8) NOT NULL COMMENT '	Nombre de défaites',
  `elo_rating` int(8) NOT NULL COMMENT '	Points ELO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `clans`
--

INSERT INTO `clans` (`id_clan`, `nom_clan`, `wins`, `loses`, `elo_rating`) VALUES
(257, 'lol', 0, 0, 11111),
(2161882, 'Asakai', 0, 0, 1200);

-- --------------------------------------------------------

--
-- Structure de la table `match_verif`
--

CREATE TABLE `match_verif` (
  `id_verification` int(8) NOT NULL,
  `id_tournoi` int(8) NOT NULL,
  `img` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `players`
--

CREATE TABLE `players` (
  `id_player` int(10) NOT NULL COMMENT 'Identifiant unique du joueur\r\n',
  `player_name` varchar(50) NOT NULL COMMENT 'Nom du joueur\r\n',
  `id_clan` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `players`
--

INSERT INTO `players` (`id_player`, `player_name`, `id_clan`) VALUES
(396123, 'Usul.', 2161882),
(1118423, 'DWES | WhaleDone', 2161882),
(2863580, 'Diddori', 2161882),
(3184429, 'Coton Thaï', 2161882),
(3199258, 'Mtk', 2161882),
(4164480, 'RubenB) (60hz,learning bow,back)', 2161882),
(4563514, 'Exodus', 2161882),
(5424846, 'CocoZ !', 2161882),
(5609156, 'Eneko.', 2161882),
(6983656, '[LG]ÆØ', 2161882),
(7167345, 'Sumtag', 2161882),
(7601507, 'Zaneko', 2161882),
(8006806, 'lionel mesi oficial', 2161882),
(8592528, 'MAHÉ FAN CLUB', 2161882),
(9549831, 'Cereza', 2161882),
(9558549, 'twitch.tv/mahebh', 2161882),
(9757291, 'zBlackneight Prime', 2161882),
(10080900, '✮ N-M', 2161882),
(29757637, 'SpoopyCode <3', 2161882),
(30072746, 'Maaxis szn', 2161882),
(32542183, 'Goliath', 2161882),
(35527409, 'Arthur', 2161882),
(37524179, 'SwizzleMcDizzle', 2161882),
(42026463, '4000H On FORNITE', 2161882),
(43349428, 'scrawny james', 2161882),
(45750120, 'Dr.Love', 2161882),
(53572692, 'LTPKiller', 2161882),
(53965041, 'Chigga', 2161882),
(55347075, 'Azur.', 2161882),
(57452641, 'ReinerGOAT', 2161882),
(57734993, 'ComboTopaz', 2161882),
(63072326, 'larafy', 2161882),
(63114984, 'Yuh Madda Bwoyfren Izziiiツ', 2161882),
(65320761, 'INA', 2161882),
(66992136, 'oTanuki', 2161882),
(74010056, 'AssassinNetwork', 2161882),
(83764657, 'Dopa', 2161882),
(85699079, 'DWES | CROUSTIFLEX', 2161882),
(87056635, 'Aya ?!', 2161882),
(93835200, 'aWoks', 2161882),
(94942171, 'Général Mobutu', 2161882),
(95675979, 'flashyy', 2161882),
(96129530, 'Quillin', 2161882),
(98125910, 'Sekai', 2161882),
(98341473, 'ABP | MTH', 2161882),
(102326446, 'iDrxp!?', 2161882),
(105693481, 'Pizza Hawaïenne', 2161882),
(108499902, 'Sucre', 2161882),
(117003562, 'Tilen', 2161882);

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
  `on_page` smallint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `clans`
--
ALTER TABLE `clans`
  ADD PRIMARY KEY (`id_clan`);

--
-- Index pour la table `match_verif`
--
ALTER TABLE `match_verif`
  ADD PRIMARY KEY (`id_verification`),
  ADD KEY `id_tournoi` (`id_tournoi`) USING BTREE;

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
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `match_verif`
--
ALTER TABLE `match_verif`
  MODIFY `id_verification` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tournoi`
--
ALTER TABLE `tournoi`
  MODIFY `id_tournoi` int(8) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant unique de la demande\r\n', AUTO_INCREMENT=21;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `match_verif`
--
ALTER TABLE `match_verif`
  ADD CONSTRAINT `match_verif_ibfk_1` FOREIGN KEY (`id_tournoi`) REFERENCES `tournoi` (`id_tournoi`);

--
-- Contraintes pour la table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`id_clan`) REFERENCES `clans` (`id_clan`);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
