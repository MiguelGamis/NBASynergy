SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE nbasynergy;

USE nbasynergy;

CREATE TABLE IF NOT EXISTS `player` (
  `playerID` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(128) NOT NULL,
  `lastName` varchar(128) NOT NULL,
  PRIMARY KEY (`playerID`),
  UNIQUE KEY `fullName` (`firstName`, `lastName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `shot` (
  `shotID` int(11) NOT NULL AUTO_INCREMENT,
  `playerID` int(11) NOT NULL,
  `type` enum('shot','layup', 'reverse layup', 'jump', 'floating jump', 'fadeaway jump','3PT', 'dunk', 'driving dunk') NOT NULL,
  `made` tinyint(1) NOT NULL DEFAULT '0',
  `gameID` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`shotID`),
  KEY `playerID` (`playerID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `game` (
  `gameID` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  PRIMARY KEY (`gameID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `shift` (
  `shiftID` int(11) NOT NULL AUTO_INCREMENT,
  `playerID` int(11) NOT NULL,
  `gameID` int(11) NOT NULL,
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  PRIMARY KEY (`shiftID`),
  KEY `playerID` (`playerID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `team` (
  `teamID` int(11) NOT NULL AUTO_INCREMENT,
  `teamName` varchar(128) NOT NULL,
  PRIMARY KEY (`teamID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `playerteam` (
  `playerteamID` int(11) NOT NULL AUTO_INCREMENT,
  `playerID` int(11) NOT NULL,
  `teamID` int(11) NOT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  PRIMARY KEY (`playerteamID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Constraints for table `shot`
--
ALTER TABLE `shot` 
	ADD CONSTRAINT `shot_ibfk_1` FOREIGN KEY (`playerID`) REFERENCES `player` (`playerID`) ON DELETE CASCADE;
ALTER TABLE `shot` 
	ADD CONSTRAINT `shot_ibfk_2` FOREIGN KEY (`gameID`) REFERENCES `game` (`gameID`) ON DELETE CASCADE;
	
--
-- Constraints for table `shift`
--
ALTER TABLE `shift`
  ADD CONSTRAINT `shift_ibfk_1` FOREIGN KEY (`playerID`) REFERENCES `player` (`playerID`) ON DELETE CASCADE;
ALTER TABLE `shift`
  ADD CONSTRAINT `shift_ibfk_2` FOREIGN KEY (`game`) REFERENCES `game` (`gameID`) ON DELETE CASCADE;
  
--
-- Constraints for table `playerteam`
--
ALTER TABLE `playerteam`
  ADD CONSTRAINT `playerteam_ibfk_1` FOREIGN KEY (`playerID`) REFERENCES `player` (`playerID`) ON DELETE CASCADE;
ALTER TABLE `playerteam`
  ADD CONSTRAINT `playerteam_ibfk_2` FOREIGN KEY (`teamID``) REFERENCES `team` (`teamID`) ON DELETE CASCADE;
  
  
--SELECT * FROM player WHERE playerid = {} 

--recency query
--SELECT * FROM shot JOIN (SELECT gameID FROM game WHERE date >= '2013-01-03' AND date <= '2013-01-09') WHERE game.gameID = shot.gameID AND shot.playerID == {}; 

--matchup query
--(SELECT * FROM shot WHERE shot.playerID == {}) JOIN (SELECT * FROM shift WHERE playerID == {}) ON gameID WHERE shot.time >= shift.start AND shot.time <= shift.end

