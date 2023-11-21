-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 07 Lip 2023, 20:59
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
-- Struktura tabeli dla tabeli `site_text`
--

CREATE TABLE IF NOT EXISTS `pqcms_site_text` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `group` int(11) DEFAULT NULL COMMENT 'Grupa, do której należy pole',
  `value` text DEFAULT NULL COMMENT 'Tekst pola'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `site_text`
--
ALTER TABLE `pqcms_site_text`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `site_text`
--
ALTER TABLE `pqcms_site_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
