<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("inc/common.php");
require_once("synergystatscontrols.php");

$content .= "<div id='synergyboard'>";

$content .= "<div style='width:15%'>";
$content .= "<img id='team-picked-logo'/>";
$content .= show_team_select();
$content .= "</div>";

$content .= "<div style='width:15%'>";
$content .= "<div id='player-identifier'><img id='player-picked-photo'/><div id='player-picked-name'></div></div>";
$content .= prepare_player_select();
$content .= "</div>";

$content .= "<div style='width:55%'><div id='matchup-players-identifier'></div>".prepare_multi_player_checkboxes()."</div>";

$content .= "<div style='width:15%'>";
$content .= "<img id='matchup-team-picked-logo'/>";
$content .= show_matchup_team_select();
$content .= "</div>";

$content .= "<input id='matchup-team-picked' type='hidden'></input>"
        . "<input id='team-picked' type='hidden'></input>"
        . "<input id='player-picked' type='hidden'></input>";
        //. "<input id='matchup-players-picked' type='hidden'></input>";

$content .= "</div>";
$content .= "<button id='compute-button'>See matchup stats</button>";
$content .= "<div id='playerdata'></div>";
$content .= "<div id='customdata'></div>";

$content .= "<script src='synergystats.js'></script>";

render_page();