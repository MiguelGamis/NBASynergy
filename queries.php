<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('config.php');
require_once("classes.php");

class DataManager
{
    private $database;
    
    function __construct() {
        global $db;
        $database = $db;
    }
    
    static function insertShift($shift)
    {
        global $db;
        if($db)
        {
            $query = "INSERT IGNORE INTO shift (playerID, gameID, starttime, endtime, home) VALUES (?, ?, ?, ?, ?);";
            $sh = $db->prepare($query);
            $result = $sh->execute(array($shift->playerID, $shift->gameID, $shift->starttime, $shift->endtime, $shift->isHome));
            if(!$result)
            {
                echo "Error: Something went wrong inserting a shift: ".$sh->errorCode();
                exit();
            }
        }
    }
    
    static function insertGame(&$game)
    {
        global $db;
        $query = "INSERT IGNORE INTO game (gameID, date, hometeam, awayteam) VALUES (?, FROM_UNIXTIME(?), ?, ?);";
        $sh = $db->prepare($query);
        $result = $sh->execute(array($game->gameID, $game->date, $game->home, $game->away));
        if(!$result)
        {
            echo "Error: Something went wrong inserting a game: ".$sh->errorCode();
            exit();
        }
    }
    
    static function selectGame($date, $home, $away)
    {
        global $db;
        $query = "SELECT gameID FROM game WHERE date = FROM_UNIXTIME(?) AND hometeam = ? AND awayteam = ?;";
        $sh = $db->prepare($query);
        $sh->execute(array($date, $home, $away));
        $row = $sh->fetch();
        if($row)
        {
            $game = new Game($date, $home, $away);
            $game->gameID = $row['gameID'];
            return $game;
        }
    }
    
    static function getTeam($abbrev)
    {
        global $db;
        
        $query = "SELECT * FROM team WHERE shortName = ?;";
        $sh = $db->prepare($query);
        $sh->execute(array($abbrev));
        $result = $sh->fetch();
        if(!$result)
        {
            echo "Error: Could not find team from abbreviation '$abbrev'";
            exit();
        }
        $team = new Team($result['shortName'], $result['city'], $result['teamName']);
        return $team;
    }
    
    static function insertPlayer($playerID, $firstname, $lastname, $team)
    {
        #Go and include the assignment page
        global $db;
        if($db)
        {
            $query = "INSERT INTO player (playerID, firstname, lastname, team) VALUES (?, ?, ?, ?);";
            $sh = $db->prepare($query);
            $sh->execute(array($playerID, $firstname, $lastname, $team));
            $player = new Player($playerID, $firstname, $lastname, $team);
            return $player;
        }
    }

    static function getPlayer($playerID)
    {
        global $db;
        #Go and include the assignment page
        if($db)
        {
            $query = "SELECT * FROM player WHERE playerID = ?";
            $sh = $db->prepare($query);
            $sh->execute(array($playerID));
            $row = $sh->fetch();
            
            if($row)
            {
                $player = new Player($row['firstname'], $row['lastname'], $row['playerID'], $row['team']);
                return $player;
            }
        }
    }

    static function getPlayerByID($id)
    {
        global $db;
        if($db)
        {
            $query = "SELECT * FROM player WHERE playerID = ?;";
            $sh = $db->prepare($query);
            $sh->execute(array($id));
            $row = $sh->fetch();
            $player = new player($row['firstname'], $row['lastname']);
            $player->playerID = $id;
            return $player;
        }
    }
    
    static function insertShot(&$shot, $lineID)
    {
        global $db;
        $query = "INSERT IGNORE INTO shot (playerID, type, made, gameID, lineID, time, home, distance, shotclock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $sh = $db->prepare($query);
        $result = $sh->execute(array($shot->playerID, $shot->type, $shot->success, $shot->gameID, $lineID, $shot->time, $shot->isHome, $shot->distance, $shot->shotclock));
        if(!$result)
        {
            echo "Error: Something went wrong inserting a shot at line $lineID";
            exit();
        }
        $shotID = $db->lastInsertID();
        if($shotID)
        {
            $shot->shotID = $shotID;
        }
        else
        {
            $query = "SELECT shotID FROM shot WHERE gameID = ? AND lineID = ?";
            $sh = $db->prepare($query);
            $result = $sh->execute(array($shot->gameID, $lineID));
            $row = $sh->fetch();
            if($row)
            {
                $shot->shotID = $row['shotID'];
            }
        }
    }
    
