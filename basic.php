<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function getPlayer($namestring, $players)
{
    if(isset($players[$namestring]))
    {
        return $players[$namestring];
    }
    else
    {
        foreach($players as $player)
        {
            if(endsWith($namestring, $player->lastname) && startsWith($player->firstname, substr($namestring, 0, -strlen($player->lastname))))
            {
                return $player;
            }
        }
    }
}

function timeformat($milliseconds)
{
    $minutes = floor($milliseconds/ 60000);
    $leftoverseconds = ($milliseconds - $minutes*60000)/1000;
    if($leftoverseconds < 10) $leftoverseconds = "0".$leftoverseconds;
    return "$minutes:$leftoverseconds";
}

function timeintotalms($quarter, $clockstring)
{
    $timecomponents = explode(":", $clockstring);
    $ms = ($quarter - 1) * 720000 + (720000 - (floatval($timecomponents[0]) * 60000 + floatval($timecomponents[1]) * 1000));
    echo "$clockstring => $ms <br/>";
    return $ms;
}

function findPlayerInPlay($play, $teamplayers)
{
    $matchingplayer = null;
    $length = 0;
    foreach($teamplayers as $player)
    {
        if(strpos($play, $player->lastname) !== false)
        {   
            echo $player->lastname;
            if(strlen($player->lastname) > $length)
            {
                $length = strlen($player->lastname);
                $matchingplayer = $player;
            }
        }
    }
    return $matchingplayer;
}