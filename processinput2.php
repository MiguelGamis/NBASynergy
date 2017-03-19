<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require("classes.php");
    require("queries.php");
    require("basic.php");

    $filename = "testtest-20161102-TORWAS.csv";
    $fcontents = file_get_contents("scraper/games/".$filename);
    $date = substr($filename, 9, 8);
    $date = substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2);
    $dashpos = strpos($filename, "-");
    $away = substr($filename, 18, 3);
    $home = substr($filename, 21, 3);
    $game = new Game($date, $home, $away);
    DataManager::insertGame($game);
    
    $hometeam = DataManager::getTeam($home);
    $awayteam = DataManager::getTeam($away);

    $players = array($home=>array(), $away=>array());
    $shifts = array($home=>array(), $away=>array());

    $lines = explode("\r\n",$fcontents);
    

    $awayline = $lines[0];
    $awayRoster = explode(",", $awayline);
    $homeline = $lines[1];
    $homeRoster = explode(",", $homeline);
    
    initiateRosters($homeRoster, $players, $shifts, $game, $home);
    initiateRosters($awayRoster, $players, $shifts, $game, $away);
    
    $shots = array();
    $assists = array();

    $quarter = 1;
    
    $totalms = 0;
    
    #KEEPING TRACK OF SOME THINGS
    $currentShot = null;
    $currentFoul = null;
    $possessionStartTime = 0;
    $foulQueues = array($home => new SplQueue(), $away => new SplQueue());
    $testQueue = new SplQueue();
    
    foreach($lines as $lineID => $line){
        if($lineID < 2) continue;
        #SPECIAL CASE: LINE IS FOR START OF A PERIOD
        if(strpos($line, 'Start of ') === 0)
        {
            $isOT = strpos($line, 'OT');
            $isReg = strpos($line, 'Q');
            if($isReg !== false)
            {
                $quarter = intval($line[$isReg+1]);
                var_dump("It is period $quarter");
            }
            else if($isOT !== false)
            {
                //Assuming OT10 or higher wont be reached
                $quarter = intval($line[$isOT+1]);
            }
            continue;
        }
        
        $lineparts = explode(',', $line); 
        if(sizeof($lineparts) != 3)
        {
            echo "Error: Three components were not found in line '$line'";
            exit();
        }
        
        $timescore = $lineparts[0];
        $awayplay = $lineparts[1];
        $homeplay = $lineparts[2];
        
        #TIME AND SCORE
        $clockstring = substr($timescore, 0, strpos($timescore, ":") + 3);
        $totalms = timeintotalms($quarter, $clockstring);

        processPlay($awayplay, $awayteam, $lineID);
        processPlay($homeplay, $hometeam, $lineID);
        
        $totalms;
    }
    
    function processPlay($play, $team, $lineID)
    {
        if(strlen($play) < 2)
            return;
        
        global $game, $players, $totalms, $home, $away, $foulQueues;
        
        #First check if this is a team play. If so skip ... for now
        if(strpos(strtolower($play), strtolower($team->teamname)) !== false)
        {
            echo "This is a team play by ".$team->teamname." in play '$play'";
            return;
        }
        
        #First get player in play
        $player = findPlayerInPlay($play, $players[$team->abbrev]);
        if(!$player)
        {
            echo "Error: Player could not be found in play '$play'";
        }
        
        #Then see if home
        $isHome = $team->abbrev == $home;
        
        #Turnover
        if(strpos($play, "Turnover") !== false)
        {
            #not recording these yet
        }
        
        else if(strpos($play, "Free Throw") !== false)
        {
            $freethrow_seq_of_total_pattern = "/[0-9] of [0-9]/";
            preg_match($freethrow_seq_of_total_pattern, $play, $matches, PREG_OFFSET_CAPTURE);
            
            $seq = 1;
            $total = 1;
            
            if(sizeof($matches) > 0)
            { 
                $seqoftotal = $matches[0][0];
                $seqoftotalparts = explode(" ", $seqoftotal);
                if(sizeof($seqoftotalparts) != 3)
                {
                    echo "Error: Unexpected format of free throw number and total";
                    exit();
                }
                $seq = $seqoftotalparts[0];
                $total = $seqoftotalparts[2];
            }
            
            $foulID = NULL;
            if($foulQueues[$team->abbrev]->isEmpty())
            {
                //echo "Error: No shooting fouls found in queue to register for free throw on line $lineID";
                //exit();
            }
            else{
                #Register ID of  shooting foul call recorded earlier to free throw
                $foulID = $foulQueues[$team->abbrev]->bottom()->foulID;
                if($seq == $total)
                {
                    $foulQueues[$team->abbrev]->dequeue();
                }
            }
            
            $success = strpos($play, "MISS") === false;
            $freethrow = new FreeThrow($player->playerID, $game->gameID, $totalms, $success, $isHome, $foulID, $seq, $total);
            DataManager::insertFreeThrow($freethrow, $lineID);
        }
        
        else if(strpos($play, "Shot") !== false || strpos($play, "Layup") !== false || strpos($play, "Dunk"))
        {
            $shot_distance_pattern = "/ [0-9]+\'/";
            preg_match($shot_distance_pattern, $play, $matches, PREG_OFFSET_CAPTURE);
            $distance = null;
            if(sizeof($matches) > 0)
            {
                $distance = str_replace("'", "", $matches[0][0]);
            }
            
            $shotclock = 24;//$totalms - $possessionStartTime;
            
            $start = -1;
            if(sizeof($matches) > 0)
            {
                $start = $matches[0][1] + strlen($matches[0][0]) + 1;
            }
            else
            {
                $start = strpos($play, $player->lastname) + strlen($player->lastname) + 1;
            }
            
            $end = strpos($play, "Shot") + 4;
            if($end === false)
            {
                $end = strpos($play, "Layup") + 5;
            }
            if($end === false)
            {
                $end = strpos($play, "Dunk") + 4;
            }
            
            $type = substr($play, $start);
            
            $success = strpos($play, "MISS") === false;
            
            echo "Shot ".$success? "made":"missed"." by ".$player->lastname." : $distance' $type";
            
            $shot = new Shot($player->playerID, $game->gameID, $totalms, $type, false, $isHome, $distance, $shotclock);
            DataManager::insertShot($shot, $lineID);
        }
        #Foul
        else if(strpos($play, "Foul") !== false || strpos($play, "FOUL") !== false)
        {      
            $start = 0;
            $end = 0;
            $type = substr($play, $start);
            
            $referee_pattern = "/\([A-Z].[A-Za-z]+\)$/";
            preg_match($referee_pattern, $play, $matches, PREG_OFFSET_CAPTURE);
            if(sizeof($matches) == 0)
            {
                echo "Error: Referee not found in foul on play '$play' at line $lineID";
                exit();
            }  
            $referee = str_replace(array('(',')'), '', $matches[0][0]);
            
            $foul = new Foul($game->gameID, $totalms, $player->playerID, $type, $referee, NULL);
            DataManager::insertFoul($foul, $lineID);
            
            if(strpos($play, "Shooting Block Foul") !== false || strpos($play, "S.FOUL") !== false )
            {
                if($team->abbrev == $home){
                    $foulQueues[$away]->enqueue($foul);
                }
                else {
                    $foulQueues[$home]->enqueue($foul);
                }
            }
        }
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