    static function insertFreeThrow(&$freethrow, $lineID)
    {
        global $db;
        if($db)
        {
            $query = "INSERT IGNORE INTO shot (playerID, type, made, gameID, lineID, time, home, distance, shotclock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
            $sh = $db->prepare($query);
            $result = $sh->execute(array($freethrow->playerID, $freethrow->type, $freethrow->success, $freethrow->gameID, $lineID, $freethrow->time, $freethrow->isHome, $freethrow->distance, $freethrow->shotclock));
            $freethrow->shotID = $db->lastInsertID();
            if(!$result)
            {
                echo "Error: Something went wrong when insert a free throw";
                exit();
            }
            
            $query2 = "INSERT IGNORE INTO freethrow (shotID, foultype, seq, total) VALUES (?, ?, ?, ?);";
            $sh2 = $db->prepare($query2);
            $result2 = $sh2->execute(array($freethrow->shotID, $freethrow->foultype, $freethrow->seq, $freethrow->total));
            if(!$result2)
            {
                echo "Error: Something went wrong inserting a free throw at line $lineID: ".$sh2->errorCode();
                exit();
            }
        }
    }
    
    static function insertFoul(&$foul, $lineID)
    {
        global $db;
        if($db)
        {
            $query = "INSERT IGNORE INTO foul (foulerID, type, referee, gameID, lineID, time) VALUES (?, ?, ?, ?, ?, ?);";
            $sh = $db->prepare($query);
            $result = $sh->execute(array($foul->foulerID, $foul->type, $foul->referee, $foul->gameID, $lineID, $foul->time));
            if(!$result)
            {
                echo "Error: Something went wrong inserting a foul at line $lineID: ".$sh->errorCode();
                exit();
            }
            $foul->foulID = $db->lastInsertID();
        }
    }
    
    static function insertAssist($assist, $lineID)
    {
        global $db;
        $query = "INSERT IGNORE INTO assist (playerID, shotID) VALUES (?, ?);";
        $sh = $db->prepare($query);
        $result = $sh->execute(array($assist->playerID, $assist->shotID));
        if(!$result)
        {
            echo "Error: Something went wrong inserting an assist at line $lineID: ".$sh->errorCode();
            exit();
        }
    }
    
    static function insertRebound($rebound, $lineID)
    {
        global $db;
        $query = "INSERT IGNORE INTO rebound (playerID, gameID, lineID, time, offensive) VALUES (?, ?, ?, ?, ?);";
        $sh = $db->prepare($query);
        $result = $sh->execute(array($rebound->playerID, $rebound->gameID, $lineID, $rebound->time, $rebound->offensive));
        if(!$result)
        {
            echo "Error: Something went wrong inserting a rebound at line $lineID: ".$sh->errorCode();
            exit();
        }
    }
    
    static function insertTurnover(&$turnover, $lineID)
    {
        global $db;
        $query = "INSERT IGNORE INTO turnover (playerID, gameID, lineID, time, type) VALUES (?, ?, ?, ?, ?);";
        $sh = $db->prepare($query);
        $result = $sh->execute(array($turnover->playerID, $turnover->gameID, $lineID, $turnover->time, $turnover->type));
        if(!$result)
        {
            echo "Error: Something went wrong inserting a turnover at line $lineID: ".$sh->errorCode();
            exit();
        }
        $turnoverID = $db->lastInsertID();
        if($turnoverID)
        {
            $turnover->turnoverID = $turnoverID;
        }
        else
        {
            $query = "SELECT turnoverID FROM turnover WHERE gameID = ? AND lineID = ?";
            $sh = $db->prepare($query);
            $result = $sh->execute(array($turnover->gameID, $lineID));
            $row = $sh->fetch();
            if($row)
            {
                $turnover->shotID = $row['turnoverID'];
            }
        }
    }
    
