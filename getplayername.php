<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('queries.php');

if(isset($_GET['playerID']))
{
    $playerID = $_GET['playerID'];
    $player = DataManager::getPlayerByID($playerID);
    if($player)
    {
        echo $player->firstname.' '.$player->lastname;
    }
}
