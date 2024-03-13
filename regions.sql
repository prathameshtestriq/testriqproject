-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 05, 2024 at 07:05 PM
-- Server version: 8.0.35-0ubuntu0.22.04.1
-- PHP Version: 8.1.2-1ubuntu2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Races2.0_Old_5Feb`
--

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` mediumint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `translations` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  `wikiDataId` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Rapid API GeoDB Cities'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `name`, `translations`, `created_at`, `updated_at`, `flag`, `wikiDataId`) VALUES
(1, 'Africa', '{\"kr\":\"아프리카\",\"pt-BR\":\"África\",\"pt\":\"África\",\"nl\":\"Afrika\",\"hr\":\"Afrika\",\"fa\":\"آفریقا\",\"de\":\"Afrika\",\"es\":\"África\",\"fr\":\"Afrique\",\"ja\":\"アフリカ\",\"it\":\"Africa\",\"cn\":\"非洲\",\"tr\":\"Afrika\"}', '2023-08-14 01:41:03', '2023-08-14 01:41:03', 1, 'Q15'),
(2, 'Americas', '{\"kr\":\"아메리카\",\"pt-BR\":\"América\",\"pt\":\"América\",\"nl\":\"Amerika\",\"hr\":\"Amerika\",\"fa\":\"قاره آمریکا\",\"de\":\"Amerika\",\"es\":\"América\",\"fr\":\"Amérique\",\"ja\":\"アメリカ州\",\"it\":\"America\",\"cn\":\"美洲\",\"tr\":\"Amerika\"}', '2023-08-14 01:41:03', '2023-08-14 01:41:03', 1, 'Q828'),
(3, 'Asia', '{\"kr\":\"아시아\",\"pt-BR\":\"Ásia\",\"pt\":\"Ásia\",\"nl\":\"Azië\",\"hr\":\"Ázsia\",\"fa\":\"آسیا\",\"de\":\"Asien\",\"es\":\"Asia\",\"fr\":\"Asie\",\"ja\":\"アジア\",\"it\":\"Asia\",\"cn\":\"亚洲\",\"tr\":\"Asya\"}', '2023-08-14 01:41:03', '2023-08-14 01:41:03', 1, 'Q48'),
(4, 'Europe', '{\"kr\":\"유럽\",\"pt-BR\":\"Europa\",\"pt\":\"Europa\",\"nl\":\"Europa\",\"hr\":\"Európa\",\"fa\":\"اروپا\",\"de\":\"Europa\",\"es\":\"Europa\",\"fr\":\"Europe\",\"ja\":\"ヨーロッパ\",\"it\":\"Europa\",\"cn\":\"欧洲\",\"tr\":\"Avrupa\"}', '2023-08-14 01:41:03', '2023-08-14 01:41:03', 1, 'Q46'),
(5, 'Oceania', '{\"kr\":\"오세아니아\",\"pt-BR\":\"Oceania\",\"pt\":\"Oceania\",\"nl\":\"Oceanië en Australië\",\"hr\":\"Óceánia és Ausztrália\",\"fa\":\"اقیانوسیه\",\"de\":\"Ozeanien und Australien\",\"es\":\"Oceanía\",\"fr\":\"Océanie\",\"ja\":\"オセアニア\",\"it\":\"Oceania\",\"cn\":\"大洋洲\",\"tr\":\"Okyanusya\"}', '2023-08-14 01:41:03', '2023-08-14 01:41:03', 1, 'Q55643'),
(6, 'Polar', '{\"kr\":\"남극\",\"pt-BR\":\"Antártida\",\"pt\":\"Antártida\",\"nl\":\"Antarctica\",\"hr\":\"Antarktika\",\"fa\":\"جنوبگان\",\"de\":\"Antarktika\",\"es\":\"Antártida\",\"fr\":\"Antarctique\",\"ja\":\"南極大陸\",\"it\":\"Antartide\",\"cn\":\"南極洲\",\"tr\":\"Antarktika\"}', '2023-08-14 01:41:03', '2023-08-14 01:41:03', 1, 'Q51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
