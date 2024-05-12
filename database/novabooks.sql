-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2024. Máj 12. 17:09
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `novabooks`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `bank`
--

CREATE TABLE `bank` (
  `ceg_adoszam` varchar(13) NOT NULL,
  `bankszamlaszam` varchar(26) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `bank`
--

INSERT INTO `bank` (`ceg_adoszam`, `bankszamlaszam`) VALUES
('12554896-2-25', '22548781-45684512-00000000'),
('12554896-2-25', '22568974-22156638-00024877');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ceg`
--

CREATE TABLE `ceg` (
  `adoszam` varchar(13) NOT NULL,
  `nev` varchar(150) NOT NULL,
  `elerhetoseg` varchar(150) NOT NULL,
  `cim` varchar(250) NOT NULL,
  `afabevallas` varchar(150) NOT NULL,
  `felhasznalo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `ceg`
--

INSERT INTO `ceg` (`adoszam`, `nev`, `elerhetoseg`, `cim`, `afabevallas`, `felhasznalo_id`) VALUES
('12345678-2-22', 'GreenTech Innovations Kft.', '06305467895', '1172,Budapest,Kacsa utca 23', 'negyedeves', 1),
('12554896-2-25', 'Alma Kft', '0630/5628-963', '6723,Szeged,Csaba utca 36.', 'havi', 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `egyeb`
--

CREATE TABLE `egyeb` (
  `id` int(11) NOT NULL,
  `megnevezes` varchar(150) NOT NULL,
  `megjegyzes` varchar(250) NOT NULL,
  `ceg_adoszam` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `egyeb`
--

INSERT INTO `egyeb` (`id`, `megnevezes`, `megjegyzes`, `ceg_adoszam`) VALUES
(8, 'kamerák üzembe helyezése', '', '12554896-2-25'),
(9, 'kamerák beszerelési ktg.', '', '12554896-2-25'),
(10, '5db MSI G2412F LED monitor aktiválás', '', '12554896-2-25'),
(11, 'bér 01', '', '12554896-2-25'),
(12, 'bér 02', '', '12554896-2-25'),
(13, 'bér 03', '', '12554896-2-25'),
(14, 'bank nyitás', '', '12554896-2-25');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `felhasznalo`
--

CREATE TABLE `felhasznalo` (
  `id` int(11) NOT NULL,
  `nev` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `jelszo` varchar(150) NOT NULL,
  `utolso_belepes` date NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `felhasznalo`
--

INSERT INTO `felhasznalo` (`id`, `nev`, `email`, `jelszo`, `utolso_belepes`, `admin`, `aktiv`) VALUES
(1, 'Barna Bea', 'barnabea@novabooks.com', '$2y$10$7sid5lbbJK6eGXI4g694oe5Ava52C2e9BfvmTKqZYVPqiwvrXYZse', '2024-05-07', 1, 1),
(2, 'teszt név', 'tesztnev@novabooks.com', '$2y$10$B.e5vnEeK5PP55N7KH.4HevrAXdHdQafacM1uI9MIi8U5Kz70Nlx2', '2024-04-13', 0, 0),
(4, 'teszt név3', 'tesztnev3@novabooks.com', '$2y$10$4mb0/nF2lnQYZYM7H6lgNOEC.HuM7clC.xYuNLCMX8iw3Fwk.NvEu', '2024-05-04', 0, 0);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `konyvelesi_tetel`
--

CREATE TABLE `konyvelesi_tetel` (
  `id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `tartozik` int(11) NOT NULL,
  `kovetel` int(11) NOT NULL,
  `osszeg` int(11) NOT NULL,
  `bank_szamlaszam` varchar(26) DEFAULT NULL,
  `penztar_id` int(11) DEFAULT NULL,
  `szamla_szamlaszam` varchar(150) DEFAULT NULL,
  `egyeb_id` int(11) DEFAULT NULL,
  `felhasznalo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `konyvelesi_tetel`
--

INSERT INTO `konyvelesi_tetel` (`id`, `datum`, `tartozik`, `kovetel`, `osszeg`, `bank_szamlaszam`, `penztar_id`, `szamla_szamlaszam`, `egyeb_id`, `felhasznalo_id`) VALUES
(238, '2023-01-28', 527, 454, 7998, NULL, NULL, '2023/12335897E', NULL, 1),
(239, '2023-01-28', 466, 454, 1072, NULL, NULL, '2023/12335897E', NULL, 1),
(246, '2023-02-28', 527, 454, 7998, NULL, NULL, '2023/25559632E', NULL, 1),
(247, '2023-02-28', 466, 454, 1072, NULL, NULL, '2023/25559632E', NULL, 1),
(248, '2023-03-28', 527, 454, 7998, NULL, NULL, '2023/32251128E', NULL, 1),
(249, '2023-03-28', 466, 454, 1072, NULL, NULL, '2023/32251128E', NULL, 1),
(250, '2023-04-28', 527, 454, 7998, NULL, NULL, '2023/42213391E', NULL, 1),
(251, '2023-04-28', 466, 454, 1072, NULL, NULL, '2023/42213391E', NULL, 1),
(254, '2023-06-28', 527, 454, 7998, NULL, NULL, '2023/63325194E', NULL, 1),
(255, '2023-06-28', 466, 454, 1072, NULL, NULL, '2023/63325194E', NULL, 1),
(258, '2023-08-28', 527, 454, 7998, NULL, NULL, '2023/85263971 E', NULL, 1),
(259, '2023-08-28', 466, 454, 1072, NULL, NULL, '2023/85263971 E', NULL, 1),
(262, '2023-01-10', 161, 455, 110960, NULL, NULL, '2023/00025', NULL, 1),
(263, '2023-01-10', 466, 455, 41040, NULL, NULL, '2023/00025', NULL, 1),
(264, '2023-01-10', 143, 161, 110960, NULL, NULL, NULL, 8, 1),
(265, '2023-01-10', 161, 455, 16790, NULL, NULL, '2023/00026', NULL, 1),
(266, '2023-01-10', 466, 455, 6210, NULL, NULL, '2023/00026', NULL, 1),
(267, '2023-01-10', 143, 161, 16790, NULL, NULL, NULL, 9, 1),
(268, '2023-02-13', 161, 455, 153300, NULL, NULL, 'MM/2023/25587999', NULL, 1),
(269, '2023-02-13', 466, 455, 56700, NULL, NULL, 'MM/2023/25587999', NULL, 1),
(270, '2023-02-15', 143, 161, 153300, NULL, NULL, NULL, 10, 1),
(271, '2023-01-10', 311, 911, 3363, NULL, NULL, 'VE/2023-91358', NULL, 1),
(272, '2023-01-10', 311, 467, 908, NULL, NULL, 'VE/2023-91358', NULL, 1),
(273, '2023-02-11', 311, 911, 5628, NULL, NULL, 'VE/2023-91359', NULL, 1),
(274, '2023-02-11', 311, 467, 1520, NULL, NULL, 'VE/2023-91359', NULL, 1),
(275, '2023-03-11', 311, 911, 35990, NULL, NULL, 'GE/2023-91360', NULL, 1),
(276, '2023-03-11', 311, 467, 9717, NULL, NULL, 'GE/2023-91360', NULL, 1),
(277, '2023-04-10', 311, 911, 16693, NULL, NULL, 'VE/2023-9136l', NULL, 1),
(278, '2023-04-10', 311, 467, 4507, NULL, NULL, 'VE/2023-9136l', NULL, 1),
(279, '2023-05-15', 311, 911, 26925, NULL, NULL, 'VE/2023-91362', NULL, 1),
(280, '2023-05-15', 311, 467, 7270, NULL, NULL, 'VE/2023-91362', NULL, 1),
(281, '2023-06-10', 311, 911, 25100, NULL, NULL, 'VE/2023-91363', NULL, 1),
(282, '2023-06-10', 311, 467, 6777, NULL, NULL, 'VE/2023-91363', NULL, 1),
(283, '2023-10-25', 311, 911, 9048, NULL, NULL, 'VE/2023-91364', NULL, 1),
(284, '2023-10-25', 311, 467, 2443, NULL, NULL, 'VE/2023-91364', NULL, 1),
(289, '2024-01-03', 523, 454, 31025, NULL, NULL, '5122693/2024', NULL, 1),
(290, '2024-01-03', 466, 454, 11475, NULL, NULL, '5122693/2024', NULL, 1),
(295, '2024-02-03', 161, 455, 45196, NULL, NULL, 'SZA/2024-5554789', NULL, 1),
(296, '2024-02-03', 466, 455, 16716, NULL, NULL, 'SZA/2024-5554789', NULL, 1),
(329, '2023-12-10', 381, 911, 233790, NULL, NULL, 'VE/2023-91365', NULL, 1),
(330, '2023-12-10', 381, 467, 63123, NULL, NULL, 'VE/2023-91365', NULL, 1),
(331, '2023-12-12', 381, 911, 34375, NULL, NULL, 'VE/2023-91366', NULL, 1),
(332, '2023-12-12', 381, 467, 9281, NULL, NULL, 'VE/2023-91366', NULL, 1),
(333, '2023-12-15', 381, 911, 26874, NULL, NULL, 'VE/2023-91367', NULL, 1),
(334, '2023-12-15', 381, 467, 7256, NULL, NULL, 'VE/2023-91367', NULL, 1),
(338, '2023-01-15', 454, 384, 152000, '22568974-22156638-00024877', NULL, '2023/00025', NULL, 1),
(339, '2023-01-15', 454, 384, 23000, '22568974-22156638-00024877', NULL, '2023/00026', NULL, 1),
(340, '2023-01-28', 454, 384, 9000, '22568974-22156638-00024877', NULL, '2023/12335897E', NULL, 1),
(341, '2023-01-31', 54, 471, 500000, NULL, NULL, NULL, 11, 1),
(342, '2023-01-31', 471, 462, 120000, NULL, NULL, NULL, 11, 1),
(343, '2023-01-31', 471, 463, 25000, NULL, NULL, NULL, 11, 1),
(344, '2023-01-31', 56, 463, 5000, NULL, NULL, NULL, 11, 1),
(345, '2023-02-28', 54, 471, 500000, NULL, NULL, NULL, 12, 1),
(346, '2023-02-28', 471, 462, 120000, NULL, NULL, NULL, 12, 1),
(347, '2023-02-28', 471, 463, 15000, NULL, NULL, NULL, 12, 1),
(348, '2023-02-28', 56, 463, 20000, NULL, NULL, NULL, 12, 1),
(349, '2023-03-31', 54, 471, 500000, NULL, NULL, NULL, 13, 1),
(350, '2023-03-31', 471, 462, 12000, NULL, NULL, NULL, 13, 1),
(351, '2023-03-31', 471, 463, 15000, NULL, NULL, NULL, 13, 1),
(352, '2023-03-31', 471, 463, 20000, NULL, NULL, NULL, 13, 1),
(353, '2023-03-02', 381, 389, 577601, NULL, 9, NULL, NULL, 1),
(354, '2023-01-01', 384, 491, 1155202, NULL, NULL, NULL, 14, 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `partner`
--

CREATE TABLE `partner` (
  `adoszam` varchar(13) NOT NULL,
  `nev` varchar(250) NOT NULL,
  `vevo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `partner`
--

INSERT INTO `partner` (`adoszam`, `nev`, `vevo`) VALUES
('12175128-2-44', 'DYGY Távközlési és Szolg. KFT', 0),
('12223548-2-26', 'KristályTiszta Takarítás Kft.', 1),
('12248879-2-26', 'Szupergyors Logisztika Zrt.', 1),
('12554879-2-26', 'TisztaForrás Víztechnika Kft.', 0),
('12554897-2-26', 'MesterDesign Dekor Zrt.', 0),
('14478598-2-26', 'ÖkoGarden Kertészet Kft.', 1),
('14487698-2-26', 'ÉletStílus Wellness Kft.', 0),
('15487458-2-44', 'Media Márk', 0),
('15526287-2-26', 'ŰrTech Kutatásfejlesztési Zrt.', 0),
('15528996-2-26', 'GyorsNet Kommunikáció Zrt.', 0),
('15587968-2-26', 'BioÉtel Gasztronómia Kft.', 0),
('15887996-2-26', 'ElektroMobil Járműtechnika Kft.', 1),
('17784457-2-26', 'KódVarázs Szoftverfejlesztés Zrt.', 1),
('17784579-2-26', 'Precíz Pénzügyi Tanácsadás Kft.', 1),
('19558779-2-26', 'AranyKéz Építészeti Stúdió Zrt.', 1),
('19966359-2-26', 'VízióVonal Marketing Kft.', 1),
('19968579-2-26', 'BolygóBarát Bioüzemanyag Zrt.', 1),
('19986587-2-26', 'Új Horizont Oktatás Zrt.', 1),
('25487984-2-26', 'Virág Kft', 0),
('32554189-2-26', 'Okos Otthon Rendszerek Zrt.', 0),
('55478159-2-26', 'Zöldenergia Technológiák Zrt.', 0),
('65147854-2-26', 'Építőipari Csúcs Kft.', 0),
('66358978-2-26', 'Innovatív Megoldások Kft.', 0);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `penztar`
--

CREATE TABLE `penztar` (
  `id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `megjegyzes` varchar(150) NOT NULL,
  `ceg_adoszam` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `penztar`
--

INSERT INTO `penztar` (`id`, `datum`, `megjegyzes`, `ceg_adoszam`) VALUES
(9, '2023-03-02', '', '12554896-2-25');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `sablon`
--

CREATE TABLE `sablon` (
  `megnevezes` varchar(150) NOT NULL,
  `tartozik` int(11) NOT NULL,
  `kovetel` int(11) NOT NULL,
  `felhasznalo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `sablon`
--

INSERT INTO `sablon` (`megnevezes`, `tartozik`, `kovetel`, `felhasznalo_id`) VALUES
('net', 527, 454, 1),
('szállító_áfa', 466, 454, 1),
('vevő', 311, 911, 1),
('vevő_áfa', 311, 467, 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `szamla`
--

CREATE TABLE `szamla` (
  `szamlaszam` varchar(150) NOT NULL,
  `teljesites` date NOT NULL,
  `fizhat` date NOT NULL,
  `kiallitas` date NOT NULL,
  `partner_adoszam` varchar(13) NOT NULL,
  `penztar` tinyint(1) NOT NULL COMMENT 'pénztárból rendezve?',
  `megjegyzes` varchar(250) NOT NULL,
  `pdf` varchar(250) DEFAULT NULL,
  `fizetve` tinyint(1) NOT NULL COMMENT 'rendezve?',
  `ceg_adoszam` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `szamla`
--

INSERT INTO `szamla` (`szamlaszam`, `teljesites`, `fizhat`, `kiallitas`, `partner_adoszam`, `penztar`, `megjegyzes`, `pdf`, `fizetve`, `ceg_adoszam`) VALUES
('2023/00025', '2023-01-10', '2023-02-01', '2023-01-02', '32554189-2-26', 0, 'kamerák', NULL, 1, '12554896-2-25'),
('2023/00026', '2023-01-10', '2023-01-23', '2023-01-10', '32554189-2-26', 0, 'kamerák beszerelése', NULL, 1, '12554896-2-25'),
('2023/12335897E', '2023-01-28', '2023-02-28', '2023-01-06', '12175128-2-44', 0, '', '../invoices/szallito/12554896-2-25/net1.pdf', 0, '12554896-2-25'),
('2023/25559632E', '2023-02-28', '2023-03-28', '2023-02-06', '12175128-2-44', 0, '', '../invoices/szallito/12554896-2-25/net2.pdf', 0, '12554896-2-25'),
('2023/32251128E', '2023-03-28', '2023-04-28', '2023-03-06', '12175128-2-44', 0, '', '../invoices/szallito/12554896-2-25/net3.pdf', 0, '12554896-2-25'),
('2023/42213391E', '2023-04-28', '2023-05-28', '2023-04-06', '12175128-2-44', 0, '', '../invoices/szallito/12554896-2-25/net4.pdf', 0, '12554896-2-25'),
('2023/63325194E', '2023-06-28', '2023-07-28', '2023-06-06', '12175128-2-44', 0, '', '../invoices/szallito/12554896-2-25/net6.pdf', 0, '12554896-2-25'),
('2023/85263971 E', '2023-08-28', '2023-09-28', '2023-08-06', '12175128-2-44', 0, '', '../invoices/szallito/12554896-2-25/net8.pdf', 0, '12554896-2-25'),
('5122693/2024', '2024-01-03', '2024-01-15', '2024-01-05', '15587968-2-26', 0, '', NULL, 0, '12554896-2-25'),
('GE/2023-91360', '2023-03-11', '2023-03-28', '2023-03-06', '15887996-2-26', 0, '', '../invoices/vevo/12554896-2-25/03.pdf', 0, '12554896-2-25'),
('MM/2023/25587999', '2023-02-13', '2023-02-25', '2023-02-12', '15487458-2-44', 0, '5db MSI G2412F LED monitor', NULL, 0, '12554896-2-25'),
('SZA/2024-5554789', '2024-02-03', '2024-02-15', '2024-02-01', '32554189-2-26', 0, '', NULL, 0, '12554896-2-25'),
('VE/2023-91358', '2023-01-10', '2023-01-20', '2023-01-05', '19558779-2-26', 0, '', '../invoices/vevo/12554896-2-25/01.pdf', 0, '12554896-2-25'),
('VE/2023-91359', '2023-02-11', '2023-02-25', '2023-02-05', '19968579-2-26', 0, '', '../invoices/vevo/12554896-2-25/02.pdf', 0, '12554896-2-25'),
('VE/2023-91362', '2023-05-15', '2023-05-20', '2023-05-10', '12223548-2-26', 0, '', '../invoices/vevo/12554896-2-25/05.pdf', 0, '12554896-2-25'),
('VE/2023-91363', '2023-06-10', '2023-06-25', '2023-06-06', '14478598-2-26', 0, '', '../invoices/vevo/12554896-2-25/06.pdf', 0, '12554896-2-25'),
('VE/2023-91364', '2023-10-25', '2023-10-30', '2023-10-21', '17784579-2-26', 0, '', '../invoices/vevo/12554896-2-25/07.pdf', 0, '12554896-2-25'),
('VE/2023-91365', '2023-12-10', '2023-12-10', '2023-12-10', '12248879-2-26', 1, '', '../invoices/penztar/12554896-2-25/08.pdf', 1, '12554896-2-25'),
('VE/2023-91366', '2023-12-12', '2023-12-15', '2023-12-15', '19986587-2-26', 1, '', '../invoices/penztar/12554896-2-25/09.pdf', 1, '12554896-2-25'),
('VE/2023-91367', '2023-12-15', '2023-12-15', '2023-12-15', '19966359-2-26', 1, '', '../invoices/penztar/12554896-2-25/10.pdf', 1, '12554896-2-25'),
('VE/2023-9136l', '2023-04-10', '2023-04-20', '2023-04-03', '17784457-2-26', 0, '', '../invoices/vevo/12554896-2-25/04.pdf', 0, '12554896-2-25');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `targyi_eszkoz`
--

CREATE TABLE `targyi_eszkoz` (
  `id` int(11) NOT NULL,
  `megnevezes` varchar(150) NOT NULL,
  `bekerulesi_ertek` int(11) NOT NULL,
  `ertekcsokkenes` int(11) NOT NULL,
  `megjegyzes` varchar(150) DEFAULT NULL,
  `hasznalati_ido` int(2) DEFAULT NULL,
  `szamla_szamlaszam` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `targyi_eszkoz`
--

INSERT INTO `targyi_eszkoz` (`id`, `megnevezes`, `bekerulesi_ertek`, `ertekcsokkenes`, `megjegyzes`, `hasznalati_ido`, `szamla_szamlaszam`) VALUES
(20, 'kamerák', 110960, 0, 'eszköz', 1, '2023/00025'),
(21, 'kamerák', 16790, 0, 'beszerelési ktg.', NULL, '2023/00026'),
(22, '5db MSI G2412F LED monitor 30666/db', 153330, 0, 'eszköz', 1, 'MM/2023/25587999'),
(23, 'Amazon Echo Dot 5th Gen', 45196, 0, 'eszköz', 1, 'SZA/2024-5554789');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`bankszamlaszam`),
  ADD KEY `bank_ceg` (`ceg_adoszam`);

--
-- A tábla indexei `ceg`
--
ALTER TABLE `ceg`
  ADD PRIMARY KEY (`adoszam`),
  ADD KEY `ugyfel_felhasznalo` (`felhasznalo_id`);

--
-- A tábla indexei `egyeb`
--
ALTER TABLE `egyeb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `egyeb_ceg` (`ceg_adoszam`);

--
-- A tábla indexei `felhasznalo`
--
ALTER TABLE `felhasznalo`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `konyvelesi_tetel`
--
ALTER TABLE `konyvelesi_tetel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `konyv_bank` (`bank_szamlaszam`),
  ADD KEY `konyv_egyeb` (`egyeb_id`),
  ADD KEY `konyv_felhaszn` (`felhasznalo_id`),
  ADD KEY `konyv_penztar` (`penztar_id`),
  ADD KEY `konyv_szamla` (`szamla_szamlaszam`);

--
-- A tábla indexei `partner`
--
ALTER TABLE `partner`
  ADD PRIMARY KEY (`adoszam`);

--
-- A tábla indexei `penztar`
--
ALTER TABLE `penztar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penztar_ceg` (`ceg_adoszam`);

--
-- A tábla indexei `sablon`
--
ALTER TABLE `sablon`
  ADD PRIMARY KEY (`megnevezes`),
  ADD KEY `sablon_felhasznalo` (`felhasznalo_id`);

--
-- A tábla indexei `szamla`
--
ALTER TABLE `szamla`
  ADD PRIMARY KEY (`szamlaszam`),
  ADD KEY `szamla_partner` (`partner_adoszam`),
  ADD KEY `szamla_ceg` (`ceg_adoszam`);

--
-- A tábla indexei `targyi_eszkoz`
--
ALTER TABLE `targyi_eszkoz`
  ADD PRIMARY KEY (`id`),
  ADD KEY `targyi_szamla` (`szamla_szamlaszam`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `egyeb`
--
ALTER TABLE `egyeb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT a táblához `felhasznalo`
--
ALTER TABLE `felhasznalo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT a táblához `konyvelesi_tetel`
--
ALTER TABLE `konyvelesi_tetel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=355;

--
-- AUTO_INCREMENT a táblához `penztar`
--
ALTER TABLE `penztar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT a táblához `targyi_eszkoz`
--
ALTER TABLE `targyi_eszkoz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `bank`
--
ALTER TABLE `bank`
  ADD CONSTRAINT `bank_ceg` FOREIGN KEY (`ceg_adoszam`) REFERENCES `ceg` (`adoszam`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `ceg`
--
ALTER TABLE `ceg`
  ADD CONSTRAINT `ugyfel_felhasznalo` FOREIGN KEY (`felhasznalo_id`) REFERENCES `felhasznalo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `egyeb`
--
ALTER TABLE `egyeb`
  ADD CONSTRAINT `egyeb_ceg` FOREIGN KEY (`ceg_adoszam`) REFERENCES `ceg` (`adoszam`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `konyvelesi_tetel`
--
ALTER TABLE `konyvelesi_tetel`
  ADD CONSTRAINT `konyv_bank` FOREIGN KEY (`bank_szamlaszam`) REFERENCES `bank` (`bankszamlaszam`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `konyv_egyeb` FOREIGN KEY (`egyeb_id`) REFERENCES `egyeb` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `konyv_felhaszn` FOREIGN KEY (`felhasznalo_id`) REFERENCES `felhasznalo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `konyv_penztar` FOREIGN KEY (`penztar_id`) REFERENCES `penztar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `konyv_szamla` FOREIGN KEY (`szamla_szamlaszam`) REFERENCES `szamla` (`szamlaszam`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `sablon`
--
ALTER TABLE `sablon`
  ADD CONSTRAINT `sablon_felhasznalo` FOREIGN KEY (`felhasznalo_id`) REFERENCES `felhasznalo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `szamla`
--
ALTER TABLE `szamla`
  ADD CONSTRAINT `szamla_ceg` FOREIGN KEY (`ceg_adoszam`) REFERENCES `ceg` (`adoszam`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `szamla_partner` FOREIGN KEY (`partner_adoszam`) REFERENCES `partner` (`adoszam`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `targyi_eszkoz`
--
ALTER TABLE `targyi_eszkoz`
  ADD CONSTRAINT `targyi_szamla` FOREIGN KEY (`szamla_szamlaszam`) REFERENCES `szamla` (`szamlaszam`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
