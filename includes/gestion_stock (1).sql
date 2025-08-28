-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 08 août 2025 à 15:54
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_stock`
--

-- --------------------------------------------------------

--
-- Structure de la table `alerts`
--

DROP TABLE IF EXISTS `alerts`;
CREATE TABLE IF NOT EXISTS `alerts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int DEFAULT NULL,
  `type_alerte` enum('seuil_critique') DEFAULT 'seuil_critique',
  `date_alerte` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('active','traitee') DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `produit_id` (`produit_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`) VALUES
(2, 'Communication'),
(3, 'cables'),
(4, 'ordinateur'),
(5, 'Aliment');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `adresse` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `telephone`, `email`, `adresse`) VALUES
(2, 'lele amand', '06 94 45 31 39', 'l@gmail.com', 'mvan'),
(3, 'tebu', '657662216', 'jolinetebu@gmail.com', 'Tsinga'),
(4, 'teba', '', 'jolinetebo@gmail.com', 'Tsingo');

-- --------------------------------------------------------

--
-- Structure de la table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int DEFAULT NULL,
  `stock_theorique` int DEFAULT NULL,
  `stock_reel` int DEFAULT NULL,
  `ecart` int DEFAULT NULL,
  `date_inventaire` datetime DEFAULT CURRENT_TIMESTAMP,
  `utilisateur_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produit_id` (`produit_id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `inventory`
--

INSERT INTO `inventory` (`id`, `produit_id`, `stock_theorique`, `stock_reel`, `ecart`, `date_inventaire`, `utilisateur_id`) VALUES
(1, 1, 99, 4, -95, '2025-08-07 11:45:24', NULL),
(2, 1, 99, 4, -95, '2025-08-07 11:46:50', NULL),
(3, 1, 99, 4, -95, '2025-08-07 11:48:02', NULL),
(4, 1, 99, 4, -95, '2025-08-07 11:48:38', NULL),
(5, 1, 99, 4, -95, '2025-08-07 11:52:58', NULL),
(6, 5, 14, 5, -9, '2025-08-08 15:33:31', NULL),
(7, 4, 26, 4, -22, '2025-08-08 15:33:31', NULL),
(8, 5, 14, 8, -6, '2025-08-08 15:35:46', NULL),
(9, 4, 26, 6, -20, '2025-08-08 15:35:46', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `action` text,
  `module` varchar(100) DEFAULT NULL,
  `date_action` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parametres`
--

DROP TABLE IF EXISTS `parametres`;
CREATE TABLE IF NOT EXISTS `parametres` (
  `id` int NOT NULL,
  `nom_entreprise` varchar(100) DEFAULT NULL,
  `email_contact` varchar(100) DEFAULT NULL,
  `telephone_contact` varchar(30) DEFAULT NULL,
  `adresse` text,
  `stock_minimum_defaut` int DEFAULT '5',
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `parametres`
--

INSERT INTO `parametres` (`id`, `nom_entreprise`, `email_contact`, `telephone_contact`, `adresse`, `stock_minimum_defaut`, `logo`) VALUES
(1, 'DreamStock', 'contact@dreamstock.com', '+237600000000', 'Adresse ici', 5, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `code_barres` varchar(100) DEFAULT NULL,
  `description` text,
  `stock_actuel` int DEFAULT '0',
  `stock_minimum` int DEFAULT '0',
  `unite` varchar(50) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `categorie_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `categorie_id` (`categorie_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id`, `nom`, `reference`, `code_barres`, `description`, `stock_actuel`, `stock_minimum`, `unite`, `prix`, `categorie_id`) VALUES
(4, 'CHOCOLAT', 'bvefvyhyf', 'huygf  è-f(fè_uè', 'bon', 26, 2, '2', '40000.00', 5),
(5, 'azert', 'zrfeffffdev', 'dqfefde', 'xcvfvfb', 14, 1, '2', '200.00', 4);

-- --------------------------------------------------------

--
-- Structure de la table `stock_entries`
--

DROP TABLE IF EXISTS `stock_entries`;
CREATE TABLE IF NOT EXISTS `stock_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int DEFAULT NULL,
  `quantite` int DEFAULT NULL,
  `prix_unitaire` decimal(10,2) DEFAULT NULL,
  `date_entree` datetime DEFAULT CURRENT_TIMESTAMP,
  `motif` varchar(100) DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL,
  `fournisseur_id` int DEFAULT NULL,
  `numero_bon` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produit_id` (`produit_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `fournisseur_id` (`fournisseur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `stock_entries`
--

INSERT INTO `stock_entries` (`id`, `produit_id`, `quantite`, `prix_unitaire`, `date_entree`, `motif`, `utilisateur_id`, `fournisseur_id`, `numero_bon`) VALUES
(1, 1, 50, '2.27', '2025-08-06 15:48:06', 'Achat', 1, NULL, 'BE-68936AF163E32'),
(3, 1, 3, '398.00', '2025-08-08 14:25:15', 'Achat', 6, 3, 'BE-6895FA976EEFE'),
(4, 4, 2, '123456.00', '2025-08-08 15:23:03', 'Achat', 6, 4, 'BE-6896081D2B707');

-- --------------------------------------------------------

--
-- Structure de la table `stock_outputs`
--

DROP TABLE IF EXISTS `stock_outputs`;
CREATE TABLE IF NOT EXISTS `stock_outputs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int DEFAULT NULL,
  `quantite` int DEFAULT NULL,
  `prix_unitaire` decimal(10,2) DEFAULT NULL,
  `date_sortie` datetime DEFAULT CURRENT_TIMESTAMP,
  `motif` varchar(100) DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL,
  `client_id` int DEFAULT NULL,
  `numero_bon` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produit_id` (`produit_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `stock_outputs`
--

INSERT INTO `stock_outputs` (`id`, `produit_id`, `quantite`, `prix_unitaire`, `date_sortie`, `motif`, `utilisateur_id`, `client_id`, `numero_bon`) VALUES
(3, 3, 2, NULL, '2025-08-07 14:30:41', 'vente', 3, 2, 'BS20250807133041616'),
(2, 1, 1, NULL, '2025-08-06 17:55:17', 'vente', 1, NULL, 'BS20250806165517236');

-- --------------------------------------------------------

--
-- Structure de la table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `adresse` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `suppliers`
--

INSERT INTO `suppliers` (`id`, `nom`, `telephone`, `email`, `adresse`) VALUES
(3, 'Elect ada', '06 94 45 31 39', 'l@gmail.com', 'mvan'),
(4, 'Pierre Leonel Nguiamba', '06 55 43 47 54', 'symphonixleo27@gmail.com', 'Mvan');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `role` enum('admin','magasinier','consultation') DEFAULT 'consultation',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `mot_de_passe`, `role`, `date_creation`) VALUES
(6, 'Administrateur', 'admin@dreamstock.com', '$2y$10$fDuMCDpV6E2zDWKzf5LGm.Fxk/v.D.MUbcHYrH/D1emsHMvs0YFHK', 'admin', '2025-08-08 14:10:57');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
