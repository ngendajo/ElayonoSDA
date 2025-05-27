-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 07:38 AM
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
-- Database: `elayono`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `pdf` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `approved_user1_id` int(11) DEFAULT NULL,
  `approved_user2_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `image`, `pdf`, `created_by`, `approved_user1_id`, `approved_user2_id`, `created_at`, `updated_at`) VALUES
(4, 'Abakurambere n\' abahanuzi', 'uploads/images/1746976881_abakuramberenamahanuzi.JPG', 'uploads/pdfs/1746970727_AbakuramberenAbahanuzi.pdf', 10, 10, 11, '2025-05-11 13:38:47', '2025-05-16 14:25:56'),
(5, 'Abahanuzi n\' abami', 'uploads/images/1746976873_abahanuzinabami.JPG', 'uploads/pdfs/1746970800_1689665279_ABAHANUZI NABAMI (IFITE NAMAFOTO[UPDATED]).pdf', 10, 10, 11, '2025-05-11 13:40:00', '2025-05-16 14:25:52');

-- --------------------------------------------------------

--
-- Table structure for table `daily_content`
--

CREATE TABLE `daily_content` (
  `id` int(11) NOT NULL,
  `daily_verse` varchar(255) NOT NULL,
  `daily_verse_details` text NOT NULL,
  `daily_chapter` varchar(100) NOT NULL,
  `daily_ssl_title` varchar(255) NOT NULL,
  `chapter_pdf` varchar(255) NOT NULL,
  `ssl_pdf` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_content`
--

INSERT INTO `daily_content` (`id`, `daily_verse`, `daily_verse_details`, `daily_chapter`, `daily_ssl_title`, `chapter_pdf`, `ssl_pdf`, `date`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'Uwiteka agotesha amahema yanyu ubwiza bwe.', 'Abisirayeli bajye babamba amahema yabo, umuntu wese ahererane n\'ibendera ry\'ababo, kandi babe munsi y\'utubendera tw\'amazu ya ba sekuru, berekeze amahema yabo ihema ry\'ibonaniro bayarigoteshe.\r\nKubara 2:2', 'Itangiriro 28', 'IMANA MU BWOKO BWAYO', 'uploads/pdf/1747248157_chapter_itangiriro 28.pdf', 'uploads/pdf/1747248157_ssl_KU WA KANE.pdf', '2025-05-14', 10, '2025-05-14 18:42:37', '2025-05-14 19:09:43'),
(4, 'Ubwiza bwe ni bwo bwamuteye ubwibone.', 'Ezekiyeli  28:12\r\n“Mwana w\'umuntu, curira umwami w\'i Tiro umuborogo umubwire uti ‘Umva uko Umwami Uwiteka avuga ngo wari intungane rwose, wuzuye ubwenge n\'ubwiza buhebuje.', 'Itangiriro 29', 'KUGWA KWA LUSIFERI', 'uploads/pdf/1747320866_chapter_itangiriro 29.pdf', 'uploads/pdf/1747320866_ssl_KU WA GATANU.pdf', '2025-05-15', 10, '2025-05-15 14:54:26', '2025-05-15 14:54:26'),
(5, 'Umwana w\'intama Ni We Ukwiriye icyubahiro cyose.', 'Ibyahishuwe 4:11\r\n“Mwami wacu, Mana yacu, ukwiriye guhabwa icyubahiro no guhimbazwa n\'ubutware koko, kuko ari wowe waremye byose. Igituma biriho kandi icyatumye biremwa ni uko wabishatse.”', 'Itangiriro 30', 'IBINDI BYO KWIGWA NO KUZIRIKANWA:', 'uploads/pdf/1747406290_chapter_itangiriro 30.pdf', 'uploads/pdf/1747406290_ssl_KU WA GATANDATU.pdf', '2025-05-26', 11, '2025-05-16 14:38:10', '2025-05-26 18:15:10'),
(6, 'Mukore ibitunganye mugume mu ihema Rye.', 'Zaburi 15:1,2\r\nUwiteka, ni nde uzaguma mu ihema ryawe? Ni nde uzatura ku musozi wawe wera? Ni ugendera mu bitunganye agakora ibyo gukiranuka, Akavuga iby\'ukuri nk\'uko biri mu mutima we.', 'Itangiriro 34', 'AMATEGEKO MU MITIMA YACU', 'uploads/pdf/1747763944_chapter_itangiriro 34.pdf', 'uploads/pdf/1747763944_ssl_KU WA GATATU.pdf', '2025-05-20', 10, '2025-05-20 17:59:04', '2025-05-20 17:59:04'),
(7, 'Uwiteka Nyiringabo ari kumwe natwe.', 'Zaburi 46:8\r\nUwiteka Nyiringabo ari kumwe natwe, Imana ya Yakobo ni igihome kirekire kidukingira. Sela.', 'Itangiriro 39', 'Umufasha utabura kuboneka mubyago no mumakuba', 'uploads/pdf/1748150170_chapter_itangiriro 39.pdf', 'uploads/pdf/1748150170_ssl_Doc1.pdf', '2025-05-25', 10, '2025-05-25 05:16:10', '2025-05-25 05:16:10');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `department_leader_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`, `department_leader_id`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Ishuri ryo kw\' Isabato', 16, 'Icyiciro cy\' ishuri ryo ku Isabato ni umutima w\' itorero. Rifasha abizera kwiga Bibiliya no gusabana. Ibi bigizwe no kwiga Bibiliya, gusenga no gufashanya.', '2025-05-08 18:15:59', '2025-05-08 18:15:59'),
(2, 'Itumanaho', 11, 'Icyiciro gishinzwe kugeza ku bizera amakuru yose arebana n\' itorero', '2025-05-08 18:17:41', '2025-05-08 19:16:05'),
(5, 'Abadiyakoni', 22, 'Icyiciro gishinzwe gutunganya gahunda, gutegura urusengero ndetse n\' imihango y\' itorero, gukomeza abizera, n\' imihango y\' itorero muri rusange', '2025-05-16 05:49:02', '2025-05-16 05:49:02'),
(6, 'Ivugabutumwa', 23, 'Amavuna, gusura urugo kururndi, gutanga ibitabo, genzura', '2025-05-16 05:50:05', '2025-05-16 05:50:05');

