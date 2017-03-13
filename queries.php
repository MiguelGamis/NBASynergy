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
    
    function insertShift($shift)
    {
        global $db;
        if($db)
        {
            $query = "INSERT INTO shift (playerID, gameID, starttime, endtime, home) VALUES ( $shift->playerID, $shift->gameID, $shift->starttime, $shift->endtime, $shift->isHome) ON DUPLICATE KEY UPDATE playerID = playerID;";
            $db->query($query);
        }
    }
    
    function insertGame($date, $hometeam, $awayteam)
    {
        global $db;
        if($db)
        {
            $query = "SELECT gameID FROM game WHERE date = FROM_UNIXTIME($date) AND hometeam = '$hometeam' AND awayteam = '$awayteam';";
            $result = $db->query($query);
            $row = $result->fetch();
            if($row)
            {
                $game = new Game($row['gameID']);
                $game->home = $hometeam;
                $game->away = $awayteam;
                $game->date = $date;
                return $game;
            }
            echo "not found. now inserting <br/>";
            $query2 = "INSERT INTO game (date, hometeam, awayteam) VALUES ( FROM_UNIXTIME($date), '$hometeam', '$awayteam') ON DUPLICATE KEY UPDATE date = date;";
            $db->query($query2);
            //TODO: Check if it succeeded
            $id = $db->lastInsertID();
            $game = new Game($id);
            $game->home = $hometeam;
            $game->away = $awayteam;
            $game->date = $date;
            return $game;
        }
    }
    
    function insertPlayer($firstname, $lastname, $team)
    {
        #Go and include the assignment page
        global $db;
        if($db)
        {
            $query = "INSERT INTO player (firstname, lastname, team) VALUES ('$firstname', '$lastname', '$team');";
            $db->query($query);
        }
    }

    function getPlayer($firstname, $lastname)
    {
        global $db;
        #Go and include the assignment page
        if($db)
        {
//            echo "trying to find $firstname $lastname <br/>";
//            $query = "SELECT playerID FROM players WHERE firstname = ':firstname' AND lastname = ':lastname;'";
//            $sh = $db->prepare($query);
//            $result = $sh->execute(array("firstname" => $firstname, "lastname" => $lastname));
            
            $query = "SELECT playerID FROM player WHERE firstname = '$firstname' AND lastname = '$lastname';";
            $result = $db->query($query);
            $row = $result->fetch();
            
            if($row)
            {
//                echo "found $firstname $lastname <br/>";
                $player = new player($firstname, $lastname);
                $player->playerID = $row['playerID'];
                return $player;
            }
            echo "did not find $firstname $lastname <br/>";
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
    
    function getShiftsFromGame($date, $hometeam, $awayteam, $home)
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
    
    function getPlayersFromTeam($team)
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
    
    function insertShot($shot)
    {
        global $db;
        if($db)
        {
            $query = "INSERT INTO shot (playerID, type, made, gameID, time, home) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE playerID = playerID;";
            $sh = $db->prepare($query);
            $sh->execute(array($shot->playerID, $shot->type, $shot->success, $shot->gameID, $shot->time, $shot->isHome));
        }
    }
    
    function getPlayerData($playerID)
    {
        global $db;
        $query = "SELECT * FROM player WHERE playerID = ?";
        $sh = $db->prepare($query);
        $sh->execute(array($playerID));
        $row = $sh->fetch();
        $player = new Player($row['firstname'], $row['lastname']);
        return $player;
    }
    
    function prepareQuery($name, $query)
    {
        if(!isset($this->$name)) {
            $this->$name = $this->db->prepare($query);
        }
        return $this->$name;
    }
}