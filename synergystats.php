<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("inc/common.php");
require_once("teamselectcontol.php");

$content .= "<div>";

if(isset($_GET['team']))
{
    $team = $_GET['team'];
    
    $content .= show_team_select($team);
}
else
{    
    $content .= show_team_select();
}
$content .= "</div>";

$content .= "<div>";

$content .= show_others_team_select();

$content .= "</div>";

$content .= "<div id='playerdata'></div>";

$content .= "<div id='customdata'></div>";

$content .= "<script src='teamselect.js'></script>";

render_page();