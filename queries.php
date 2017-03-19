<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('config.php');

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
            $query = "INSERT IGNORE INTO shift (playerID, gameID, starttime, endtime, home) VALUES ( $shift->playerID, $shift->gameID, $shift->starttime, $shift->endtime, $shift->isHome);";
            $db->query($query);
        }
    }
    
    static function insertGame(&$game)
    {
        global $db;
        if($db)
        {
            $query = "SELECT gameID FROM game WHERE date = FROM_UNIXTIME(?) AND hometeam = ? AND awayteam = ?;";
            $sh = $db->prepare($query);
            $sh->execute(array($game->date, $game->home, $game->away));
            $row = $sh->fetch();
            if($row)
            {
                $game->gameID = intval($row['gameID']);
                return;
            }
            $query2 = "INSERT IGNORE INTO game (date, hometeam, awayteam) VALUES ( FROM_UNIXTIME(?), ?, ?);";
            $sh2 = $db->prepare($query2);
            $sh2->execute(array($game->date, $game->home, $game->away));
            //TODO: Check if it succeeded
            $game->gameID = intval($db->lastInsertID());
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
            echo "Error: Could not find team from abbreviation '$abbrev";
            exit();
        }
        $team = new Team($result['shortName'], $result['city'], $result['teamName']);
        return $team;
    }
    
    static function insertPlayer($firstname, $lastname, $team)
    {
        #Go and include the assignment page
        global $db;
        if($db)
        {
            $query = "INSERT INTO player (firstname, lastname, team) VALUES (?, ?, ?);";
            $sh = $db->prepare($query);
            $sh->execute(array(trim($firstname), trim($lastname), $team));
        }
    }

    static function getPlayer($firstname, $lastname)
    {
        global $db;
        #Go and include the assignment page
        if($db)
        {
            //echo "trying to find $firstname $lastname <br/>";
            $query = "SELECT playerID FROM player WHERE firstname = ? AND lastname = ?";
            $sh = $db->prepare($query);
            $sh->execute(array(trim($firstname), trim($lastname)));
            $row = $sh->fetch();
            
            if($row)
            {
                //echo "found $firstname $lastname <br/>";
                $player = new player($firstname, $lastname);
                $player->playerID = $row['playerID'];
                return $player;
            }
            //echo "did not find $firstname $lastname : $query <br/>";
            
        }
    }

    function getPlayerByID($id)
    {
        global $db;
        if($db)
        {
            $query = "SELECT * FROM player WHERE playerID = ?;";
            $sh = $db->prepare($query);
            $sh->execute(array($id));
            $row = $sh->fetch();
            $player = new player($row['firstName'], $row['lastName']);
            $player->playerID = $id;
            return $player;
        }
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
            $player = new player($res['firstName'], $res['lastName']);
            $player->playerID = $res['playerID'];
            $players[] = $player;
        }
        return $players;
    }
    
    static function insertShot(&$shot, $lineID)
    {
        global $db;
        if($db)
        {
            $query = "INSERT IGNORE INTO shot (playerID, type, made, gameID, lineID, time, home, distance, shotclock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
            $sh = $db->prepare($query);
            $result = $sh->execute(array($shot->playerID, $shot->type, $shot->success, $shot->gameID, $lineID, $shot->time, $shot->isHome, $shot->distance, $shot->shotclock));
            if(!$result)
            {
                echo "Error: Something went wrong inserting a shot at line $lineID";
                exit();
            }
            
            $shot->shotID = intval($db->lastInsertID());
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
            $freethrow->shotID = intval($db->lastInsertID());
            if(!$result)
            {
                echo "Error: Something went wrong when insert a free throw";
                exit();
            }
            
            $query2 = "INSERT IGNORE INTO freethrow (shotID, foulID, seq, total) VALUES (?, ?, ?, ?);";
            $sh2 = $db->prepare($query2);
            var_export(array($freethrow->shotID, $freethrow->foulID, $freethrow->seq, $freethrow->total));
            $result2 = $sh2->execute(array($freethrow->shotID, $freethrow->foulID, $freethrow->seq, $freethrow->total));
            if(!$result2)
            {
                echo "Error: Something went wrong inserting a free throw at line $lineID";
                exit();
            }
        }
    }
    
    static function getShotByLineID($gameID, $lineID)
    {
        global $db;
        if($db)
        {
            $query = "SELECT shotID FROM shot WHERE gameID = ? AND lineID = ?;";
            $sh = $db->prepare($query);
            $sh->execute(array($gameID, $lineID));
            $row = $sh->fetch();
            if($row)
            {
                return $row['shotID'];
            }
        }
    }
    
    static function getLatestFoulByTeam($gameID, $lineID, $isHome)
    {
        global $db;
        if($db)
        {
            $query = "SELECT * FROM foul WHERE gameID = ? AND home = ? ";
            $sh = $db->prepare($query);
            $sh->execute(array($gameID, $lineID, $isHome));
            $row = $sh->fetch();
            if($row)
            {
                return $row['shotID'];
            }
        }
    }
    
    static function insertFoul(&$foul, $lineID)
    {
        global $db;
        if($db)
        {
            $query = "INSERT IGNORE INTO foul (shotID, foulerID, type, referee, gameID, lineID, time) VALUES (?, ?, ?, ?, ?, ?, ?);";
            $sh = $db->prepare($query);
            var_dump(array($foul->shotID, $foul->foulerID, $foul->type, $foul->referee, $foul->gameID, $lineID, $foul->time));
            $result = $sh->execute(array($foul->shotID, $foul->foulerID, $foul->type, $foul->referee, $foul->gameID, $lineID, $foul->time));
            if(!$result)
            {
                echo "Error: Something went wrong inserting a foul at line $lineID";
                exit();
            }
            $foul->shotID = intval($db->lastInsertID());
        }
    }
    
    //INSERT INTO foul (shotID, foulerID, type, referee, gameID, time) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE playerID = playerID;
    static function getPlayerData($playerID)
    {
        global $db;
        $query = "SELECT * FROM player WHERE playerID = ?";
        $sh = $db->prepare($query);
        $sh->execute(array($playerID));
        $row = $sh->fetch();
        $player = new Player($row['firstname'], $row['lastname']);
        return $player;
    }
    
    static function getShiftsFromGame($date, $hometeam, $awayteam, $home)
    {
        global $db;
        $query = "SELECT * FROM shift JOIN (SELECT gameID FROM game WHERE date = FROM_UNIXTIME($date) AND hometeam = '$hometeam' AND awayteam = '$awayteam') specificgame ON specificgame.gameID = shift.gameID WHERE home = $home";
        $sh = $db->query($query);
        $shifts = array();
        while($res = $sh->fetch())
        {
            $shift = new shift;
            $shift->playerID = $res['playerID'];
            $shift->shiftID = $res['shiftID'];
            $shift->gameID = $res['gameID'];
            $shift->starttime = $res['starttime'];
            $shift->endtime = $res['endtime'];
            $shift->isHome = $res['home'];
            if(!array_key_exists($res['playerID'], $shifts))
            {
                $playershifts = new stdClass();
                $playershifts->player = self::getPlayerByID($res['playerID']);
                $playershifts->shifts = array();
                $shifts[$res['playerID']] = $playershifts;
            }
            $shifts[$res['playerID']]->shifts[] = $shift; 
        }
        return array_values($shifts);
    }
    
    static function prepareQuery($name, $query)
    {
        if(!isset($this->$name)) {
            $this->$name = $this->db->prepare($query);
        }
        return $this->$name;
    }
}