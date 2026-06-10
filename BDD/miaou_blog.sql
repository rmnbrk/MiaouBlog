-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 07 nov. 2025 à 14:39
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `miaou_blog`
--

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `nom_categorie` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id_categorie`, `nom_categorie`) VALUES
(1, 'Mignon'),
(2, 'Tire la langue'),
(3, 'Besoin d\'aide'),
(4, 'Chat noir'),
(5, 'Chaton');

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

DROP TABLE IF EXISTS `commentaire`;
CREATE TABLE IF NOT EXISTS `commentaire` (
  `id_commentaire` int NOT NULL AUTO_INCREMENT,
  `contenu` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_like` int NOT NULL,
  `id_user` int NOT NULL,
  `id_post` int DEFAULT NULL,
  PRIMARY KEY (`id_commentaire`,`id_user`),
  KEY `fk_user_comm` (`id_user`),
  KEY `fk_post_comm` (`id_post`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commentaire`
--

INSERT INTO `commentaire` (`id_commentaire`, `contenu`, `nombre_like`, `id_user`, `id_post`) VALUES
(11, 'trop mimi il s&#039;appelle comment ? &lt;3', 0, 20, 27),
(12, 'oula !! c&#039;est pas normal !!!', 0, 20, 20),
(13, 'mais OMG il est dodu', 0, 20, 28),
(14, 'mdr c quoi ca', 0, 20, 26),
(15, 'coucou ca m&#039;est deja arrivé, parle en a ton vétérinaire', 0, 20, 21),
(16, 'awwww', 0, 20, 30),
(17, '#mdr #lol #php', 0, 20, 31),
(18, '&lt;3', 0, 20, 25),
(19, 'trop chou', 0, 12, 27),
(20, 'mdr il est trop bete ce chat', 0, 12, 20),
(21, 'MDR', 0, 12, 28),
(22, 'le pauvre bb', 0, 12, 26);

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` int DEFAULT NULL,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `id_post` int NOT NULL AUTO_INCREMENT,
  `titre_post` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `date_post` date NOT NULL,
  `description` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `id_user` int DEFAULT NULL,
  PRIMARY KEY (`id_post`),
  KEY `fk_user_post` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id_post`, `titre_post`, `date_post`, `description`, `id_user`) VALUES
(20, 'HELP ! Mon chat bave quand il dort', '2025-07-19', 'est ce que c normal ??? je stress !!!', 7),
(21, 'Minou ne mange plus...', '2025-01-14', 'Coucou tout le monde…\r\nMon chat ne veut plus manger depuis quelque temps, il reste dans son coin et je commence à m’inquiéter ????\r\nSi quelqu’un s’y connaît un peu ou a déjà vécu ça, j’aimerais bien vos conseils ????', 13),
(22, 'Je veux devenir comme kahla', '2022-12-25', 'Rohroh elle a un chat noir nommée kahla. Je suis tombée amoureuse regardez :(', 17),
(23, 'Mon chat tire tout le temps la langue', '2023-05-26', 'Mon chat fait que de tirer la langue lol', 17),
(24, 'Chat fou malicieux', '2024-12-03', 'Mon chat est rempli de malice et de malveillance, que faire svp ???', 12),
(25, 'Chat trop mimi :3', '2024-01-01', 'Coucou, je vous présente mon chat. C&#039;est un chat noir et il s&#039;appelle Jiji, comme dans Kiki la petite sorcière !', 12),
(26, 'Mon chat ne sait pas manger', '2025-02-06', 'SVP, il se noie tout le temps', 13),
(27, 'Chat toux mignon', '2025-08-15', 'il est trop mignon mon chat', 13),
(28, 'mon chat est trop gros', '2025-06-13', 'vous pensez que mon chat est obèse svp ? je lui ai donné des tacos hier soir et ce matin il était comme ca', 18),
(29, 'mon chat', '2024-05-22', 'coucou c&#039;est mon chat :3 il s&#039;appelle mimi', 18),
(30, 'chaton trop mimi', '2024-03-07', 'je l&#039;ai trouvé dehors, je vais l&#039;emmener au veto..', 18),
(31, 'mon chat fait du php', '2023-06-05', 'il vole mon ordi et code des blogs sans me demander, aidez moi', 19),
(33, 'Chaton à donner sur Lyon ! ', '2025-11-07', 'Salut, ma chatte a eu une portée de 4 chatons et ils pourront bientôt être donnés ! ', 12),
(34, 'chat migon', '2025-11-07', 'faezubhfaekf', 12);

-- --------------------------------------------------------

--
-- Structure de la table `post_categorie`
--

DROP TABLE IF EXISTS `post_categorie`;
CREATE TABLE IF NOT EXISTS `post_categorie` (
  `id_post` int NOT NULL,
  `id_categorie` int NOT NULL,
  PRIMARY KEY (`id_post`,`id_categorie`),
  KEY `fk_id_categorie` (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `post_categorie`
--

INSERT INTO `post_categorie` (`id_post`, `id_categorie`) VALUES
(25, 1),
(27, 1),
(30, 1),
(34, 1),
(23, 2),
(24, 2),
(29, 2),
(20, 3),
(21, 3),
(23, 3),
(26, 3),
(28, 3),
(31, 3),
(22, 4),
(25, 4),
(30, 5),
(33, 5);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `email` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `pseudo` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `mot_de_passe` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `pseudo` (`pseudo`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_user`, `email`, `pseudo`, `mot_de_passe`) VALUES
(6, 'admin@localhost.fr', 'Admin', '$2y$10$w62QXnVIaobb5DtSDYyi4.ITFKg5CE94MGQ1FJj/RmWjlaV8fSkFG'),
(7, 'romane@gmail.com', 'roro', '$2y$10$1CXUjiPLwuT0oIHpWq82j.g48RpfLHW/Ky25FFYiVgce/GcY6jo9y'),
(9, 'fiona@gmail.com', 'fiona', '$2y$10$E.fuGESaLsR9RBNltoXXkehx6iaS4N9rLYTOz9e0j7A90wbgLKSSO'),
(12, 'ugo@gmail.com', 'ugo_lacolere', '$2y$10$EVGRI4uAJ3agbTfCAEfc/OnJN3R/v2ms7R0KjFa84dMCdGQneQrTG'),
(13, 'walid@gmail.com', 'walid_bg_du_69', '$2y$10$XB8BRTlsgMG2mAouSPo0I.ZsrG5JI66pUmEaoaZ9TFlGckGG.UXna'),
(14, 'kittyfan@outlook.fr', 'kitty_fan_653', '$2y$10$J5kWucQKvLJ7bAGaGRyH8OQ4oZXqxFDRV6NmeIK.lC1kHASYREuIu'),
(15, 'phpfan@gmail.com', 'fan_de_chat_123', '$2y$10$Fca0GD8rI9CudR1g5Foz3u5BpzTJW.zYT6iwjPHOAOvowCC20PxPO'),
(16, 'comptespam@gmail.com', 'mimi89', '$2y$10$5AwW8L4DKkoHrgOjXqwSpeJMEv949mpZuSLvuu.JmXZX3kxnKmBmi'),
(17, 'roro@gmail.com', 'roro19', '$2y$10$DBSb8.RTDBPsuAmey4PFDetecWAmdSxkYvJTPp286/Cjrg9vahDFG'),
(18, 'dzafqzq@gmail.com', 'fiona_radi', '$2y$10$GXmB99F9p95V9hvpGXh2f.yTui8nVl93EHrAbs4NG5aUry5KTAmki'),
(19, 'phpro@gmail.com', 'jaime_le_php', '$2y$10$w9g9PmujImToZnYLXqD0ku1seRH7hO6Ra.VkEpMCbqGOpFQC2jF6S'),
(20, 'test@gmail.com', 'kitt_fan874', '$2y$10$/p4AM.9M9dEHZtfQeV7bvuXkXd.N9W1xFMfsnQgL9RAhunAdCy/se');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `fk_post_comm` FOREIGN KEY (`id_post`) REFERENCES `post` (`id_post`),
  ADD CONSTRAINT `fk_user_comm` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`);

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`) ON DELETE SET NULL;

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `fk_user_post` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`);

--
-- Contraintes pour la table `post_categorie`
--
ALTER TABLE `post_categorie`
  ADD CONSTRAINT `fk_id_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_post` FOREIGN KEY (`id_post`) REFERENCES `post` (`id_post`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
