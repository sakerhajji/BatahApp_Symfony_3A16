-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 11 avr. 2024 à 19:23
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
-- Base de données : `hamza`
--

-- --------------------------------------------------------

--
-- Structure de la table `achats`
--

CREATE TABLE `achats` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `basket`
--

CREATE TABLE `basket` (
  `id_client` int(11) DEFAULT NULL,
  `id_produit` int(11) DEFAULT NULL,
  `idBasket` int(11) NOT NULL,
  `remise` varchar(255) DEFAULT NULL,
  `date_ajout` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `basket`
--

INSERT INTO `basket` (`id_client`, `id_produit`, `idBasket`, `remise`, `date_ajout`) VALUES
(1, 1, 2, NULL, '2024-04-11 07:00:16'),
(1, 3, 4, NULL, '2024-04-11 07:40:04'),
(4, 4, 6, NULL, '2024-04-11 09:17:21'),
(1, 4, 7, NULL, '2024-04-11 09:24:34'),
(8, 1, 8, NULL, '2024-04-11 09:29:51'),
(8, 3, 9, NULL, '2024-04-11 09:29:57'),
(10, 1, 10, NULL, '2024-04-11 18:55:46');

-- --------------------------------------------------------

--
-- Structure de la table `commands`
--

CREATE TABLE `commands` (
  `id` int(11) NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `date_commande` datetime NOT NULL,
  `mode_livraison` varchar(30) DEFAULT NULL,
  `mode_paiement` varchar(30) DEFAULT NULL,
  `cout_totale` double DEFAULT NULL,
  `etat_commande` varchar(30) DEFAULT 'En attente',
  `adresse` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commands`
--

INSERT INTO `commands` (`id`, `id_client`, `date_commande`, `mode_livraison`, `mode_paiement`, `cout_totale`, `etat_commande`, `adresse`) VALUES
(15, 8, '2024-04-11 09:30:13', 'livraison', 'paiement', 100008, 'En Attente', '7 boulevard Habib Bourguiba'),
(16, 8, '2024-04-11 09:31:03', 'livraison', 'paiement', 100008, 'En Attente', '7 boulevard Habib Bourguiba');

-- --------------------------------------------------------

--
-- Structure de la table `command_articles`
--

CREATE TABLE `command_articles` (
  `id` int(11) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `command_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `command_articles`
--

INSERT INTO `command_articles` (`id`, `article_id`, `command_id`) VALUES
(22, 1, 15),
(23, 3, 15),
(24, 1, 16),
(25, 3, 16);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20240407035132', '2024-04-07 05:51:44', 459),
('DoctrineMigrations\\Version20240411043623', '2024-04-11 06:36:40', 93),
('DoctrineMigrations\\Version20240411044853', '2024-04-11 06:48:59', 63),
('DoctrineMigrations\\Version20240411045111', '2024-04-11 06:51:16', 53);

-- --------------------------------------------------------

--
-- Structure de la table `encheres`
--

CREATE TABLE `encheres` (
  `idEnchere` int(11) NOT NULL,
  `dateDebut` date DEFAULT NULL,
  `dateFin` date DEFAULT NULL,
  `Status` tinyint(1) DEFAULT NULL,
  `prixMin` double DEFAULT NULL,
  `prixMax` double DEFAULT NULL,
  `prixActuelle` double DEFAULT NULL,
  `nbrParticipants` int(11) NOT NULL,
  `idProduit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `image`
--

CREATE TABLE `image` (
  `idImage` int(11) NOT NULL,
  `url` varchar(250) NOT NULL,
  `idProduits` int(11) DEFAULT NULL,
  `idLocations` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `location`
--

CREATE TABLE `location` (
  `id` int(11) DEFAULT NULL,
  `idLocation` int(11) NOT NULL,
  `type` varchar(300) NOT NULL,
  `description` varchar(300) NOT NULL,
  `prix` double NOT NULL,
  `adresse` varchar(300) NOT NULL,
  `disponibilite` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `partenaires`
--

CREATE TABLE `partenaires` (
  `idPartenaire` int(11) NOT NULL,
  `nom` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL,
  `adresse` varchar(20) NOT NULL,
  `telephone` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `logo` varchar(200) NOT NULL,
  `points` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `idProduit` int(11) NOT NULL,
  `type` varchar(300) NOT NULL,
  `description` varchar(300) NOT NULL,
  `prix` double NOT NULL,
  `labelle` varchar(300) NOT NULL,
  `status` varchar(255) NOT NULL,
  `periodeGarantie` int(11) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `video` varchar(250) DEFAULT NULL,
  `localisation` varchar(255) NOT NULL,
  `nombreDeVues` int(11) NOT NULL,
  `idUtilisateur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`idProduit`, `type`, `description`, `prix`, `labelle`, `status`, `periodeGarantie`, `photo`, `video`, `localisation`, `nombreDeVues`, `idUtilisateur`) VALUES
(1, 'maison', 'Renault Megane 2023, essence, boîte automatique, 5 places', 40000, 'Renault Megane 2023', 'disponible', 0, 'Renault_Megane_2023.jpg', NULL, 'https://maps.google.com/maps?q=tunis&t=&z=13&ie=UTF8&iwloc=&output=embed', 4, 2),
(3, 'Voiture', 'Audi A4 2023, essence, boîte automatique, 5 places', 60000, 'Audi A4', 'Disponible', 0, 'audi_a4.jpg', NULL, 'https://maps.google.com/maps?q=tunis&t=&z=13&ie=UTF8&iwloc=&output=embed', 1, 1),
(4, 'Voiture', 'BMW Serie 3 2022, diesel, boîte manuelle, 5 portes', 55000, 'BMW Serie 3', 'Disponible', 1, 'bmw_serie_3.jpg', NULL, 'https://maps.google.com/maps?q=Sousse&t=&z=13&ie=UTF8&iwloc=&output=embed', 1, 3),
(5, 'Maison', 'Villa moderne, 4 chambres, 2 salles de bains, jardin spacieux', 350000, 'Villa Moderne', 'Disponible', 0, 'villa_moderne.jpg', NULL, 'https://maps.google.com/maps?q=Hammamet&t=&z=13&ie=UTF8&iwloc=&output=embed', 0, 5),
(6, 'Maison', 'Appartement de 3 pièces, rénové, avec balcon', 120000, 'Appartement Rénové', 'Disponible', 0, 'appartement_renove.jpg', NULL, 'Sfaxhttps://maps.google.com/maps?q=Sfax&t=&z=13&ie=UTF8&iwloc=&output=embed', 0, 7),
(7, 'Terrain', 'Terrain constructible de 500 m², proche du centre-ville', 80000, 'Terrain Constructible', 'Disponible', 0, 'terrain_constructible.jpg', NULL, 'Nabeulhttps://maps.google.com/maps?q=Nabeul&t=&z=13&ie=UTF8&iwloc=&output=embed', 0, 9),
(8, 'terrain', 'Terrain agricole de 2 hectares, avec accès à l eau', 150000, 'Terrain Agricole', 'disponible', 0, 'terrain_agricole.jpg', 'https://www.youtube.com/watch?v=3RvSPuUX5PA', 'https://maps.google.com/maps?q=https%3A%2F%2Fmaps.google.com%2Fmaps%3Fq%3DKairouan%26t%3D%26z%3D13%26ie%3DUTF8%26iwloc%3D%26output%3Dembed&t=&z=13&ie=UTF8&iwloc=&output=embed', 1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `ratings`
--

CREATE TABLE `ratings` (
  `id_rating` int(11) NOT NULL,
  `id_produit` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `rating` double NOT NULL,
  `commentaire` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation_enchere`
--

CREATE TABLE `reservation_enchere` (
  `idReservation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `service_apres_vente`
--

CREATE TABLE `service_apres_vente` (
  `idService` int(11) NOT NULL,
  `description` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL,
  `idAchats` int(11) DEFAULT NULL,
  `idPartenaire` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `idGoogle` varchar(255) DEFAULT NULL,
  `nomUtilisateur` varchar(30) DEFAULT NULL,
  `prenomUtilisateur` varchar(50) DEFAULT NULL,
  `sexe` char(1) DEFAULT NULL,
  `dateDeNaissance` date DEFAULT NULL,
  `adresseEmail` varchar(100) DEFAULT NULL,
  `motDePasse` varchar(30) DEFAULT NULL,
  `adressePostale` varchar(60) DEFAULT NULL,
  `numeroTelephone` varchar(30) DEFAULT NULL,
  `numeroCin` varchar(9) DEFAULT NULL,
  `pays` varchar(50) DEFAULT NULL,
  `nbrProduitAchat` int(11) DEFAULT NULL,
  `nbrProduitVendu` int(11) DEFAULT NULL,
  `nbrProduit` int(11) DEFAULT NULL,
  `nbrPoint` int(11) DEFAULT NULL,
  `languePreferree` varchar(50) DEFAULT NULL,
  `evaluationUtilisateur` double DEFAULT NULL,
  `statutVerificationCompte` tinyint(1) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `dateInscription` datetime DEFAULT NULL,
  `role` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `idGoogle`, `nomUtilisateur`, `prenomUtilisateur`, `sexe`, `dateDeNaissance`, `adresseEmail`, `motDePasse`, `adressePostale`, `numeroTelephone`, `numeroCin`, `pays`, `nbrProduitAchat`, `nbrProduitVendu`, `nbrProduit`, `nbrPoint`, `languePreferree`, `evaluationUtilisateur`, `statutVerificationCompte`, `avatar`, `dateInscription`, `role`) VALUES
(1, '100', 'ayari', 'hamza', 'm', '2020-04-02', 'hamza@gmail.com', '0000', '4111 bardo, somrane', '95316683', '123', 'tunisia', 2, 1, 2, 2, 'francais', 10, 0, 'hamza.png', '2019-04-02 20:20:00', 'U'),
(2, 'google1', 'Doe', 'John', 'M', '1990-01-01', 'john.doe@example.com', 'motdepasse1', '1 rue de Tunis', '123456789', '123456789', 'Tunisie', 0, 0, 0, 0, 'Français', 0, 1, NULL, '2024-04-11 04:37:36', 'U'),
(3, 'google2', 'Smith', 'Emily', 'F', '1995-02-02', 'emily.smith@example.com', 'motdepasse2', '2 avenue Habib Bourguiba', '234567890', '234567890', 'Tunisie', 0, 0, 0, 0, 'Arabe', 0, 1, NULL, '2024-04-11 04:37:36', 'A'),
(4, 'google3', 'Garcia', 'Carlos', 'M', '1985-03-03', 'carlos.garcia@example.com', 'motdepasse3', '3 rue de Sousse', '345678901', '345678901', 'Tunisie', 0, 0, 0, 0, 'Anglais', 0, 1, NULL, '2024-04-11 04:37:36', 'U'),
(5, 'google4', 'Kim', 'Soo', 'F', '1980-04-04', 'soo.kim@example.com', 'motdepasse4', '4 boulevard Habib Thameur', '456789012', '456789012', 'Tunisie', 0, 0, 0, 0, 'Français', 0, 1, NULL, '2024-04-11 04:37:36', 'U'),
(6, 'google5', 'Abdelhadi', 'Yasmine', 'F', '1992-05-05', 'yasmine.abdelhadi@example.com', 'motdepasse5', '5 rue de Sfax', '567890123', '567890123', 'Tunisie', 0, 0, 0, 0, 'Arabe', 0, 1, NULL, '2024-04-11 04:37:36', 'A'),
(7, 'google6', 'Abed', 'Mohamed', 'M', '1988-06-06', 'mohamed.abed@example.com', 'motdepasse6', '6 avenue de la Liberté', '678901234', '678901234', 'Tunisie', 0, 0, 0, 0, 'Anglais', 0, 1, NULL, '2024-04-11 04:37:36', 'U'),
(8, 'google7', 'Hassan', 'Fatma', 'F', '1998-07-07', 'fatma.hassan@example.com', 'motdepasse7', '7 boulevard Habib Bourguiba', '789012345', '789012345', 'Tunisie', 0, 0, 0, 0, 'Français', 0, 1, NULL, '2024-04-11 04:37:36', 'U'),
(9, 'google8', 'Brahmi', 'Ali', 'M', '1996-08-08', 'ali.brahmi@example.com', 'motdepasse8', '8 rue de Nabeul', '890123456', '890123456', 'Tunisie', 0, 0, 0, 0, 'Arabe', 0, 1, NULL, '2024-04-11 04:37:36', 'A'),
(10, 'google9', 'Maatoug', 'Leila', 'F', '1989-09-09', 'leila.maatoug@example.com', 'motdepasse9', '9 avenue Habib Bourguiba', '901234567', '901234567', 'Tunisie', 0, 0, 0, 0, 'Anglais', 0, 1, NULL, '2024-04-11 04:37:36', 'U'),
(32, 'aa', 'aa', 'aa', 'm', '2024-02-07', 'aa@aa.com', 'aaa', 'bardo', '23589657', '100', 'tunis', 2, 2, 2, 200, 'francias', 10, 1, 'aa.jpg', '2024-04-17 06:34:06', 'U');

-- --------------------------------------------------------

--
-- Structure de la table `views`
--

CREATE TABLE `views` (
  `utilisateur_id` int(11) DEFAULT NULL,
  `produit_id` int(11) DEFAULT NULL,
  `idViews` int(11) NOT NULL,
  `likes` int(11) DEFAULT 0,
  `dislikes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `views`
--

INSERT INTO `views` (`utilisateur_id`, `produit_id`, `idViews`, `likes`, `dislikes`) VALUES
(1, 1, 1, 0, 0),
(1, NULL, 2, 0, 0),
(3, 1, 3, 0, 0),
(3, NULL, 4, 0, 0),
(3, 4, 5, 0, 0),
(3, 8, 6, 0, 0),
(3, 3, 7, 0, 0),
(4, 1, 8, 0, 0),
(4, NULL, 9, 0, 0),
(10, 1, 10, 0, 0),
(10, NULL, 11, 0, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `achats`
--
ALTER TABLE `achats`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `basket`
--
ALTER TABLE `basket`
  ADD PRIMARY KEY (`idBasket`),
  ADD KEY `IDX_2246507BE173B1B8` (`id_client`),
  ADD KEY `IDX_2246507BF7384557` (`id_produit`);

--
-- Index pour la table `commands`
--
ALTER TABLE `commands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9A3E132CE173B1B8` (`id_client`);

--
-- Index pour la table `command_articles`
--
ALTER TABLE `command_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8631E8F97294869C` (`article_id`),
  ADD KEY `IDX_8631E8F933E1689A` (`command_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `encheres`
--
ALTER TABLE `encheres`
  ADD PRIMARY KEY (`idEnchere`),
  ADD KEY `IDX_8B89031D391C87D5` (`idProduit`);

--
-- Index pour la table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`idImage`),
  ADD KEY `IDX_C53D045FED8EF5D7` (`idProduits`),
  ADD KEY `IDX_C53D045FEB0C61EC` (`idLocations`);

--
-- Index pour la table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`idLocation`),
  ADD KEY `IDX_5E9E89CBBF396750` (`id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `partenaires`
--
ALTER TABLE `partenaires`
  ADD PRIMARY KEY (`idPartenaire`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`idProduit`),
  ADD KEY `IDX_BE2DDF8C5D419CCB` (`idUtilisateur`);

--
-- Index pour la table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id_rating`),
  ADD KEY `IDX_CEB607C9F7384557` (`id_produit`),
  ADD KEY `IDX_CEB607C96B3CA4B` (`id_user`);

--
-- Index pour la table `reservation_enchere`
--
ALTER TABLE `reservation_enchere`
  ADD PRIMARY KEY (`idReservation`);

--
-- Index pour la table `service_apres_vente`
--
ALTER TABLE `service_apres_vente`
  ADD PRIMARY KEY (`idService`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `views`
--
ALTER TABLE `views`
  ADD PRIMARY KEY (`idViews`),
  ADD KEY `IDX_11F09C87FB88E14F` (`utilisateur_id`),
  ADD KEY `IDX_11F09C87F347EFB` (`produit_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `achats`
--
ALTER TABLE `achats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `basket`
--
ALTER TABLE `basket`
  MODIFY `idBasket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `commands`
--
ALTER TABLE `commands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `command_articles`
--
ALTER TABLE `command_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `encheres`
--
ALTER TABLE `encheres`
  MODIFY `idEnchere` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `image`
--
ALTER TABLE `image`
  MODIFY `idImage` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `location`
--
ALTER TABLE `location`
  MODIFY `idLocation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `partenaires`
--
ALTER TABLE `partenaires`
  MODIFY `idPartenaire` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `idProduit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id_rating` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservation_enchere`
--
ALTER TABLE `reservation_enchere`
  MODIFY `idReservation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `service_apres_vente`
--
ALTER TABLE `service_apres_vente`
  MODIFY `idService` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `views`
--
ALTER TABLE `views`
  MODIFY `idViews` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `basket`
--
ALTER TABLE `basket`
  ADD CONSTRAINT `FK_2246507BE173B1B8` FOREIGN KEY (`id_client`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `FK_2246507BF7384557` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`idProduit`);

--
-- Contraintes pour la table `commands`
--
ALTER TABLE `commands`
  ADD CONSTRAINT `FK_9A3E132CE173B1B8` FOREIGN KEY (`id_client`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `command_articles`
--
ALTER TABLE `command_articles`
  ADD CONSTRAINT `FK_8631E8F933E1689A` FOREIGN KEY (`command_id`) REFERENCES `commands` (`id`),
  ADD CONSTRAINT `FK_8631E8F97294869C` FOREIGN KEY (`article_id`) REFERENCES `produits` (`idProduit`);

--
-- Contraintes pour la table `encheres`
--
ALTER TABLE `encheres`
  ADD CONSTRAINT `FK_8B89031D391C87D5` FOREIGN KEY (`idProduit`) REFERENCES `produits` (`idProduit`);

--
-- Contraintes pour la table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `FK_C53D045FEB0C61EC` FOREIGN KEY (`idLocations`) REFERENCES `location` (`idLocation`),
  ADD CONSTRAINT `FK_C53D045FED8EF5D7` FOREIGN KEY (`idProduits`) REFERENCES `produits` (`idProduit`);

--
-- Contraintes pour la table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `FK_5E9E89CBBF396750` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `FK_BE2DDF8C5D419CCB` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `FK_CEB607C96B3CA4B` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `FK_CEB607C9F7384557` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`idProduit`);

--
-- Contraintes pour la table `views`
--
ALTER TABLE `views`
  ADD CONSTRAINT `FK_11F09C87F347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`idProduit`),
  ADD CONSTRAINT `FK_11F09C87FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
