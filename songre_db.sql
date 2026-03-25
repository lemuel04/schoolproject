-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 24 mars 2026 à 00:09
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `songre_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `cartes`
--

DROP TABLE IF EXISTS `cartes`;
CREATE TABLE IF NOT EXISTS `cartes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `liste_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `liste_id` (`liste_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `listes`
--

DROP TABLE IF EXISTS `listes`;
CREATE TABLE IF NOT EXISTS `listes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tableau_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tableau_id` (`tableau_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `projets`
--

DROP TABLE IF EXISTS `projets`;
CREATE TABLE IF NOT EXISTS `projets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_projet` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `utilisateur_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tableaux`
--

DROP TABLE IF EXISTS `tableaux`;
CREATE TABLE IF NOT EXISTS `tableaux` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `utilisateur_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `email`, `mot_de_passe`) VALUES
(1, 'lemuelbagnan08@gmail.com', '$2y$10$lTuHHJxPTJa8ohKN9N9ooOR3EOHn0bNL84.od0WnnvLf3GJGmuYE.');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cartes`
--
ALTER TABLE `cartes`
  ADD CONSTRAINT `cartes_ibfk_1` FOREIGN KEY (`liste_id`) REFERENCES `listes` (`id`);

--
-- Contraintes pour la table `listes`
--
ALTER TABLE `listes`
  ADD CONSTRAINT `listes_ibfk_1` FOREIGN KEY (`tableau_id`) REFERENCES `tableaux` (`id`);

--
-- Contraintes pour la table `tableaux`
--
ALTER TABLE `tableaux`
  ADD CONSTRAINT `tableaux_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `taches`
--
ALTER TABLE `taches`
  ADD CONSTRAINT `taches_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
