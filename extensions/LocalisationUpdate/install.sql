-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 18 Mei 2009 om 12:45
-- Server versie: 5.0.51
-- PHP Versie: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `mw15`
--

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `localisation`
--

CREATE TABLE IF NOT EXISTS `localisation` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `identifier` varchar(2048) collate utf8_bin NOT NULL,
  `language` varchar(2048) collate utf8_bin NOT NULL,
  `value` varchar(2048) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=13858 ;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `localisation_file_hash`
--

CREATE TABLE IF NOT EXISTS `localisation_file_hash` (
  `file` varchar(250) NOT NULL,
  `hash` varchar(50) NOT NULL,
  UNIQUE KEY `file` (`file`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
