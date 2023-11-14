-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 30 Cze 2023, 16:29
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
-- Struktura tabeli dla tabeli `login_bans_ip`
--

CREATE TABLE IF NOT EXISTS `login_bans_ip` (
  `id` int(11) NOT NULL,
  `ip` varchar(39) NOT NULL,
  `blocked` timestamp DEFAULT CURRENT_TIMESTAMP COMMENT 'Data zablokowania'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `login_bans_ip`
--
ALTER TABLE `login_bans_ip`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `login_bans_ip`
--
ALTER TABLE `login_bans_ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
