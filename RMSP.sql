# phpMyAdmin SQL Dump
# version 2.5.3
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Jan 30, 2007 at 01:34 PM
# Server version: 4.0.18
# PHP Version: 4.3.5
#
# Database : `RMSP`
#

# --------------------------------------------------------

#
# Table structure for table `assignments`
#

CREATE TABLE `assignments` (
  `Date` varchar(14) NOT NULL default '',
  `StartTime` varchar(12) default NULL,
  `EndTime` varchar(12) default NULL,
  `EventName` varchar(128) default NULL,
  `ShiftType` tinyint(4) NOT NULL default '0',
  `Count` tinyint(4) NOT NULL default '0',
  `P0` varchar(7) default NULL,
  `P1` varchar(7) default NULL,
  `P2` varchar(7) default NULL,
  `P3` varchar(7) default NULL,
  `P4` varchar(7) default NULL,
  `P5` varchar(7) default NULL,
  `P6` varchar(7) default NULL,
  `P7` varchar(7) default NULL,
  `P8` varchar(7) default NULL,
  `P9` varchar(7) default NULL,
  PRIMARY KEY  (`Date`)
) TYPE=MyISAM;

#
# Dumping data for table `assignments`
#


# --------------------------------------------------------

#
# Table structure for table `directorsettings`
#

CREATE TABLE `directorsettings` (
  `PatrolName` varchar(32) NOT NULL default '',
  `emailReminder` char(1) NOT NULL default '0',
  `reminderDays` tinyint(4) NOT NULL default '3',
  `emailOnChanges` char(1) NOT NULL default '0',
  `useTeams` char(1) NOT NULL default '0',
  `directorsOnlyChange` char(1) NOT NULL default '0',
  `emailAll` char(1) NOT NULL default '0',
  `nameFormat` tinyint(4) NOT NULL default '0',
  `startDate` varchar(6) default NULL,
  `endDate` varchar(6) default NULL,
  `useBlackOut` tinyint(4) NOT NULL default '0',
  `startBlackOut` varchar(8) NOT NULL default '01-02-03',
  `endBlackOut` varchar(8) NOT NULL default '02-03-03',
  `lastSkiHistoryUpdate` int(11) NOT NULL default '0',
  `lastVoucherHistoryUpdate` date NOT NULL default '0000-00-00',
  `signinLockout` int(11) NOT NULL default '0',
  `removeAccess` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`PatrolName`)
) TYPE=MyISAM;

#
# Dumping data for table `directorsettings`
#

INSERT INTO `directorsettings` VALUES ('Ragged Mountain', '1', 2, '1', '0', '0', '0', 0, '23-11', '05-04', 0, '01-02-05', '03-04-05', 0, '0000-00-00', 0, 0);

# --------------------------------------------------------

#
# Table structure for table `roster`
#

CREATE TABLE `roster` (
  `IDNumber` varchar(6) NOT NULL default '',
  `ClassificationCode` varchar(4) default NULL,
  `LastName` varchar(24) NOT NULL default '',
  `FirstName` varchar(24) NOT NULL default '',
  `Spouse` varchar(24) default NULL,
  `Address` varchar(48) default NULL,
  `City` varchar(32) default NULL,
  `State` varchar(16) default NULL,
  `ZipCode` varchar(10) default NULL,
  `HomePhone` varchar(26) default NULL,
  `WorkPhone` varchar(26) default NULL,
  `CellPhone` varchar(26) default NULL,
  `Pager` varchar(26) default NULL,
  `email` varchar(48) default NULL,
  `EmergencyCallUp` varchar(8) default NULL,
  `Password` varchar(16) NOT NULL default '',
  `NightSubsitute` varchar(4) default NULL,
  `Commitment` tinyint(4) NOT NULL default '2',
  `Instructor` tinyint(4) NOT NULL default '0',
  `Director` varchar(10) default NULL,
  `lastUpdated` date NOT NULL default '2003-01-01',
  `carryOverCredits` smallint(6) NOT NULL default '0',
  `lastCreditUpdate` bigint(11) NOT NULL default '0',
  `canEarnCredits` tinyint(4) NOT NULL default '0',
  `creditsEarned` smallint(6) NOT NULL default '0',
  `creditsUsed` smallint(6) NOT NULL default '0',
  `teamLead` tinyint(4) NOT NULL default '0',
  `mentoring` tinyint(4) NOT NULL default '0',
  `comments` text NOT NULL,
  PRIMARY KEY  (`IDNumber`)
) TYPE=MyISAM;

#
# Dumping data for table `roster`
#


# --------------------------------------------------------

#
# Table structure for table `shiftdefinitions`
#

CREATE TABLE `shiftdefinitions` (
  `EventName` varchar(128) NOT NULL default '',
  `StartTime` varchar(16) NOT NULL default '',
  `EndTime` varchar(16) NOT NULL default '',
  `Count` tinyint(4) NOT NULL default '0',
  `ShiftType` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`EventName`)
) TYPE=MyISAM;

#
# Dumping data for table `shiftdefinitions`
#

