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
            $game = new Game($row['gameID'], $date, $home, $away);
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
            $sh->execute(array($playerID, ucfirst($firstname), ucfirst($lastname), $team));
            $player = new Player($playerID, ucfirst($firstname), ucfirst($lastname), $team);
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
        $query = "INSERT IGNORE INTO shot (playerID, type, made, gameID, lineID, time, home) VALUES (?, ?, ?, ?, ?, ?, ?);";
        $sh = $db->prepare($query);
        $result = $sh->execute(array($shot->playerID, $shot->type, $shot->success, $shot->gameID, $lineID, $shot->time, $shot->isHome));
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
    
    static function getShot($gameID, $time, $playerID, $isHome, $made)
    {
        global $db;
        if($db)
        {
            $query = "SELECT shotID FROM shot WHERE gameID = ? AND time = ? AND playerID = ? AND home = ? AND made = ?";
            $sh = $db->prepare($query);
            $result = $sh->execute(array($gameID, $time, $playerID, $isHome, $made));
            if(!$result)
            {
                echo "Error: Something went wrong when finding a shot";
                exit();
            }
            $row = $sh->fetch();
            if($row)
            {
                return $row['shotID'];
            }
        }
    }
    
    static function addShotClockTime($shotID, $shotclock)
    {
        global $db;
        if($db)
        {
            $query = "INSERT INTO shotdetails (shotID, shotclock) VALUES (:shotID, :shotclock) ON DUPLICATE KEY UPDATE shotclock = :shotclock";
            $sh = $db->prepare($query);
            $result = $sh->execute(array('shotID'=>$shotID, 'shotclock'=>$shotclock));
            if(!$result)
            {
                echo "Error: Something went wrong when adding shot clock time at line $lineID: ".$sh->errorCode();
                exit();
            }
        }
    }
    
    static function addShotLocation($shotID, $X, $Y, $lineID)
    {
        global $db;
        if($db)
        {
            $query = "INSERT INTO shotdetails (shotID, X, Y) VALUES (:shotID, :X, :Y) ON DUPLICATE KEY UPDATE X = :X AND Y = :Y";
            $sh = $db->prepare($query);
            $result = $sh->execute(array('shotID'=>$shotID, 'X'=>$X, 'Y'=>$Y));
            if(!$result)
            {
                echo "Error: Something went wrong when adding a shot location at line $lineID: ".$sh->errorCode();
                exit();
            }
        }
    }
    
    static function addMissingShotLocation($gameID, $totalms, $playerID, $made, $isHome, $X, $Y, $lineID)
    {
        global $db;
        if($db)
        {
            $query = "INSERT IGNORE INTO missingshotlocation (gameID, time, playerID, made, home, X, Y, lineID) VALUES (:gameID, :time, :playerID, :made, :home, :X, :Y, :lineID)";
            $sh = $db->prepare($query);
            $result = $sh->execute(array('gameID'=>$gameID, 'time'=>$totalms, 'playerID'=>$playerID, 'made'=>$made, 'home'=>$isHome, 'X'=>$X, 'Y'=>$Y, 'lineID'=>$lineID));
            if(!$result)
            {
                echo "Error: Something went wrong when inserting a missing shot location at line $lineID: ".$sh->errorCode();
                exit();
            }
        }
    }
    
    static function insertFreeThrow(&$freethrow, $lineID)
    {
        global $db;
        if($db)
        {
            $query = "INSERT IGNORE INTO shot (playerID, type, made, gameID, lineID, time, home) VALUES (?, ?, ?, ?, ?, ?, ?);";
            $sh = $db->prepare($query);
            $result = $sh->execute(array($freethrow->playerID, $freethrow->type, $freethrow->success, $freethrow->gameID, $lineID, $freethrow->time, $freethrow->isHome));
            $freethrow->shotID = $db->lastInsertID();
            if(!$result)
            {
                echo "Error: Something went wrong inserting a free throw at line $lineID: ".$sh->errorCode();
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
            $query = "INSERT IGNORE INTO foul (foulerID, type, referee, gameID, lineID, time, home) VALUES (?, ?, ?, ?, ?, ?, ?);";
            $sh = $db->prepare($query);
            $result = $sh->execute(array($foul->foulerID, $foul->type, $foul->referee, $foul->gameID, $lineID, $foul->time, $foul->isHome));
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
        $query = "INSERT IGNORE INTO rebound (playerID, gameID, lineID, time, home, offensive) VALUES (?, ?, ?, ?, ?, ?);";
        $sh = $db->prepare($query);
        $result = $sh->execute(array($rebound->playerID, $rebound->gameID, $lineID, $rebound->time, $rebound->isHome, $rebound->offensive));
        if(!$result)
        {
            echo "Error: Something went wrong inserting a rebound at line $lineID: ".$sh->errorCode();
            exit();
        }
    }
    
    static function insertTurnover(&$turnover, $lineID)
    {
        global $db;
        $query = "INSERT IGNORE INTO turnover (playerID, gameID, lineID, time, home, type) VALUES (?, ?, ?, ?, ?, ?);";
        $sh = $db->prepare($query);
        $result = $sh->execute(array($turnover->playerID, $turnover->gameID, $lineID, $turnover->time, $turnover->isHome, $turnover->type));
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
        $players = [];
        while($res = $sh->fetch())
        {
            $player = new Player($res['firstname'], $res['lastname']);
            $player->playerID = $res['playerID'];
            $player->team = $res['team'];
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
    
    static function getTeams()
    {
        global $db;
        $query = "SELECT * FROM team";
        $sh = $db->prepare($query);
        $sh->execute();
        $teams = [];
        while($row = $sh->fetch())
        {
            $teams[] = new Team($row['shortName'], $row['city'], $row['teamName']);
        }
        return $teams;
    }
    
    static function getShotsFromPlayer($playerID)
    {
        global $db;
        $query = "SELECT * FROM shot WHERE playerID = ? ORDER BY type";
        $sh = $db->prepare($query);
        $sh->execute(array($playerID));
        
        $shots = [];
        $totalpts = 0;
        while($row = $sh->fetch())
        {
            $type = $row['type'];
            if($row['made'])
            {
                if(strpos($type, "3") !== false){
                    $totalpts += 3;
                }               
                else if($type == "Free Throw"){
                    $totalpts += 1;
                }
                else{
                    $totalpts += 2;
                }
            }
        }
        
        return $totalpts;
    }
    
    static function getShots($playerID)
    {
        global $db;
        $query = "SELECT * FROM shot WHERE playerID = ? ORDER BY time";
        $sh = $db->prepare($query);
        $sh->execute(array($playerID));
        
        $shots = [];
        while($row = $sh->fetch())
        {
            $shot = new stdClass();
            $shot->time = $row['time'];
            $shot->type = $row['type'];
            $shot->made = intval($row['made']);
            $gameID = $row['gameID'];
            if(!array_key_exists($gameID, $shots))
            {
                $shots[$gameID] = [];
            }
            $shots[$gameID][] = $shot;
        }
        
        return $shots;
    }
    
    static function getAssists($playerID)
    {
        global $db;
        $query = "SELECT shot.gameID, shot.time FROM shot JOIN (SELECT * FROM assist WHERE playerID = ?) playerassists ON shot.shotID = playerassists.shotID ORDER BY shot.time";
        $sh = $db->prepare($query);
        $sh->execute(array($playerID));
        
        $assists = [];
        while($row = $sh->fetch())
        {
            $assist = new stdClass();
            $assist->time = $row['time'];
            $gameID = $row['gameID'];
            if(!array_key_exists($gameID, $assists))
            {
                $assists[$gameID] = [];
            }
            $assists[$gameID][] = $assist;
        }
        
        return $assists;
    }
    
    static function getShiftsFromPlayer($playerID)
    {
        global $db;
        $query = "SELECT * FROM shift WHERE playerID = ? ORDER BY starttime;";
        $sh = $db->prepare($query);
        $sh->execute(array($playerID));
        
        $shifts = [];
        while($row = $sh->fetch())
        {
            $shift = new stdClass();
            $shift->starttime = $row['starttime'];
            $shift->endtime = $row['endtime'];
            $gameID = $row['gameID'];
            if(!array_key_exists($gameID, $shifts))
            {
                $shifts[$gameID] = array();
            }
            $shifts[$gameID][] = $shift;
        }
        
        return $shifts;
    }
    
    static function getRebounds($playerID)
    {
        global $db;
        $query = "SELECT * FROM rebound WHERE playerID = ? ORDER by time";
        $sh = $db->prepare($query);
        $sh->execute(array($playerID));
        
        $rebounds = [];
        while($row = $sh->fetch())
        {
            $rebound = new stdClass();
            $rebound->time = $row['time'];
            $rebound->offensive = $row['offensive'];
            $gameID = $row['gameID'];
            if(!array_key_exists($gameID, $rebounds))
            {
                $rebounds[$gameID] = [];
            }
            $rebounds[$gameID][] = $rebound;
        }
        
        return $rebounds;
    }
    
    static function getBlocks($playerID)
    {
        global $db;
        $query = "SELECT shot.gameID, shot.time FROM shot JOIN (SELECT * FROM block WHERE playerID = ?) playerblocks ON shot.shotID = playerblocks.shotID ORDER BY shot.time";
        $sh = $db->prepare($query);
        $sh->execute(array($playerID));
        
        $blocks = [];
        while($row = $sh->fetch())
        {
            $block = new stdClass();
            $block->time = $row['time'];
            $gameID = $row['gameID'];
            if(!array_key_exists($gameID, $blocks))
            {
                $blocks[$gameID] = [];
            }
            $blocks[$gameID][] = $block;
        }
        
        return $blocks;
    }
    
    static function getTurnovers($playerID)
    {
        global $db;
        $query = "SELECT * FROM turnover WHERE playerID = ? ORDER BY time";
        $sh = $db->prepare($query);
        $sh->execute(array($playerID));
        
        $turnovers = [];
        while($row = $sh->fetch())
        {
            $turnover = new stdClass();
            $turnover->time = $row['time'];
            $gameID = $row['gameID'];
            if(!array_key_exists($gameID, $turnovers))
            {
                $turnovers[$gameID] = [];
            }
            $turnovers[$gameID][] = $turnover;
        }
        
        return $turnovers;
    }
    
    static function getSteals($playerID)
    {
        global $db;
        $query = "SELECT turnover.gameID, turnover.time FROM turnover JOIN (SELECT * FROM steal WHERE playerID = ?) playersteals ON turnover.turnoverID = playersteals.turnoverID ORDER BY turnover.time";
        $sh = $db->prepare($query);
        $sh->execute(array($playerID));
        
        $steals = [];
        while($row = $sh->fetch())
        {
            $steal = new stdClass();
            $steal->time = $row['time'];
            $gameID = $row['gameID'];
            if(!array_key_exists($gameID, $steals))
            {
                $steals[$gameID] = [];
            }
            $steals[$gameID][] = $steal;
        }
        
        return $steals;
    }
    
    static function getGamesPlayedbyPlayer($playerID)
    {
        global $db;
        $query = "SELECT game.gameID, date, hometeam, awayteam FROM game JOIN (SELECT DISTINCT(gameID) FROM shift WHERE playerID = ?) gamesplayed ON game.gameID = gamesplayed.gameID;";
        $sh = $db->prepare($query);
        $sh->execute(array($playerID));
        $games = [];
        while($row = $sh->fetch())
        {
            $game = new stdClass();
            $game->gameID = $row['gameID'];
            $game->home = $row['hometeam'];
            $game->away = $row['awayteam'];
            $game->date = $row['date'];
            $games[$game->gameID] = $game;
        }
        return $games;
    }
    
    static function getGamesInDateOrder()
    {
        global $db;
        $query = "SELECT * FROM game WHERE (date BETWEEN FROM_UNIXTIME(:startdate) AND FROM_UNIXTIME(:enddate)) ORDER BY gameID;";
        $sh = $db->prepare($query);
        $sh->execute(array('startdate'=>strtotime("2016-10-25"), 'enddate'=>strtotime("today")));
        $games = [];
        while($row = $sh->fetch())
        {
            $game = new stdClass();
            $game->gameID = $row['gameID'];
            $game->date = $row['date'];
            $game->home = $row['hometeam'];
            $game->away = $row['awayteam'];
            if(!array_key_exists($game->date, $games))
            {
                $games[$game->date] = [];
            }
            $games[$game->date][] = $game;
        }
        return $games;
    }
    
    static function prepareQuery($name, $query)
    {
        if(!isset($this->$name)) {
            $this->$name = $this->db->prepare($query);
        }
        return $this->$name;
    }
}