<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("inc/common.php");
require_once("teamselectcontol.php");

$team = "";
$player = "";
if(isset($_GET['team']) && isset($_GET['playerID']))
{
    $team = $_GET['team'];
    $playerID = $_GET['playerID'];
}
$content .= show_team_select2($team);

render_page();