<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
        require("classes.php");
        require("queries.php");
        require("basic.php");
        
        $fcontents = file_get_contents("Resources/20170216-BOSCHI.txt");
        $home = 'CHI';
        $away = 'BOS';
        $game = DataManager::insertGame(strtotime('2017-02-16'), $home, $away);
        
        $awayRoster =
        "Jae
        Crowder
        Amir
        Johnson
        Al
        Horford
        Marcus
        Smart
        Isaiah
        Thomas
        Gerald
        Green
        Kelly
        Olynyk
        Tyler
        Zeller
        James
        Young
        Terry
        Rozier
        Jonas
        Jerebko";

        $homeRoster = 
        "Jimmy
        Butler
        Taj
        Gibson
        Robin
        Lopez
        Jerian
        Grant
        Michael
        Carter-Williams
        Rajon
        Rondo
        Bobby
        Portis
        Doug
        McDermott
        Cristiano
        Felicio
        Denzel
        Valentine";
        
        $players = array('CHI'=>array(), 'BOS'=>array());
        $shifts = array('CHI'=>array(), 'BOS'=>array());
        
        function initiateRosters($roster, &$players, &$shifts, $game, $team)
        {
            global $home, $away;
            
            $playersstring = preg_split('/\s+/', $roster);
            $playersstring = array_chunk($playersstring , 2);
            $i=0;
            foreach($playersstring as $p)
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
                    $players[$player->lastname] = $player;
                    if($i < 5)
                    {
                        $shift = new shift;
                        $shift->playerID = $player->playerID;
                        $shift->starttime = 0;
                        $shift->gameID = $game->gameID;
                        $shift->isHome = $team == $home ? 1 : 0;
                        $shifts[$player->playerID] = $shift;
                    }
                }
                else
                {
                    echo "alert: couldn't get player";
                }
                $i++;
            }
        }
        
        initiateRosters($homeRoster, $players[$home], $shifts[$home], $game, 'CHI');
        initiateRosters($awayRoster, $players[$away], $shifts[$away], $game, 'BOS');
        
        $lines = explode("\r\n",$fcontents);
        $lines = array_reverse($lines);
        
        $shots = array();
        $assists = array();
        $keywords = ['Rebound', '3pt Shot', 'Shot', 'Layup Shot', 'Reverse Layup Shot', 'Assist'];
        
        $line = current($lines);
        $linenum = 0;
        $quarter = 1;
        while($line !== false)
        {
            if(preg_match("/^Start of [1234]/", $line))
            {
                $quarter = intval($line[9]);
            }
            if(preg_match("/^End of [1234]/", $line))
            {
                foreach($shifts as $team => &$teamshifts)
                {
                    $numteamshifts = sizeof($teamshifts);
                    if($numteamshifts != 5)
                    {
                        echo "$team only has $numteamshifts shifts";
                    }
                    foreach($teamshifts as &$playershift)
                    {
                        $playershift->endtime = $quarter * 720000;
                        DataManager::insertShift($playershift);
                        unset($teamshifts[$playershift->playerID]);
                    }
                }
            }
            //PLAY
            if(preg_match("/^\[([A-Z]{3})\]/", $line) || preg_match("/^\[([A-Z]{3})/", $line) )
            {
                if(preg_match("/(1[012]|0[0-9]):([0-5][0-9])/", $lines[$linenum+3]))
                {
                    //get time in milliseconds
                    $timestring = trim($lines[$linenum+3]);
                    $timecomponents = explode(":", $timestring);
                    $time = ($quarter - 1) * 720000 + (720000 - (floatval($timecomponents[0]) * 60000 + floatval($timecomponents[1]) * 1000));

                    //get team
                    $team = substr($line, 1, 3);

                    //cutoff team from string
//                    $playdetails = preg_replace("/^\[([A-Z]{3})\]\\s/", "", $line);
//                    var_dump("$playdetails");
                    $pos = strpos($line, "] ");
                    $playdetails = substr($line, $pos+2);
                    
                    $spacepos = strpos($playdetails, " ");
                    $lastname = substr($playdetails, 0, $spacepos);
                    $player = getPlayer($lastname, $players[$team]);
                    
                    if(!$player)
                    {
                        echo "could not find $lastname";
                    }
                    else
                    {
                        
                    if(!array_key_exists($player->playerID, $shifts[$team]))
                    {
                        $shift = new shift;
                        $shift->starttime = ($quarter - 1) * 720000;
                        $shift->playerID = $player->playerID;
                        $shift->gameID = $game->gameID;
                        $shift->isHome = $team == $home ? 1 : 0;
                        $shifts[$team][$shift->playerID] = $shift;
                    }
                    
                    if(strpos($line, "Shot") !== false)
                    {
                        $shot = new shot();
                        $shot->time = $time;
                        $shot->playerID = $player->playerID;
                        if(strpos($playdetails, "Missed") !== false)
                        {
                            $shot->success = false;
                            if(strpos($playdetails, "Blocked") !== false)
                            {
                                $block = new block;

                            }
                        }
                        else if(strpos($line, "Made") !== false)
                        {
                            $shot->success = true;
                            $assistpos = strpos($playdetails, "Assist: ");
                            if($assistpos !== false)
                            {
                                //echo "Found Assist <br/>";
                                $assist = new assist;
                                $assist->shot = $shot;
                                $assist->time = $time;
                                $assistdetails = substr($playdetails, $assistpos + 8);
                                $pos2 = strpos($assistdetails, " ");
                                $assister = substr($assistdetails, 0, $pos2);
                                $assist->playerID = $assister;
                                $assists[] = $assist;
                            }
                        }
                    }
                    else if(strpos($line, "Rebound") !== false)
                    {

                    }
                    else if(strpos($line, "Substitution") !== false)
                    {
                        $playeroff = $player;
                        if($playeroff)
                        {
                            if(!array_key_exists($player->playerID, $shifts[$team]))
                                die("<strong>Error</strong> Could not find shift");
                            
                            $shift = $shifts[$team][$playeroff->playerID];
                            $shift->endtime = $time;
                            DataManager::insertShift($shift);
                            unset($shifts[$team][$playeroff->playerID]);

                            //get replacement's lastname
                            $pos2 = strpos($playdetails, "replaced by ");
                            $replastname = substr($playdetails, $pos2 + 12);

                            $playeron = getPlayer($replastname, $players[$team]);
                            if($playeron)
                            {
                                $shifton = new shift;
                                $shifton->playerID = $playeron->playerID;
                                $shifton->starttime = $time;
                                $shifton->gameID = $game->gameID;
                                $shifton->isHome = $team == $home ? 1 : 0;
                                $shifts[$team][$playeron->playerID] = $shifton;
                            }
                        }
                    }
                    }
                }
                else
                {
                    //throw
                }
            }
            
            $line = next($lines);
            $linenum++;
        }