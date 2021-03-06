<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require("classes.php");
    require("queries.php");
    require("basic.php");

    $pbpProcessor = new pbpProcessor;
    
    if ($handle = opendir('scraper/games/')) {
        while (false !== ($file = readdir($handle)))
        {
            if (strtolower(substr($file, strrpos($file, '.') + 1)) == 'csv')
            {
                $pbpProcessor->processGame($file);
            }
        }
        closedir($handle);
    }
    
    class pbpProcessor{
        
        function __construct() {
        }
        
        private $game;
        private $players;
        private $home;
        private $away;
        private $shifts;
        private $rebounds;
        private $quarter = 1;
        private $ot = 0;
        private $totalms = 0;
        
        private $possessionStartTime = 0;
        
        function processGame($gamefile)
        {
            $fcontents = file_get_contents("scraper/games/".$gamefile);
            $date = substr($gamefile, 9, 8);
            $date = substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2);
            $datetime = strtotime($date);
            $awayabbrev = substr($gamefile, 18, 3);
            $homeabbrev = substr($gamefile, 21, 3);
            if(DataManager::selectGame($datetime, $homeabbrev, $awayabbrev))
            {
                echo "Game '$gamefile' is already logged";
                return;
            }
            $this->game = new Game($datetime, $homeabbrev, $awayabbrev);
            DataManager::insertGame($this->game);

            $this->home = DataManager::getTeam($homeabbrev);
            $this->away = DataManager::getTeam($awayabbrev);

            $this->players = array($this->home->abbrev=>array(), $this->away->abbrev=>array());
            $this->shifts = array($this->home->abbrev=>array(), $this->away->abbrev=>array());
            $this->rebounds = array($this->home->abbrev=>array(), $this->away->abbrev=>array());

            $this->quarter = 1;
            $this->ot = 0;
            $this->totalms = 0;
            
            $lines = explode("\r\n",$fcontents);

            $awayline = $lines[0];
            $awayRoster = explode(",", $awayline);
            $homeline = $lines[1];
            $homeRoster = explode(",", $homeline);

            $this->initiateRosters($homeRoster, $this->home);
            $this->initiateRosters($awayRoster, $this->away);
            
            //$this->foulQueues = array($this->home->abbrev => new SplQueue(), $this->away->abbrev => new SplQueue());

            foreach($lines as $lineID => $line){
                if($lineID < 2) continue;

                #SPECIAL CASE: LINE IS FOR START OF A PERIOD
                if(strpos($line, 'Start of ') === 0)
                {   
                    $isOT = strpos($line, 'OT');
                    $isReg = strpos($line, 'Q');
                    if($isReg !== false)
                    {
                        $this->quarter = $line[$isReg+1];
                        var_dump("It is quarter $this->quarter");
                    }
                    else if($isOT !== false)
                    {
                        //Assuming OT10 or higher wont be reached
                        $this->ot = $line[$isOT+1];
                        var_dump("It is OT $this->ot");
                    }
                    continue;
                }

                $lineparts = explode(',', $line); 
                if(sizeof($lineparts) != 3)
                {
                    echo "Error: Three components were not found in line '$line'";
                    return;
                }

                $timescore = $lineparts[0];
                $awayplay = $lineparts[1];
                $homeplay = $lineparts[2];

                #TIME AND SCORE
                $clockstring = substr($timescore, 0, strpos($timescore, ":") + 3);
                $this->totalms = timeintotalms($this->quarter, $this->ot, $clockstring);

                //End shifts at the end of quarter/overtime period. Look for time and score chracteristics
                if($awayplay == "" && $homeplay == "" && preg_match("/[0-9]+ - [0-9]+/", $line))
                {
                    if($this->totalms > 0 && $this->totalms <= 2880000 ? $this->totalms % 720000 == 0 : ($this->totalms - 2880000) % 300000 == 0)
                    {
                        echo "made it";
                        foreach($this->shifts as $team => &$teamshifts)
                        {
                            $numteamshifts = sizeof($teamshifts);
//                            if($numteamshifts != 5)
//                            {
//                                echo "Error $team only has $numteamshifts players on the floor at ".$this->totalms;
//                                exit();
//                            }
                            foreach($teamshifts as &$playershift)
                            {
                                $playershift->endtime = $this->totalms;
                                DataManager::insertShift($playershift);
                                unset($teamshifts[$playershift->playerID]);
                            }
                        }
                    }
                }

                $awayitem = $this->processPlay($awayplay, $this->away, $lineID);
                $homeitem = $this->processPlay($homeplay, $this->home, $lineID);

                if($awayitem && $homeitem)
                {
                    if(get_class($awayitem) == 'Steal')
                    {
                        if(get_class($homeitem) != 'Turnover')
                        {
                            echo "Error: Steal found in $awayplay does not match with turnover in $homeplay at $lineID";
                        }
                        $awayitem->turnoverID = $homeitem->turnoverID;
                        DataManager::insertSteal($awayitem, $lineID);
                    }
                    else if(get_class($homeitem) == 'Steal')
                    {
                        if(get_class($awayitem) != 'Turnover')
                        {
                            echo "Error: Steal found in $homeplay does not match with turnover in $awayplay at $lineID";
                        }
                        $homeitem->turnoverID = $awayitem->turnoverID;
                        DataManager::insertSteal($homeitem, $lineID);
                    }

                    else if(get_class($awayitem) == 'Block')
                    {
                        if(get_class($homeitem) != 'Shot')
                        {
                            echo "Error: Block found in $awayplay does not match with shot in $homeplay at $lineID";
                        }
                        $awayitem->shotID = $homeitem->shotID;
                        DataManager::insertBlock($awayitem, $lineID);
                    }
                    else if(get_class($homeitem) == 'Block')
                    {
                        if(get_class($awayitem) != 'Shot')
                        {
                            echo "Error: Block found in $homeplay does not match with shot in $awayplay at $lineID";
                        }
                        $homeitem->shotID = $awayitem->shotID;
                        DataManager::insertBlock($homeitem, $lineID);
                    }
                }
            }

        }

        private function processPlay($play, $team, $lineID)
        {
            if(strlen($play) < 2)
                return;

            //global $game, $players, $totalms, $home, $shifts, $rebounds, $quarter, $ot;

            #First check if this is a team play. If so skip ... for now
            if(strpos(strtolower($play), strtolower($team->teamname)) !== false)
            {
                echo "This is a team play by ".$team->teamname." in play '$play'";
                return;
            }

            #First get player in play
            $player = getPlayerInPlay($play, $this->players[$team->abbrev]);
            if(!$player)
            {
                echo "Error: Player could not be found in play '$play'";
            }

            #See if this is a home play
            $isHome = $team->abbrev == $this->home->abbrev;

            if(!array_key_exists($player->playerID, $this->shifts[$team->abbrev]))
            {
                $starttime = ($this->quarter - 1) * 720000 + $this->ot * 300000;
                $gameID = $this->game->gameID;
                $shift = new Shift($player->playerID, $gameID, $starttime, $isHome);
                $this->shifts[$team->abbrev][$shift->playerID] = $shift;
            }

            #Turnover
            if(strpos($play, "Turnover") !== false)
            {
                $turnover = new Turnover($player->playerID, $this->game->gameID, $this->totalms);
                getTypeInPlay($turnover, $play, $player);
                DataManager::insertTurnover($turnover, $lineID);
                return $turnover;
            }
            #Steal
            else if(strpos($play, "STEAL") !== false)
            {
                $steal = new Steal($player->playerID, null);
                return $steal;
            }
            #Free throw
            else if(strpos($play, "Free Throw") !== false)
            {
                $freethrow_seq_of_total_pattern = "/[0-9] of [0-9]/";
                $matches = array();
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

                #type is one word after 'Free Throw' but may not be explicitly indicated
                $playwords = explode(' ', $play);
                $foultype = "Regular";
                foreach($playwords as $i => $playword)
                {
                    if($i > sizeof($playwords) - 2)
                    {
                        break;
                    }
                    if($playword == "Free" && $playwords[$i+1] == "Throw")
                    {
                        $j = $i+2;
                        if(!is_int($playwords[$j]))
                        {
                            $foultype = $playwords[$j];
                        }
                    }
                }

                $foulID = NULL;
    //            if(!$foulQueues[$team->abbrev]->isEmpty()){
    //                #Register ID of shooting foul call recorded earlier to free throw
    //                $foulID = $foulQueues[$team->abbrev]->bottom()->foulID;
    //                
    //                echo "Free Throw $seq of $total referencing foul";// $foulID";
    //                if($seq == $total)
    //                {
    //                    $foulQueues[$team->abbrev]->dequeue();
    //                }
    //            }

                $success = strpos($play, "MISS") === false;
                $freethrow = new FreeThrow($player->playerID, $this->game->gameID, $this->totalms, $success, $isHome, $foulID, $foultype, $seq, $total);
                DataManager::insertFreeThrow($freethrow, $lineID);
            }

            else if(strpos($play, "Shot") !== false || strpos($play, "Layup") !== false || strpos($play, "Dunk") !== false || strpos($play, "Jumper") !== false)
            {
                #space at the front to get all digits
                $shot_distance_pattern = "/ [0-9]+\'/";
                preg_match($shot_distance_pattern, $play, $matches, PREG_OFFSET_CAPTURE);
                $distance = null;
                if(sizeof($matches) > 0)
                {
                    $distance = str_replace("'", "", $matches[0][0]);
                }

                $shotclock = 24;

                $start = 0;
                #If the distance is recorded, shot type starts after it
                if(sizeof($matches) > 0)
                {
                    $start = $matches[0][1] + strlen($matches[0][0]) + 1;
                }
                #.. or else shot type is after name
                else
                {
                    $start = strpos($play, $player->lastname) + strlen($player->lastname) + 1;
                }
                $end = 0;
                if(strpos($play, "Shot") !== false)
                {
                    $end = strpos($play, "Shot");
                    $end += 4;
                }
                else if(strpos($play, "Layup") !== false)
                {
                    $end = strpos($play, "Layup");
                    $end += 5;
                }
                else if(strpos($play, "Dunk") !== false)
                {
                    $end = strpos($play, "Dunk");
                    $end += 4;
                }
                else if(strpos($play, "Jumper") !== false)
                {
                    $end = strpos($play, "Jumper");
                    $end += 6;
                }
                $type = substr($play, $start, $end - $start);

                $success = strpos($play, "MISS") === false;
                $isMade = $success? "made":"missed";
                echo "Shot ".$isMade." by ".$player->lastname." : $distance' $type";

                $shot = new Shot($player->playerID, $this->game->gameID, $this->totalms, $type, false, $isHome, $distance, $shotclock);
                DataManager::insertShot($shot, $lineID);

                #ASSIST
                $assist_pattern = "/\([^(]+ [0-9]+ AST\)/";
                preg_match($assist_pattern, $play, $matches, PREG_OFFSET_CAPTURE);
                if(sizeof($matches) > 0)
                {
                    $assistplay = $matches[0][0];
                    $player = getPlayerInPlay($assistplay, $this->players[$team->abbrev]);
                    if(!$player)
                    {
                        echo "Error: could not find player with assist in play '$play' at line $lineID";
                        exit();
                    }
                    $assist = new Assist($player->playerID, $shot->shotID);
                    DataManager::insertAssist($assist, $lineID);
                }
                return $shot;
            }
            #Block
            else if(strpos($play, "BLOCK") !== false)
            {
                $block = new Block($player->playerID);
                return $block;
            }
            #Foul
            else if(strpos($play, "Foul") !== false || strpos($play, "FOUL") !== false)
            {      
                $start = strpos($play, $player->lastname) + strlen($player->lastname) + 1;
                $end = 0;
                if(strpos($play, "Foul") !== false)
                {
                    $end = strpos($play, "Foul");
                    $end += 4;
                }
                else if(strpos($play, "FOUL") !== false)
                {
                    $end = strpos($play, "FOUL");
                    $end += 4;
                }
                $type = substr($play, $start);

                $referee_pattern = "/\([A-Z].[A-Za-z]+\)$/";
                preg_match($referee_pattern, $play, $matches, PREG_OFFSET_CAPTURE);
                if(sizeof($matches) == 0)
                {
                    echo "Error: Referee not found in foul on play '$play' at line $lineID";
                    exit();
                }  
                $referee = str_replace(array('(',')'), '', $matches[0][0]);

                $foul = new Foul($this->game->gameID, $this->totalms, $player->playerID, $type, $referee, NULL);
                DataManager::insertFoul($foul, $lineID);

    //            if(strpos($play, "Shooting Block Foul") !== false || strpos($play, "S.FOUL") !== false )
    //            {
    //                if($team->abbrev == $home){
    //                    echo "Away Foul Enqueued from $lineID: ".$foul->foulID;
    //                    $foulQueues[$away]->enqueue($foul);
    //                }
    //                else {
    //                    echo "Home Foul Enqueued from $lineID: ".$foul->foulID;
    //                    $foulQueues[$home]->enqueue($foul);
    //                }
    //            }
            }
            #Rebound
            else if(strpos($play, "REBOUND") !== false){
                if($player->playerID == 0)
                {
                    $rebound = new Rebound($player->playerID, $this->game->gameID, $this->totalms, false);
                    DataManager::insertRebound($rebound, $lineID);
                    return;
                }
                
                $rebound_off_def_pattern = "/\(Off:[0-9]+ Def:[0-9]+\)/";
                preg_match($rebound_off_def_pattern, $play, $matches, PREG_OFFSET_CAPTURE);
                if(sizeof($matches) == 0)
                {
                    echo "Error: Could not get offensive/defensive rebound data at line $lineID";
                    exit();
                }
                $offrebsmatches = array(); $defrebsmatches = array();
                preg_match("/Off:[0-9]+ /", $matches[0][0], $offrebsmatches, PREG_OFFSET_CAPTURE);
                preg_match("/Def:[0-9]+\)/", $matches[0][0], $defrebsmatches, PREG_OFFSET_CAPTURE);

                $offrebs = intval(substr($offrebsmatches[0][0], 4, -1));
                $defrebs = intval(substr($defrebsmatches[0][0], 4, -1));

                $offensive = true;
                if($offrebs  == $this->rebounds[$team->abbrev][$player->playerID]["off"] && $defrebs == $this->rebounds[$team->abbrev][$player->playerID]["def"] + 1)
                {
                    $offensive = false; $this->rebounds[$team->abbrev][$player->playerID]["def"]++; 
                }
                else if($offrebs  == $this->rebounds[$team->abbrev][$player->playerID]["off"] + 1 && $defrebs == $this->rebounds[$team->abbrev][$player->playerID]["def"])
                {
                    $offensive = true; $this->rebounds[$team->abbrev][$player->playerID]["off"]++; 
                }
                else 
                {
                    echo "Error: Lost track of offensive/defensive rebound data for $player->lastname at line $lineID";
                    exit();
                }
                $rebound = new Rebound($player->playerID, $this->game->gameID, $this->totalms, $offensive);
                DataManager::insertRebound($rebound, $lineID);
            }
            #Substitution
            else if(strpos($play, "SUB:") !== false)
            {
                $subwords = explode(" ", $play);
                $forpos = array_search("FOR", $subwords);
                $subin = implode(" ", array_slice($subwords, 1, $forpos - 1));
                $subout = implode(" ", array_slice($subwords, $forpos + 1));

                $playeron = getPlayer($subin, $this->players[$team->abbrev]);
                if(is_null($playeron)){
                    echo "Error: Player subbing in, $subin, could not be found in player list at line $lineID";
                    exit();
                }

                $playeroff = getPlayer($subout, $this->players[$team->abbrev]);
                if(!array_key_exists($playeroff->playerID, $this->shifts[$team->abbrev])){
                    $starttime = ($this->quarter - 1) * 720000 + $this->ot * 300000;
                    $gameID = $this->game->gameID;
                    $shift = new Shift($playeroff->playerID, $gameID, $starttime, $isHome);
                    $this->shifts[$team->abbrev][$shift->playerID] = $shift;
                }

                echo "$team->abbrev SUB: $playeron->lastname FOR $playeroff->lastname";

                $shiftoff = $this->shifts[$team->abbrev][$playeroff->playerID];
                $shiftoff->endtime = $this->totalms;
                DataManager::insertShift($shiftoff);
                unset($this->shifts[$team->abbrev][$playeroff->playerID]);

                $shifton = new Shift($playeron->playerID, $this->game->gameID, $this->totalms, $isHome);
                $this->shifts[$team->abbrev][$playeron->playerID] = $shifton;
            }
        }

        function initiateRosters($roster, $team)
        {
            $playernames = array_chunk($roster , 2);
            $i=0;
            foreach($playernames as $p)
            {
                $player = DataManager::getPlayer($p[0], $p[1]);
                if(!$player)
                {
                    echo "inserting ".$p[0]." ".$p[1]."<br/>";
                    DataManager::insertPlayer($p[0], $p[1], $team->abbrev);
                    $player = DataManager::getPlayer($p[0], $p[1]);
                }
                if($player)
                {
                    $this->players[$team->abbrev][$player->playerID] = $player;
                    if($i < 5)
                    {
                        $isHome = $team->abbrev == $this->home->abbrev;
                        $shift = new Shift($player->playerID, $this->game->gameID, 0, $isHome);
                        $this->shifts[$team->abbrev][$player->playerID] = $shift;
                    }
                    $this->rebounds[$team->abbrev][$player->playerID] = array("off"=>0, "def"=>0);
                }
                else
                {
                    echo "alert: couldn't get player";
                }
                $i++;
            }
        }
    }