-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost
-- Vytvořeno: Pát 26. dub 2019, 11:17
-- Verze serveru: 10.1.37-MariaDB
-- Verze PHP: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `edomp`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `category`
--

CREATE TABLE `category` (
  `category_id` bigint(20) NOT NULL,
  `label` text COLLATE utf8_czech_ci NOT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `category`
--

INSERT INTO `category` (`category_id`, `label`, `created`) VALUES
(1, '1. Rovnice', '2019-04-08 17:48:59'),
(3, '2. Posloupnosti', '2019-04-08 18:00:22'),
(9, '3. Logika', '2019-04-12 14:44:36');

-- --------------------------------------------------------

--
-- Struktura tabulky `condition`
--

CREATE TABLE `condition` (
  `condition_id` bigint(20) NOT NULL,
  `accessor` int(11) DEFAULT NULL,
  `label` text COLLATE utf8_czech_ci,
  `condition_type_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `condition`
--

INSERT INTO `condition` (`condition_id`, `accessor`, `label`, `condition_type_id`) VALUES
(1, 0, 'Bez omezení', 1),
(2, 1, 'Kladný', 1),
(3, 2, 'Nulový', 1),
(4, 3, 'Záporný', 1),
(5, 0, 'Bez omezení', 2),
(6, 1, 'Kladný', 2),
(7, 2, 'Nulový', 2),
(8, 3, 'Záporný', 2),
(9, 4, 'Celočíselný', 2),
(10, 5, 'Kladný a odmocnitelný', 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `condition_problem_rel`
--

CREATE TABLE `condition_problem_rel` (
  `condition_id` bigint(20) NOT NULL,
  `problem_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `condition_problem_rel`
--

INSERT INTO `condition_problem_rel` (`condition_id`, `problem_id`) VALUES
(1, 643),
(1, 644),
(1, 646),
(1, 656),
(1, 657),
(1, 663),
(1, 697),
(1, 699),
(1, 703),
(2, 611),
(2, 642),
(2, 651),
(3, 607),
(3, 647),
(3, 648),
(3, 649),
(3, 700),
(3, 701),
(3, 702),
(4, 612),
(4, 613),
(4, 639),
(4, 640),
(5, 645),
(5, 706),
(6, 667),
(6, 707),
(8, 608),
(8, 637),
(8, 638),
(10, 619),
(10, 681);

-- --------------------------------------------------------

--
-- Struktura tabulky `condition_type`
--

CREATE TABLE `condition_type` (
  `condition_type_id` bigint(20) NOT NULL,
  `accessor` int(11) NOT NULL,
  `label` text COLLATE utf8_czech_ci,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `condition_type`
--

INSERT INTO `condition_type` (`condition_type_id`, `accessor`, `label`, `created`) VALUES
(1, 1, 'Podmínka výsledku', '2019-04-05 19:21:16'),
(2, 2, 'Podmínka diskriminantu', '2019-04-05 19:21:16');

-- --------------------------------------------------------

--
-- Struktura tabulky `difficulty`
--

CREATE TABLE `difficulty` (
  `difficulty_id` bigint(20) NOT NULL,
  `label` text COLLATE utf8_czech_ci,
  `created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `difficulty`
--

INSERT INTO `difficulty` (`difficulty_id`, `label`, `created`) VALUES
(1, 'Lehká', '2019-02-17 10:29:19'),
(2, 'Střední', '2019-02-17 10:29:19'),
(3, 'Těžká', '2019-02-17 10:29:19');

-- --------------------------------------------------------

--
-- Struktura tabulky `group`
--

CREATE TABLE `group` (
  `group_id` bigint(20) NOT NULL,
  `label` text COLLATE utf8_czech_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `super_group_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `group`
--

INSERT INTO `group` (`group_id`, `label`, `created`, `super_group_id`) VALUES
(1, '1.A', '2019-04-04 19:51:20', 3),
(2, '1.B', '2019-04-04 19:51:20', 3),
(3, '1.C', '2019-04-04 19:51:20', 3),
(4, '1.D', '2019-04-04 19:51:20', 3),
(5, '1.E', '2019-04-04 19:51:20', 3),
(9, 'Externisté 1', '2019-04-12 12:01:51', 1),
(10, 'TestGroup', '2019-04-12 12:02:33', 1),
(11, 'Administrators', '2019-04-18 16:42:12', 4),
(12, 'Test', '2019-04-25 16:12:45', 3);

-- --------------------------------------------------------

--
-- Struktura tabulky `group_category_rel`
--

CREATE TABLE `group_category_rel` (
  `group_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `group_category_rel`
--

INSERT INTO `group_category_rel` (`group_id`, `category_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(9, 1),
(9, 3),
(10, 1),
(10, 3),
(12, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `group_supergroup_rel`
--

CREATE TABLE `group_supergroup_rel` (
  `group_id` bigint(20) NOT NULL,
  `supergroup_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `logo`
--

CREATE TABLE `logo` (
  `logo_id` bigint(20) NOT NULL,
  `path` text COLLATE utf8_czech_ci,
  `extension` text COLLATE utf8_czech_ci NOT NULL,
  `extension_tmp` text COLLATE utf8_czech_ci,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `label` text COLLATE utf8_czech_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `logo`
--

INSERT INTO `logo` (`logo_id`, `path`, `extension`, `extension_tmp`, `is_used`, `created`, `label`) VALUES
(76, '/data_public/logos/76/file.jpg', '.jpg', '.jpg', 1, '2019-04-14 15:00:02', 'Logo FIT'),
(77, '/data_public/logos/77/file.png', '.png', '.png', 0, '2019-04-23 16:21:42', 'Logo2'),
(78, '/data_public/logos/78/file.png', '.png', '.png', 1, '2019-04-25 18:17:11', 'NewLogo');

-- --------------------------------------------------------

--
-- Struktura tabulky `problem`
--

CREATE TABLE `problem` (
  `problem_id` bigint(20) NOT NULL,
  `structure` text COLLATE utf8_czech_ci,
  `text_before` text COLLATE utf8_czech_ci,
  `text_after` text COLLATE utf8_czech_ci,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `difficulty_id` bigint(20) NOT NULL,
  `problem_type_id` bigint(20) NOT NULL DEFAULT '1',
  `is_prototype` tinyint(1) NOT NULL DEFAULT '0',
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `is_generatable` tinyint(1) NOT NULL DEFAULT '1',
  `sub_category_id` bigint(20) NOT NULL DEFAULT '10',
  `success_rate` float DEFAULT NULL,
  `variable` char(1) COLLATE utf8_czech_ci DEFAULT NULL,
  `first_n` int(11) DEFAULT NULL,
  `difference` int(11) DEFAULT NULL,
  `quotient` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `problem`
--

INSERT INTO `problem` (`problem_id`, `structure`, `text_before`, `text_after`, `created`, `difficulty_id`, `problem_type_id`, `is_prototype`, `is_used`, `is_generatable`, `sub_category_id`, `success_rate`, `variable`, `first_n`, `difference`, `quotient`) VALUES
(607, '\\( <par min=\"1\" max=\"10\"/> x + 15 x + 20 = <par min=\"10\" max=\"20\"/> \\)', '', '', '2019-04-07 15:14:01', 1, 1, 1, 1, 1, 1, 0.5, 'x', NULL, NULL, NULL),
(608, '\\( <par min=\"1\" max=\"20\"/> x^2 + 20 x + 5 = -1 \\)', '', '', '2019-04-07 15:15:57', 1, 2, 1, 0, 1, 2, NULL, 'x', NULL, NULL, NULL),
(611, '\\( \\frac{5 x + 2 x}{4} = \\frac{15 + x}{2} \\)', '', '', '2019-04-07 16:23:04', 1, 1, 0, 0, 1, 1, NULL, 'x', NULL, NULL, NULL),
(612, '\\( <par min=\"1\" max=\"10\"/> x + 15 x + 20 = 0 \\)', '', '', '2019-04-07 16:33:30', 1, 1, 1, 0, 1, 1, NULL, 'x', NULL, NULL, NULL),
(613, '\\( <par min=\"1\" max=\"20\"/> x + 20 x + 5 = <par min=\"0\" max=\"10\"/> \\)', '', '', '2019-04-07 16:45:29', 1, 1, 1, 0, 1, 1, NULL, 'x', NULL, NULL, NULL),
(614, '\\( a_n = \\frac{n + <par min=\"1\" max=\"15\"/>}{<par min=\"5\" max=\"10\"/>} \\)', '', '', '2019-04-07 16:49:57', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(615, '\\( q_n = \\frac{<par min=\"2\" max=\"4\"/>^n}{3^(n + <par min=\"5\" max=\"25\"/>)} \\)', '', '', '2019-04-07 17:42:51', 1, 5, 1, 0, 1, 4, NULL, 'n', 5, NULL, NULL),
(616, '\\( q_n = \\frac{<par min=\"2\" max=\"4\"/>^n}{3^(n + <par min=\"5\" max=\"25\"/>)} \\)', '', '', '2019-04-07 17:50:14', 1, 5, 1, 0, 1, 4, NULL, 'n', 5, NULL, NULL),
(618, '\\( q_n = \\frac{<par min=\"2\" max=\"4\"/>^n}{3^(n + <par min=\"5\" max=\"25\"/>)} \\)', '', '', '2019-04-07 17:59:44', 1, 5, 1, 0, 1, 4, NULL, 'n', 5, NULL, NULL),
(619, '\\( \\bigg(x+1\\bigg)^2 + 6 x + 3 = -3 x^2 \\)', '', '', '2019-04-09 19:16:29', 2, 2, 0, 0, 1, 2, NULL, 'x', NULL, NULL, NULL),
(625, '\\( a_n = \\frac{n + 3}{5} \\)', '', '', '2019-04-10 13:02:14', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(626, '\\( a_n = \\frac{n + 3}{5} \\)', '', '', '2019-04-10 13:17:24', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(627, '\\( a_n = \\frac{n + 3}{5} \\)', '', '', '2019-04-10 13:18:58', 1, 3, 0, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(628, '\\( q_n = \\frac{2^n}{3^{n+1}} \\)', '', '', '2019-04-10 15:23:26', 3, 5, 0, 0, 1, 4, NULL, 'n', 5, NULL, NULL),
(630, '\\( a_n = \\frac{n + 3}{5} \\)', '', '', '2019-04-10 16:26:30', 1, 3, 0, 0, 1, 3, NULL, 'n', 10, NULL, NULL),
(631, '\\( aa \\)', '', '', '2019-04-10 16:28:04', 1, 6, 0, 0, 0, 14, NULL, NULL, NULL, NULL, NULL),
(637, '\\( 20 x^2 + 20 x + 5 = -1 \\)', 'Vyřešte následující rovnici:', 'Zdůvodněte.', '2019-04-12 15:38:29', 1, 2, 0, 0, 1, 2, NULL, 'x', NULL, NULL, NULL),
(638, '\\( 18 x^2 + 20 x + 5 = -1 \\)', 'Vyřešte následující rovnici:', 'Zdůvodněte.', '2019-04-12 15:38:29', 1, 2, 0, 0, 1, 2, NULL, 'x', NULL, NULL, NULL),
(639, '\\( 9 x + 20 x + 5 = 3 \\)', '', '', '2019-04-14 14:22:59', 1, 1, 0, 0, 1, 1, NULL, 'x', NULL, NULL, NULL),
(640, '\\( 11 x + 20 x + 5 = 1 \\)', '', '', '2019-04-14 14:22:59', 1, 1, 0, 0, 1, 1, NULL, 'x', NULL, NULL, NULL),
(642, '\\( 10 x + 5x + 1 + 15 = -5 \\)', 'Vyřešte následující rovnici', 'Zdůvodněte.', '2019-04-16 14:27:09', 2, 1, 0, 0, 0, 1, NULL, 'x', NULL, NULL, NULL),
(643, '$$ 15 x + 10 x + 20 = 0 $$', '', '', '2019-04-22 22:35:58', 2, 1, 1, 0, 1, 2, NULL, 'x', NULL, NULL, NULL),
(644, '$$ 15 x = 10 $$', '', '', '2019-04-22 22:45:55', 1, 1, 0, 0, 1, 1, NULL, 'x', NULL, NULL, NULL),
(645, '$$ 15 x^2 + 10 x = 10 $$', '', '', '2019-04-22 22:47:03', 1, 2, 0, 0, 1, 2, NULL, 'x', NULL, NULL, NULL),
(646, '$$ x + 2 = 0 $$', '', '', '2019-04-22 23:02:48', 1, 1, 0, 0, 1, 1, NULL, 'x', NULL, NULL, NULL),
(647, '\\( 7 x + 15 x + 20 = 20 \\)', '', '', '2019-04-22 23:53:50', 1, 1, 0, 1, 1, 1, 0.5, 'x', NULL, NULL, NULL),
(648, '\\( 10 x + 15 x + 20 = 20 \\)', '', '', '2019-04-23 08:06:17', 1, 1, 0, 1, 1, 1, 0.25, 'x', NULL, NULL, NULL),
(649, '\\( 7 x + 15 x + 20 = 20 \\)', '', '', '2019-04-23 08:06:17', 1, 1, 0, 1, 1, 1, 0.75, 'x', NULL, NULL, NULL),
(651, '$$ 2 x + \\frac{15 x}{2} = <par min=\"0\" max=\"15\"/> $$', '', '', '2019-04-23 17:22:08', 1, 1, 1, 0, 1, 1, NULL, 'x', NULL, NULL, NULL),
(656, '$$ y + \\frac{y}{2} + <par min=\"5\" max=\"10\"/> = 0 $$', '', '', '2019-04-23 19:33:08', 1, 1, 1, 0, 1, 1, NULL, 'y', NULL, NULL, NULL),
(657, '$$ \\big( y + \\frac{y}{2}\\big) + <par min=\"5\" max=\"10\"/> + <par min=\"1\" max=\"10\"/>^2 = 0 $$', '', '', '2019-04-24 11:01:23', 1, 1, 1, 0, 1, 1, NULL, 'y', NULL, NULL, NULL),
(663, '$$ \\big( y + \\frac{y}{2}\\big) + <par min=\"5\" max=\"10\"/> + <par min=\"1\" max=\"10\"/> = 0 $$', '', '', '2019-04-24 11:08:24', 1, 1, 1, 0, 1, 1, NULL, 'y', NULL, NULL, NULL),
(667, '$$ \\big( y^2 + \\frac{y}{2}\\big) + <par min=\"5\" max=\"10\"/> - <par min=\"1\" max=\"10\"/>^2 = 0 $$', '', '', '2019-04-24 11:56:41', 1, 2, 1, 0, 1, 2, NULL, 'y', NULL, NULL, NULL),
(668, '$$ a_n = n + 5 $$', '', '', '2019-04-24 12:16:45', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(671, '$$ a_n = \\big( 2 n \\big) $$', '', '', '2019-04-24 12:36:02', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(672, '$$ a_n = \\big( 2 n \\big) $$', '', '', '2019-04-24 12:45:01', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(673, '$$ a_n = \\big( 2 n \\big) $$', '', '', '2019-04-24 12:46:04', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(674, '$$ a_n = \\big( + 2 n \\big) $$', '', '', '2019-04-24 12:46:28', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(675, '$$ a_n = \\big( - 2 n \\big) $$', '', '', '2019-04-24 12:46:35', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(676, '$$ a_n = 4 \\big( - <par min=\"0\" max=\"10\"/> n \\big) $$', '', '', '2019-04-24 12:48:43', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(677, '$$ a_n = 4 \\big( - \\frac{<par min=\"2\" max=\"10\"/>}{2} n \\big) $$', '', '', '2019-04-24 12:51:21', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(680, '$$ a_n = 4 \\big( - \\frac{<par min=\"2\" max=\"10\"/>}{2} n \\big) $$', '', '', '2019-04-24 13:03:00', 1, 3, 1, 0, 1, 1, NULL, 'n', 5, NULL, NULL),
(681, '$$ x^2 = <par min=\"1\" max=\"10\"/> $$', '', '', '2019-04-24 14:02:36', 1, 2, 1, 0, 1, 2, NULL, 'x', NULL, NULL, NULL),
(683, '$$ a_n = 4 \\big( - \\frac{<par min=\"2\" max=\"10\"/>}{2} n \\big) $$', '', '', '2019-04-24 14:21:01', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(685, '$$ a_n = n + 3 $$', '', '', '2019-04-24 14:29:29', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(687, '$$ q_n = \\frac{2^n}{3^{n+1}} $$', '', '', '2019-04-24 14:45:37', 1, 5, 1, 0, 1, 4, NULL, 'n', 5, NULL, NULL),
(689, '$$ q_n = \\frac{2^n}{3^{n+1}} $$', '', '', '2019-04-24 19:06:28', 1, 5, 1, 0, 1, 4, NULL, 'n', 10, NULL, NULL),
(690, '$$ q_n = \\frac{2^n}{3^{n+1}} $$', '', '', '2019-04-24 19:12:03', 1, 5, 1, 0, 1, 4, NULL, 'n', 5, NULL, NULL),
(691, '$$ a_n = n + 5 $$', '', '', '2019-04-24 19:12:45', 1, 3, 1, 0, 1, 3, NULL, 'n', 10, NULL, NULL),
(692, '$$ a_n = n + 3 $$', '', '', '2019-04-24 19:15:56', 1, 3, 1, 0, 1, 1, NULL, 'n', 5, NULL, NULL),
(693, '$$ a_n = n + 3 $$', '', '', '2019-04-24 19:18:31', 1, 3, 1, 0, 1, 1, NULL, 'n', 10, NULL, NULL),
(694, '$$ q_n = \\frac{2^n}{3^{n+1}} $$', '', '', '2019-04-24 19:25:40', 1, 5, 1, 0, 1, 4, NULL, 'n', 15, NULL, NULL),
(696, '$$ a_n = n + 1 $$', '', '', '2019-04-24 21:12:02', 1, 3, 0, 0, 0, 3, NULL, NULL, 5, NULL, NULL),
(697, '$$ 15 x + \\big( 10 x \\big) = <par min=\"10\" max=\"50\"/> $$', '', '', '2019-04-25 18:09:16', 1, 1, 1, 0, 1, 1, NULL, 'x', NULL, NULL, NULL),
(698, '$$ a_n = n + 5 $$', '', '', '2019-04-25 18:31:12', 1, 3, 1, 0, 1, 3, NULL, 'n', 5, NULL, NULL),
(699, '$$ 15 x + 20 x + 5 = 0 $$\n$$ 5 x + 20 x + 5 = 0 $$', '', '', '2019-04-25 21:59:20', 1, 1, 0, 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(700, '\\( 8 x + 15 x + 20 = 20 \\)', '', '', '2019-04-26 09:06:12', 1, 1, 0, 0, 1, 1, NULL, NULL, NULL, NULL, NULL),
(701, '\\( 7 x + 15 x + 20 = 20 \\)', '', '', '2019-04-26 09:07:43', 1, 1, 0, 1, 1, 1, NULL, NULL, NULL, NULL, NULL),
(702, '\\( 5 x + 15 x + 20 = 20 \\)', '', '', '2019-04-26 09:10:19', 1, 1, 0, 1, 1, 1, NULL, 'x', NULL, NULL, NULL),
(703, '$$ x + x $$', '', '', '2019-04-26 10:12:33', 1, 1, 0, 0, 0, 1, NULL, '', NULL, NULL, NULL),
(704, '$$ a_n = 2 $$', '', '', '2019-04-26 10:16:21', 1, 3, 1, 0, 1, 1, NULL, 'n', NULL, NULL, NULL),
(705, '$$ a_n = 2 $$', '', '', '2019-04-26 10:16:45', 1, 5, 1, 0, 1, 1, NULL, 'n', NULL, NULL, NULL),
(706, '$$ x^2 + x + 2 = 0 $$', '', '', '2019-04-26 10:17:11', 1, 2, 1, 0, 1, 1, NULL, 'x', NULL, NULL, NULL),
(707, '$$ x^2 + x + 2 = <par min=\"0\" max=\"10\"/> $$', '', '', '2019-04-26 10:17:44', 1, 2, 1, 0, 1, 1, NULL, 'x', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `problem_final`
--

CREATE TABLE `problem_final` (
  `problem_id` bigint(20) NOT NULL,
  `result` text COLLATE utf8_czech_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `problem_final`
--

INSERT INTO `problem_final` (`problem_id`, `result`) VALUES
(611, '\\(x = 0.375, \\)'),
(619, '\\(x = -16, \\)'),
(627, '$$a_{1} = 0.8$$$$a_{2} = 1$$$$a_{3} = 1.2$$$$a_{4} = 1.4$$$$a_{5} = 1.6$$$$Diference = 0.2$$'),
(628, '$$q_{1} = 0.22222222222222$$$$q_{2} = 0.14814814814815$$$$q_{3} = 0.098765432098765$$$$q_{4} = 0.065843621399177$$$$q_{5} = 0.043895747599451$$$$Kvocient = 0.7$$'),
(630, '$$a_{1} = 0.8$$$$a_{2} = 1$$$$a_{3} = 1.2$$$$a_{4} = 1.4$$$$a_{5} = 1.6$$$$a_{6} = 1.8$$$$a_{7} = 2$$$$a_{8} = 2.2$$$$a_{9} = 2.4$$$$a_{10} = 2.6$$$$Diference = 0.2$$'),
(631, ''),
(637, ''),
(638, ''),
(639, '\\(x = -0.068965517241379, \\)'),
(640, ''),
(642, ''),
(644, '\\(x = 0.66666666666667, \\)'),
(645, '$$x_1 = 0.54858377035486$$$$x_2 = -1.2152504370215$$'),
(646, '\\(x = -2, \\)'),
(647, ''),
(648, '\\(x = 0, \\)'),
(649, '\\(x = 0, \\)'),
(696, ''),
(699, ''),
(700, ''),
(701, ''),
(702, '$$x = 0$$'),
(703, '');

-- --------------------------------------------------------

--
-- Struktura tabulky `problem_prototype`
--

CREATE TABLE `problem_prototype` (
  `problem_id` bigint(20) NOT NULL,
  `matches` text COLLATE utf8_czech_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `problem_prototype`
--

INSERT INTO `problem_prototype` (`problem_id`, `matches`) VALUES
(607, '[{\"p0\":1,\"p1\":20},{\"p0\":2,\"p1\":20},{\"p0\":3,\"p1\":20},{\"p0\":4,\"p1\":20},{\"p0\":5,\"p1\":20},{\"p0\":6,\"p1\":20},{\"p0\":7,\"p1\":20},{\"p0\":8,\"p1\":20},{\"p0\":9,\"p1\":20},{\"p0\":10,\"p1\":20}]'),
(608, '[{\"p0\":17},{\"p0\":18},{\"p0\":19},{\"p0\":20}]'),
(612, '[{\"p0\":1},{\"p0\":2},{\"p0\":3},{\"p0\":4},{\"p0\":5},{\"p0\":6},{\"p0\":7},{\"p0\":8},{\"p0\":9},{\"p0\":10}]'),
(613, '[{\"p0\":1,\"p1\":0},{\"p0\":1,\"p1\":1},{\"p0\":1,\"p1\":2},{\"p0\":1,\"p1\":3},{\"p0\":1,\"p1\":4},{\"p0\":2,\"p1\":0},{\"p0\":2,\"p1\":1},{\"p0\":2,\"p1\":2},{\"p0\":2,\"p1\":3},{\"p0\":2,\"p1\":4},{\"p0\":3,\"p1\":0},{\"p0\":3,\"p1\":1},{\"p0\":3,\"p1\":2},{\"p0\":3,\"p1\":3},{\"p0\":3,\"p1\":4},{\"p0\":4,\"p1\":0},{\"p0\":4,\"p1\":1},{\"p0\":4,\"p1\":2},{\"p0\":4,\"p1\":3},{\"p0\":4,\"p1\":4},{\"p0\":5,\"p1\":0},{\"p0\":5,\"p1\":1},{\"p0\":5,\"p1\":2},{\"p0\":5,\"p1\":3},{\"p0\":5,\"p1\":4},{\"p0\":6,\"p1\":0},{\"p0\":6,\"p1\":1},{\"p0\":6,\"p1\":2},{\"p0\":6,\"p1\":3},{\"p0\":6,\"p1\":4},{\"p0\":7,\"p1\":0},{\"p0\":7,\"p1\":1},{\"p0\":7,\"p1\":2},{\"p0\":7,\"p1\":3},{\"p0\":7,\"p1\":4},{\"p0\":8,\"p1\":0},{\"p0\":8,\"p1\":1},{\"p0\":8,\"p1\":2},{\"p0\":8,\"p1\":3},{\"p0\":8,\"p1\":4},{\"p0\":9,\"p1\":0},{\"p0\":9,\"p1\":1},{\"p0\":9,\"p1\":2},{\"p0\":9,\"p1\":3},{\"p0\":9,\"p1\":4},{\"p0\":10,\"p1\":0},{\"p0\":10,\"p1\":1},{\"p0\":10,\"p1\":2},{\"p0\":10,\"p1\":3},{\"p0\":10,\"p1\":4},{\"p0\":11,\"p1\":0},{\"p0\":11,\"p1\":1},{\"p0\":11,\"p1\":2},{\"p0\":11,\"p1\":3},{\"p0\":11,\"p1\":4},{\"p0\":12,\"p1\":0},{\"p0\":12,\"p1\":1},{\"p0\":12,\"p1\":2},{\"p0\":12,\"p1\":3},{\"p0\":12,\"p1\":4},{\"p0\":13,\"p1\":0},{\"p0\":13,\"p1\":1},{\"p0\":13,\"p1\":2},{\"p0\":13,\"p1\":3},{\"p0\":13,\"p1\":4},{\"p0\":14,\"p1\":0},{\"p0\":14,\"p1\":1},{\"p0\":14,\"p1\":2},{\"p0\":14,\"p1\":3},{\"p0\":14,\"p1\":4},{\"p0\":15,\"p1\":0},{\"p0\":15,\"p1\":1},{\"p0\":15,\"p1\":2},{\"p0\":15,\"p1\":3},{\"p0\":15,\"p1\":4},{\"p0\":16,\"p1\":0},{\"p0\":16,\"p1\":1},{\"p0\":16,\"p1\":2},{\"p0\":16,\"p1\":3},{\"p0\":16,\"p1\":4},{\"p0\":17,\"p1\":0},{\"p0\":17,\"p1\":1},{\"p0\":17,\"p1\":2},{\"p0\":17,\"p1\":3},{\"p0\":17,\"p1\":4},{\"p0\":18,\"p1\":0},{\"p0\":18,\"p1\":1},{\"p0\":18,\"p1\":2},{\"p0\":18,\"p1\":3},{\"p0\":18,\"p1\":4},{\"p0\":19,\"p1\":0},{\"p0\":19,\"p1\":1},{\"p0\":19,\"p1\":2},{\"p0\":19,\"p1\":3},{\"p0\":19,\"p1\":4},{\"p0\":20,\"p1\":0},{\"p0\":20,\"p1\":1},{\"p0\":20,\"p1\":2},{\"p0\":20,\"p1\":3},{\"p0\":20,\"p1\":4}]'),
(614, NULL),
(615, NULL),
(616, NULL),
(618, NULL),
(625, NULL),
(626, NULL),
(643, NULL),
(651, '[{\"p0\":1},{\"p0\":2},{\"p0\":3},{\"p0\":4},{\"p0\":5},{\"p0\":6},{\"p0\":7},{\"p0\":8},{\"p0\":9},{\"p0\":10},{\"p0\":11},{\"p0\":12},{\"p0\":13},{\"p0\":14},{\"p0\":15}]'),
(656, NULL),
(657, '[{\"p0\":5,\"p1\":1},{\"p0\":5,\"p1\":2},{\"p0\":5,\"p1\":3},{\"p0\":5,\"p1\":4},{\"p0\":5,\"p1\":5},{\"p0\":5,\"p1\":6},{\"p0\":5,\"p1\":7},{\"p0\":5,\"p1\":8},{\"p0\":5,\"p1\":9},{\"p0\":5,\"p1\":10},{\"p0\":6,\"p1\":1},{\"p0\":6,\"p1\":2},{\"p0\":6,\"p1\":3},{\"p0\":6,\"p1\":4},{\"p0\":6,\"p1\":5},{\"p0\":6,\"p1\":6},{\"p0\":6,\"p1\":7},{\"p0\":6,\"p1\":8},{\"p0\":6,\"p1\":9},{\"p0\":6,\"p1\":10},{\"p0\":7,\"p1\":1},{\"p0\":7,\"p1\":2},{\"p0\":7,\"p1\":3},{\"p0\":7,\"p1\":4},{\"p0\":7,\"p1\":5},{\"p0\":7,\"p1\":6},{\"p0\":7,\"p1\":7},{\"p0\":7,\"p1\":8},{\"p0\":7,\"p1\":9},{\"p0\":7,\"p1\":10},{\"p0\":8,\"p1\":1},{\"p0\":8,\"p1\":2},{\"p0\":8,\"p1\":3},{\"p0\":8,\"p1\":4},{\"p0\":8,\"p1\":5},{\"p0\":8,\"p1\":6},{\"p0\":8,\"p1\":7},{\"p0\":8,\"p1\":8},{\"p0\":8,\"p1\":9},{\"p0\":8,\"p1\":10},{\"p0\":9,\"p1\":1},{\"p0\":9,\"p1\":2},{\"p0\":9,\"p1\":3},{\"p0\":9,\"p1\":4},{\"p0\":9,\"p1\":5},{\"p0\":9,\"p1\":6},{\"p0\":9,\"p1\":7},{\"p0\":9,\"p1\":8},{\"p0\":9,\"p1\":9},{\"p0\":9,\"p1\":10},{\"p0\":10,\"p1\":1},{\"p0\":10,\"p1\":2},{\"p0\":10,\"p1\":3},{\"p0\":10,\"p1\":4},{\"p0\":10,\"p1\":5},{\"p0\":10,\"p1\":6},{\"p0\":10,\"p1\":7},{\"p0\":10,\"p1\":8},{\"p0\":10,\"p1\":9},{\"p0\":10,\"p1\":10}]'),
(663, NULL),
(667, '[{\"p0\":5,\"p1\":3},{\"p0\":5,\"p1\":4},{\"p0\":5,\"p1\":5},{\"p0\":5,\"p1\":6},{\"p0\":5,\"p1\":7},{\"p0\":5,\"p1\":8},{\"p0\":5,\"p1\":9},{\"p0\":5,\"p1\":10},{\"p0\":6,\"p1\":3},{\"p0\":6,\"p1\":4},{\"p0\":6,\"p1\":5},{\"p0\":6,\"p1\":6},{\"p0\":6,\"p1\":7},{\"p0\":6,\"p1\":8},{\"p0\":6,\"p1\":9},{\"p0\":6,\"p1\":10},{\"p0\":7,\"p1\":3},{\"p0\":7,\"p1\":4},{\"p0\":7,\"p1\":5},{\"p0\":7,\"p1\":6},{\"p0\":7,\"p1\":7},{\"p0\":7,\"p1\":8},{\"p0\":7,\"p1\":9},{\"p0\":7,\"p1\":10},{\"p0\":8,\"p1\":3},{\"p0\":8,\"p1\":4},{\"p0\":8,\"p1\":5},{\"p0\":8,\"p1\":6},{\"p0\":8,\"p1\":7},{\"p0\":8,\"p1\":8},{\"p0\":8,\"p1\":9},{\"p0\":8,\"p1\":10},{\"p0\":9,\"p1\":3},{\"p0\":9,\"p1\":4},{\"p0\":9,\"p1\":5},{\"p0\":9,\"p1\":6},{\"p0\":9,\"p1\":7},{\"p0\":9,\"p1\":8},{\"p0\":9,\"p1\":9},{\"p0\":9,\"p1\":10},{\"p0\":10,\"p1\":4},{\"p0\":10,\"p1\":5},{\"p0\":10,\"p1\":6},{\"p0\":10,\"p1\":7},{\"p0\":10,\"p1\":8},{\"p0\":10,\"p1\":9},{\"p0\":10,\"p1\":10}]'),
(668, NULL),
(671, NULL),
(672, NULL),
(673, NULL),
(674, NULL),
(675, NULL),
(676, NULL),
(677, NULL),
(680, NULL),
(681, '[{\"p0\":1},{\"p0\":2},{\"p0\":3},{\"p0\":4},{\"p0\":5},{\"p0\":6},{\"p0\":7},{\"p0\":8},{\"p0\":9},{\"p0\":10}]'),
(683, NULL),
(685, NULL),
(687, NULL),
(689, NULL),
(690, NULL),
(691, NULL),
(692, NULL),
(693, NULL),
(694, NULL),
(697, '[{\"p0\":5},{\"p0\":6},{\"p0\":7},{\"p0\":8},{\"p0\":9},{\"p0\":10}]'),
(698, NULL),
(704, NULL),
(705, NULL),
(706, NULL),
(707, '[{\"p0\":2},{\"p0\":3},{\"p0\":4},{\"p0\":5},{\"p0\":6},{\"p0\":7},{\"p0\":8},{\"p0\":9},{\"p0\":10}]');

-- --------------------------------------------------------

--
-- Struktura tabulky `problem_test_rel`
--

CREATE TABLE `problem_test_rel` (
  `test_id` bigint(20) NOT NULL,
  `problem_prototype_id` bigint(20) DEFAULT NULL,
  `problem_final_id` bigint(20) NOT NULL,
  `variant` text COLLATE utf8_czech_ci NOT NULL,
  `newpage` tinyint(1) DEFAULT '0',
  `success_rate` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `problem_test_rel`
--

INSERT INTO `problem_test_rel` (`test_id`, `problem_prototype_id`, `problem_final_id`, `variant`, `newpage`, `success_rate`) VALUES
(11, NULL, 647, 'A', 0, 0.5),
(11, NULL, 647, 'B', 0, 0.5),
(11, 607, 648, 'A', 0, 0.25),
(11, 607, 649, 'B', 0, 0.75),
(13, NULL, 699, 'A', 0, NULL),
(18, 607, 701, 'A', 0, NULL),
(19, 607, 702, 'A', 0, NULL);

--
-- Spouště `problem_test_rel`
--
DELIMITER $$
CREATE TRIGGER `problem_final_test_rel_insert_check` BEFORE INSERT ON `problem_test_rel` FOR EACH ROW BEGIN
    DECLARE cnt INT;
    SET cnt = ( SELECT COUNT(*) FROM problem WHERE problem_id = NEW.problem_prototype_id AND is_prototype = TRUE );
    IF NEW.problem_prototype_id IS NOT NULL AND cnt < 1 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Problem prototype id does not match any existing prototype.';
    END IF;
  END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `problem_final_test_rel_update_check` BEFORE UPDATE ON `problem_test_rel` FOR EACH ROW BEGIN
    DECLARE cnt INT;
    SET cnt = ( SELECT COUNT(*) FROM problem WHERE problem_id = NEW.problem_prototype_id AND is_prototype = TRUE );
    IF NEW.problem_prototype_id IS NOT NULL AND cnt < 1 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Problem prototype id does not match any existing prototype.';
    END IF;
  END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabulky `problem_tp_condition_tp_rel`
--

CREATE TABLE `problem_tp_condition_tp_rel` (
  `problem_type_id` bigint(20) NOT NULL,
  `condition_type_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `problem_tp_condition_tp_rel`
--

INSERT INTO `problem_tp_condition_tp_rel` (`problem_type_id`, `condition_type_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `problem_type`
--

CREATE TABLE `problem_type` (
  `problem_type_id` bigint(20) NOT NULL,
  `label` text COLLATE utf8_czech_ci,
  `accessor` int(11) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_generatable` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `problem_type`
--

INSERT INTO `problem_type` (`problem_type_id`, `label`, `accessor`, `created`, `is_generatable`) VALUES
(1, 'Lineární rovnice', 1, '2019-04-05 19:19:49', 1),
(2, 'Kvadratická rovnice', 2, '2019-04-05 19:19:49', 1),
(3, 'Aritmetická posloupnost', 3, '2019-04-05 19:19:49', 1),
(5, 'Geometická posloupnost', 5, '2019-04-05 19:19:49', 1),
(6, 'Logika', 6, '2019-04-10 16:12:10', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `prototype_json_data`
--

CREATE TABLE `prototype_json_data` (
  `prototype_json_data_id` bigint(20) NOT NULL,
  `json_data` longtext COLLATE utf8_czech_ci,
  `problem_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `prototype_json_data`
--

INSERT INTO `prototype_json_data` (`prototype_json_data_id`, `json_data`, `problem_id`) VALUES
(583, '{\"matches\":[{\"p0\":1,\"p1\":0},{\"p0\":1,\"p1\":1},{\"p0\":1,\"p1\":2},{\"p0\":1,\"p1\":3},{\"p0\":1,\"p1\":4},{\"p0\":2,\"p1\":0},{\"p0\":2,\"p1\":1},{\"p0\":2,\"p1\":2},{\"p0\":2,\"p1\":3},{\"p0\":2,\"p1\":4},{\"p0\":3,\"p1\":0},{\"p0\":3,\"p1\":1},{\"p0\":3,\"p1\":2},{\"p0\":3,\"p1\":3},{\"p0\":3,\"p1\":4},{\"p0\":4,\"p1\":0},{\"p0\":4,\"p1\":1},{\"p0\":4,\"p1\":2},{\"p0\":4,\"p1\":3},{\"p0\":4,\"p1\":4},{\"p0\":5,\"p1\":0},{\"p0\":5,\"p1\":1},{\"p0\":5,\"p1\":2},{\"p0\":5,\"p1\":3},{\"p0\":5,\"p1\":4},{\"p0\":6,\"p1\":0},{\"p0\":6,\"p1\":1},{\"p0\":6,\"p1\":2},{\"p0\":6,\"p1\":3},{\"p0\":6,\"p1\":4},{\"p0\":7,\"p1\":0},{\"p0\":7,\"p1\":1},{\"p0\":7,\"p1\":2},{\"p0\":7,\"p1\":3},{\"p0\":7,\"p1\":4},{\"p0\":8,\"p1\":0},{\"p0\":8,\"p1\":1},{\"p0\":8,\"p1\":2},{\"p0\":8,\"p1\":3},{\"p0\":8,\"p1\":4},{\"p0\":9,\"p1\":0},{\"p0\":9,\"p1\":1},{\"p0\":9,\"p1\":2},{\"p0\":9,\"p1\":3},{\"p0\":9,\"p1\":4},{\"p0\":10,\"p1\":0},{\"p0\":10,\"p1\":1},{\"p0\":10,\"p1\":2},{\"p0\":10,\"p1\":3},{\"p0\":10,\"p1\":4},{\"p0\":11,\"p1\":0},{\"p0\":11,\"p1\":1},{\"p0\":11,\"p1\":2},{\"p0\":11,\"p1\":3},{\"p0\":11,\"p1\":4},{\"p0\":12,\"p1\":0},{\"p0\":12,\"p1\":1},{\"p0\":12,\"p1\":2},{\"p0\":12,\"p1\":3},{\"p0\":12,\"p1\":4},{\"p0\":13,\"p1\":0},{\"p0\":13,\"p1\":1},{\"p0\":13,\"p1\":2},{\"p0\":13,\"p1\":3},{\"p0\":13,\"p1\":4},{\"p0\":14,\"p1\":0},{\"p0\":14,\"p1\":1},{\"p0\":14,\"p1\":2},{\"p0\":14,\"p1\":3},{\"p0\":14,\"p1\":4},{\"p0\":15,\"p1\":0},{\"p0\":15,\"p1\":1},{\"p0\":15,\"p1\":2},{\"p0\":15,\"p1\":3},{\"p0\":15,\"p1\":4},{\"p0\":16,\"p1\":0},{\"p0\":16,\"p1\":1},{\"p0\":16,\"p1\":2},{\"p0\":16,\"p1\":3},{\"p0\":16,\"p1\":4},{\"p0\":17,\"p1\":0},{\"p0\":17,\"p1\":1},{\"p0\":17,\"p1\":2},{\"p0\":17,\"p1\":3},{\"p0\":17,\"p1\":4},{\"p0\":18,\"p1\":0},{\"p0\":18,\"p1\":1},{\"p0\":18,\"p1\":2},{\"p0\":18,\"p1\":3},{\"p0\":18,\"p1\":4},{\"p0\":19,\"p1\":0},{\"p0\":19,\"p1\":1},{\"p0\":19,\"p1\":2},{\"p0\":19,\"p1\":3},{\"p0\":19,\"p1\":4},{\"p0\":20,\"p1\":0},{\"p0\":20,\"p1\":1},{\"p0\":20,\"p1\":2},{\"p0\":20,\"p1\":3},{\"p0\":20,\"p1\":4}]}', 613),
(584, '{\"matches\":[{\"p0\":17},{\"p0\":18},{\"p0\":19},{\"p0\":20}]}', 608),
(585, '{\"matches\":[{\"p0\":1,\"p1\":20},{\"p0\":2,\"p1\":20},{\"p0\":3,\"p1\":20},{\"p0\":4,\"p1\":20},{\"p0\":5,\"p1\":20},{\"p0\":6,\"p1\":20},{\"p0\":7,\"p1\":20},{\"p0\":8,\"p1\":20},{\"p0\":9,\"p1\":20},{\"p0\":10,\"p1\":20}]}', 607),
(589, '{\"matches\":[{\"p0\":1,\"p1\":8},{\"p0\":1,\"p1\":9},{\"p0\":1,\"p1\":10}]}', 635),
(590, '{\"matches\":[{\"p0\":0}]}', 651),
(593, '{\"matches\":[{\"p0\":5},{\"p0\":6},{\"p0\":7},{\"p0\":8},{\"p0\":9},{\"p0\":10}]}', 658),
(594, '{\"matches\":[{\"p0\":5,\"p1\":1},{\"p0\":5,\"p1\":2},{\"p0\":5,\"p1\":3},{\"p0\":5,\"p1\":4},{\"p0\":5,\"p1\":5},{\"p0\":5,\"p1\":6},{\"p0\":5,\"p1\":7},{\"p0\":5,\"p1\":8},{\"p0\":5,\"p1\":9},{\"p0\":5,\"p1\":10},{\"p0\":6,\"p1\":1},{\"p0\":6,\"p1\":2},{\"p0\":6,\"p1\":3},{\"p0\":6,\"p1\":4},{\"p0\":6,\"p1\":5},{\"p0\":6,\"p1\":6},{\"p0\":6,\"p1\":7},{\"p0\":6,\"p1\":8},{\"p0\":6,\"p1\":9},{\"p0\":6,\"p1\":10},{\"p0\":7,\"p1\":1},{\"p0\":7,\"p1\":2},{\"p0\":7,\"p1\":3},{\"p0\":7,\"p1\":4},{\"p0\":7,\"p1\":5},{\"p0\":7,\"p1\":6},{\"p0\":7,\"p1\":7},{\"p0\":7,\"p1\":8},{\"p0\":7,\"p1\":9},{\"p0\":7,\"p1\":10},{\"p0\":8,\"p1\":1},{\"p0\":8,\"p1\":2},{\"p0\":8,\"p1\":3},{\"p0\":8,\"p1\":4},{\"p0\":8,\"p1\":5},{\"p0\":8,\"p1\":6},{\"p0\":8,\"p1\":7},{\"p0\":8,\"p1\":8},{\"p0\":8,\"p1\":9},{\"p0\":8,\"p1\":10},{\"p0\":9,\"p1\":1},{\"p0\":9,\"p1\":2},{\"p0\":9,\"p1\":3},{\"p0\":9,\"p1\":4},{\"p0\":9,\"p1\":5},{\"p0\":9,\"p1\":6},{\"p0\":9,\"p1\":7},{\"p0\":9,\"p1\":8},{\"p0\":9,\"p1\":9},{\"p0\":9,\"p1\":10},{\"p0\":10,\"p1\":1},{\"p0\":10,\"p1\":2},{\"p0\":10,\"p1\":3},{\"p0\":10,\"p1\":4},{\"p0\":10,\"p1\":5},{\"p0\":10,\"p1\":6},{\"p0\":10,\"p1\":7},{\"p0\":10,\"p1\":8},{\"p0\":10,\"p1\":9},{\"p0\":10,\"p1\":10}]}', 657),
(605, '{\"matches\":[{\"p0\":5,\"p1\":3},{\"p0\":5,\"p1\":4},{\"p0\":5,\"p1\":5},{\"p0\":5,\"p1\":6},{\"p0\":5,\"p1\":7},{\"p0\":5,\"p1\":8},{\"p0\":5,\"p1\":9},{\"p0\":5,\"p1\":10},{\"p0\":6,\"p1\":3},{\"p0\":6,\"p1\":4},{\"p0\":6,\"p1\":5},{\"p0\":6,\"p1\":6},{\"p0\":6,\"p1\":7},{\"p0\":6,\"p1\":8},{\"p0\":6,\"p1\":9},{\"p0\":6,\"p1\":10},{\"p0\":7,\"p1\":3},{\"p0\":7,\"p1\":4},{\"p0\":7,\"p1\":5},{\"p0\":7,\"p1\":6},{\"p0\":7,\"p1\":7},{\"p0\":7,\"p1\":8},{\"p0\":7,\"p1\":9},{\"p0\":7,\"p1\":10},{\"p0\":8,\"p1\":3},{\"p0\":8,\"p1\":4},{\"p0\":8,\"p1\":5},{\"p0\":8,\"p1\":6},{\"p0\":8,\"p1\":7},{\"p0\":8,\"p1\":8},{\"p0\":8,\"p1\":9},{\"p0\":8,\"p1\":10},{\"p0\":9,\"p1\":3},{\"p0\":9,\"p1\":4},{\"p0\":9,\"p1\":5},{\"p0\":9,\"p1\":6},{\"p0\":9,\"p1\":7},{\"p0\":9,\"p1\":8},{\"p0\":9,\"p1\":9},{\"p0\":9,\"p1\":10},{\"p0\":10,\"p1\":4},{\"p0\":10,\"p1\":5},{\"p0\":10,\"p1\":6},{\"p0\":10,\"p1\":7},{\"p0\":10,\"p1\":8},{\"p0\":10,\"p1\":9},{\"p0\":10,\"p1\":10}]}', 667),
(606, '{\"matches\":[{\"p0\":1},{\"p0\":2},{\"p0\":3},{\"p0\":4},{\"p0\":5},{\"p0\":6},{\"p0\":7},{\"p0\":8},{\"p0\":9},{\"p0\":10}]}', 681),
(607, '{\"matches\":[{\"p0\":5},{\"p0\":6},{\"p0\":7},{\"p0\":8},{\"p0\":9},{\"p0\":10}]}', 697),
(608, '{\"matches\":[{\"p0\":2},{\"p0\":3},{\"p0\":4},{\"p0\":5},{\"p0\":6},{\"p0\":7},{\"p0\":8},{\"p0\":9},{\"p0\":10}]}', 707);

-- --------------------------------------------------------

--
-- Struktura tabulky `role`
--

CREATE TABLE `role` (
  `role_id` bigint(20) NOT NULL,
  `label` text COLLATE utf8_czech_ci,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `role`
--

INSERT INTO `role` (`role_id`, `label`, `created`) VALUES
(1, 'admin', '2019-03-21 00:15:46'),
(2, 'user', '2019-04-14 18:04:18');

-- --------------------------------------------------------

--
-- Struktura tabulky `specialization`
--

CREATE TABLE `specialization` (
  `specialization_id` bigint(20) NOT NULL,
  `label` text COLLATE utf8_czech_ci,
  `created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `specialization`
--

INSERT INTO `specialization` (`specialization_id`, `label`, `created`) VALUES
(2, 'Informatika: Počítačové sítě', '2019-03-14 18:21:23'),
(3, 'Informatika: Tvorba webových aplikací', '2019-03-14 18:21:23'),
(4, 'Informatika: IoT', '2019-03-14 18:21:23'),
(5, 'Elektrotechnika: Slaboproud', '2019-03-14 18:21:23'),
(6, 'Elektrotechnika: Silnoproud', '2019-03-14 18:21:23');

-- --------------------------------------------------------

--
-- Struktura tabulky `sub_category`
--

CREATE TABLE `sub_category` (
  `sub_category_id` bigint(20) NOT NULL,
  `label` text COLLATE utf8_czech_ci NOT NULL,
  `category_id` bigint(20) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `sub_category`
--

INSERT INTO `sub_category` (`sub_category_id`, `label`, `category_id`, `created`) VALUES
(1, '1.1. Lineárná rovnice', 1, '2019-04-11 14:24:36'),
(2, '1.2. Kvadratické rovnice', 1, '2019-04-11 14:24:42'),
(3, '2.1. Aritmetické posloupnosti', 3, '2019-04-11 16:58:39'),
(4, '2.2. Geometrické posloupnosti', 3, '2019-04-12 14:45:25'),
(14, '3.1. Výroková logika', 9, '2019-04-12 14:54:40');

-- --------------------------------------------------------

--
-- Struktura tabulky `super_group`
--

CREATE TABLE `super_group` (
  `super_group_id` bigint(20) NOT NULL,
  `label` text COLLATE utf8_czech_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `super_group`
--

INSERT INTO `super_group` (`super_group_id`, `label`, `created`) VALUES
(1, 'Externí kurzy', '2019-04-04 20:03:59'),
(3, 'Střední škola', '2019-04-12 08:50:30'),
(4, 'Administrators', '2019-04-18 16:42:00');

-- --------------------------------------------------------

--
-- Struktura tabulky `super_group_category_rel`
--

CREATE TABLE `super_group_category_rel` (
  `super_group_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `super_group_category_rel`
--

INSERT INTO `super_group_category_rel` (`super_group_id`, `category_id`) VALUES
(1, 1),
(1, 3),
(3, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `test`
--

CREATE TABLE `test` (
  `test_id` bigint(20) NOT NULL,
  `logo_id` bigint(20) DEFAULT NULL,
  `introduction_text` text COLLATE utf8_czech_ci,
  `test_term_id` bigint(20) NOT NULL,
  `school_year` text COLLATE utf8_czech_ci,
  `test_number` int(11) NOT NULL DEFAULT '1',
  `group_id` bigint(20) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `test`
--

INSERT INTO `test` (`test_id`, `logo_id`, `introduction_text`, `test_term_id`, `school_year`, `test_number`, `group_id`, `created`) VALUES
(11, 76, NULL, 1, '2018/2019', 1, 1, '2019-04-23 08:06:17'),
(13, 78, NULL, 1, '2018/2019', 2, 1, '2019-04-26 08:28:12'),
(18, 78, NULL, 1, '2018/2019', 3, 1, '2019-04-26 09:07:43'),
(19, 78, NULL, 1, '2018/2019', 3, 1, '2019-04-26 09:10:19');

-- --------------------------------------------------------

--
-- Struktura tabulky `test_term`
--

CREATE TABLE `test_term` (
  `test_term_id` bigint(20) NOT NULL,
  `label` text COLLATE utf8_czech_ci,
  `created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `test_term`
--

INSERT INTO `test_term` (`test_term_id`, `label`, `created`) VALUES
(1, '1. pol.', '2019-04-12 17:38:09'),
(2, '2. pol.', '2019-04-12 17:38:09');

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE `user` (
  `user_id` bigint(20) NOT NULL,
  `email` text COLLATE utf8_czech_ci,
  `username` text COLLATE utf8_czech_ci NOT NULL,
  `password` text COLLATE utf8_czech_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`user_id`, `email`, `username`, `password`, `created`) VALUES
(1, '', 'admin', '$2y$10$czW3Ss123X2v5rnzD.xsUOokAxtGW8NCPSoCwCcZA1jMKtMV3LO1e', '2019-03-21 00:09:54'),
(2, NULL, 'testuser', '$2y$10$ahOYl0eSqW1oCxT0MalW3.vMJKqBQ3vGl5tAUwBpmJafgCCZi3roS', '2019-04-14 17:43:00'),
(6, NULL, 'testuser5', '$2y$10$N.DgdrZiRU3WMKvZlPBpn.8hXR3dHaJNcSHEwk/BRLYtdbdp6FSYu', '2019-04-18 12:18:56'),
(7, NULL, 'testuser6', '$2y$10$mZyN6YfQkX2GsD7viSehy.y6nBBjHCg6DI1Nr2yoRq//uhFSEZiTG', '2019-04-18 12:20:53'),
(8, NULL, 'uzivatel', '$2y$10$cI3nuidGoGesBDbtWQcT6OSuPAVdJb/wB9MOtPPxhWp4XxDgKMhxS', '2019-04-25 18:15:36');

-- --------------------------------------------------------

--
-- Struktura tabulky `user_group_rel`
--

CREATE TABLE `user_group_rel` (
  `user_id` bigint(20) NOT NULL,
  `group_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `user_group_rel`
--

INSERT INTO `user_group_rel` (`user_id`, `group_id`) VALUES
(1, 10),
(1, 11),
(2, 1),
(6, 2),
(7, 2),
(8, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `user_role_rel`
--

CREATE TABLE `user_role_rel` (
  `user_id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `user_role_rel`
--

INSERT INTO `user_role_rel` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 2),
(6, 2),
(7, 2),
(8, 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `user_super_group_rel`
--

CREATE TABLE `user_super_group_rel` (
  `user_id` bigint(20) NOT NULL,
  `super_group_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `user_super_group_rel`
--

INSERT INTO `user_super_group_rel` (`user_id`, `super_group_id`) VALUES
(2, 3);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Klíče pro tabulku `condition`
--
ALTER TABLE `condition`
  ADD PRIMARY KEY (`condition_id`);

--
-- Klíče pro tabulku `condition_problem_rel`
--
ALTER TABLE `condition_problem_rel`
  ADD PRIMARY KEY (`condition_id`,`problem_id`),
  ADD KEY `condition_problem_rel_ibfk_3` (`problem_id`);

--
-- Klíče pro tabulku `condition_type`
--
ALTER TABLE `condition_type`
  ADD PRIMARY KEY (`condition_type_id`),
  ADD UNIQUE KEY `condition_type_accessor_uindex` (`accessor`);

--
-- Klíče pro tabulku `difficulty`
--
ALTER TABLE `difficulty`
  ADD PRIMARY KEY (`difficulty_id`);

--
-- Klíče pro tabulku `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `group_super_group_super_group_id_fk` (`super_group_id`);

--
-- Klíče pro tabulku `group_category_rel`
--
ALTER TABLE `group_category_rel`
  ADD PRIMARY KEY (`group_id`,`category_id`),
  ADD KEY `group_category_rel_category_category_id_fk` (`category_id`);

--
-- Klíče pro tabulku `group_supergroup_rel`
--
ALTER TABLE `group_supergroup_rel`
  ADD PRIMARY KEY (`group_id`,`supergroup_id`),
  ADD KEY `group_supergroup_rel_supergroup_supergroup_id_fk` (`supergroup_id`);

--
-- Klíče pro tabulku `logo`
--
ALTER TABLE `logo`
  ADD PRIMARY KEY (`logo_id`);

--
-- Klíče pro tabulku `problem`
--
ALTER TABLE `problem`
  ADD PRIMARY KEY (`problem_id`),
  ADD KEY `problem_problem_type_id_fk` (`problem_type_id`),
  ADD KEY `difficulty_id` (`difficulty_id`),
  ADD KEY `problem_sub_category_sub_category_id_fk` (`sub_category_id`);

--
-- Klíče pro tabulku `problem_final`
--
ALTER TABLE `problem_final`
  ADD PRIMARY KEY (`problem_id`),
  ADD UNIQUE KEY `problem_final_problem_id_fk` (`problem_id`) USING BTREE;

--
-- Klíče pro tabulku `problem_prototype`
--
ALTER TABLE `problem_prototype`
  ADD PRIMARY KEY (`problem_id`);

--
-- Klíče pro tabulku `problem_test_rel`
--
ALTER TABLE `problem_test_rel`
  ADD PRIMARY KEY (`test_id`,`problem_final_id`,`variant`(1)),
  ADD KEY `problem_test_rel_problem_problem_final_id_fk` (`problem_final_id`),
  ADD KEY `problem_test_rel_problem_problem_id_fk` (`problem_prototype_id`);

--
-- Klíče pro tabulku `problem_tp_condition_tp_rel`
--
ALTER TABLE `problem_tp_condition_tp_rel`
  ADD PRIMARY KEY (`problem_type_id`,`condition_type_id`),
  ADD KEY `problem_tp_condition_tp_rel_condition_type_condition_type_id_fk` (`condition_type_id`);

--
-- Klíče pro tabulku `problem_type`
--
ALTER TABLE `problem_type`
  ADD PRIMARY KEY (`problem_type_id`);

--
-- Klíče pro tabulku `prototype_json_data`
--
ALTER TABLE `prototype_json_data`
  ADD PRIMARY KEY (`prototype_json_data_id`),
  ADD UNIQUE KEY `prototype_json_data_problem_problem_id_fk` (`problem_id`);

--
-- Klíče pro tabulku `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Klíče pro tabulku `specialization`
--
ALTER TABLE `specialization`
  ADD PRIMARY KEY (`specialization_id`);

--
-- Klíče pro tabulku `sub_category`
--
ALTER TABLE `sub_category`
  ADD PRIMARY KEY (`sub_category_id`),
  ADD KEY `sub_category_category_category_id_fk` (`category_id`);

--
-- Klíče pro tabulku `super_group`
--
ALTER TABLE `super_group`
  ADD PRIMARY KEY (`super_group_id`);

--
-- Klíče pro tabulku `super_group_category_rel`
--
ALTER TABLE `super_group_category_rel`
  ADD PRIMARY KEY (`super_group_id`,`category_id`),
  ADD KEY `super_group_category_rel_category_category_id_fk` (`category_id`);

--
-- Klíče pro tabulku `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `test_group_group_id_fk` (`group_id`),
  ADD KEY `test_logo_logo_id_fk` (`logo_id`);

--
-- Klíče pro tabulku `test_term`
--
ALTER TABLE `test_term`
  ADD PRIMARY KEY (`test_term_id`);

--
-- Klíče pro tabulku `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Klíče pro tabulku `user_group_rel`
--
ALTER TABLE `user_group_rel`
  ADD PRIMARY KEY (`user_id`,`group_id`),
  ADD KEY `user_group_rel_group_group_id_fk` (`group_id`);

--
-- Klíče pro tabulku `user_role_rel`
--
ALTER TABLE `user_role_rel`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `user_role_rel_role_role_id_fk` (`role_id`);

--
-- Klíče pro tabulku `user_super_group_rel`
--
ALTER TABLE `user_super_group_rel`
  ADD PRIMARY KEY (`user_id`,`super_group_id`),
  ADD KEY `user_super_group_rel_super_group_super_group_id_fk` (`super_group_id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `category`
--
ALTER TABLE `category`
  MODIFY `category_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pro tabulku `condition`
--
ALTER TABLE `condition`
  MODIFY `condition_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pro tabulku `condition_type`
--
ALTER TABLE `condition_type`
  MODIFY `condition_type_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `difficulty`
--
ALTER TABLE `difficulty`
  MODIFY `difficulty_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pro tabulku `group`
--
ALTER TABLE `group`
  MODIFY `group_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pro tabulku `logo`
--
ALTER TABLE `logo`
  MODIFY `logo_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT pro tabulku `problem`
--
ALTER TABLE `problem`
  MODIFY `problem_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=708;

--
-- AUTO_INCREMENT pro tabulku `problem_type`
--
ALTER TABLE `problem_type`
  MODIFY `problem_type_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pro tabulku `prototype_json_data`
--
ALTER TABLE `prototype_json_data`
  MODIFY `prototype_json_data_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=609;

--
-- AUTO_INCREMENT pro tabulku `role`
--
ALTER TABLE `role`
  MODIFY `role_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `specialization`
--
ALTER TABLE `specialization`
  MODIFY `specialization_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pro tabulku `sub_category`
--
ALTER TABLE `sub_category`
  MODIFY `sub_category_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pro tabulku `super_group`
--
ALTER TABLE `super_group`
  MODIFY `super_group_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pro tabulku `test`
--
ALTER TABLE `test`
  MODIFY `test_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pro tabulku `test_term`
--
ALTER TABLE `test_term`
  MODIFY `test_term_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `user`
--
ALTER TABLE `user`
  MODIFY `user_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `condition_problem_rel`
--
ALTER TABLE `condition_problem_rel`
  ADD CONSTRAINT `condition_problem_rel_ibfk_2` FOREIGN KEY (`condition_id`) REFERENCES `condition` (`condition_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `condition_problem_rel_ibfk_3` FOREIGN KEY (`problem_id`) REFERENCES `problem` (`problem_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `group`
--
ALTER TABLE `group`
  ADD CONSTRAINT `group_super_group_super_group_id_fk` FOREIGN KEY (`super_group_id`) REFERENCES `super_group` (`super_group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `group_category_rel`
--
ALTER TABLE `group_category_rel`
  ADD CONSTRAINT `group_category_rel_category_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `group_category_rel_group_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `group_supergroup_rel`
--
ALTER TABLE `group_supergroup_rel`
  ADD CONSTRAINT `group_supergroup_rel_group_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `group_supergroup_rel_supergroup_supergroup_id_fk` FOREIGN KEY (`supergroup_id`) REFERENCES `super_group` (`super_group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `problem`
--
ALTER TABLE `problem`
  ADD CONSTRAINT `problem_ibfk_1` FOREIGN KEY (`difficulty_id`) REFERENCES `difficulty` (`difficulty_id`),
  ADD CONSTRAINT `problem_ibfk_3` FOREIGN KEY (`problem_type_id`) REFERENCES `problem_type` (`problem_type_id`),
  ADD CONSTRAINT `problem_sub_category_sub_category_id_fk` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`sub_category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `problem_final`
--
ALTER TABLE `problem_final`
  ADD CONSTRAINT `problem_final_ibfk_1` FOREIGN KEY (`problem_id`) REFERENCES `problem` (`problem_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `problem_prototype`
--
ALTER TABLE `problem_prototype`
  ADD CONSTRAINT `problem_prototype_problem_problem_id_fk` FOREIGN KEY (`problem_id`) REFERENCES `problem` (`problem_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `problem_test_rel`
--
ALTER TABLE `problem_test_rel`
  ADD CONSTRAINT `problem_test_rel_problem_final_problem_id_fk` FOREIGN KEY (`problem_final_id`) REFERENCES `problem_final` (`problem_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `problem_test_rel_problem_problem_id_fk` FOREIGN KEY (`problem_prototype_id`) REFERENCES `problem` (`problem_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `problem_test_rel_test_test_id_fk` FOREIGN KEY (`test_id`) REFERENCES `test` (`test_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `problem_tp_condition_tp_rel`
--
ALTER TABLE `problem_tp_condition_tp_rel`
  ADD CONSTRAINT `problem_tp_condition_tp_rel_condition_type_condition_type_id_fk` FOREIGN KEY (`condition_type_id`) REFERENCES `condition_type` (`condition_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `problem_tp_condition_tp_rel_problem_type_problem_type_id_fk` FOREIGN KEY (`problem_type_id`) REFERENCES `problem_type` (`problem_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `sub_category`
--
ALTER TABLE `sub_category`
  ADD CONSTRAINT `sub_category_category_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `super_group_category_rel`
--
ALTER TABLE `super_group_category_rel`
  ADD CONSTRAINT `super_group_category_rel_category_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `super_group_category_rel_super_group_super_group_id_fk` FOREIGN KEY (`super_group_id`) REFERENCES `super_group` (`super_group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `test_group_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `test_logo_logo_id_fk` FOREIGN KEY (`logo_id`) REFERENCES `logo` (`logo_id`) ON UPDATE CASCADE;

--
-- Omezení pro tabulku `user_group_rel`
--
ALTER TABLE `user_group_rel`
  ADD CONSTRAINT `user_group_rel_group_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_group_rel_user_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `user_role_rel`
--
ALTER TABLE `user_role_rel`
  ADD CONSTRAINT `user_role_rel_role_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_role_rel_user_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `user_super_group_rel`
--
ALTER TABLE `user_super_group_rel`
  ADD CONSTRAINT `user_super_group_rel_super_group_super_group_id_fk` FOREIGN KEY (`super_group_id`) REFERENCES `super_group` (`super_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_super_group_rel_user_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
