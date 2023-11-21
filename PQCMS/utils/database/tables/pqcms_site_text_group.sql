-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 07 Lip 2023, 20:57
-- Wersja serwera: 10.4.19-MariaDB
-- Wersja PHP: 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Baza danych: `electrocms`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `site_group`
--

CREATE TABLE IF NOT EXISTS `pqcms_site_text_group` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT 'Nazwa grupy'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `site_group`
--
ALTER TABLE `pqcms_site_text_group`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `site_group`
--
ALTER TABLE `pqcms_site_text_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
