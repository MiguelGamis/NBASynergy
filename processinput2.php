<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require("classes.php");
    require("queries.php");
    require("basic.php");

    $filename = "21600001-20161025-NYKCLE.csv";
    $fcontents = file_get_contents("scraper/games/".$filename);
    $date = substr($filename, 0, 8);
    $date = substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2);
    $dashpos = strpos($filename, "-");
    $away = substr($filename, $dashpos+1, 3);
    $home = substr($filename, $dashpos+4, 3);
    $game = DataManager::insertGame(strtotime($date), $home, $away);

    $players = array($home=>array(), $away=>array());
    $shifts = array($home=>array(), $away=>array());

    $lines = explode("\n",$fcontents);
    $awayline = $lines[0];
    $awayRoster = explode(",", $awayline);
    $homeline = $lines[1];
    $homeRoster = explode(",", $homeline);
    unset($lines[0]); unset($lines[1]);
    
    initiateRosters($homeRoster, $players, $shifts, $game, $home);
    initiateRosters($awayRoster, $players, $shifts, $game, $away);
    
    $shots = array();
    $assists = array();

    $quarter = 1;
    
    $lastesttotalms = 0;
    
    $previousfoul = null;
    
    foreach($lines as $line){
        
        #SPECIAL CASE: LINE IS FOR START OF A PERIOD
        if(strpos($line, 'Start of ') === 0)
        {
            $isOT = strpos($line, 'OT');
            $isReg = strpos($line, 'Q');
            if($isReg)
            {
                $quarter = intval($line[$isReg+1]);
                var_dump("It is period $quarter");
            }
            else if($isOT)
            {
                //Assuming OT10 or higher wont be reached
                $quarter = intval($line[$isOT+1]);
            }
            continue;
        }
        
        $lineparts = explode(',', $line); 
        
        $timescore = $lineparts[0];
        $awayPlay = $lineparts[1];
        $homePlay = $lineparts[2];
        
        #TIME AND SCORE
        $clockstring = substr($timescore, 0, strpos($timescore, ":") + 3);
        var_dump($clockstring);
        $totalms = timeintotalms($quarter, $clockstring);
        
        #AWAY TEAM
        if(strpos($awayPlay, "MISS") !== false)
        {
            $player = findPlayerInPlay($awayplay, $players[$away]);
            
            if(strpos($awayPlay, "Free Throw") !== false)
            {
                $freethrow = new FreeThrow();
                $freethrow->playerID = $player->playerID;
                $freethrow->foulID = $previousfoul;
                $freethrow->made = false;
            }
            
            $shot_distance_pattern = "/ [0-9]+\'/";
            preg_match($shot_distance_pattern, $awayPlay, $matches, PREG_OFFSET_CAPTURE);
            if(sizeof($matches) > 0)
            {
                echo "Shot missed at ".$matches[0][0];
            }
        }
        
        
        
        $latesttotalms = $totalms;
    }
    
    function initiateRosters($roster, &$players, &$shifts, $game, $team)
    {
        $playernames = array_chunk($roster , 2);
        $i=0;
        foreach($playernames as $p)
        {
            $player = DataManager::getPlayer($p[0], $p[1]);
            if(!$player)
            {
                echo "inserting ".$p[0]." ".$p[1]."<br/>";
                DataManager::insertPlayer($p[0], $p[1], $team);
                $player = DataManager::getPlayer($p[0], $p[1]);
            }
            if($player)
            {
                $players[$team][$player->playerID] = $player;
                if($i < 5)
                {
                    $shift = new shift;
                    $shift->playerID = $player->playerID;
                    $shift->starttime = 0;
                    $shift->gameID = $game->gameID;
                    $shift->isHome = $team == $game->home ? 1 : 0;
                    $shifts[$team][$player->playerID] = $shift;
                }
            }
            else
            {
                echo "alert: couldn't get player";
            }
            $i++;
        }
    }