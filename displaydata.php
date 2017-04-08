<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("inc/common.php");
require_once("queries.php");

if(isset($_GET['gameID']))
{
    $gameID = $_GET['gameID'];
    $awayshifts = DataManager::getShiftsFromGameById($gameID, false);

    $homeshifts = DataManager::getShiftsFromGameById($gameID, true);
    
    $jsonpackage = array('home'=>$homeshifts, 'away'=>$awayshifts);
    echo json_encode($jsonpackage);
    return;
}

$content .= "<select id='gameSelect' onChange='gameChange()'>";
$games = DataManager::getGamesInDateOrder();
foreach($games as $dategames)
{
    foreach($dategames as $game)
    {
        $content .= "<option value=$game->gameID>$game->away @ $game->home</option>";
    }
}
$content .= "</select>";

$content .= "<div id='away-gantt'></div>
        <div id='home-gantt'></div>";

$content .= "<script src='gantt-shifts.js'></script>";

render_page();