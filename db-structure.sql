-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Aug 03, 2013 at 05:23 PM
-- Server version: 5.1.66
-- PHP Version: 5.3.3-7+squeeze15

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `androidDev`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_additional_information`
--

CREATE TABLE IF NOT EXISTS `app_additional_information` (
  `additional_information_id` int(11) NOT NULL AUTO_INCREMENT,
  `additional_information_entry_id` int(11) NOT NULL,
  `additional_information_content` blob NOT NULL,
  `additional_information_type` text NOT NULL,
  PRIMARY KEY (`additional_information_id`),
  KEY `fk_additional_information_entry_id` (`additional_information_entry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `app_additional_information`
--


-- --------------------------------------------------------

--
-- Table structure for table `app_addresses`
--

CREATE TABLE IF NOT EXISTS `app_addresses` (
  `addresses_id` int(11) NOT NULL AUTO_INCREMENT,
  `addresses_street` text NOT NULL,
  `addresses_street_number` text NOT NULL,
  `addresses_zipcode` int(11) NOT NULL,
  `addresses_city` text NOT NULL,
  `addresses_country` int(11) NOT NULL,
  `addresses_entry_id` int(11) NOT NULL,
  PRIMARY KEY (`addresses_id`),
  KEY `ix_addresses_entry_id` (`addresses_entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;


-- --------------------------------------------------------

--
-- Table structure for table `app_category`
--

CREATE TABLE IF NOT EXISTS `app_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(256) COLLATE utf8_bin NOT NULL,
  `category_parent` int(11) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Dumping data for table `app_category`
--


-- --------------------------------------------------------

--
-- Table structure for table `app_countries`
--

CREATE TABLE IF NOT EXISTS `app_countries` (
  `countries_id` int(11) NOT NULL AUTO_INCREMENT,
  `countries_name` text NOT NULL,
  `countries_country_code` text NOT NULL,
  `countries_language_name` text NOT NULL,
  PRIMARY KEY (`countries_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `app_countries`
--


-- --------------------------------------------------------

--
-- Table structure for table `app_entry`
--

CREATE TABLE IF NOT EXISTS `app_entry` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_title` varchar(128) COLLATE utf8_bin NOT NULL,
  `entry_description` text COLLATE utf8_bin NOT NULL,
  `entry_latitude` double NOT NULL,
  `entry_longitude` double NOT NULL,
  `entry_category` int(11) NOT NULL,
  `entry_address` int(11) DEFAULT NULL,
  PRIMARY KEY (`entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=27 ;

--
-- Dumping data for table `app_entry`
--


-- --------------------------------------------------------

--
-- Table structure for table `app_file_types`
--

CREATE TABLE IF NOT EXISTS `app_file_types` (
  `file_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_type_name` text NOT NULL,
  PRIMARY KEY (`file_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `app_file_types`
--

INSERT INTO `app_file_types` (`file_type_id`, `file_type_name`) VALUES
(1, 'image');

-- --------------------------------------------------------

--
-- Table structure for table `app_labels`
--

CREATE TABLE IF NOT EXISTS `app_labels` (
  `label_id` int(11) NOT NULL AUTO_INCREMENT,
  `label_category_id` int(11) NOT NULL,
  `label_entry_id` int(11) NOT NULL,
  PRIMARY KEY (`label_id`),
  KEY `ix_label_category_id` (`label_category_id`),
  KEY `ix_label_entry_id` (`label_entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41 ;

--
-- Dumping data for table `app_labels`
--


-- --------------------------------------------------------

--
-- Table structure for table `app_sources`
--

CREATE TABLE IF NOT EXISTS `app_sources` (
  `sources_id` int(11) NOT NULL AUTO_INCREMENT,
  `sources_link` varchar(256) COLLATE utf8_bin NOT NULL,
  `sources_parent` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`sources_id`),
  KEY `ix_sources_parent` (`sources_parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=13 ;

--
-- Dumping data for table `app_sources`
--


-- --------------------------------------------------------

--
-- Table structure for table `app_user`
--

CREATE TABLE IF NOT EXISTS `app_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `iser_email` varchar(256) NOT NULL,
  `user_name` varchar(32) NOT NULL,
  `user_pwd` varchar(32) NOT NULL,
  `user_sid` varchar(40) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `app_user`
--

--
-- Constraints for dumped tables
--

--
-- Constraints for table `app_additional_information`
--
ALTER TABLE `app_additional_information`
  ADD CONSTRAINT `fk_additional_information_entry_id` FOREIGN KEY (`additional_information_entry_id`) REFERENCES `app_entry` (`entry_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `app_addresses`
--
ALTER TABLE `app_addresses`
  ADD CONSTRAINT `app_addresses_ibfk_1` FOREIGN KEY (`addresses_entry_id`) REFERENCES `app_entry` (`entry_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `app_labels`
--
ALTER TABLE `app_labels`
  ADD CONSTRAINT `fk_label_category_id` FOREIGN KEY (`label_category_id`) REFERENCES `app_category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_label_entry_id` FOREIGN KEY (`label_entry_id`) REFERENCES `app_entry` (`entry_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `app_sources`
--
ALTER TABLE `app_sources`
  ADD CONSTRAINT `fk_sources_parent` FOREIGN KEY (`sources_parent`) REFERENCES `app_entry` (`entry_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
