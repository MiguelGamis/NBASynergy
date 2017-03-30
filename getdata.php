<?php
require_once("queries.php");
require_once("classes.php");
require_once("basic.php");

$action = $_GET['action'];

if($action == "getplayers")
{
    $team = $_GET['team'];
    
    $players = DataManager::getPlayersFromTeam($team);
    
    echo json_encode($players);
}
else if($action == "getplayerdata")
{    
    $playerID = intval($_GET['playerID']);

    $shots = DataManager::getShots($playerID);
    $assists = DataManager::getAssists($playerID);
    $totalassists = array_reduce($assists, function($carry, $gameassists){$carry+=sizeof($gameassists); return $carry;});
    $rebounds = DataManager::getRebounds($playerID);
    $blocks = DataManager::getBlocks($playerID);
    $steals = DataManager::getSteals($playerID);
    $turnovers = DataManager::getTurnovers($playerID);
    $games = DataManager::getGamesPlayedbyPlayer($playerID);
    
    if(isset($_GET['otherplayerID1']))
    {
        $otherplayerID1 = intval($_GET['otherplayerID1']);
        
        $shifts = DataManager::getShiftsFromPlayer($playerID);
        $othershifts1 = DataManager::getShiftsFromPlayer($otherplayerID1);
        
        $matchedintervals = overlappingshifts($shifts, $othershifts1);
        
        #SIFT SHOTS STATS
        $shotsplits = siftstats($shots, $matchedintervals);
        $shotswithout = $shotsplits["without"];
        $shotswith = $shotsplits["with"];
        $shotstatswithout = getShotStats($shotswithout);
        $shotstatswith = getShotStats($shotswith);
        
        $totalshotswithout = $shotstatswithout['totalshots'];
        $shotsmadewithout = $shotstatswithout['shotsmade'];
        $freethrowsmadewithout = $shotstatswithout['freethrowsmade'];
        $totalfreethrowswithout = $shotstatswithout['totalfreethrows'];
        $_3pointersmadewithout = $shotstatswithout['3pointersmade'];
        $total3pointerswithout = $shotstatswithout['total3pointers'];
        
        $FGwithout = $totalshotswithout == 0 ? 0 : number_format($shotsmadewithout/$totalshotswithout, 3);
        $_3FGwithout = $total3pointerswithout == 0 ? 0: number_format($_3pointersmadewithout/$total3pointerswithout, 3);
        
        $totalshotswith = $shotstatswith['totalshots'];
        $shotsmadewith = $shotstatswith['shotsmade'];
        $freethrowsmadewith = $shotstatswith['freethrowsmade'];
        $totalfreethrowswith = $shotstatswith['totalfreethrows'];
        $_3pointersmadewith = $shotstatswith['3pointersmade'];
        $total3pointerswith = $shotstatswith['total3pointers'];
        
        $FGwith = $totalshotswith == 0 ? 0 : number_format($shotsmadewith/$totalshotswith, 3);
        $_3FGwith = $total3pointerswith == 0 ? 0: number_format($_3pointersmadewith/$total3pointerswith, 3);
        
        #SIFT REBOUNDS STATS
        $reboundsplits = siftstats($rebounds, $matchedintervals);
        $reboundswithout = $reboundsplits["without"];
        $reboundswith = $reboundsplits["with"];
        $offreboundswithout = 0;
        $defreboundswithout = 0;
        foreach($reboundswithout as $rebound)
        {
            if($rebound->offensive)
                $offreboundswithout++;
            else
                $defreboundswithout++;
        }
        $totalreboundswithout = $offreboundswithout + $defreboundswithout;
    
        $offreboundswith = 0;
        $defreboundswith = 0;
        foreach($reboundswith as $rebound)
        {
            if($rebound->offensive)
                $offreboundswith++;
            else
                $defreboundswith++;
        }
        $totalreboundswith = $offreboundswith + $defreboundswith;
        
        #SIFT ASSISTS STATS
        $assistsplits = siftstats($assists, $matchedintervals);
        $assistswithout = sizeof($assistsplits["without"]);
        $assistswith = sizeof($assistsplits["with"]);
        
        #SIFT BLOCK STATS
        $blocksplits = siftstats($blocks, $matchedintervals);
        $blockswithout = sizeof($blocksplits["without"]);
        $blockswith = sizeof($blocksplits["with"]);
        
        #SIFT STEAL STATS
        $stealsplits = siftstats($steals, $matchedintervals);
        $stealswithout = sizeof($stealsplits["without"]);
        $stealswith = sizeof($stealsplits["with"]);
        
        #SIFT TURNOVERS
        $turnoversplits = siftstats($turnovers, $matchedintervals);
        $turnoverswithout = sizeof($turnoversplits["without"]);
        $turnoverswith = sizeof($turnoversplits["with"]);

        $totalminuteswith = 0;
        foreach($matchedintervals as $gameID => $gamematchedintervals)
        {
            $totalminuteswith += array_reduce($gamematchedintervals, function($carry, $interval){
                $length = ($interval->endtime - $interval->starttime);
                $carry += $length;
                return $carry;
            });
        }
        
        $totalminutes = 0;
        foreach($matchedintervals as $gameID => $gamematchedintervals)
        {
            $totalminutes += array_reduce($shifts[$gameID], function($carry, $interval){
                $carry += ($interval->endtime - $interval->starttime);
                return $carry;
            });
        }
        $totalminuteswithout = $totalminutes - $totalminuteswith;
        $minswithout = number_format($totalminuteswithout/60000, 1);
        $minswith = number_format($totalminuteswith/60000, 1);
        
        $html = "<table>
        <tr>
        </th><th><th>MIN</th><th>FGM</th><th>FGA</th><th>FG%</th><th>3PM</th><th>3PA</th><th>3FG%</th><th>OREB</th><th>DREB</th><th>REB</th><th>AST</th><th>BLK</th><th>STL</th><th>TO</th>
        </tr>";
        
        $html .= "<tr><td>Without</td><td>$minswithout</td><td>$shotsmadewithout</td><td>$totalshotswithout</td><td>$FGwithout</td><td>$_3pointersmadewithout</td><td>$total3pointerswithout</td><td>$_3FGwithout</td><td>$offreboundswithout</td><td>$defreboundswithout</td><td>$totalreboundswithout</td><td>$assistswithout</td><td>$blockswithout</td><td>$stealswithout</td><td>$turnoverswithout</td></tr>";
        $html .= "<tr><td>With</td><td>$minswith</td><td>$shotsmadewith</td><td>$totalshotswith</td><td>$FGwith</td><td>$_3pointersmadewithout</td><td>$total3pointerswith</td><td>$_3FGwith</td><td>$offreboundswith</td><td>$defreboundswith</td><td>$totalreboundswith</td><td>$assistswith</td><td>$blockswith</td><td>$stealswith</td><td>$turnoverswith</td></tr>";
        
        var_dump(json_encode($shotstatswithout['types']));
        var_dump(json_encode($shotstatswith['types']));
        
        $html .= "</table>";
            
        echo $html;
        return;
    }

    $points = 0; $totalfreethrows = 0; $freethrowsmade = 0; $_3pointersmade = 0; $total3pointers = 0; $totalshots = 0; $shotsmade = 0;
    $totalblocks = 0;$totalsteals = 0; $totalturnovers = 0; $defrebounds = 0; $offrebounds = 0;
    foreach($shots as $gameshots)
    {
        foreach($gameshots as $shot)
        {
            if($shot->type == "Free Throw")
            {
                $totalfreethrows++;
                if($shot->made)
                {
                    $freethrowsmade++;
                    $points += 1;
                }
            }
            else if($shot->type[0] == 3){
                $total3pointers++;
                $totalshots++;
                if($shot->made)
                {
                    $shotsmade++;
                    $_3pointersmade++;
                    $points += 3;
                }
            }
            else
            {
                $totalshots++;
                if($shot->made)
                {
                    $shotsmade++;
                    $points += 2;
                }
            }
        }
    }

    foreach($rebounds as $gamerebounds)
    {
        foreach($gamerebounds as $rebound)
        {
            if($rebound->offensive)
                $offrebounds++;
            else
                $defrebounds++;
        }
    }
    $totalrebounds = $defrebounds + $offrebounds;
    
    foreach($blocks as $gameblocks)
    {
        $totalblocks += sizeof($gameblocks);
    }

    foreach($steals as $gamesteals)
    {
        $totalsteals += sizeof($gamesteals);
    }

    foreach($turnovers as $gameturnovers)
    {
        $totalturnovers += sizeof($gameturnovers);
    }
    
    $html = "<table>
    <tr>
    <th>PTS</th><th>FG%</th><th>3FG%</th><th>FT%</th><th>AST</th><th>REB</th><th>BLK</th><th>STL</th><th>TO</th>
    </tr>";

    $PPG = $games == 0 ? 0 : number_format($points/$games, 1);
    $FG = $totalshots == 0 ? 0 : number_format($shotsmade/$totalshots, 3);
    $_3FG = $total3pointers == 0 ? 0: number_format($_3pointersmade/$total3pointers, 3);
    $FT = $totalfreethrows == 0 ? 0 : number_format($freethrowsmade/$totalfreethrows, 3);
    $AST = $games == 0 ? 0 : number_format($totalassists/$games, 1);
    $REB = $games == 0 ? 0 : number_format($totalrebounds/$games, 1);
    $BLK = $games == 0 ? 0 : number_format($totalblocks/$games, 1);
    $STL = $games == 0 ? 0 : number_format($totalsteals/$games, 1);
    $TO = $games == 0 ? 0 : number_format($totalturnovers/$games, 1);
    
    $html .= "<tr><td>$PPG</td><td>$FG</td><td>$_3FG</td><td>$FT</td><td>$AST</td><td>$REB</td><td>$BLK</td><td>$STL</td><td>$TO</td></tr>";
    
    $html .= "</table>";

    echo $html;
}

