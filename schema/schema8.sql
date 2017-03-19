SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE IF NOT EXISTS nbasynergy;

USE nbasynergy;

CREATE TABLE IF NOT EXISTS `player` (
  `playerID` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(128) NOT NULL,
  `lastname` varchar(128) NOT NULL,
  `team` varchar(3),
  PRIMARY KEY (`playerID`),
  UNIQUE KEY `fullName` (`firstName`, `lastName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `shot` (
  `shotID` int(11) NOT NULL AUTO_INCREMENT,
  `playerID` int(11) NOT NULL,
  `type` varchar(128) NOT NULL,
  `made` tinyint(1) NOT NULL DEFAULT '0',
  `gameID` int(11) NOT NULL,
  `lineID` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `home` tinyint(1) NOT NULL,
  `distance` int(11),
  `shotclock` int(11),
  PRIMARY KEY (`shotID`),
  UNIQUE KEY `gameline` (`gameID`, `lineID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `freethrow` (
  `shotID` int(11) NOT NULL,
  `freethrowbatchID` int(11) NOT NULL,
  `seq` tinyint(1) NOT NULL,
  PRIMARY KEY (`shotID`),
  UNIQUE KEY `freethrowseq` (`freethrowbatchID`, `seq`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `freethrowbatch` (
  `freethrowbatchID` int(11) NOT NULL,
  `foulID` int(11) NOT NULL,
  `total` tinyint(1) NOT NULL,
  PRIMARY KEY (`freethrowbatchID`),
  UNIQUE KEY (`foulID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `foul` (
  `foulID` int(11) NOT NULL AUTO_INCREMENT,
  `shotID` int(11),
  `foulerID` int(11) NOT NULL,
  `type` varchar(128) NOT NULL,
  `referee` varchar(128),
  `home` tinyint(1) NOT NULL,
  `gameID` int(11) NOT NULL,
  `lineID` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`foulID`),
  UNIQUE KEY (`shotID`),
  UNIQUE KEY `gameline` (`gameID`, `lineID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `assist` (
  `assistID` int(11) NOT NULL AUTO_INCREMENT,
  `playerID` int(11) NOT NULL,
  `shotID` int(11) NOT NULL,
  PRIMARY KEY (`assistID`),
  UNIQUE KEY `shotassisted` (`shotID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rebound` (
  `reboundID` int(11) NOT NULL AUTO_INCREMENT,
  `playerID` int(11) NOT NULL,
  `shotID` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`reboundID`),
  UNIQUE KEY `shotrebounded` (`shotID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `game` (
  `gameID` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `hometeam` varchar(3) NOT NULL,
  `awayteam` varchar(3) NOT NULL,
  UNIQUE KEY `homeawayGame` (`date`, `hometeam`, `awayteam`),
  PRIMARY KEY (`gameID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `shift` (
  `shiftID` int(11) NOT NULL AUTO_INCREMENT,
  `playerID` int(11) NOT NULL,
  `gameID` int(11) NOT NULL,
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `home` tinyint(1) NOT NULL,
  PRIMARY KEY (`shiftID`),
  UNIQUE KEY `playerID` (`playerID`, `gameID`, `starttime`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `team` (
  `shortName` varchar(3) NOT NULL,
  `city` varchar(128) NOT NULL,
  `teamName` varchar(128) NOT NULL,
  PRIMARY KEY (`shortName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Constraints for table `player`
--
ALTER TABLE `player` 
	ADD CONSTRAINT `player_ibfk_1` FOREIGN KEY (`team`) REFERENCES `team` (`shortName`) ON DELETE CASCADE;

--
-- Constraints for table `shot`
--
ALTER TABLE `shot` 
	ADD CONSTRAINT `shot_ibfk_1` FOREIGN KEY (`playerID`) REFERENCES `player` (`playerID`) ON DELETE CASCADE;
ALTER TABLE `shot` 
	ADD CONSTRAINT `shot_ibfk_2` FOREIGN KEY (`gameID`) REFERENCES `game` (`gameID`) ON DELETE CASCADE;

--
-- Constraints for table `freethrow`
--
ALTER TABLE `freethrow` 
	ADD CONSTRAINT `freethrow_ibfk_1` FOREIGN KEY (`shotID`) REFERENCES `shot` (`shotID`) ON DELETE CASCADE;

--
-- Constraints for table `freethrowbatch`
--
ALTER TABLE `freethrowbatch`
	ADD CONSTRAINT `freethrowbatch_ibfk_1` FOREIGN KEY (`foulID`) REFERENCES `foul` (`foulID`) ON DELETE CASCADE;	
	
--
-- Constraints for table `assist`
--
ALTER TABLE `assist`
	ADD CONSTRAINT `assist_ibfk_1` FOREIGN KEY (`playerID`) REFERENCES `player` (`playerID`) ON DELETE CASCADE;
ALTER TABLE `assist` 
	ADD CONSTRAINT `assist_ibfk_2` FOREIGN KEY (`shotID`) REFERENCES `shot` (`shotID`) ON DELETE CASCADE;

--
-- Constraints for table `rebound`
--
ALTER TABLE `rebound` 
	ADD CONSTRAINT `rebound_ibfk_1` FOREIGN KEY (`playerID`) REFERENCES `player` (`playerID`) ON DELETE CASCADE;
ALTER TABLE `rebound`
	ADD CONSTRAINT `rebound_ibfk_2` FOREIGN KEY (`shotID`) REFERENCES `shot` (`shotID`) ON DELETE CASCADE;
	
--
-- Constraints for table `foul`
--
ALTER TABLE `foul` 
	ADD CONSTRAINT `foul_ibfk_1` FOREIGN KEY (`shotID`) REFERENCES `shot` (`shotID`) ON DELETE CASCADE;
ALTER TABLE `foul` 
	ADD CONSTRAINT `foul_ibfk_2` FOREIGN KEY (`foulerID`) REFERENCES `player` (`playerID`) ON DELETE CASCADE;
ALTER TABLE `foul` 
	ADD CONSTRAINT `foul_ibfk_4` FOREIGN KEY (`gameID`) REFERENCES `game` (`gameID`) ON DELETE CASCADE;

--
-- Constraints for table `shift`
--
ALTER TABLE `shift`
  ADD CONSTRAINT `shift_ibfk_1` FOREIGN KEY (`playerID`) REFERENCES `player` (`playerID`) ON DELETE CASCADE;
ALTER TABLE `shift`
  ADD CONSTRAINT `shift_ibfk_2` FOREIGN KEY (`gameID`) REFERENCES `game` (`gameID`) ON DELETE CASCADE;

--
-- Constraints for table `game`
--
ALTER TABLE `game`
  ADD CONSTRAINT `game_ibfk_1` FOREIGN KEY (`hometeam`) REFERENCES `team` (`shortName`) ON DELETE CASCADE;
ALTER TABLE `game`
  ADD CONSTRAINT `game_ibfk_2` FOREIGN KEY (`awayteam`) REFERENCES `team` (`shortName`) ON DELETE CASCADE;

INSERT INTO `team` (shortName, city, teamName) VALUES
  ('BOS', 'Boston', 'Celtics' ),
  ('PHX', 'Phoenix', 'Suns'),
  ('DEN', 'Denver', 'Nuggets'),
  ('CHA', 'Charlotte', 'Hornets'),
  ('MEM', 'Memphis', 'Grizzlies'),
  ('LAL', 'Los Angeles', 'Lakers'),
  ('LAC', 'Los Angeles', 'Clippers'),
  ('OKC', 'Oklahoma City', 'Thunder'),
  ('MIA', 'Miami', 'Heat'),
  ('CHI', 'Chicago', 'Bulls' ),
  ('TOR', 'Toronto', 'Raptors' ),
  ('WAS', 'Washington', 'Wizards'),
  ('GSW', 'Golden State', 'Warriors'),
  ('HOU', 'Houston', 'Rockets'),
  ('DAL', 'Dallas', 'Mavericks'),
  ('SAS', 'San Antonio', 'Spurs' ),
  ('NOP', 'New Orleans', 'Pelicans'),
  ('NYK', 'New York', 'Knicks'),
  ('SAC', 'Sacramento', 'Kings'),
  ('PHI', 'Philadelphia', '76ers'),
  ('POR', 'Portland', 'Trailblazers'),
  ('BKN', 'Brooklyn', 'Nets'),
  ('IND', 'Indiana', 'Pacers'),
  ('DET', 'Detroit', 'Pistons' ),
  ('MIL', 'Milwaukee', 'Bucks' ),
  ('MIN', 'Minnesota', 'Timberwolves'),
  ('ATL', 'Atlanta', 'Hawks'),
  ('UTA', 'Utah', 'Jazz'),
  ('ORL', 'Orlando', 'Magic'),
  ('CLE', 'Cleveland', 'Cavaliers');

