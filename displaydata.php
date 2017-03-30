<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require_once("queries.php");

    $awayshifts = DataManager::getShiftsFromGameById(21600007, false);
    $jsonawayshifts = json_encode($awayshifts);
    
    $homeshifts = DataManager::getShiftsFromGameById(21600007, true);
    $jsonhomeshifts = json_encode($homeshifts);
    
    $awayPlayers = DataManager::getPlayersFromTeam('NYK');

    $homePlayers = DataManager::getPlayersFromTeam('CLE');
    
    $jsonawayPlayers = json_encode($awayPlayers);
    
    $jsonhomePlayers = json_encode($homePlayers);
    
    $graph = "<script>";

    $graph .= "
        window.onload = addOptions($jsonawayPlayers);
        window.onload = addOtherOptions($jsonhomePlayers);
        window.onload = renderGanttShifts($jsonawayshifts, 'away-gantt');
        window.onload = renderGanttShifts($jsonhomeshifts, 'home-gantt');
    ";
        
    $graph .= "</script>";

    echo $graph;