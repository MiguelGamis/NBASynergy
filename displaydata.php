<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require("queries.php");
    require("classes.php");

    $awayshifts = DataManager::getShiftsFromGame(strtotime('2016-11-02'), 'TOR', 'WAS', false);
    $jsonawayshifts = json_encode($awayshifts);
    
    $awayPlayers = DataManager::getPlayersFromTeam('TOR');

    $homePlayers = DataManager::getPlayersFromTeam('WAS');
    
    $jsonawayPlayers = json_encode($awayPlayers);
    
    $jsonhomePlayers = json_encode($homePlayers);

    var_export($jsonawayshifts);
    
    $graph = "<script>";

    $graph .= "
        window.onload = addOptions($jsonawayPlayers);
        window.onload = addOtherOptions($jsonhomePlayers);
        window.onload = renderGanttShifts($jsonawayshifts);
    ";
        
    $graph .= "</script>";

    echo $graph;