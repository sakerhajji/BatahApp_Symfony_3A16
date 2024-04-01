-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 01 avr. 2024 à 15:12
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
-- Base de données : `batahappfinal`
--

-- --------------------------------------------------------

--
-- Structure de la table `achats`
--

CREATE TABLE `achats` (
  `idAchats` int(11) NOT NULL,
  `idProduits` int(11) DEFAULT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `dateAchats` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `achats`
--

INSERT INTO `achats` (`idAchats`, `idProduits`, `idUtilisateur`, `dateAchats`) VALUES
(1, NULL, 1, '2024-02-25'),
(2, NULL, 2, '2024-02-26'),
(3, NULL, 3, '2024-02-27'),
(4, NULL, 4, '2024-02-28'),
(5, NULL, 5, '2024-02-29'),
(6, NULL, 6, '2024-03-01');

-- --------------------------------------------------------

--
-- Structure de la table `basket`
--

CREATE TABLE `basket` (
  `idBasket` int(11) NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `id_produit` int(11) DEFAULT NULL,
  `date_ajout` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `remise` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `basket`
--

INSERT INTO `basket` (`idBasket`, `id_client`, `id_produit`, `date_ajout`, `remise`) VALUES
(22, 3, 3, '2024-03-30 06:04:06', NULL),
(24, NULL, NULL, '2024-03-30 00:00:00', NULL),
(25, 9, 1, '2024-03-30 00:00:00', NULL),
(31, 15, 1, '2024-03-31 06:42:56', NULL),
(34, 15, 2, '2024-03-31 07:26:45', NULL),
(35, 15, 3, '2024-03-31 07:26:50', NULL),
(36, 15, 33, '2024-04-01 00:55:58', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `encheres`
--

CREATE TABLE `encheres` (
  `idEnchere` int(11) NOT NULL,
  `dateDebut` date DEFAULT NULL,
  `dateFin` date DEFAULT NULL,
  `Status` tinyint(1) DEFAULT NULL,
  `prixMin` float(10,2) DEFAULT NULL,
  `prixMax` float(10,2) DEFAULT NULL,
  `prixActuelle` float(10,2) DEFAULT NULL,
  `nbrParticipants` int(200) NOT NULL,
  `idProduit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `encheres`
--

INSERT INTO `encheres` (`idEnchere`, `dateDebut`, `dateFin`, `Status`, `prixMin`, `prixMax`, `prixActuelle`, `nbrParticipants`, `idProduit`) VALUES
(1, '2024-03-01', '2024-03-07', 1, 500.00, 1000.00, 0.00, 5, 1);

-- --------------------------------------------------------

--
-- Structure de la table `image`
--

CREATE TABLE `image` (
  `idImage` int(11) NOT NULL,
  `url` varchar(250) NOT NULL,
  `idProduits` int(11) DEFAULT NULL,
  `idLocations` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `image`
--

INSERT INTO `image` (`idImage`, `url`, `idProduits`, `idLocations`) VALUES
(1, 'https://example.com/image1.jpg', 1, NULL),
(2, 'https://example.com/image2.jpg', 3, 1),
(3, 'https://example.com/image3.jpg', 2, NULL),
(4, 'https://example.com/image4.jpg', NULL, 2),
(5, 'https://example.com/image5.jpg', 3, NULL),
(6, 'https://example.com/image6.jpg', NULL, 3),
(7, 'https://example.com/image7.jpg', 4, NULL),
(8, 'https://example.com/image8.jpg', NULL, 4),
(9, 'https://example.com/image9.jpg', 5, NULL),
(10, 'https://example.com/image10.jpg', NULL, 5),
(11, 'uploads/example-scrum-board-88b9d7eabf42933456f36c79732b500f-660340ec72232.webp', NULL, 31),
(19, '/uploads/cupra-6603a95146852.jpg', NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `location`
--

CREATE TABLE `location` (
  `idLocation` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `disponibilite` tinyint(1) NOT NULL,
  `id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `location`
--

INSERT INTO `location` (`idLocation`, `prix`, `type`, `description`, `adresse`, `disponibilite`, `id`) VALUES
(1, 11.00, 'Maison', 'Belle maison avec jardin dans un quartier calme', '123 Rue de la Paix, Ville, Pays', 1, 1),
(2, 2000.00, 'Appartement', 'Appartement moderne avec vue sur la ville', '456 Avenue des Champs-Élysées, Ville, Pays', 1, 2),
(3, 1250.00, 'Maison', 'Maison de ville récemment rénovée près du centre-ville', '789 Rue Principale, Ville, Pays', 1, 3),
(4, 1800.00, 'Appartement', 'Appartement spacieux avec balcon près des transports en commun', '1010 Boulevard Saint-Michel, Ville, Pays', 1, 4),
(5, 2500.00, 'Maison', 'Grande maison de campagne avec piscine', '111 Rue des Fleurs, Ville, Pays', 1, 5),
(6, 1700.00, 'Appartement', 'Appartement lumineux avec vue sur la rivière', '1313 Quai des Braves, Ville, Pays', 1, 6),
(7, 2200.00, 'Maison', 'Maison familiale avec grand jardin et aire de jeux pour enfants', '1515 Avenue des Enfants, Ville, Pays', 1, 7),
(8, 1900.00, 'Appartement', 'Appartement élégant dans un immeuble historique du centre-ville', '1717 Rue Historique, Ville, Pays', 1, 8),
(9, 2700.00, 'Maison', 'Maison de luxe avec vue panoramique sur la mer', '1919 Boulevard des Océans, Ville, Pays', 1, 9),
(10, 1600.00, 'Appartement', 'Appartement confortable avec cheminée dans un quartier résidentiel', '2121 Rue des Pins, Ville, Pays', 1, 10),
(11, 50.00, 'Voiture', 'Petite citadine économique', '123 Rue de la Paix, Ville, Pays', 1, 1),
(12, 80.00, 'Voiture', 'Berline confortable pour les longs trajets', '456 Avenue des Champs-Élysées, Ville, Pays', 1, 2),
(13, 70.00, 'Voiture', 'SUV spacieux pour les familles', '789 Rue Principale, Ville, Pays', 1, 3),
(14, 60.00, 'Voiture', 'Compacte idéale pour les déplacements en ville', '1010 Boulevard Saint-Michel, Ville, Pays', 1, 4),
(15, 90.00, 'Voiture', 'Cabriolet pour des voyages ensoleillés', '111 Rue des Fleurs, Ville, Pays', 1, 5),
(16, 75.00, 'Voiture', 'Monospace pratique pour les grands groupes', '1313 Quai des Braves, Ville, Pays', 1, 6),
(17, 85.00, 'Voiture', 'Voiture de luxe pour une expérience premium', '1515 Avenue des Enfants, Ville, Pays', 1, 7),
(18, 65.00, 'Voiture', 'Électrique pour une conduite écologique', '1717 Rue Historique, Ville, Pays', 1, 8),
(21, 11.00, 'SZX', 'BJH', 'b', 1, 4),
(27, 99.00, 'hamza', 'hamza', 'aa', 1, 1),
(28, 1.00, 'ha', 'a', 'a', 1, 11),
(29, 1.00, 'A', 'a', 'a', 1, 10),
(30, 1.00, 'A', 'A', 'A', 1, 13),
(31, 1.00, 'zzzz', 'a', 'aa', 1, 1);

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
  `status` int(11) DEFAULT 0,
  `logo` varchar(200) NOT NULL,
  `points` int(50) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `partenaires`
--

INSERT INTO `partenaires` (`idPartenaire`, `nom`, `type`, `adresse`, `telephone`, `email`, `status`, `logo`, `points`) VALUES
(1, 'sofienne', 'voiture', 'Adresse 1', 12345678, 'sofiennemrabet321@gmail.com', 1, 'logo_voiture.png', 9),
(2, 't', 'maison', 'Adresse 2', 21474836, 'partenaire2@example.com', 1, 'logo_maison.png', 5),
(3, 'Partenaire 3', 'terrain', 'Adresse 3', 21474836, 'partenaire3@example.com', 1, 'logo_terrain.png', 5),
(4, 'Partenaire 4', 'Type 4', 'Adresse 4', 2147483647, 'partenaire4@example.com', 1, '', 7),
(5, 'Partenaire 5', 'Type 5', 'Adresse 5', 2147483647, 'partenaire5@example.com', 1, '', 5),
(6, 'Partenaire 6', 'Type 6', 'Adresse 6', 2147483647, 'partenaire6@example.com', 1, '', 0),
(7, 'Partenaire 7', 'Type 7', 'Adresse 7', 2147483647, 'partenaire7@example.com', 1, '', 1),
(9, 'Partenaire ', 'Type 9', 'Adresse 9', 21474836, 'partenaire9@example.com', 1, '', 0),
(12, 'zeineb', 'voiture', 'nabeul', 54497997, 'cherifzeineb741@gmail.com', NULL, '', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `idProduit` int(11) NOT NULL,
  `type` varchar(300) NOT NULL,
  `description` varchar(300) NOT NULL,
  `prix` float NOT NULL,
  `labelle` varchar(300) NOT NULL,
  `status` varchar(255) NOT NULL,
  `periodeGarantie` int(11) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `localisation` varchar(200) NOT NULL,
  `idUtilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`idProduit`, `type`, `description`, `prix`, `labelle`, `status`, `periodeGarantie`, `photo`, `localisation`, `idUtilisateur`) VALUES
(1, 'maison', 'Luxury sedan with advanced technology features and comfortable interior.', 100000, 'Mercedes-Benz E-Class', 'disponible', 0, 'e01f2738ac9822ecf0e6858c31e509b5.jpg', 'https://maps.google.com/maps?q=bardo&t=&z=13&ie=UTF8&iwloc=&output=embed', 2),
(2, 'voiture', 'Reliable midsize sedan known for its fuel efficiency and spacious cabin.', 70000, 'Toyota Camry', 'disponible', 18, '8bf4397880d8da52abc2cd37402383a1.jpg', 'https://maps.google.com/maps?q=menzah&t=&z=13&ie=UTF8&iwloc=&output=embed', 8),
(3, 'voiture', 'Premium SUV with powerful engine options and luxurious amenities.', 150000, 'BMW X5', 'disponible', 36, '60ee9dfdb9ad194eadb0bf5cefb809da.webp', 'https://maps.google.com/maps?q=london&t=&z=13&ie=UTF8&iwloc=&output=embed', 3),
(4, 'maison', 'Chalet confortable avec des vues panoramiques et un grand jardin', 200000, 'Chalet Pittoresque', 'disponible', 0, '3723b775d99b2e08a9d8643732fcfb00.jpg', 'https://maps.google.com/maps?q=bardo&t=&z=13&ie=UTF8&iwloc=&output=embed', 3),
(5, 'maison', 'Citadine compacte avec une excellente économie de carburant', 100000, 'bmw Économe en Carburant', 'disponible', 0, 'e7e1059cd4e72b676a5cf5563e9bb642.webp', 'https://maps.google.com/maps?q=bardo&t=&z=13&ie=UTF8&iwloc=&output=embed', 9),
(19, 'maison', 'oppa', 200000, 'korsi', 'disponible', 20, '19052ac172d80b1f5d33e5b4e9a58f5a.jpg', 'aa', 12),
(20, 'maison', 'oppa', 200000, 'korsiiiii', 'disponible', 20, '4deae5cc42013e350dc78d86d435fe10.jpg', 'aa', 12),
(22, 'voiture', 'Versatile hatchback with a spacious interior and responsive performance.', 60000, 'Volkswagen Golf', 'disponible', 24, 'd0df9990da06005bba348a620d7dfe11.png', 'https://maps.google.com/maps?q=NewYork&t=&z=13&ie=UTF8&iwloc=&output=embed', 12),
(23, 'maison', 'Iconic muscle car with powerful engine options and classic styling.', 80000, 'Ford Mustang', 'disponible', 18, 'e3b28e7877b4d28fdb1839baeb0e7770.jpg', 'q', 13),
(33, 'maison', 'rrrrr', 220000, 'audi', 'disponible', 2, 'f1e269d62072629533f2e7bdada4100f.webp', 'https://maps.google.com/maps?q=paris&t=&z=13&ie=UTF8&iwloc=&output=embed', 2);

-- --------------------------------------------------------

--
-- Structure de la table `ratings`
--

CREATE TABLE `ratings` (
  `id_rating` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `rating` double NOT NULL,
  `commentaire` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ratings`
--

INSERT INTO `ratings` (`id_rating`, `id_user`, `id_produit`, `rating`, `commentaire`) VALUES
(8, 5, 1, 5, '');

-- --------------------------------------------------------

--
-- Structure de la table `reservation_enchere`
--

CREATE TABLE `reservation_enchere` (
  `idReservation` int(11) NOT NULL,
  `idEnchere` int(11) DEFAULT NULL,
  `idUser` int(11) DEFAULT NULL,
  `dateReservation` date DEFAULT NULL,
  `confirmation` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation_location`
--

CREATE TABLE `reservation_location` (
  `id_reservation_location` int(11) NOT NULL,
  `dateDebut` date DEFAULT NULL,
  `dateFin` date DEFAULT NULL,
  `idUtilisateur` int(11) DEFAULT NULL,
  `idLocation` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation_location`
--

INSERT INTO `reservation_location` (`id_reservation_location`, `dateDebut`, `dateFin`, `idUtilisateur`, `idLocation`, `notes`) VALUES
(1, '2024-02-25', '2024-02-28', 1, 1, 'Notes pour la première réservation de location'),
(2, '2024-02-26', '2024-03-01', 2, 2, 'Notes pour la deuxième réservation de location'),
(3, '2024-02-27', '2024-03-02', 3, 3, 'Notes pour la troisième réservation de location'),
(4, '2024-02-28', '2024-03-03', 4, 4, 'Notes pour la quatrième réservation de location'),
(5, '2024-02-29', '2024-03-04', 5, 5, 'Notes pour la cinquième réservation de location'),
(6, '2024-03-01', '2024-03-05', 6, 6, 'Notes pour la sixième réservation de location'),
(7, '2024-03-02', '2024-03-06', 7, 7, 'Notes pour la septième réservation de location'),
(8, '2024-03-03', '2024-03-07', 8, 8, 'Notes pour la huitième réservation de location'),
(9, '2024-03-04', '2024-03-08', 9, 9, 'Notes pour la neuvième réservation de location'),
(10, '2024-03-05', '2024-03-09', 10, 10, 'Notes pour la dixième réservation de location');

-- --------------------------------------------------------

--
-- Structure de la table `service_apres_vente`
--

CREATE TABLE `service_apres_vente` (
  `idService` int(11) NOT NULL,
  `description` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `idPartenaire` int(11) DEFAULT NULL,
  `idAchats` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `service_apres_vente`
--

INSERT INTO `service_apres_vente` (`idService`, `description`, `type`, `date`, `status`, `idPartenaire`, `idAchats`) VALUES
(1, 'Révision annuelle de la voiture', 'Voiture', '2024-02-25 10:00:00', 1, 1, 1),
(2, 'Entretien du système de chauffage', 'Maison', '2024-02-26 11:00:00', 1, 2, 2),
(3, 'Changement des pneus et équilibrage', 'Voiture', '2024-02-27 12:00:00', 1, 3, 3),
(4, 'Rénovation de la cuisine', 'Maison', '2024-02-28 13:00:00', 1, 4, 4),
(13, 'hhhhh', 'voiture', '2024-02-27 00:00:00', 1, NULL, 2),
(14, 'hhhhh', 'voiture', '2024-03-05 00:00:00', 1, 1, 2);

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
  `evaluationUtilisateur` float DEFAULT NULL,
  `statutVerificationCompte` tinyint(1) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `dateInscription` datetime DEFAULT NULL,
  `role` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `idGoogle`, `nomUtilisateur`, `prenomUtilisateur`, `sexe`, `dateDeNaissance`, `adresseEmail`, `motDePasse`, `adressePostale`, `numeroTelephone`, `numeroCin`, `pays`, `nbrProduitAchat`, `nbrProduitVendu`, `nbrProduit`, `nbrPoint`, `languePreferree`, `evaluationUtilisateur`, `statutVerificationCompte`, `avatar`, `dateInscription`, `role`) VALUES
(1, NULL, 'Dos', 'John', 'M', '1990-05-15', 'john.doe@example.com', 'password123', '123 Main St, City, Country', '+1234567890', '123456789', 'Country', 5, 10, 15, 100, 'English', 4.5, 1, 'avatar1.jpg', '2024-02-20 12:00:00', 'U'),
(2, NULL, 'Smiths', 'Alices', 'F', '1985-08-20', 'alice.smith@tt.com', '24512995', '456 Elm St, City, Country', '12345678', '12345678', 'Tunis', 8, 12, 20, 150, 'French', 4.8, 1, 'SakerHajji.png', '2024-02-20 12:01:00', 'U'),
(3, NULL, 'Johnson', 'Robert', 'M', '1975-03-10', 'robert.johnson@example.com', 'password789', '789 Oak St, City, Country', '+1122334455', '112233445', 'Country', 10, 15, 25, 200, 'Spanish', 4.2, 1, 'avatar3.jpg', '2024-02-20 12:02:00', 'U'),
(4, NULL, 'Brown', 'Emma', 'F', '1995-11-25', 'emma.brown@example.com', 'passwordabc', '101 Pine St, City, Country', '+4433221100', '443322110', 'Country', 3, 5, 8, 80, 'German', 4, 1, 'avatar4.jpg', '2024-02-20 12:03:00', 'U'),
(5, NULL, 'Miller', 'Michael', 'M', '1980-07-05', 'michael.miller@example.com', 'passworddef', '202 Cedar St, City, Country', '+5566778899', '556677889', 'Country', 12, 18, 30, 250, 'Chinese', 4.6, 1, 'avatar5.jpg', '2024-02-20 12:04:00', 'U'),
(6, NULL, 'Wilson', 'Olivia', 'F', '1988-02-28', 'olivia.wilson@example.com', 'passwordghi', '303 Walnut St, City, Country', '+9988776655', '998877665', 'Country', 6, 9, 15, 120, 'Japanese', 4.3, 1, 'avatar6.jpg', '2024-02-20 12:05:00', 'U'),
(7, NULL, 'Taylor', 'David', 'M', '1972-09-12', 'david.taylor@example.com', 'passwordjkl', '404 Maple St, City, Country', '+1122334455', '112233445', 'Country', 7, 11, 18, 130, 'Korean', 4.1, 1, 'avatar7.jpg', '2024-02-20 12:06:00', 'U'),
(8, NULL, 'Anderson', 'Sophia', 'F', '1992-12-10', 'sophia.anderson@example.com', 'passwordmno', '505 Oak St, City, Country', '+9988776655', '998877665', 'Country', 9, 14, 23, 180, 'Italian', 4.9, 1, 'avatar8.jpg', '2024-02-20 12:07:00', 'U'),
(9, NULL, 'Thomas', 'James', 'M', '1983-04-18', 'james.thomas@example.com', 'passwordpqr', '606 Pine St, City, Country', '+5566778899', '556677889', 'Country', 11, 16, 27, 220, 'Russian', 4.4, 1, 'avatar9.jpg', '2024-02-20 12:08:00', 'U'),
(10, NULL, 'Ayari', 'Hamza', 'M', '1978-06-30', 'hamza@gmail.com', '0000', '707 Cedar St, City, Country', '+4433221100', '443322110', 'Country', 4, 7, 11, 90, 'Portuguese', 4.7, 1, 'hamza.jpg', '2024-02-20 12:09:00', 'A'),
(11, NULL, 'Hajji', 'Saker', 'H', '2001-05-28', 'saker.hajji13@esprit.tn', '24512995', '39 rue omar ibno abi safwa', '24512995', '03769104', 'tuni', 0, 0, 0, 0, 'Arabe', 0, 1, 'saker.jpg', '2024-02-21 00:00:00', 'A'),
(12, NULL, 'Ahmed', 'ali', 'H', '2005-02-18', 'ali@ali.tn', '159753', NULL, '24512995', '03769104', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'A'),
(13, NULL, 'test', 'test', 'H', '2024-02-09', 'test', 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'A'),
(14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL),
(15, '100', 'batta', 'opa', 'm', '2019-04-01', 'batta@gmail.com', '0000', '4444', '95316683', '123', 'tunisia', 2, 1, 2, 2, 'francais', 10, 1, 'batta.png', '2022-09-16 08:05:00', 'C');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `achats`
--
ALTER TABLE `achats`
  ADD PRIMARY KEY (`idAchats`);

--
-- Index pour la table `basket`
--
ALTER TABLE `basket`
  ADD PRIMARY KEY (`idBasket`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_produit_fk` (`id_produit`);

--
-- Index pour la table `encheres`
--
ALTER TABLE `encheres`
  ADD PRIMARY KEY (`idEnchere`),
  ADD KEY `fk_enchere_produit` (`idProduit`);

--
-- Index pour la table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`idImage`),
  ADD KEY `fk_id_prod` (`idProduits`),
  ADD KEY `fk_id_location` (`idLocations`);

--
-- Index pour la table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`idLocation`),
  ADD KEY `fk_location_user` (`id`);

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
  ADD KEY `fk_user_prod` (`idUtilisateur`);

--
-- Index pour la table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id_rating`),
  ADD KEY `fk_user` (`id_user`),
  ADD KEY `fk_produit` (`id_produit`);

--
-- Index pour la table `reservation_enchere`
--
ALTER TABLE `reservation_enchere`
  ADD PRIMARY KEY (`idReservation`),
  ADD KEY `idEnchere` (`idEnchere`),
  ADD KEY `idUser` (`idUser`);

--
-- Index pour la table `reservation_location`
--
ALTER TABLE `reservation_location`
  ADD PRIMARY KEY (`id_reservation_location`),
  ADD KEY `idUtilisateur` (`idUtilisateur`),
  ADD KEY `idLocation` (`idLocation`);

--
-- Index pour la table `service_apres_vente`
--
ALTER TABLE `service_apres_vente`
  ADD PRIMARY KEY (`idService`),
  ADD KEY `idPartenaire` (`idPartenaire`),
  ADD KEY `fk_id_achats` (`idAchats`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `adresseEmail` (`adresseEmail`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `basket`
--
ALTER TABLE `basket`
  MODIFY `idBasket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT pour la table `encheres`
--
ALTER TABLE `encheres`
  MODIFY `idEnchere` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `image`
--
ALTER TABLE `image`
  MODIFY `idImage` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `location`
--
ALTER TABLE `location`
  MODIFY `idLocation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `partenaires`
--
ALTER TABLE `partenaires`
  MODIFY `idPartenaire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `idProduit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT pour la table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id_rating` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `reservation_enchere`
--
ALTER TABLE `reservation_enchere`
  MODIFY `idReservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `reservation_location`
--
ALTER TABLE `reservation_location`
  MODIFY `id_reservation_location` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `service_apres_vente`
--
ALTER TABLE `service_apres_vente`
  MODIFY `idService` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `basket`
--
ALTER TABLE `basket`
  ADD CONSTRAINT `id_client` FOREIGN KEY (`id_client`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `id_produit_fk` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`idProduit`);

--
-- Contraintes pour la table `encheres`
--
ALTER TABLE `encheres`
  ADD CONSTRAINT `fk_enchere_produit` FOREIGN KEY (`idProduit`) REFERENCES `produits` (`idProduit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `fk_id_location` FOREIGN KEY (`idLocations`) REFERENCES `location` (`idLocation`),
  ADD CONSTRAINT `fk_id_prod` FOREIGN KEY (`idProduits`) REFERENCES `produits` (`idProduit`);

--
-- Contraintes pour la table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `fk_location_user` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `fk_produit_user` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `fk_produit` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`idProduit`),
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `reservation_enchere`
--
ALTER TABLE `reservation_enchere`
  ADD CONSTRAINT `fk_reservation_enchere_enchere` FOREIGN KEY (`idEnchere`) REFERENCES `encheres` (`idEnchere`),
  ADD CONSTRAINT `fk_reservation_enchere_utilisateur` FOREIGN KEY (`idUser`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `reservation_location`
--
ALTER TABLE `reservation_location`
  ADD CONSTRAINT `fk_reservation_location_location` FOREIGN KEY (`idLocation`) REFERENCES `location` (`idLocation`),
  ADD CONSTRAINT `fk_reservation_location_utilisateur` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `service_apres_vente`
--
ALTER TABLE `service_apres_vente`
  ADD CONSTRAINT `fk_id_achats` FOREIGN KEY (`idAchats`) REFERENCES `achats` (`idAchats`),
  ADD CONSTRAINT `fk_service_partenaire` FOREIGN KEY (`idPartenaire`) REFERENCES `partenaires` (`idPartenaire`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
