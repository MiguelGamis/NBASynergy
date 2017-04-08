<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("inc/common.php");
require_once("trends/trendscontrols.php");

$content .= show_player_search();

$content .= "<div id='player-profile'><div>";
$content .= "<div id='player-identifier'><img id='player-picked-photo'/><div id='player-picked-name'></div></div>";
$content .= "<div id='player-trending-statistics'><div>";

render_page();