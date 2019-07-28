-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2018 at 06:15 PM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dru`
--
CREATE DATABASE IF NOT EXISTS `dru` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `dru`;

-- --------------------------------------------------------

--
-- Table structure for table `adm_config`
--

DROP TABLE IF EXISTS `adm_config`;
CREATE TABLE `adm_config` (
  `config_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `config_value` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `adm_config`
--

INSERT INTO `adm_config` (`config_key`, `config_value`) VALUES
('allowRestores', 'true'),
('allowArchives', 'true'),
('apiKeyDIVA', 'dc62fa7b-aeb9-4984-bc3f-d62388379737'),
('restorePaths', '[]'),
('groupPermissions', '[]'),
('userPermissions', '[]'),
('ldapConfig', '[{\"ldap_host\":\"dc.example.com\",\"ldap_dn\":\"DC=example,DC=com\",\"ldap_domain\":\"@example.com\"}]'),
('loginToSearch', 'true'),
('localAdmin', 'true'),
('divaConfig', '[{"diva_wsdl":"http://diva.example.com:9763/services/DIVArchiveWS_SOAP_2.1?wsdl","diva_endpoint":"http://diva.example.com:9763/services/DIVArchiveWS_SOAP_2.1.DIVArchiveWSHttpSoap11Endpoint/"}]');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `session_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `session_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(15) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_tracker`
--

DROP TABLE IF EXISTS `site_tracker`;
CREATE TABLE `site_tracker` (
  `id` int(11) NOT NULL,
  `visit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_visited` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `page_referer` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `submitted_data` text COLLATE utf8_unicode_ci NOT NULL,
  `browser` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip_address` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `user` tinytext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `site_tracker`
--
ALTER TABLE `site_tracker`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `site_tracker`
--
ALTER TABLE `site_tracker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2252;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