function getShotStats($shots){
    $stats = ["points" => 0, "shotsmade" => 0, "totalshots" => 0, "freethrowsmade" => 0, "totalfreethrows" => 0, "3pointersmade" => 0, "total3pointers" => 0, "types" => []];
    foreach($shots as $shot)
    {
        if($shot->type == "Free Throw")
        {
            $stats['totalfreethrows']++;
            if($shot->made)
            {
                $stats['freethrowsmade']++;
                $stats['points'] += 1;
            }
        }
        else if($shot->type[0] == 3){
            if(!array_key_exists($shot->type, $stats['types']))
                $stats['types'][$shot->type] = 0;
            $stats['types'][$shot->type]++;

            $stats['total3pointers']++;
            $stats['totalshots']++;
            if($shot->made)
            {
                $stats['shotsmade']++;
                $stats['3pointersmade']++;
                $stats['points'] += 3;
            }
        }
        else
        {
            if(!array_key_exists($shot->type, $stats['types']))
                $stats['types'][$shot->type] = 0;
            $stats['types'][$shot->type]++;

            $stats['totalshots']++;
            if($shot->made)
            {
                $stats['shotsmade']++;
                $stats['points'] += 2;
            }
        }
    }
    return $stats;
}

function overlappingshifts($playershifts, $othershifts)
{
    $matchedintervals = [];
    #find games they played together
    $gamesmatchedup = array_intersect(array_keys($playershifts), array_keys($othershifts));
    
    foreach($gamesmatchedup as $gameID)
    {
        $matchedintervals[$gameID] = [];
        $playershift = current($playershifts[$gameID]);
        $othershift = current($othershifts[$gameID]);
        while($playershift !== false && $othershift !== false)
        {
            if($playershift->starttime >= $othershift->endtime)
            {
                $othershift = next($othershifts[$gameID]);
                continue;
            }
            if($playershift->endtime <= $othershift->starttime)
            {                
                $playershift = next($playershifts[$gameID]);
                continue;
            }
            
            $matchedinterval = new stdClass();
            $matchedinterval->starttime = max(array($playershift->starttime, $othershift->starttime));
            $matchedinterval->endtime = min(array($playershift->endtime, $othershift->endtime));
            $matchedintervals[$gameID][] = $matchedinterval;
            
//            if($playershift->starttime != $othershift->starttime)
//            {
//                $unmatchedinterval = new stdClass();
//                $unmatchedinterval->starttime = min(array($playershift->starttime, $othershift->starttime));
//                $unmatchedinterval->endtime = max(array($playershift->starttime, $othershift->starttime));
//            }
//            if($playershift->endtime != $othershift->endtime)
//            {
//                $unmatchedinterval = new stdClass();
//                $unmatchedinterval->starttime = min(array($playershift->endtime, $othershift->endtime));
//                $unmatchedinterval->endtime = max(array($playershift->endtime, $othershift->endtime));
//                $unmatchedintervals[$gameID][] = $playershift;
//            }
            
            if($playershift->endtime < $othershift->endtime)
            {
                $playershift = next($playershifts[$gameID]);
            }
            else if($playershift->endtime > $othershift->endtime)
            {
                $othershift = next($othershifts[$gameID]);
            }
            else 
            {
                $playershift = next($playershifts[$gameID]);
                $othershift = next($othershifts[$gameID]);
            }
        }
//        while($playershift !== false)
//        {
//            echo "extra shift:"; 
//            var_dump($playershift);
//            echo "</br>";
//            
//            $unmatchedinterval = new stdClass();
//            $unmatchedinterval->starttime = $playershift->starttime;
//            $unmatchedinterval->endtime = $playershift->endtime;
//            $unmatchedintervals[$gameID][] = $playershift;
//            $playershift = next($playershifts[$gameID]);
//        }
    }
    return $matchedintervals;
}

function siftstats($stats, $matchedintervals)
{
    $splitstats = ["with"=>[], "without"=>[]];
    foreach($stats as $gameID => $gamestats)
    {
        if(!array_key_exists($gameID, $matchedintervals))
        {
            continue;
        }
        $intervals = $matchedintervals[$gameID]; $size = sizeof($intervals); $i = 0;
        foreach($gamestats as $stat){
            $with = false;
            while($i < $size)
            {
                $interval = $intervals[$i];
                if($interval->starttime > $stat->time || $interval->endtime < $stat->time)
                {
                    $i++;
                    continue;
                }
                $with = true;
                break;
            }
            if($with)
            {
                $splitstats["with"][] = $stat;
            }
            else
            {
                $splitstats["without"][] = $stat;
            }
        }
    }
    return $splitstats;
}

?>