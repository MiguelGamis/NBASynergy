<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require("queries.php");
require("basic.php");

$shotsProcessor = new shotsProcessor();

if ($handle = opendir('scraper/shots/')) {
    while (false !== ($file = readdir($handle)))
    {
        if (strtolower(substr($file, strrpos($file, '.') + 1)) == 'csv')
        {
            $shotsProcessor->processGame($file);
        }
    }
    closedir($handle);
}

//$shotsProcessor->processGame("21600001-20161025-NYKCLE.csv");

class shotsProcessor{
    
    private $home;
    private $away;
    
    function processGame($gamefile)
    {
        $fcontents = file_get_contents("scraper/shots/".$gamefile);
        //$lastslash = strrpos($gamefile, "/");
        //$gamefile = substr($gamefile, $lastslash+1);
        if(!preg_match("/^[0-9]{8}-[0-9]{8}-[A-Z]{6}.csv/",$gamefile))
        {
            echo "Error: invalid file name for processing game shots '$gamefile'";
            exit();
        }
        echo "Processing '$gamefile'...";
        $gameID = intval(substr($gamefile, 0, 8));
        $date = substr($gamefile, 9, 8);
        $date = substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2);
        $this->away = substr($gamefile, 18, 3);
        $this->home = substr($gamefile, 21, 3);
        
        $lines = explode("\r\n",$fcontents);
        var_dump("scraper/shots/"+$gamefile);
        
        $isHome = false;
        
        foreach($lines as $line_id => $line)
        {
            if($line == '***End of scrape***')
            {
                echo "End of play by play scrape";
                return;
            }
            else if($line == $this->away)
            {
                $isHome = false;
                continue;
            }else if($line == $this->home){
                $isHome = true;
                continue;
            }
            
            $lineID = $line_id + 1;
            $linecomponents = explode(",", $line);
            if(sizeof($linecomponents) != 7)
            {
                echo "Error: shot line '$line' does not 7 components at line $lineID";
                exit();
            }
            $quarter = $linecomponents[0];
            $clock = $linecomponents[1];
            
            #TIME
            $totalms = timeintotalms($quarter, $clock);
            
            $made = $linecomponents[2] == "true";
            $playerID = $linecomponents[4];
            $X = $linecomponents[5];
            $Y = $linecomponents[6];
            
            $shotID = DataManager::getShot($gameID, $totalms, $playerID, $isHome, $made);
            if($shotID)
            {
                DataManager::addShotLocation($shotID, $X, $Y, $lineID);
            }
            else
            {
                DataManager::addMissingShotLocation($gameID, $totalms, $playerID, $made, $isHome, $X, $Y, $lineID);
            }
        }
    }
}