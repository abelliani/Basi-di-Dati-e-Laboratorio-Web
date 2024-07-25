-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2024 at 05:22 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `PROGETTO596864`
--

-- --------------------------------------------------------

--
-- Table structure for table `Abbonamento`
--

CREATE TABLE `Abbonamento` (
  `Tipo` varchar(10) NOT NULL,
  `Tariffa` double(4,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Abbonamento`
--

INSERT INTO `Abbonamento` (`Tipo`, `Tariffa`) VALUES
('Annuale', 49.99),
('Mensile', 4.99);

-- --------------------------------------------------------

--
-- Table structure for table `Associa`
--

CREATE TABLE `Associa` (
  `IdBlog` int(10) UNSIGNED NOT NULL,
  `IdCategoria` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Associa`
--

INSERT INTO `Associa` (`IdBlog`, `IdCategoria`) VALUES
(23, 124),
(24, 177),
(24, 180),
(25, 177),
(30, 156),
(30, 162),
(34, 14);

-- --------------------------------------------------------

--
-- Table structure for table `Blog`
--

CREATE TABLE `Blog` (
  `IdBlog` int(10) UNSIGNED NOT NULL,
  `IdUtente` int(10) UNSIGNED DEFAULT NULL,
  `TitoloBlog` varchar(30) NOT NULL,
  `Descrizione` varchar(255) NOT NULL,
  `Immagine` varchar(255) DEFAULT 'immagini/placeholder.png',
  `N_Follow` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Blog`
--

INSERT INTO `Blog` (`IdBlog`, `IdUtente`, `TitoloBlog`, `Descrizione`, `Immagine`, `N_Follow`) VALUES
(23, 2, 'ciao', 'that\'s what she said', 'immagini/1_xDIevNE7HEMiJQVTYg0qDQ.png', 3),
(24, 3, 'Movies', 'Mi piace l\'animazione e vorrei parlarne in questo blog con voi!', 'immagini/Film-cartoni-per-bambini.jpg', 4),
(25, 4, 'Ciao', 'diadigg', 'immagini/placeholder.png', 0),
(30, 1, 'LoDicodaTHEOFFICE', 'best sitcom!', 'immagini/placeholder.png', 0),
(34, 7, 'Gina&Gino', 'I miei gattini', 'immagini/Immagine WhatsApp 2024-07-05 ore 17.03.52_f5949f97.jpg', 0);

--
-- Triggers `Blog`
--
DELIMITER $$
CREATE TRIGGER `NumeroBlog` BEFORE INSERT ON `Blog` FOR EACH ROW BEGIN
	DECLARE N_Blog INT;
	
	SELECT COUNT(*) INTO N_BLOG
	FROM Blog
	WHERE IdUtente = NEW.IdUtente;
	
	IF NEW.IdUtente NOT IN (
		SELECT IdUtente 
		FROM Premium 
		WHERE ScadenzaAbbonamento > NOW()
		) AND N_Blog = 5 THEN 
			SIGNAL SQLSTATE '45000' 
			SET MESSAGE_TEXT = 'Non sei autorizzato a creare più di 5 blog.'; 
	END IF; 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Categoria`
--

CREATE TABLE `Categoria` (
  `IdCategoria` int(10) UNSIGNED NOT NULL,
  `Tema` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Categoria`
--

INSERT INTO `Categoria` (`IdCategoria`, `Tema`) VALUES
(8, 'Animali'),
(9, 'Mammiferi'),
(10, 'Felini'),
(11, 'Leone'),
(12, 'Tigre'),
(13, 'Leopardo'),
(14, 'Gatto Domestico'),
(15, 'Canidi'),
(16, 'Lupo'),
(17, 'Volpe'),
(18, 'Cane Domestico'),
(19, 'Primati'),
(20, 'Scimpanzé'),
(21, 'Gorilla'),
(22, 'Lemure'),
(23, 'Roditori'),
(24, 'Topo'),
(25, 'Scoiattolo'),
(26, 'Castoro'),
(27, 'Uccelli'),
(28, 'Rapaci'),
(29, 'Aquila'),
(30, 'Falco'),
(31, 'Gufo'),
(32, 'Passeriformi'),
(33, 'Passero'),
(34, 'Merlo'),
(35, 'Rondine'),
(36, 'Palmipedi'),
(37, 'Anatra'),
(38, 'Oca'),
(39, 'Cigno'),
(40, 'Pappagalli'),
(41, 'Rettili'),
(42, 'Serpenti'),
(43, 'Cobra'),
(44, 'Pitone'),
(45, 'Serpente a sonagli'),
(46, 'Lucertole'),
(47, 'Geco'),
(48, 'Iguana'),
(49, 'Camaleonte'),
(50, 'Testuggini'),
(51, 'Tartaruga di terra'),
(52, 'Tartaruga marina'),
(53, 'Anfibi'),
(54, 'Rane'),
(55, 'Rana toro'),
(56, 'Rana verde'),
(57, 'Salamandre'),
(58, 'Salamandra pezzata'),
(59, 'Tritone'),
(60, 'Pesci'),
(61, 'Cartilaginei'),
(62, 'Squalo'),
(63, 'Ossei'),
(64, 'Trota'),
(65, 'Salmone'),
(66, 'Pesce pagliaccio'),
(67, 'Insetti'),
(68, 'Coleotteri'),
(69, 'Coccinella'),
(70, 'Scarabeo'),
(71, 'Maggiolino'),
(72, 'Lepidotteri'),
(73, 'Farfalla'),
(74, 'Falena'),
(75, 'Ditteri'),
(76, 'Mosca'),
(77, 'Zanzara'),
(78, 'Arachnidi'),
(79, 'Ragni'),
(80, 'Ragno saltatore'),
(81, 'Tarantola'),
(82, 'Scorpioni'),
(83, 'Scorpione imperatore'),
(84, 'Sport di squadra'),
(85, 'Sport individuali'),
(86, 'Sport motoristici'),
(87, 'Sport invernali'),
(88, 'Sport acquatici'),
(89, 'Sport di combattimento'),
(90, 'Sport estremi'),
(91, 'Sport con la palla'),
(92, 'Calcio'),
(93, 'Basket'),
(94, 'Rugby'),
(95, 'Pallavolo'),
(96, 'Hockey'),
(97, 'Hockey su ghiaccio'),
(98, 'Atletica leggera'),
(99, 'Corsa'),
(100, 'Salto in lungo'),
(101, 'Lancio del giavellotto'),
(102, 'Nuoto'),
(103, 'Stile libero'),
(104, 'Tennis'),
(105, 'Boxe'),
(106, 'Golf'),
(107, 'Automobilismo'),
(108, 'Formula 1'),
(109, 'Motociclismo'),
(110, 'MotoGP'),
(111, 'Motocross'),
(112, 'Karting'),
(113, 'Sci'),
(114, 'Snowbord'),
(115, 'Pattinaggio'),
(116, 'Vela'),
(117, 'Canottaggio'),
(118, 'Immersioni'),
(119, 'Arti marziali'),
(120, 'Karate'),
(121, 'Judo'),
(122, 'Taekwondo'),
(123, 'Arrampicata'),
(124, 'Alpinismo'),
(125, 'Paracadutismo'),
(126, 'Ping Pong'),
(127, 'Karate'),
(128, 'Pallamano'),
(129, 'Pallanuoto'),
(130, 'Cucina Italiana'),
(131, 'Primi piatti'),
(132, 'Pasta'),
(133, 'Risotto'),
(134, 'Zuppe'),
(135, 'Secondi piatti'),
(136, 'Carne'),
(137, 'Pesce'),
(138, 'Contorni'),
(139, 'Insalate'),
(140, 'Verdure'),
(141, 'Dolci'),
(142, 'Gelato'),
(143, 'Cannoli'),
(144, 'Tiramisu'),
(145, 'Cucina Giapponese'),
(146, 'Sushi'),
(147, 'Nigiri'),
(148, 'Sashimi'),
(149, 'Maki'),
(150, 'Piatti caldi'),
(151, 'Ramen'),
(152, 'Tempura'),
(153, 'Dolci'),
(154, 'Mochi'),
(155, 'Dorayaki'),
(156, 'Film'),
(157, 'Azione'),
(158, 'Supereroi'),
(159, 'Spionaggio'),
(160, 'Arti marziali'),
(161, 'Avventura'),
(162, 'Commedia'),
(163, 'Commedia romantica'),
(164, 'Commedia drammatica'),
(165, 'Commedia nera'),
(166, 'Dramma'),
(167, 'Dramma storico'),
(168, 'Dramma psicologico'),
(169, 'Dramma romantico'),
(170, 'Fantascienza'),
(171, 'Distopico'),
(172, 'Viaggio nel tempo'),
(173, 'Cyberpunk'),
(174, 'Horror'),
(175, 'Slasher'),
(176, 'Horror psicologico'),
(177, 'Animazione'),
(178, 'Animazione per bambini'),
(179, 'Animazione per adulti'),
(180, 'Anime giapponesi'),
(181, 'Documentario'),
(182, 'Documentario storico'),
(183, 'Documentario naturalistico'),
(184, 'Documentario biografico');

-- --------------------------------------------------------

--
-- Table structure for table `Coautore`
--

CREATE TABLE `Coautore` (
  `IdCoautore` int(10) UNSIGNED NOT NULL,
  `IdBlog` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Coautore`
--

INSERT INTO `Coautore` (`IdCoautore`, `IdBlog`) VALUES
(1, 23);

-- --------------------------------------------------------

--
-- Table structure for table `Commento`
--

CREATE TABLE `Commento` (
  `IdCommento` int(10) UNSIGNED NOT NULL,
  `IdUtente` int(10) UNSIGNED DEFAULT NULL,
  `IdPost` int(10) UNSIGNED DEFAULT NULL,
  `Testo` varchar(255) NOT NULL,
  `Data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Commento`
--

INSERT INTO `Commento` (`IdCommento`, `IdUtente`, `IdPost`, `Testo`, `Data`) VALUES
(16, 4, 45, 'bello', '2024-06-26 10:16:28'),
(18, 5, 44, 'ciao', '2024-06-28 16:46:45'),
(19, 5, 44, 'ciao', '2024-06-28 16:48:15'),
(20, 5, 44, 'ok', '2024-06-28 16:50:03'),
(21, 5, 44, 'ok', '2024-06-28 16:50:26'),
(22, 5, 44, 'ciao', '2024-06-28 16:56:03'),
(23, 5, 44, 'ciao', '2024-06-28 16:56:10'),
(24, 5, 44, 'ciao', '2024-06-28 16:56:24'),
(25, 5, 44, 'ok', '2024-06-28 16:57:19'),
(26, 5, 44, 'noia', '2024-06-28 17:00:14'),
(27, 5, 44, 'ok', '2024-06-28 17:01:30'),
(28, 5, 44, 'ok', '2024-06-28 17:01:32'),
(29, 5, 44, 'oki', '2024-06-28 17:01:35'),
(30, 5, 44, 'b', '2024-06-28 17:02:03'),
(31, 5, 44, 'a', '2024-06-28 17:03:07'),
(32, 5, 44, 'a', '2024-06-28 17:03:08'),
(33, 5, 44, 'ciao', '2024-06-28 17:04:32'),
(34, 5, 44, 'ciao', '2024-06-28 17:04:33'),
(35, 5, 44, 'boh', '2024-06-28 17:07:32'),
(36, 5, 44, 'ciao', '2024-06-28 17:07:36'),
(37, 5, 44, 'oki', '2024-06-28 17:07:41'),
(38, 5, 44, 'ho fame', '2024-06-28 17:12:25'),
(39, 5, 44, 'one piece', '2024-06-28 17:13:26'),
(42, 1, 44, 'sonno', '2024-06-28 18:59:20'),
(43, 1, 44, 'cosa faccio', '2024-06-28 18:59:32'),
(44, 1, 44, 'bu', '2024-06-28 19:01:27'),
(47, 7, 43, 'Viva le tasse', '2024-07-05 16:54:38'),
(48, 7, 58, 'Sei bellissimo ', '2024-07-05 17:06:01'),
(49, 7, 59, '<3 <3 <3 <3', '2024-07-05 17:06:22');

--
-- Triggers `Commento`
--
DELIMITER $$
CREATE TRIGGER `IncrementaCommenti` AFTER INSERT ON `Commento` FOR EACH ROW BEGIN
	UPDATE Post
	SET N_Commenti = N_Commenti+1
	WHERE IdPost = NEW.IdPost;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `RimozioneCommenti` AFTER DELETE ON `Commento` FOR EACH ROW BEGIN
	UPDATE Post
	SET N_Commenti = N_Commenti - 1
	WHERE IdPost = OLD.IdPost AND N_Commenti > 0;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Feedback`
--

CREATE TABLE `Feedback` (
  `IdUtente` int(10) UNSIGNED NOT NULL,
  `IdPost` int(10) UNSIGNED NOT NULL,
  `Tipo` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Feedback`
--

INSERT INTO `Feedback` (`IdUtente`, `IdPost`, `Tipo`) VALUES
(1, 43, 0),
(1, 44, 0),
(2, 43, 0),
(3, 44, 0),
(4, 45, 0),
(5, 43, 0),
(5, 44, 0),
(6, 43, 0),
(7, 43, 1),
(7, 44, 0),
(7, 45, 0),
(7, 58, 0),
(7, 59, 0);

--
-- Triggers `Feedback`
--
DELIMITER $$
CREATE TRIGGER `IncrementaLike` AFTER UPDATE ON `Feedback` FOR EACH ROW BEGIN 
	IF NEW.Tipo = 1 THEN
		UPDATE Post
		SET N_Like = N_Like+1
		WHERE IdPost = NEW.IdPost;
	END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `IncrementaView` AFTER INSERT ON `Feedback` FOR EACH ROW BEGIN
	UPDATE Post
	SET N_View = N_View+1
	WHERE IdPost = NEW.IdPost;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `RimozioneLike` AFTER UPDATE ON `Feedback` FOR EACH ROW BEGIN
	IF OLD.Tipo = 1 THEN
		UPDATE Post 
		SET N_Like = N_Like - 1
		WHERE IdPost = OLD.IdPost AND N_Like > 0;
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `FollowBlog`
--

CREATE TABLE `FollowBlog` (
  `IdUtente` int(10) UNSIGNED NOT NULL,
  `IdBlog` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `FollowBlog`
--

INSERT INTO `FollowBlog` (`IdUtente`, `IdBlog`) VALUES
(1, 23),
(1, 24),
(4, 24),
(5, 24),
(6, 23),
(6, 24),
(7, 23);

--
-- Triggers `FollowBlog`
--
DELIMITER $$
CREATE TRIGGER `IncrementaFollowBlog` AFTER INSERT ON `FollowBlog` FOR EACH ROW BEGIN
	UPDATE Blog
	SET N_Follow = N_Follow+1
	WHERE IdBlog = NEW.IdBlog;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `RimozioneFollowBlog` AFTER DELETE ON `FollowBlog` FOR EACH ROW BEGIN 
	UPDATE Blog 
	SET N_Follow = N_Follow - 1 
	WHERE IdBlog = OLD.IdBlog AND N_Follow > 0; 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `FollowUtente`
--

CREATE TABLE `FollowUtente` (
  `IdUtenteSeguace` int(10) UNSIGNED NOT NULL,
  `IdUtenteSeguito` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `FollowUtente`
--

INSERT INTO `FollowUtente` (`IdUtenteSeguace`, `IdUtenteSeguito`) VALUES
(1, 2),
(1, 3),
(1, 5),
(2, 1),
(3, 1),
(4, 3),
(5, 1),
(6, 1),
(6, 2),
(6, 3),
(6, 4),
(6, 5),
(7, 1),
(7, 2),
(7, 3),
(7, 5),
(7, 6);

--
-- Triggers `FollowUtente`
--
DELIMITER $$
CREATE TRIGGER `IncrementaSeguaci` AFTER INSERT ON `FollowUtente` FOR EACH ROW BEGIN
	UPDATE Utente
	SET N_Seguaci = N_Seguaci+1
	WHERE IdUtente = NEW.IdUtenteSeguito;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `IncrementaSeguiti` AFTER INSERT ON `FollowUtente` FOR EACH ROW BEGIN
	UPDATE Utente
	SET N_Seguiti = N_Seguiti+1
	WHERE IdUtente = NEW.IdUtenteSeguace;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `RimozioneSeguaci` AFTER DELETE ON `FollowUtente` FOR EACH ROW BEGIN 
	UPDATE Utente 
	SET N_Seguaci = N_Seguaci - 1 
	WHERE IdUtente = OLD.IdUtenteSeguito AND N_Seguaci > 0; 
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `RimozioneSeguiti` AFTER DELETE ON `FollowUtente` FOR EACH ROW BEGIN 
	UPDATE Utente 
	SET N_Seguiti = N_Seguiti - 1 
	WHERE IdUtente = OLD.IdUtenteSeguace AND N_Seguiti > 0; 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Immagine`
--

CREATE TABLE `Immagine` (
  `IdImmagine` int(10) UNSIGNED NOT NULL,
  `IdPost` int(10) UNSIGNED DEFAULT NULL,
  `Immagine` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Immagine`
--

INSERT INTO `Immagine` (`IdImmagine`, `IdPost`, `Immagine`) VALUES
(10, 43, 'immagini/NIGIRI-DI-SALMONE-ricetta.jpeg'),
(11, 43, 'immagini/NIGIRI-DI-SALMONE-ricetta.jpeg'),
(12, 44, 'immagini/wallpaperflare.com_wallpaper.jpg'),
(13, 45, 'immagini/1_xDIevNE7HEMiJQVTYg0qDQ.png'),
(71, 58, 'immagini/Immagine WhatsApp 2024-07-05 ore 17.02.35_b427e3a6.jpg'),
(72, 59, 'immagini/Immagine WhatsApp 2024-07-05 ore 17.02.57_4058bb14.jpg');

--
-- Triggers `Immagine`
--
DELIMITER $$
CREATE TRIGGER `NumeroImmagini` BEFORE INSERT ON `Immagine` FOR EACH ROW BEGIN 
	DECLARE image_count INT;
	
	SELECT COUNT(*) INTO image_count 
	FROM Immagine 
	WHERE IdPost = NEW.IdPost; 
	
	IF image_count >= 5 THEN 
		SIGNAL SQLSTATE '45000' 
		SET MESSAGE_TEXT = 'Limite di 5 immagini per post raggiunto'; 
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Pagamento`
--

CREATE TABLE `Pagamento` (
  `N_Carta` varchar(35) NOT NULL,
  `IdUtente` int(10) UNSIGNED DEFAULT NULL,
  `Intestatario` varchar(70) NOT NULL,
  `DataPagamento` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Pagamento`
--

INSERT INTO `Pagamento` (`N_Carta`, `IdUtente`, `Intestatario`, `DataPagamento`) VALUES
(' 4032034022632049', 2, 'mamma', '2024-06-25'),
('0000000000000000', 7, 'Francesca Berti', '2024-07-05'),
('5186950769785248', 6, 'Babbo Nachele', '2024-06-27'),
('5333171092267398', 1, 'Andrea Belliani', '2024-06-28'),
('6453474266713147345', 4, 'Andrea Belliani', '2024-06-26');

-- --------------------------------------------------------

--
-- Table structure for table `Post`
--

CREATE TABLE `Post` (
  `IdPost` int(10) UNSIGNED NOT NULL,
  `IdUtente` int(10) UNSIGNED DEFAULT NULL,
  `IdBlog` int(10) UNSIGNED DEFAULT NULL,
  `TitoloPost` varchar(30) NOT NULL,
  `Testo` varchar(255) DEFAULT NULL,
  `Data` datetime NOT NULL,
  `N_Like` int(10) UNSIGNED DEFAULT 0,
  `N_Commenti` int(10) UNSIGNED DEFAULT 0,
  `N_View` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Post`
--

INSERT INTO `Post` (`IdPost`, `IdUtente`, `IdBlog`, `TitoloPost`, `Testo`, `Data`, `N_Like`, `N_Commenti`, `N_View`) VALUES
(43, 2, 23, 'hello world', 'abbasso le tasse', '2024-06-25 17:51:38', 1, 1, 5),
(44, 3, 24, 'One Piece', 'One Piece è un manga creato dal mangaka Eiichiro Oda', '2024-06-25 18:38:42', 0, 25, 5),
(45, 4, 25, 'djaisi', 'hffhhg', '2024-06-26 10:13:02', 0, 1, 2),
(58, 7, 34, 'Gino', 'Lui è Gino :)', '2024-07-05 17:05:04', 0, 1, 1),
(59, 7, 34, 'Gina', 'Lei è Gina :)', '2024-07-05 17:05:23', 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Premium`
--

CREATE TABLE `Premium` (
  `IdUtente` int(10) UNSIGNED NOT NULL,
  `Tipo` varchar(10) DEFAULT NULL,
  `ScadenzaAbbonamento` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Premium`
--

INSERT INTO `Premium` (`IdUtente`, `Tipo`, `ScadenzaAbbonamento`) VALUES
(1, 'mensile', '2024-07-28'),
(2, 'mensile', '2024-07-25'),
(4, 'mensile', '2024-07-26'),
(6, 'annuale', '2025-06-27'),
(7, 'mensile', '2024-08-05');

-- --------------------------------------------------------

--
-- Table structure for table `Sottocategoria`
--

CREATE TABLE `Sottocategoria` (
  `IdCategoria` int(10) UNSIGNED NOT NULL,
  `IdSottocategoria` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Sottocategoria`
--

INSERT INTO `Sottocategoria` (`IdCategoria`, `IdSottocategoria`) VALUES
(8, 9),
(8, 27),
(8, 41),
(8, 53),
(8, 60),
(8, 67),
(8, 78),
(9, 10),
(9, 15),
(9, 19),
(9, 23),
(10, 11),
(10, 12),
(10, 13),
(10, 14),
(15, 16),
(15, 17),
(15, 18),
(19, 20),
(19, 21),
(19, 22),
(23, 24),
(23, 25),
(23, 26),
(27, 28),
(27, 32),
(27, 36),
(27, 40),
(28, 29),
(28, 30),
(28, 31),
(32, 33),
(32, 34),
(32, 35),
(36, 37),
(36, 38),
(36, 39),
(41, 42),
(41, 46),
(41, 50),
(42, 43),
(42, 44),
(42, 45),
(46, 47),
(46, 48),
(46, 49),
(50, 51),
(50, 52),
(53, 54),
(53, 57),
(54, 55),
(54, 56),
(57, 58),
(57, 59),
(60, 61),
(60, 63),
(61, 62),
(63, 64),
(63, 65),
(63, 66),
(67, 68),
(67, 72),
(67, 75),
(68, 69),
(68, 70),
(68, 71),
(72, 73),
(72, 74),
(75, 76),
(75, 77),
(78, 79),
(78, 82),
(79, 80),
(79, 81),
(82, 83),
(84, 92),
(84, 93),
(84, 94),
(84, 95),
(84, 96),
(85, 98),
(85, 102),
(85, 104),
(85, 105),
(85, 106),
(86, 107),
(86, 109),
(86, 112),
(87, 113),
(87, 114),
(87, 115),
(88, 116),
(88, 117),
(88, 118),
(89, 119),
(90, 123),
(90, 125),
(90, 126),
(91, 127),
(91, 128),
(91, 129),
(96, 97),
(98, 99),
(98, 100),
(98, 101),
(102, 103),
(107, 108),
(109, 110),
(109, 111),
(119, 120),
(119, 121),
(119, 122),
(123, 124),
(130, 131),
(130, 135),
(130, 138),
(130, 141),
(131, 132),
(131, 133),
(131, 134),
(135, 136),
(135, 137),
(138, 139),
(138, 140),
(141, 142),
(141, 143),
(141, 144),
(145, 146),
(145, 150),
(145, 153),
(146, 147),
(146, 148),
(146, 149),
(150, 151),
(150, 152),
(153, 154),
(153, 155),
(156, 157),
(156, 162),
(156, 166),
(156, 170),
(156, 174),
(156, 177),
(156, 181),
(157, 158),
(157, 159),
(157, 160),
(157, 161),
(162, 163),
(162, 164),
(162, 165),
(166, 167),
(166, 168),
(166, 169),
(170, 171),
(170, 172),
(170, 173),
(174, 175),
(174, 176),
(177, 178),
(177, 179),
(177, 180),
(181, 182),
(181, 183),
(181, 184);

-- --------------------------------------------------------

--
-- Table structure for table `Utente`
--

CREATE TABLE `Utente` (
  `IdUtente` int(10) UNSIGNED NOT NULL,
  `Username` varchar(35) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(64) NOT NULL,
  `N_Seguaci` int(10) UNSIGNED DEFAULT 0,
  `N_Seguiti` int(10) UNSIGNED DEFAULT 0,
  `FotoProfilo` varchar(255) DEFAULT 'immagini/profile.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Utente`
--

INSERT INTO `Utente` (`IdUtente`, `Username`, `Email`, `Password`, `N_Seguaci`, `N_Seguiti`, `FotoProfilo`) VALUES
(1, 'a.belliani', 'andrea00@yahoo.it', '034079ab4fb87e59e14dc9b1de4d24ce439a129efba9105505fc084fe9f75a5d', 5, 3, 'immagini/jim-the-office.jpg'),
(2, 'Presidente', 'sergio.mattarella_original@italia.gov', 'fc5c25120b96c9d17a61ce08e843fc3f7de84fe093bcce7851420599cc027e9e', 3, 1, 'immagini/images.jpeg'),
(3, 'daniele02', 'danielone02@gmail.com', '6d63ca8e5bd1a65a64b96d39619630a658e4a598b7881e57092ad4ea6124965d', 4, 1, 'immagini/wallpaperflare.com_wallpaper.jpg'),
(4, 'andrea', 'andreabelliani00@gmail.com', '034079ab4fb87e59e14dc9b1de4d24ce439a129efba9105505fc084fe9f75a5d', 1, 1, 'immagini/Monkey_D._Rufy.png'),
(5, 'luca94', 'luca94@gmail.com', 'b0a3cac5460a4ccc2fa1b3019c329b072f33ea8903d16b2d539b2106057af39e', 3, 1, 'immagini/profile.png'),
(6, 'babboNachele', 'babbonatale@yahoo.it', '36d6103c94324fa710d1e2f4bb28e7d50fde4f86609bbc25d0030ef1a45f8455', 1, 5, 'immagini/profile.png'),
(7, 'franci', 'franci@gmail.com', '88b7f650081fe8a3a9bc86853f797a664b5885136102e50305d6f6d5bef9d7cd', 0, 5, 'immagini/profile.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Abbonamento`
--
ALTER TABLE `Abbonamento`
  ADD PRIMARY KEY (`Tipo`);

--
-- Indexes for table `Associa`
--
ALTER TABLE `Associa`
  ADD PRIMARY KEY (`IdBlog`,`IdCategoria`),
  ADD KEY `IdCategoria` (`IdCategoria`);

--
-- Indexes for table `Blog`
--
ALTER TABLE `Blog`
  ADD PRIMARY KEY (`IdBlog`),
  ADD KEY `IdUtente` (`IdUtente`);

--
-- Indexes for table `Categoria`
--
ALTER TABLE `Categoria`
  ADD PRIMARY KEY (`IdCategoria`);

--
-- Indexes for table `Coautore`
--
ALTER TABLE `Coautore`
  ADD PRIMARY KEY (`IdCoautore`,`IdBlog`),
  ADD KEY `IdBlog` (`IdBlog`);

--
-- Indexes for table `Commento`
--
ALTER TABLE `Commento`
  ADD PRIMARY KEY (`IdCommento`),
  ADD KEY `IdUtente` (`IdUtente`),
  ADD KEY `IdPost` (`IdPost`);

--
-- Indexes for table `Feedback`
--
ALTER TABLE `Feedback`
  ADD PRIMARY KEY (`IdUtente`,`IdPost`) USING BTREE,
  ADD KEY `IdPost` (`IdPost`);

--
-- Indexes for table `FollowBlog`
--
ALTER TABLE `FollowBlog`
  ADD PRIMARY KEY (`IdUtente`,`IdBlog`),
  ADD KEY `IdBlog` (`IdBlog`);

--
-- Indexes for table `FollowUtente`
--
ALTER TABLE `FollowUtente`
  ADD PRIMARY KEY (`IdUtenteSeguace`,`IdUtenteSeguito`),
  ADD KEY `IdUtenteSeguito` (`IdUtenteSeguito`);

--
-- Indexes for table `Immagine`
--
ALTER TABLE `Immagine`
  ADD PRIMARY KEY (`IdImmagine`),
  ADD KEY `IdPost` (`IdPost`);

--
-- Indexes for table `Pagamento`
--
ALTER TABLE `Pagamento`
  ADD PRIMARY KEY (`N_Carta`),
  ADD KEY `IdUtente` (`IdUtente`);

--
-- Indexes for table `Post`
--
ALTER TABLE `Post`
  ADD PRIMARY KEY (`IdPost`),
  ADD KEY `IdUtente` (`IdUtente`),
  ADD KEY `IdBlog` (`IdBlog`);

--
-- Indexes for table `Premium`
--
ALTER TABLE `Premium`
  ADD PRIMARY KEY (`IdUtente`),
  ADD KEY `Tipo` (`Tipo`);

--
-- Indexes for table `Sottocategoria`
--
ALTER TABLE `Sottocategoria`
  ADD PRIMARY KEY (`IdCategoria`,`IdSottocategoria`),
  ADD KEY `IdSottocategoria` (`IdSottocategoria`);

--
-- Indexes for table `Utente`
--
ALTER TABLE `Utente`
  ADD PRIMARY KEY (`IdUtente`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Blog`
--
ALTER TABLE `Blog`
  MODIFY `IdBlog` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `Categoria`
--
ALTER TABLE `Categoria`
  MODIFY `IdCategoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `Commento`
--
ALTER TABLE `Commento`
  MODIFY `IdCommento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `Immagine`
--
ALTER TABLE `Immagine`
  MODIFY `IdImmagine` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `Post`
--
ALTER TABLE `Post`
  MODIFY `IdPost` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `Utente`
--
ALTER TABLE `Utente`
  MODIFY `IdUtente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Associa`
--
ALTER TABLE `Associa`
  ADD CONSTRAINT `associa_ibfk_1` FOREIGN KEY (`IdBlog`) REFERENCES `Blog` (`IdBlog`) ON DELETE CASCADE,
  ADD CONSTRAINT `associa_ibfk_2` FOREIGN KEY (`IdCategoria`) REFERENCES `Categoria` (`IdCategoria`);

--
-- Constraints for table `Blog`
--
ALTER TABLE `Blog`
  ADD CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`IdUtente`) REFERENCES `Utente` (`IdUtente`) ON DELETE CASCADE;

--
-- Constraints for table `Coautore`
--
ALTER TABLE `Coautore`
  ADD CONSTRAINT `coautore_ibfk_1` FOREIGN KEY (`IdCoautore`) REFERENCES `Utente` (`IdUtente`) ON DELETE CASCADE,
  ADD CONSTRAINT `coautore_ibfk_2` FOREIGN KEY (`IdBlog`) REFERENCES `Blog` (`IdBlog`) ON DELETE CASCADE;

--
-- Constraints for table `Commento`
--
ALTER TABLE `Commento`
  ADD CONSTRAINT `commento_ibfk_1` FOREIGN KEY (`IdUtente`) REFERENCES `Utente` (`IdUtente`) ON DELETE CASCADE,
  ADD CONSTRAINT `commento_ibfk_2` FOREIGN KEY (`IdPost`) REFERENCES `Post` (`IdPost`) ON DELETE CASCADE;

--
-- Constraints for table `Feedback`
--
ALTER TABLE `Feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`IdUtente`) REFERENCES `Utente` (`IdUtente`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`IdPost`) REFERENCES `Post` (`IdPost`) ON DELETE CASCADE;

--
-- Constraints for table `FollowBlog`
--
ALTER TABLE `FollowBlog`
  ADD CONSTRAINT `followblog_ibfk_1` FOREIGN KEY (`IdUtente`) REFERENCES `Utente` (`IdUtente`) ON DELETE CASCADE,
  ADD CONSTRAINT `followblog_ibfk_2` FOREIGN KEY (`IdBlog`) REFERENCES `Blog` (`IdBlog`) ON DELETE CASCADE;

--
-- Constraints for table `FollowUtente`
--
ALTER TABLE `FollowUtente`
  ADD CONSTRAINT `followutente_ibfk_1` FOREIGN KEY (`IdUtenteSeguace`) REFERENCES `Utente` (`IdUtente`) ON DELETE CASCADE,
  ADD CONSTRAINT `followutente_ibfk_2` FOREIGN KEY (`IdUtenteSeguito`) REFERENCES `Utente` (`IdUtente`) ON DELETE CASCADE;

--
-- Constraints for table `Immagine`
--
ALTER TABLE `Immagine`
  ADD CONSTRAINT `immagine_ibfk_1` FOREIGN KEY (`IdPost`) REFERENCES `Post` (`IdPost`) ON DELETE CASCADE;

--
-- Constraints for table `Pagamento`
--
ALTER TABLE `Pagamento`
  ADD CONSTRAINT `pagamento_ibfk_1` FOREIGN KEY (`IdUtente`) REFERENCES `Utente` (`IdUtente`) ON DELETE CASCADE;

--
-- Constraints for table `Post`
--
ALTER TABLE `Post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`IdUtente`) REFERENCES `Utente` (`IdUtente`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`IdBlog`) REFERENCES `Blog` (`IdBlog`) ON DELETE CASCADE;

--
-- Constraints for table `Premium`
--
ALTER TABLE `Premium`
  ADD CONSTRAINT `premium_ibfk_1` FOREIGN KEY (`IdUtente`) REFERENCES `Utente` (`IdUtente`) ON DELETE CASCADE,
  ADD CONSTRAINT `premium_ibfk_2` FOREIGN KEY (`Tipo`) REFERENCES `Abbonamento` (`Tipo`);

--
-- Constraints for table `Sottocategoria`
--
ALTER TABLE `Sottocategoria`
  ADD CONSTRAINT `sottocategoria_ibfk_1` FOREIGN KEY (`IdCategoria`) REFERENCES `Categoria` (`IdCategoria`),
  ADD CONSTRAINT `sottocategoria_ibfk_2` FOREIGN KEY (`IdSottocategoria`) REFERENCES `Categoria` (`IdCategoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
