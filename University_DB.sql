-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 25 juin 2025 à 21:48
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
-- Base de données : `projet2`
--

-- --------------------------------------------------------

--
-- Structure de la table `candidature`
--

CREATE TABLE `candidature` (
  `ID` int(10) NOT NULL,
  `ID_etud` int(20) NOT NULL,
  `CNE` varchar(11) NOT NULL,
  `CIN` varchar(10) NOT NULL,
  `ville` varchar(200) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `dossier_candid` varchar(200) NOT NULL,
  `spec_bac` varchar(20) NOT NULL,
  `date_bac` year(4) NOT NULL,
  `spec_bac2` varchar(20) NOT NULL,
  `date_bac2` year(4) NOT NULL,
  `spec_bac3` varchar(20) DEFAULT NULL,
  `date_bac3` year(4) DEFAULT NULL,
  `note_s1` double NOT NULL,
  `note_s2` double NOT NULL,
  `note_s3` double NOT NULL,
  `note_s4` double NOT NULL,
  `note_s5` double DEFAULT NULL,
  `note_s6` double DEFAULT NULL,
  `recu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `candidature`
--

INSERT INTO `candidature` (`ID`, `ID_etud`, `CNE`, `CIN`, `ville`, `photo`, `dossier_candid`, `spec_bac`, `date_bac`, `spec_bac2`, `date_bac2`, `spec_bac3`, `date_bac3`, `note_s1`, `note_s2`, `note_s3`, `note_s4`, `note_s5`, `note_s6`, `recu`) VALUES
(91, 92, 'R773737737', 'BB2226', 'Casablanca', 'uploads/1338068.jpg', 'uploads/TP1_LIC_ST_IRM_2324_Python (1).pdf', 'PC', '2022', 'deust', '2023', 'math', '2022', 10, 14, 15, 13, 15, 14, 'uploads_recu/recu_YAHYAYahya92.pdf'),
(92, 94, 'R873737738', 'BB2227', 'Rabat', 'uploads/1338068.jpg', 'uploads/TP1_LIC_ST_IRM_2324_Python (1).pdf', 'SVT', '2022', 'deust', '2023', 'physique', '2022', 11, 12, 13, 14, 15, 16, 'uploads_recu/recu_YAHYAYahya92.pdf'),
(93, 96, '00006002', '1076002', 'Rabat', 'uploads/1338068.jpg', 'uploads/TP1_LIC_ST_IRM_2324_Python (1).pdf', 'PC', '2020', 'deust', '2020', 'physique', '2022', 13, 14, 15, 16, 14, 15, 'uploads_recu/recu_YAHYAYahya92.pdf'),
(94, 97, '784781378', '7647678T', 'Rabat', 'uploads/1338068.jpg', 'uploads/TP1_LIC_ST_IRM_2324_Python (1).pdf', 'PC', '2020', 'deust', '2020', NULL, NULL, 13, 14, 15, 16, NULL, NULL, 'uploads_recu/recu_YAHYAYahya92.pdf'),
(95, 98, '78527642', '20947209', 'Rabat', 'uploads/1338068.jpg', 'uploads/TP1_LIC_ST_IRM_2324_Python (1).pdf', 'PC', '2020', 'deust', '2020', NULL, NULL, 13, 14, 15, 16, NULL, NULL, 'uploads_recu/recu_YAHYAYahya92.pdf'),
(96, 95, '573527427', '42491249', 'Rabat', 'uploads/1338068.jpg', 'uploads/TP1_LIC_ST_IRM_2324_Python (1).pdf', 'PC', '2020', 'deust', '2020', NULL, NULL, 13, 14, 15, 16, NULL, NULL, 'uploads_recu/recu_YAHYAYahya92.pdf'),
(97, 93, '8248124', '242889539', 'Rabat', 'uploads/1338068.jpg', 'uploads/TP1_LIC_ST_IRM_2324_Python (1).pdf', 'PC', '2020', 'deust', '2020', 'math', '2022', 13, 14, 15, 16, 14, 15, 'uploads_recu/recu_YAHYAYahya92.pdf'),
(98, 99, 'R453657889', 'BB7867900', 'Casablanca', 'uploads/1338068.jpg', 'uploads/TP1_LIC_ST_IRM_2324_Python (1).pdf', 'PC', '2022', 'deust', '2023', 'pc', '2023', 13, 15, 15, 12, 14, 14, 'uploads_recu/recu_SALHIMohammed99.pdf'),
(99, 100, 'U3T1239289', 'JZ223', 'rabat', 'uploads/SC1.jpg', 'uploads/rapport_projet_drone_arduino1.pdf', 'PC', '2021', 'deust', '2022', 'pc', '2024', 12, 12, 12, 12, 12, 12, 'uploads_recu/recu_SALHIMohammed100.pdf');

-- --------------------------------------------------------

--
-- Structure de la table `chef_filiere`
--

CREATE TABLE `chef_filiere` (
  `id` int(5) NOT NULL,
  `nom_chef` varchar(20) DEFAULT NULL,
  `prenom_chef` varchar(20) DEFAULT NULL,
  `code_verif` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `filiere` varchar(6) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `chef_filiere`
--

INSERT INTO `chef_filiere` (`id`, `nom_chef`, `prenom_chef`, `code_verif`, `password`, `filiere`, `role`) VALUES
(2, 'ZAAIRI', 'YAHYA', '1234', 'mouha1234', 'GME', 'chef'),
(3, 'MOUBARIK', 'HAMZA', '123', 'mouha123', 'RSI', 'chef'),
(4, 'SAMAKI', 'AYOUB', '12345', 'mouha12345', 'MA', 'chef'),
(5, 'HACHIMI', 'KAMAL', '1234', 'mouha1234', 'IRM', 'chef'),
(6, 'SAAIDI', 'SANAA', '123', 'mouha123', 'GEII', 'chef'),
(7, 'AYOUBI', 'JUNAID', '12345', 'mouha12345', 'ISERT', 'chef'),
(8, 'RACHIDI', 'AMINE', '1234', 'mouha1234', 'IPMA', 'chef'),
(9, 'SAHIMI', 'OMAR', '123', 'mouha123', 'GCBI', 'chef'),
(10, 'SALHI', 'MOHAMMED', '54321', '54321', 'admin', 'admin'),
(13, 'BOUZIDI', 'AZIZ', '12345', 'mouha12345', 'LSI', 'chef');

-- --------------------------------------------------------

--
-- Structure de la table `filiere`
--

CREATE TABLE `filiere` (
  `id_filiere` int(11) NOT NULL,
  `nom_fil` varchar(255) NOT NULL,
  `niveau_fil` varchar(50) NOT NULL,
  `id_candid` int(11) NOT NULL,
  `verified` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `filiere`
--

INSERT INTO `filiere` (`id_filiere`, `nom_fil`, `niveau_fil`, `id_candid`, `verified`) VALUES
(142, 'IPMA', 'Master', 91, 1),
(143, 'LSI', 'Cycle', 91, 1),
(144, 'GME', 'Cycle', 91, NULL),
(145, 'RSI', 'Cycle', 92, NULL),
(146, 'LSI', 'Cycle', 92, 1),
(147, 'IPMA', 'Master', 94, NULL),
(148, 'ISERT', 'Master', 93, NULL),
(149, 'GCBI', 'Master', 93, NULL),
(150, 'MA', 'Licence', 95, NULL),
(151, 'IRM', 'Licence', 95, NULL),
(152, 'GEII', 'Licence', 95, 1),
(153, 'LSI', 'Cycle', 96, 0),
(154, 'IPMA', 'Master', 93, NULL),
(156, 'LSI', 'Cycle', 97, 1),
(157, 'GME', 'Cycle', 97, NULL),
(161, 'GCBI', 'Master', 98, NULL),
(162, 'LSI', 'Cycle', 98, 1),
(163, 'GME', 'Cycle', 98, NULL),
(164, 'IPMA', 'Master', 99, NULL),
(165, 'LSI', 'Cycle', 99, NULL),
(166, 'GME', 'Cycle', 99, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `prenom` varchar(20) DEFAULT NULL,
  `nom` varchar(20) NOT NULL,
  `email` varchar(40) NOT NULL,
  `birthday` date NOT NULL,
  `password` varchar(20) NOT NULL,
  `verification_token` varchar(40) NOT NULL,
  `verifie` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `student`
--

INSERT INTO `student` (`id`, `prenom`, `nom`, `email`, `birthday`, `password`, `verification_token`, `verifie`) VALUES
(92, 'YAHYA', 'YAHYA', 'mohammedsalhimo@gmail.com', '2003-01-07', 'hhhh', 'b725fb3127464532575dbc99829e48e3', 1),
(93, 'omar', 'amani', 'hajamli@gmail.com', '2014-09-09', 'hhhh', 'b725fb3127464532575dbc99829e48e39', 1),
(94, 'kamal', 'fadoul', 'ahyafp@gmail.com', '2014-12-17', 'hhhh', 'b725fb3127464532575dbc99829e48e34', 1),
(95, 'hachim', 'amali', 'hachim@gmail.com', '2024-12-04', 'hhhh', 'b725fb3127464532575dbc99829e48e376', 1),
(96, 'yahya', 'aloma', 'aloma@gmail.com', '2024-12-03', 'hhhh', 'b725fb3127464532575dbc99829e48e3645', 1),
(97, 'tarik', 'ouadi', 'ouadi@gmail.com', '2024-12-11', 'hhhh', 'b725fb3127464532575dbc99829e48e3645', 1),
(98, 'reda', 'jamil', 'reda@gmail.com', '2024-12-04', 'hhhh', 'b725fb3127464532575dbc99829e48e36464', 1),
(99, 'Mohammed', 'Salhi', 'mohammedsalhisam@gmail.com', '2007-03-22', 'hhhh', '0002d9e79126ec6964ba1fe511476078', 1),
(100, 'Mohammed', 'Salhi', 'salhi.mohammed@etu.uae.ac.ma', '2024-11-14', '12345', 'ba0512068b6e13b3eff8064807f07c50', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `candidature`
--
ALTER TABLE `candidature`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `CNE` (`CNE`),
  ADD UNIQUE KEY `CIN` (`CIN`),
  ADD KEY `fk_candidature_student` (`ID_etud`);

--
-- Index pour la table `chef_filiere`
--
ALTER TABLE `chef_filiere`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `filiere` (`filiere`);

--
-- Index pour la table `filiere`
--
ALTER TABLE `filiere`
  ADD PRIMARY KEY (`id_filiere`),
  ADD KEY `fk_candidature` (`id_candid`);

--
-- Index pour la table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `candidature`
--
ALTER TABLE `candidature`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT pour la table `chef_filiere`
--
ALTER TABLE `chef_filiere`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `filiere`
--
ALTER TABLE `filiere`
  MODIFY `id_filiere` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT pour la table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `candidature`
--
ALTER TABLE `candidature`
  ADD CONSTRAINT `fk_candidature_student` FOREIGN KEY (`ID_etud`) REFERENCES `student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `filiere`
--
ALTER TABLE `filiere`
  ADD CONSTRAINT `fk_candidature` FOREIGN KEY (`id_candid`) REFERENCES `candidature` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
