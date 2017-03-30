<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require_once("queries.php");

    $awayshifts = DataManager::getShiftsFromGameById(21600004, false);
    $jsonawayshifts = json_encode($awayshifts);
    
    $graph = "<script>";

    $graph .= "
        window.onload = renderGanttShifts($jsonawayshifts);
    ";
        
    $graph .= "</script>";

    echo $graph;