-- phpMyAdmin SQL Dump
-- version 4.4.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 11, 2016 at 12:56 AM
-- Server version: 5.5.46
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `GreatDivide`
--
CREATE DATABASE IF NOT EXISTS `GreatDivide` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `GreatDivide`;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
CREATE TABLE IF NOT EXISTS `assignments` (
  `Date` varchar(14) NOT NULL DEFAULT '',
  `StartTime` varchar(12) DEFAULT NULL,
  `EndTime` varchar(12) DEFAULT NULL,
  `EventName` varchar(128) DEFAULT NULL,
  `ShiftType` tinyint(4) NOT NULL DEFAULT '0',
  `Count` tinyint(4) NOT NULL DEFAULT '0',
  `P0` varchar(7) DEFAULT NULL,
  `P1` varchar(7) DEFAULT NULL,
  `P2` varchar(7) DEFAULT NULL,
  `P3` varchar(7) DEFAULT NULL,
  `P4` varchar(7) DEFAULT NULL,
  `P5` varchar(7) DEFAULT NULL,
  `P6` varchar(7) DEFAULT NULL,
  `P7` varchar(7) DEFAULT NULL,
  `P8` varchar(7) DEFAULT NULL,
  `P9` varchar(7) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `directorsettings`
--

DROP TABLE IF EXISTS `directorsettings`;
CREATE TABLE IF NOT EXISTS `directorsettings` (
  `PatrolName` varchar(32) NOT NULL DEFAULT '',
  `emailReminder` char(1) NOT NULL DEFAULT '0',
  `reminderDays` tinyint(4) NOT NULL DEFAULT '3',
  `emailOnChanges` char(1) NOT NULL DEFAULT '0',
  `useTeams` char(1) NOT NULL DEFAULT '0',
  `directorsOnlyChange` char(1) NOT NULL DEFAULT '0',
  `emailAll` char(1) NOT NULL DEFAULT '0',
  `nameFormat` tinyint(4) NOT NULL DEFAULT '0',
  `startDate` varchar(6) DEFAULT NULL,
  `endDate` varchar(6) DEFAULT NULL,
  `useBlackOut` tinyint(4) NOT NULL DEFAULT '0',
  `startBlackOut` varchar(8) NOT NULL DEFAULT '01-02-03',
  `endBlackOut` varchar(8) NOT NULL DEFAULT '02-03-03',
  `lastSkiHistoryUpdate` int(11) NOT NULL DEFAULT '0',
  `lastVoucherHistoryUpdate` date NOT NULL DEFAULT '0000-00-00',
  `signinLockout` int(11) NOT NULL DEFAULT '0',
  `removeAccess` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `directorsettings`
--

INSERT INTO `directorsettings` (`PatrolName`, `emailReminder`, `reminderDays`, `emailOnChanges`, `useTeams`, `directorsOnlyChange`, `emailAll`, `nameFormat`, `startDate`, `endDate`, `useBlackOut`, `startBlackOut`, `endBlackOut`, `lastSkiHistoryUpdate`, `lastVoucherHistoryUpdate`, `signinLockout`, `removeAccess`) VALUES
('Great Divide', '0', 3, '0', '0', '0', '1', 0, '13-11', '29-03', 0, '01-01-15', '01-01-15', 0, '0000-00-00', 0, 127);

-- --------------------------------------------------------

--
-- Table structure for table `newindividualassignment`
--

DROP TABLE IF EXISTS `newindividualassignment`;
CREATE TABLE IF NOT EXISTS `newindividualassignment` (
  `date_shift_pos` varchar(18) DEFAULT NULL,
  `scheduledate` date DEFAULT NULL,
  `shiftType` tinyint(4) NOT NULL,
  `flags` smallint(6) NOT NULL,
  `patrollerId` varchar(6) NOT NULL,
  `lastModifiedDate` datetime DEFAULT NULL,
  `lastModifiedBy` varchar(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `roster`
--

DROP TABLE IF EXISTS `roster`;
CREATE TABLE IF NOT EXISTS `roster` (
  `IDNumber` varchar(6) NOT NULL DEFAULT '',
  `ClassificationCode` varchar(4) DEFAULT NULL,
  `LastName` varchar(24) NOT NULL DEFAULT '',
  `FirstName` varchar(24) NOT NULL DEFAULT '',
  `Spouse` varchar(24) DEFAULT '',
  `Address` varchar(48) DEFAULT '',
  `City` varchar(32) DEFAULT NULL,
  `State` varchar(16) DEFAULT NULL,
  `ZipCode` varchar(10) DEFAULT NULL,
  `HomePhone` varchar(26) DEFAULT NULL,
  `WorkPhone` varchar(26) DEFAULT '',
  `CellPhone` varchar(26) DEFAULT '',
  `Pager` varchar(26) DEFAULT '',
  `email` varchar(48) DEFAULT '',
  `EmergencyCallUp` varchar(8) DEFAULT '',
  `Password` varchar(16) NOT NULL DEFAULT '',
  `NightSubsitute` varchar(4) DEFAULT '',
  `Commitment` tinyint(4) NOT NULL DEFAULT '2',
  `Instructor` tinyint(4) NOT NULL DEFAULT '0',
  `Director` varchar(10) DEFAULT 'no',
  `lastUpdated` date NOT NULL DEFAULT '2003-01-01',
  `carryOverCredits` smallint(6) NOT NULL DEFAULT '0',
  `lastCreditUpdate` bigint(11) NOT NULL DEFAULT '0',
  `canEarnCredits` tinyint(4) NOT NULL DEFAULT '0',
  `creditsEarned` smallint(6) NOT NULL DEFAULT '0',
  `creditsUsed` smallint(6) NOT NULL DEFAULT '0',
  `teamLead` tinyint(4) NOT NULL DEFAULT '0',
  `mentoring` tinyint(4) NOT NULL DEFAULT '0',
  `comments` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `sessionId` text NOT NULL,
  `authenticatedUserId` varchar(6) NOT NULL,
  `resort` varchar(36) NOT NULL,
  `sessionCreateTime` date NOT NULL,
  `sessionLastAccessTime` date NOT NULL,
  `sessionIpAddress` varchar(64) NOT NULL,
  `isDirector` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `shiftdefinitions`
--

DROP TABLE IF EXISTS `shiftdefinitions`;
CREATE TABLE IF NOT EXISTS `shiftdefinitions` (
  `EventName` varchar(128) NOT NULL DEFAULT '',
  `StartTime` varchar(16) NOT NULL DEFAULT '',
  `EndTime` varchar(16) NOT NULL DEFAULT '',
  `Count` tinyint(4) NOT NULL DEFAULT '0',
  `ShiftType` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `shiftdefinitions`
--
--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`Date`);

--
-- Indexes for table `directorsettings`
--
ALTER TABLE `directorsettings`
  ADD PRIMARY KEY (`PatrolName`);

--
-- Indexes for table `newindividualassignment`
--
ALTER TABLE `newindividualassignment`
  ADD UNIQUE KEY `date_index_pos` (`date_shift_pos`);

--
-- Indexes for table `roster`
--
ALTER TABLE `roster`
  ADD PRIMARY KEY (`IDNumber`);

--
-- Indexes for table `shiftdefinitions`
--
ALTER TABLE `shiftdefinitions`
  ADD PRIMARY KEY (`EventName`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
