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
    $namestring = trim($namestring);
    foreach($players as $player)
    {
        if(endsWith($namestring, $player->lastname))
        {
            $lastnamepos = strpos($namestring, $player->lastname);
            $remainder = substr($namestring, 0, $lastnamepos);
            startsWith($remainder, $player->firstname);
            return $player;
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
    $ms = quarterbasetime($quarter) + (720000 - (floatval($timecomponents[0]) * 60000 + floatval($timecomponents[1]) * 1000));
    echo "$clockstring => $ms <br/>";
    return $ms;
}

function quarterbasetime($quarter)
{
    return $quarter > 4 ? 2880000 + ($quarter - 5) * 300000 : ($quarter - 1) * 720000;
}

function getPlayerInPlay($play, $teamplayers)
{
    $matchingplayers = array();
    $maxlength = 0;
    $minpos = INF;
    foreach($teamplayers as $player)
    {
        $pos = strpos($play, $player->lastname);
        if($pos !== false && $pos < $minpos)
        {   
            $minpos = $pos;
            $matchingplayers = array();
            $maxlength = strlen($player->lastname);
            $matchingplayers[] = $player;
        }
        else if($pos !== false && $pos == $minpos)
        {
            if($maxlength < strlen($player->lastname))
            {
                $matchingplayers = array();
                $maxlength = strlen($player->lastname);
                $matchingplayers[] = $player;
            }
            else if($maxlength == strlen($player->lastname))
            {
                $matchingplayers[] = $player;
            }
        }
    }
    
    if(sizeof($matchingplayers) > 1)
    {
        $unknownPlayer = new Player('Unknown', 'Player');
        $unknownPlayer->playerID = 0;
        return $unknownPlayer;
    }
    else if(sizeof($matchingplayers) == 1)
    {
        return $matchingplayers[0];
    }
}

function getTypeInPlay(&$item, $play, $player)
{
    $type = gettype($item);
    if($type == 'Turnover')
    {
        $start = strpos($play, $player->lastname) + 1;
        $end = strpos($play, 'Turnover') - 1;
        $item->type = substr($play, $start, $end-$start);
    }
}