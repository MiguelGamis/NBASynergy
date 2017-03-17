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
  `firstName` varchar(128) NOT NULL,
  `lastName` varchar(128) NOT NULL,
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
  `time` int(11) NOT NULL,
  `home` tinyint(1) NOT NULL,
  `distance` int(11) NOT NULL,
  `shotclock` int(11) NOT NULL,
  PRIMARY KEY (`shotID`),
  UNIQUE KEY `playershot` (`playerID`, `gameID`, `time`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `assist` (
  `assistID` int(11) NOT NULL AUTO_INCREMENT,
  `playerID` int(11) NOT NULL,
  `shotID` int(11) NOT NULL,
  PRIMARY KEY (`assistID`),
  UNIQUE KEY `shotassisted` (`shotID`)
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
-- Constraints for table `shot`
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
-- Constraints for table `shift`
--
ALTER TABLE `shift`
  ADD CONSTRAINT `shift_ibfk_1` FOREIGN KEY (`playerID`) REFERENCES `player` (`playerID`) ON DELETE CASCADE;
ALTER TABLE `shift`
  ADD CONSTRAINT `shift_ibfk_2` FOREIGN KEY (`gameID`) REFERENCES `game` (`gameID`) ON DELETE CASCADE;

--
-- Constraints for table `playerteam`
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

