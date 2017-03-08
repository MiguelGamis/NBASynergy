<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require("queries.php");
    require("classes.php");

    $awayshifts = DataManager::getShiftsFromGame(strtotime('2017-02-16'), 'CHI', 'BOS', 0);

    $awayPlayers = DataManager::getPlayersFromTeam('BOS');

    $awayPlayerNames = array_map(function($player){return $player->firstname." ".$player->lastname; }, $awayPlayers);

    $jsonawayPlayers = json_encode($awayPlayers);

    $graph = "<script>";

    $graph .= "function jerkalert() {
                    alert('you\'re a freakin jerk!')
            }";
    
    $graph .=  "window.onload = function() {
                    var c = document.getElementById('myCanvas');
                    var ctx = c.getContext('2d');
                    ctx.beginPath();
                    ctx.moveTo(400,0);
                    ctx.lineTo(400,400);
                    ctx.stroke();
                    ctx.moveTo(800,0);
                    ctx.lineTo(800,400);
                    ctx.stroke();
                    ctx.moveTo(1200,0);
                    ctx.lineTo(1200,400);
                    ctx.stroke();
                    ";

    $i = 0;
    foreach($awayshifts as $playershifts)
    { 
        foreach($playershifts as $shift)
        {
            $shiftstart = ($shift->starttime/2880000) * 1600;

            $shiftlength = ((intval($shift->endtime) - intval($shift->starttime)) / 2880000) * 1600;

            $graph .= 
            "
                  var timestart = $shiftstart;
                  var timelength = $shiftlength;
                  ctx.rect(timestart, $i*10, timelength, 10);
                  ctx.stroke();
            ";
        }
        $i++;
    }
    
    $graph .= "};";

    $graph .= "
        window.onload = addOptions($jsonawayPlayers)
    ";
        
    $graph .= "</script>";

    echo $graph;