    static function insertSteal($steal, $lineID)
    {
        global $db;
        $query = "INSERT IGNORE INTO steal (playerID, turnoverID) VALUES (?, ?);";
        $sh = $db->prepare($query);
        $result = $sh->execute(array($steal->playerID, $steal->turnoverID));
        if(!$result)
        {
            echo "Error: Something went wrong inserting a steal at line $lineID: ".$sh->errorCode();
            exit();
        }
    }
    
    static function insertBlock($block, $lineID)
    {
        global $db;
        $query = "INSERT IGNORE INTO block (playerID, shotID) VALUES (?, ?);";
        $sh = $db->prepare($query);
        $result = $sh->execute(array($block->playerID, $block->shotID));
        if(!$result)
        {
            echo "Error: Something went wrong inserting a block at line $lineID: ".$sh->errorCode();
            exit();
        }
    }
    
    static function getShiftsFromGame($date, $awayteam, $hometeam, $home)
    {
        global $db;
        $query = "SELECT * FROM shift JOIN (SELECT gameID FROM game WHERE date = FROM_UNIXTIME(?) AND awayteam = ? AND hometeam = ?) specificgame ON specificgame.gameID = shift.gameID WHERE home = ?";
        $sh = $db->prepare($query);
        $homebit = $home ? 1 : 0;
        $sh->execute(array($date, $awayteam, $hometeam, $homebit));
        $shifts = array();
        while($res = $sh->fetch())
        {
            $shift = new Shift($res['playerID'], $res['gameID'], $res['starttime'], boolval($res['home']), $res['endtime']);
            if(!array_key_exists($res['playerID'], $shifts))
            {
                $playershifts = new stdClass();
                $playershifts->player = DataManager::getPlayerByID($res['playerID']);
                $playershifts->shifts = array();
                $shifts[$res['playerID']] = $playershifts;
            }
            $shifts[$res['playerID']]->shifts[] = $shift; 
        }
        return array_values($shifts);
    }
    
    static function getShiftsFromGameById($gameID, $home)
    {
        global $db;
        $query = "SELECT * FROM shift JOIN (SELECT gameID FROM game WHERE gameID = ?) specificgame ON specificgame.gameID = shift.gameID WHERE home = ?";
        $sh = $db->prepare($query);
        $homebit = $home ? 1 : 0;
        $sh->execute(array($gameID, $homebit));
        $shifts = array();
        while($res = $sh->fetch())
        {
            $shift = new Shift($res['playerID'], $res['gameID'], $res['starttime'], boolval($res['home']), $res['endtime']);
            if(!array_key_exists($res['playerID'], $shifts))
            {
                $playershifts = new stdClass();
                $playershifts->player = DataManager::getPlayerByID($res['playerID']);
                $playershifts->shifts = array();
                $shifts[$res['playerID']] = $playershifts;
            }
            $shifts[$res['playerID']]->shifts[] = $shift; 
        }
        return array_values($shifts);
    }
    
        static function getPlayersFromTeam($team)
    {
        global $db;
        $query = "SELECT * FROM player WHERE team = ?;";
        $sh = $db->prepare($query);
        $args = array($team);
        $sh->execute($args);
        $players = array();
        while($res = $sh->fetch())
        {
            $player = new Player($res['firstname'], $res['lastname']);
            $player->playerID = $res['playerID'];
            $players[] = $player;
        }
        return $players;
    }
    
    static function getPlayersFromGame($gameID)
    {
        global $db;
        $query = "SELECT * FROM player JOIN (SELECT * FROM shift WHERE gameID = ?) gameshift ON player.playerID = gameshift.playerID ORDER BY gameshift.home";
        $sh = $db->prepare($query);
        $args = array($team);
        $sh->execute($args);
        $players = array();
        while($res = $sh->fetch())
        {
            $player = new Player($res['firstname'], $res['lastname']);
            $player->playerID = $res['playerID'];
            $players[] = $player;
        }
        return $players;
    }
    
    static function prepareQuery($name, $query)
    {
        if(!isset($this->$name)) {
            $this->$name = $this->db->prepare($query);
        }
        return $this->$name;
    }
}