-- --------------------------------------------------------

--
-- Table structure for table `letters`
--

CREATE TABLE `letters` (
  `id` int(11) NOT NULL,
  `reference_number` varchar(50) NOT NULL,
  `letter_type` varchar(50) NOT NULL,
  `member_id` int(11) NOT NULL,
  `from_church` varchar(100) NOT NULL,
  `from_region` varchar(100) NOT NULL,
  `from_field` varchar(100) NOT NULL,
  `to_church` varchar(100) DEFAULT NULL,
  `to_region` varchar(100) DEFAULT NULL,
  `to_field` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `additional_info` text DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `groom_name` varchar(100) DEFAULT NULL,
  `groom_church` varchar(100) DEFAULT NULL,
  `bride_name` varchar(100) DEFAULT NULL,
  `bride_church` varchar(100) DEFAULT NULL,
  `wedding_date` date DEFAULT NULL,
  `wedding_location` varchar(100) DEFAULT NULL,
  `created_at` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `letters`
--

INSERT INTO `letters` (`id`, `reference_number`, `letter_type`, `member_id`, `from_church`, `from_region`, `from_field`, `to_church`, `to_region`, `to_field`, `start_date`, `end_date`, `additional_info`, `role`, `groom_name`, `groom_church`, `bride_name`, `bride_church`, `wedding_date`, `wedding_location`, `created_at`, `created_by`, `updated_at`) VALUES
(1, '20250513160521968', 'sabbath_transfer', 12, 'Elayono', 'Mujyejuru', 'CRF', 'Ruhango', 'Ruhango', 'CRF', '2025-05-17', '2025-05-24', 'Kuhasengera', '', '', '', '', '', '0000-00-00', '', '2025-05-13', 10, NULL),
(2, '20250513161940717', 'sabbath_attendance', 19, 'Elayono', 'Mujyejuru', 'CRF', 'Muhororo', 'Gikoma', 'CRF', '2025-05-17', '2025-05-24', 'Kuhasengera', '', '', '', '', '', '0000-00-00', '', '2025-05-13', 10, NULL),
(4, '20250513163510604', 'wedding_permission', 17, 'Elayono', 'Mujyejuru', 'CRF', 'Ntenyo', 'Byimana', 'CRF', '0000-00-00', '0000-00-00', '', 'Umushyingira', 'Amahoro Sandra', 'Munini', 'Kanake Stiven', 'Muhororo', '2025-05-26', 'Muhororo', '2025-05-13', 10, NULL),
(5, '20250513165413204', 'wedding_permission', 12, 'Elayono', 'Mujyejuru', 'CRF', 'Muhororo', 'Gikoma', 'CRF', '0000-00-00', '0000-00-00', '', 'gukimbagira', 'Amahoro Sandra', 'Munini', 'Kanake Stiven', 'Muhororo', '2025-05-20', 'Muhororo', '2025-05-13', 10, NULL),
(6, '20250514103602174', 'wedding_permission', 17, 'Elayono', 'Mujyejuru', 'CRF', 'Muhororo', 'Gikoma', 'CRF', '0000-00-00', '0000-00-00', '', 'Umushyingira', 'Amahoro Sandra', 'Munini', 'Kanake Stiven', 'Muhororo', '2025-05-21', 'Muhororo', '2025-05-14', 10, NULL),
(7, '20250515193441226', 'wedding_permission', 20, 'Elayono', 'Mujyejuru', 'CRF', 'Mabera', 'Mujyejuru', 'CRF', '0000-00-00', '0000-00-00', '', 'gukimbagira', 'Amahoro Sandra', 'Munini', 'Kanake Stiven', 'Muhororo', '2025-05-21', 'Mabera', '2025-05-15', 10, NULL),
(8, '20250525074156479', 'sabbath_transfer', 10, 'Elayono', 'Mujyejuru', 'CRF', 'Ruhango', 'Ruhango', 'CRF', '2025-05-31', '2025-05-31', 'Niho azateranira', '', '', '', '', '', '0000-00-00', '', '2025-05-25', 10, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `message`, `created_at`) VALUES
(1, 'Dukunda Itorero rya Elayono', '2025-05-08 15:25:57'),
(2, 'Bibaye byiza amatsinda yanozwa pe!', '2025-05-08 15:29:34'),
(3, 'Mbifurije imyiteguro myiza y&#039; Isabato. Uwiteka abahire.', '2025-05-09 04:39:40'),
(4, 'Mbifurije Isabato nziza. Bibaye byiza mwatwigisha uko bakoresha CFMS.', '2025-05-09 11:59:04'),
(5, 'mwiriwe neza bishobotse amatangazo akajya aca kuru projecteur byaba ari byiza', '2025-05-09 15:16:04'),
(7, 'Mwaramutse neza nifuza ga ko naba Umwizera wo kuri Elayono. Ese bica muzihe nzira?', '2025-05-16 04:39:36'),
(8, 'Mwaramutse neza nifuza ga ko naba Umwizera wo kuri Elayono. Ese bica muzihe nzira?', '2025-05-16 04:55:17'),
(9, 'Mwaramutse neza nifuza ga ko naba Umwizera wo kuri Elayono. Ese bica muzihe nzira?', '2025-05-16 04:56:25'),
(10, 'Turashima Imana', '2025-05-16 12:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `time` time DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_by_id` int(11) NOT NULL,
  `approved_user1_id` int(11) DEFAULT NULL,
  `approved_user2_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `description`, `start_date`, `end_date`, `time`, `photo`, `created_by_id`, `approved_user1_id`, `approved_user2_id`, `created_at`, `updated_at`) VALUES
(1, 'Amavuna', 'Ivugabutumwa ryihuta', '2025-03-08', '2025-03-22', '17:00:00', 'uploads/681d9fb07ad9a.jpg', 10, 10, 11, '2025-05-09 06:24:48', '2025-05-15 15:38:37'),
(2, 'Igitaramo nterankunga ', 'Itorero rya Elayono', '2025-03-01', '2025-03-01', '14:00:00', 'uploads/681da52cc31b6.png', 10, 10, 11, '2025-05-09 06:48:12', '2025-05-15 15:38:31'),
(4, 'Isabato yo Kwiyiriza Ubusa no Gusenga n\' Ifunguro Ryera', 'Isabato ngaruka gihembwe yahariwe kwiyiriza ubusa no gusenga', '2025-07-05', '2025-07-05', '08:00:00', NULL, 10, 10, 11, '2025-05-15 15:21:11', '2025-05-15 15:42:36'),
(5, 'Abifuza kwakiriza abana biyandikishe ku muyobozi w\' abana', 'Umuhango wo kwakira abana urateguwe abifuza kwakiriza abana biyandikishe ku muyobozi w\\\' abana', '2025-05-31', '2025-05-31', '10:00:00', NULL, 10, 10, 11, '2025-05-15 15:23:31', '2025-05-15 15:38:18'),
(6, 'Buriwese yitegure kuzatsinda ibirundo. Biri mu kwezi gutaha kwa 6', 'Mwitegure gutsinda ibirundo', '2025-06-01', '2025-06-30', '08:00:00', NULL, 11, 11, 10, '2025-05-15 17:28:05', '2025-05-15 17:28:57');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `uploaded_user_id` int(11) NOT NULL,
  `approved_user1_id` int(11) DEFAULT NULL,
  `approved_user2_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `title`, `description`, `image`, `uploaded_user_id`, `approved_user1_id`, `approved_user2_id`, `created_at`, `updated_at`) VALUES
(2, 'Inzu y\' Uwiteka', 'Muzirikane yuko Imibiri yanyu ni insengero z\' Umwuka w\' Imana', 'uploads/sliders/1746715811_MIR_8394.jpg', 10, 10, 11, '2025-05-08 14:50:11', '2025-05-20 18:07:21'),
(3, 'Urakaza Neza Kuri Elayono', 'Ngwino dufatanye guhimabaza Umuremyi wacu.', 'uploads/sliders/1746731982_abizera1.jpg', 10, 10, 11, '2025-05-08 19:19:42', '2025-05-14 14:20:51'),
(5, 'Abana b\' Imana', 'Nibyiza kwibera munzu y\' Uwiteka', 'uploads/sliders/1746894212_elayonchurch.PNG', 10, 10, 11, '2025-05-10 16:23:32', '2025-05-14 14:20:46'),
(6, 'Intego', 'Guhindura abantu abigishwa ba Yesu Kristo babaho nkabatangabuhamya be b\'urukundo', 'uploads/sliders/1747231512_abizera2.jpg', 10, 10, 11, '2025-05-14 14:05:12', '2025-05-14 14:20:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `names` varchar(255) DEFAULT NULL,
  `igihande` varchar(70) NOT NULL DEFAULT 'Elayono',
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','comminicator','secretary','staff') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `leader` varchar(100) DEFAULT NULL,
  `is_elder` enum('yes','no') DEFAULT NULL,
  `status` enum('yarabatijwe','yarakiriwe','kubwokwizera','mumugayo','yarahejwe','yarazimiye','PCM') DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_image` varchar(255) DEFAULT 'uploads/default.jpeg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `names`, `igihande`, `username`, `password`, `user_type`, `email`, `phone`, `year`, `leader`, `is_elder`, `status`, `details`, `date`, `description`, `created_at`, `updated_at`, `profile_image`) VALUES
(7, 'ITANGISHAKA Maurice', 'Elayono', 'mauriceIT', '$2y$10$9wtSOEt8YAlFBz0qLxxthuN4q0QEOppXkItN1bYOKLP0lkL9J1u7O', 'secretary', 'mauricesda@gmail.com', '0783588800', 2025, 'Umwanditsi w\' itorero', '', NULL, NULL, NULL, 'Niyitanga Morice ni umugabo w’intangarugero mu rugendo rw’ukwemera no mu kuyobora itorero. Amaze imyaka minshi akora umurimo w’Ubukristo mu', '2025-05-04 05:54:52', '2025-05-07 05:52:42', 'uploads/profile_681aea285584c.jpg'),
(10, 'Ngendahimana Joseph', 'Elayono', 'ngendajo', '$2y$10$3CVtgBKbfBZ1hur9daoeuu1Y9CHv2emifgKE9Cnx4bkrIX0TgWAWa', 'admin', 'ngendajo@gmail.com', '0784921483', 2025, 'Umukuru w\' Itorero', 'yes', NULL, NULL, NULL, NULL, '2025-05-06 19:34:42', '2025-05-06 19:37:55', 'uploads/profile_681a6451e53f7.jpeg'),
(11, 'Ishimwe Olivier', 'Elayono', 'lishimwe', '$2y$10$q15Q5iEmy0sNqIugVxfP/eAgrz2pPShgr8V7vbPzOJix.ooLFdBlm', 'admin', 'ishimwolivier01@gmail.com', '0781265211', 2025, 'Icyiciro cy itumanaho', '', NULL, NULL, NULL, NULL, '2025-05-06 19:45:12', '2025-05-07 05:55:26', 'uploads/profile_681af5ceaf959.jpg'),
(12, 'Ishimwe Prince', 'Heburoni', 'pishimwe', '$2y$10$/Z2fOXkA2oKg5i34JM3t0OWtLcFkue94ufL4kAUBoqZrDf1r2ITzW', 'comminicator', 'pishimwe@gmail.com', '0794705005', 2025, 'MG', 'no', 'yarabatijwe', 'Elayono', '2025-05-07', 'Umwizera ushimwa cyane', '2025-05-07 04:46:28', '2025-05-08 06:51:22', 'uploads/profile_681ae5a467090.jpeg'),
(13, 'Elder Rubibi Louis Pasteur', 'Betaniya', 'prubibi', '$2y$10$QHQtAmKppgf0v2IdVm/FTeKSU8t0LxPThpuxIr5vNeU6WMlD2Gh9W', 'staff', 'rubibilouispasteur@yahoo.fr', 'rubibilouispasteur@y', 2025, 'Umukuru w\' itorero mukuru', 'yes', 'yarakiriwe', '', '2025-05-07', 'Rubibi Louis Pasteur ni umugabo w’intangarugero mu rugendo rw’ukwemera no mu kuyobora itorero. Amaze imyaka irenga icumi akora umurimo w’Ubukristo nk’umukuru w’’itorero (elder) mu Itorero Ry\' Abadivrntiste B\' Umunsi Wa Karindwi. Muri iyo myaka yose, yagaragaje ubwitange budasanzwe mu kuyobora no gufasha abakristo gukura mu kwizera no mu gucengera inyigisho za Bibiliya. Kuri ubu, Rubibi Louis Pasteur ni umuyobozi mukuru w’itorero rya Elayono, aho ashinzwe gufasha no guhuza imirimo yose y’itorero. Ubumenyi n’ubunararibonye yakuye mu myaka myinshi amaze akorera Imana byamufashije gutegura gahunda zihamye zo kwigisha no gukomeza abakristo mu kwizera. Afite ubushake bwo gukemura ibibazo by’itorero mu mahoro, kandi akunze gufatanya n’abandi mu kubaka umuryango w’abizera uhamye. Rubibi kandi azwiho kugira umutima w’urukundo n’ubugwaneza, byatumye yubaka umubano mwiza n’abakristo bose akorana nabo. Mu murimo we, akunze gushyira imbere inyigisho zigaragaza urukundo rw’Imana no kubaka itorero rikomeye ku byanditswe byera. Ubwitange bwe mu kuyobora Elayono byahinduye ubuzima bw’abatari bake, kandi akomeje kuba urugero rwiza ku bakristo ndetse no ku bayobozi bagenzi be.', '2025-05-07 13:01:34', '2025-05-15 18:41:13', 'uploads/profile_68263549123cc.jpg'),
(14, 'Elder Hakizimana Dieudonne', 'Moriya', 'dhakizimana', '$2y$10$WDYWAQ5adX3twp1Ouz7j6.mX3kDTcTPoOhCpjyq8fo9/xBFLfzgvG', 'staff', '1980dhakididi@gmail.com', '+250 788 555 708', 2025, 'Umukuru w\' itorero', 'yes', 'yarakiriwe', 'Nyarubande', '2025-05-08', 'Hakizimana Dieudonne ni umugabo w’intangarugero mu rugendo rw’ukwemera no mu kuyobora itorero. Amaze imyaka irenga icumi akora umurimo w’Ubukristo', '2025-05-08 06:55:04', '2025-05-15 18:40:42', 'uploads/profile_6826352ac30fa.jpg'),
(15, 'Elder Nsengumuremyi Justin', 'Ishuri rya Bibiliya', 'jngengumuremyi', '$2y$10$BGDbWiPGTZyFZZiUbyynjuyh//0eZMqDwsu44RCvY9k0odQXqdhKW', 'staff', 'nsengjustin3@gmail.com', '+250 783 038 947', 2025, 'Umukuru w\' itorero', 'yes', 'yarabatijwe', 'Elayono', '2025-05-08', 'Nsengumuremyi Justin ni umugabo w’umunyamurava, ufite umwihariko mu guharanira iterambere ry’umuryango n’ubwiyunge mu nzira yo gukorera Imana.', '2025-05-08 07:15:43', '2025-05-15 18:40:59', 'uploads/profile_6826353b6cf1e.jpg'),
(16, 'Ishimwe Joswe', 'Karumeri', 'ishimwejoswe', '$2y$10$E/swbFdIvLAFAgU0TfflHOg2k6Cn7QTK.j6EHf78DWjwty/LwngOW', 'staff', 'jishimwe@gmail.com', '250735000000', 2025, NULL, NULL, 'yarabatijwe', NULL, NULL, NULL, '2025-05-07 22:00:00', '2025-05-08 09:32:28', 'uploads/default.jpeg'),
(17, 'Uwase Alice', 'Karumeri', 'uwasealice', '$2y$10$aJSVv8abuEEHMAP/O7Y76OjRmCFf8LSZAdaqPmSQhwbvJDp1QRlYO', 'staff', 'alicewase2019@gmail.com', '250782532526', 2025, NULL, NULL, 'yarakiriwe', NULL, NULL, NULL, '2018-01-08 22:00:00', '2025-05-08 15:01:09', 'uploads/default.jpeg'),
(18, 'Umukundwa Liliane', 'Karumeri', 'umukundwaliliane', '$2y$10$aJSVv8abuEEHMAP/O7Y76OjRmCFf8LSZAdaqPmSQhwbvJDp1QRlYO', 'staff', 'lumukundwa@gmail.com', '250783992836', 2025, NULL, NULL, 'yarakiriwe', NULL, NULL, NULL, '2021-12-31 22:00:00', '2025-05-08 15:01:09', 'uploads/default.jpeg'),
(19, 'Tuyisenge Anaclet', 'Shiro', 'tuyisengeanaclet', '$2y$10$EU1x2lKRbZtDjB4lo.0H3.rJSBN4IhlhWyhKkLnJGXbZrVEXhQnDW', 'staff', 'tuyisengeanaclet@gmail.com', '250782532527', 2025, '', NULL, 'yarakiriwe', '', '2025-05-08', '', '2018-01-08 22:00:00', '2025-05-08 15:05:08', 'uploads/default.jpeg'),
(20, 'Niyonkuru Nelson', 'Betaniya', 'niyonkuru', '$2y$10$93OTdPTZoAo27cYIpCtOaOIjbrEXFxoP4KYgL9ArzeVRyDbbN3yJa', 'comminicator', 'niyonelson2000@gmail.com', '+250785924558', 2024, 'Umudiyakoni', 'no', 'yarabatijwe', 'Elayono', '2018-12-15', 'Umwizera ushimwa', '2025-05-15 17:33:21', '2025-05-15 18:42:30', 'uploads/default.jpeg'),
(21, 'Pr. Mugabe Jean Paul', 'Elayono', 'jmugabe', '$2y$10$rUv9HqkxHXgF6b3ZHyc62eKYvMULtNUXrz.6RcNP4iFcB5LyLMxny', 'admin', 'jmugabe@gmail.com', '+250 788 680 255', 2025, 'Umukuru w\' Intara', 'yes', 'yarakiriwe', 'Gitarama', '2024-04-15', 'Umugabura w\' ijambo ry\' imana mu itorero ry\' abadiventiste b\' umunsi wa karindwi', '2025-05-15 18:40:09', '2025-05-15 18:40:09', 'uploads/profile_68263509796c0.jpg'),
(22, 'Mutabaruka Sept', 'Heburoni', 'smutabaruka', '$2y$10$ZjEXyDmaAK3.SLmt8/KKleS6TjAO0WoweWtgr8HAhviLRKaw5M.Lu', 'staff', 'smutabaruka@gmail.com', '+250788384922', 2025, 'Umudiakoni Mukuru', 'no', 'yarakiriwe', '', '2018-09-02', 'Umwizera ushimwa', '2025-05-16 05:41:59', '2025-05-16 05:53:55', 'uploads/default.jpeg'),
(23, 'Sibomana Cyprien', 'Betaniya', 'csibomana', '$2y$10$13M5nT.R7qAk4JF8PGY0hOqIooOOxlxpFaPRDXUSLDrjHbwToNhwS', 'staff', 'csibomana@gmail.com', '+250783125173', 2024, 'Ivugabutumwa', 'no', 'yarakiriwe', '', '2019-10-16', 'Umwizera ushimwa', '2025-05-16 05:46:49', '2025-05-16 05:54:15', 'uploads/default.jpeg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_books_created_by` (`created_by`),
  ADD KEY `idx_books_approved_user1_id` (`approved_user1_id`),
  ADD KEY `idx_books_approved_user2_id` (`approved_user2_id`);

--
-- Indexes for table `daily_content`
--
ALTER TABLE `daily_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_department_leader` (`department_leader_id`);

--
-- Indexes for table `letters`
--
ALTER TABLE `letters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_number` (`reference_number`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_user2_id` (`approved_user2_id`),
  ADD KEY `idx_news_created_by` (`created_by_id`),
  ADD KEY `idx_news_dates` (`start_date`,`end_date`),
  ADD KEY `idx_news_approvers` (`approved_user1_id`,`approved_user2_id`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_user_id` (`uploaded_user_id`),
  ADD KEY `approved_user1_id` (`approved_user1_id`),
  ADD KEY `approved_user2_id` (`approved_user2_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_content`
--
ALTER TABLE `daily_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `letters`
--
ALTER TABLE `letters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`approved_user1_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `books_ibfk_3` FOREIGN KEY (`approved_user2_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `daily_content`
--
ALTER TABLE `daily_content`
  ADD CONSTRAINT `daily_content_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_department_leader` FOREIGN KEY (`department_leader_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `letters`
--
ALTER TABLE `letters`
  ADD CONSTRAINT `letters_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `letters_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`created_by_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `news_ibfk_2` FOREIGN KEY (`approved_user1_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `news_ibfk_3` FOREIGN KEY (`approved_user2_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `sliders`
--
ALTER TABLE `sliders`
  ADD CONSTRAINT `sliders_ibfk_1` FOREIGN KEY (`uploaded_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sliders_ibfk_2` FOREIGN KEY (`approved_user1_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sliders_ibfk_3` FOREIGN KEY (`approved_user2_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
